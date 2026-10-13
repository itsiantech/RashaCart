<?php

namespace App\Services\Tracking\Adapters;

use App\Models\TrackingPlatform;
use App\Services\Tracking\HashingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaAdapter implements TrackingAdapterInterface
{
    protected HashingService $hashingService;
    protected bool $healthy = true;
    protected ?string $healthError = null;

    public function __construct(HashingService $hashingService)
    {
        $this->hashingService = $hashingService;
    }

    /**
     * Send event to Meta Conversions API
     */
    public function send(array $eventData, TrackingPlatform $platform): AdapterResult
    {
        if (!$this->healthy) {
            return AdapterResult::failure('Platform marked as unhealthy (circuit breaker)', $this->healthError, null, false);
        }

        try {
            $pixelId = $platform->pixel_id;
            $accessToken = $platform->access_token;
            $apiVersion = $platform->api_version ?? 'v19.0';
            $testEventCode = $platform->test_event_code;

            if (empty($pixelId)) {
                return AdapterResult::failure('Pixel ID is missing', null, null, false);
            }

            if (empty($accessToken)) {
                return AdapterResult::failure('Access token is missing', null, null, false);
            }

            // Build Conversions API payload
            $payload = $this->buildPayload($eventData, $platform, $testEventCode);

            // Send to Meta API
            $url = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events";

            $response = Http::timeout(15)->asJson()->post($url, [
                'data' => [$payload],
                'access_token' => $accessToken,
            ]);

            $httpStatus = $response->status();
            $responseData = $response->json();

            // Check for errors
            if ($response->failed()) {
                $error = $responseData['error']['message'] ?? 'Unknown error';
                $errorCode = $responseData['error']['code'] ?? null;

                // Determine if retryable
                $retryable = $this->isErrorRetryable($httpStatus, $errorCode);

                // Mark as unhealthy if permanent error
                if (!$retryable) {
                    $this->markAsUnhealthy();
                    $this->healthError = $error;
                }

                return AdapterResult::failure(
                    'Meta API request failed',
                    $error,
                    $httpStatus,
                    $retryable
                );
            }

            // Success
            $eventsReceived = $responseData['events_received'] ?? 0;
            $message = "Event sent successfully. Events received: {$eventsReceived}";

            return AdapterResult::success($message, $responseData, $httpStatus);

        } catch (\Exception $e) {
            Log::error('MetaAdapter error: ' . $e->getMessage(), [
                'platform_id' => $platform->id,
                'event_id' => $eventData['event_id'] ?? null,
            ]);

            return AdapterResult::failure(
                'Exception: ' . $e->getMessage(),
                $e->getMessage(),
                null,
                true // Retry on exceptions
            );
        }
    }

    /**
     * Test connection to Meta API
     */
    public function testConnection(TrackingPlatform $platform): AdapterResult
    {
        try {
            $pixelId = $platform->pixel_id;
            $accessToken = $platform->access_token;
            $apiVersion = $platform->api_version ?? 'v19.0';

            if (empty($pixelId)) {
                return AdapterResult::failure('Pixel ID is missing', null, null, false);
            }

            if (empty($accessToken)) {
                return AdapterResult::failure('Access token is missing', null, null, false);
            }

            // Test by sending a test event
            $testEventData = [
                'event_name' => 'TestEvent',
                'event_id' => $this->hashingService->generateEventId(),
                'event_time' => time(),
                'user_data' => [
                    'em' => $this->hashingService->hashEmail('test@example.com'),
                ],
                'custom_data' => [
                    'test' => true,
                ],
            ];

            return $this->sendTestEventInternal($platform, $testEventData);

        } catch (\Exception $e) {
            return AdapterResult::failure(
                'Exception: ' . $e->getMessage(),
                $e->getMessage(),
                null,
                false
            );
        }
    }

    /**
     * Send test event
     */
    public function sendTestEvent(TrackingPlatform $platform): AdapterResult
    {
        $testEventData = [
            'event_name' => 'TestEvent',
            'event_id' => $this->hashingService->generateEventId(),
            'event_time' => time(),
            'user_data' => [
                'em' => $this->hashingService->hashEmail('test@example.com'),
            ],
            'custom_data' => [
                'test' => true,
                'value' => 1.00,
                'currency' => 'BDT',
            ],
        ];

        return $this->sendTestEventInternal($platform, $testEventData);
    }

    /**
     * Internal test event sender
     */
    protected function sendTestEventInternal(TrackingPlatform $platform, array $testEventData): AdapterResult
    {
        try {
            $pixelId = $platform->pixel_id;
            $accessToken = $platform->access_token;
            $apiVersion = $platform->api_version ?? 'v19.0';
            $testEventCode = $platform->test_event_code;

            $payload = $this->buildPayload($testEventData, $platform, $testEventCode);

            $url = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events";

            $response = Http::timeout(15)->asJson()->post($url, [
                'data' => [$payload],
                'access_token' => $accessToken,
            ]);

            $httpStatus = $response->status();
            $responseData = $response->json();

            if ($response->failed()) {
                $error = $responseData['error']['message'] ?? 'Unknown error';
                return AdapterResult::failure('Test event failed', $error, $httpStatus, false);
            }

            // Get pixel name from response
            $pixelName = $responseData['data'][0]['pixel_name'] ?? 'Unknown Pixel';

            return AdapterResult::success(
                "Test event sent successfully. Pixel: {$pixelName}",
                $responseData,
                $httpStatus
            );

        } catch (\Exception $e) {
            return AdapterResult::failure('Exception: ' . $e->getMessage(), $e->getMessage(), null, false);
        }
    }

    /**
     * Build Conversions API payload
     */
    protected function buildPayload(array $eventData, TrackingPlatform $platform, ?string $testEventCode = null): array
    {
        $payload = [
            'event_name' => $eventData['event_name'] ?? 'CustomEvent',
            'event_time' => $eventData['event_time'] ?? time(),
            'event_id' => $eventData['event_id'] ?? $this->hashingService->generateEventId(),
            'action_source' => $eventData['action_source'] ?? 'website',
            'event_source_url' => $eventData['event_source_url'] ?? request()->fullUrl(),
        ];

        // Add test event code if provided
        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        // Add user data (already hashed by caller)
        if (!empty($eventData['user_data'])) {
            $payload['user_data'] = $eventData['user_data'];
        }

        // Add custom data
        if (!empty($eventData['custom_data'])) {
            $payload['custom_data'] = $eventData['custom_data'];
        }

        // Add fbp/fbc if available
        if (!empty($eventData['fbp'])) {
            $payload['user_data']['fbp'] = $eventData['fbp'];
        }

        if (!empty($eventData['fbc'])) {
            $payload['user_data']['fbc'] = $eventData['fbc'];
        }

        return $payload;
    }

    /**
     * Determine if error is retryable
     */
    protected function isErrorRetryable(?int $httpStatus, ?string $errorCode): bool
    {
        // 5xx errors are retryable
        if ($httpStatus && $httpStatus >= 500) {
            return true;
        }

        // 429 (rate limit) is retryable
        if ($httpStatus === 429) {
            return true;
        }

        // 4xx errors (except 429) are not retryable
        if ($httpStatus && $httpStatus >= 400 && $httpStatus < 500) {
            return false;
        }

        // Specific Meta error codes
        $nonRetryableCodes = [
            100, // Invalid parameter
            190, // Access token expired
            200, // Permissions error
            80004, // Too many calls
        ];

        if ($errorCode && in_array($errorCode, $nonRetryableCodes)) {
            return false;
        }

        // Default to retryable
        return true;
    }

    /**
     * Get platform name
     */
    public function getPlatformName(): string
    {
        return 'meta';
    }

    /**
     * Check if platform is healthy
     */
    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    /**
     * Mark as unhealthy
     */
    public function markAsUnhealthy(): void
    {
        $this->healthy = false;
    }

    /**
     * Mark as healthy
     */
    public function markAsHealthy(): void
    {
        $this->healthy = true;
        $this->healthError = null;
    }
}
