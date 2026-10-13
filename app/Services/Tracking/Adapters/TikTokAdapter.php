<?php

namespace App\Services\Tracking\Adapters;

use App\Models\TrackingPlatform;
use App\Services\Tracking\HashingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokAdapter implements TrackingAdapterInterface
{
    protected HashingService $hashingService;
    protected bool $healthy = true;
    protected ?string $healthError = null;

    public function __construct(HashingService $hashingService)
    {
        $this->hashingService = $hashingService;
    }

    /**
     * Send event to TikTok Events API
     */
    public function send(array $eventData, TrackingPlatform $platform): AdapterResult
    {
        if (!$this->healthy) {
            return AdapterResult::failure('Platform marked as unhealthy (circuit breaker)', $this->healthError, null, false);
        }

        try {
            $pixelId = $platform->pixel_id;
            $accessToken = $platform->access_token;

            if (empty($pixelId)) {
                return AdapterResult::failure('Pixel ID is missing', null, null, false);
            }

            if (empty($accessToken)) {
                return AdapterResult::failure('Access token is missing', null, null, false);
            }

            // Build TikTok Events API payload
            $payload = $this->buildPayload($eventData, $platform);

            // Send to TikTok API
            $url = "https://business-api.tiktok.com/open_api/v1.3/pixel/events/";

            $response = Http::timeout(15)->asJson()->post($url, [
                'pixel_id' => $pixelId,
                'events' => [$payload],
                'access_token' => $accessToken,
            ]);

            $httpStatus = $response->status();
            $responseData = $response->json();

            // Check for errors
            if ($response->failed()) {
                $error = $responseData['message'] ?? $responseData['data']['error_message'] ?? 'Unknown error';
                $errorCode = $responseData['code'] ?? null;

                // Determine if retryable
                $retryable = $this->isErrorRetryable($httpStatus, $errorCode);

                if (!$retryable) {
                    $this->markAsUnhealthy();
                    $this->healthError = $error;
                }

                return AdapterResult::failure(
                    'TikTok API request failed',
                    $error,
                    $httpStatus,
                    $retryable
                );
            }

            // Success
            $message = 'Event sent successfully';

            return AdapterResult::success($message, $responseData, $httpStatus);

        } catch (\Exception $e) {
            Log::error('TikTokAdapter error: ' . $e->getMessage(), [
                'platform_id' => $platform->id,
                'event_id' => $eventData['event_id'] ?? null,
            ]);

            return AdapterResult::failure(
                'Exception: ' . $e->getMessage(),
                $e->getMessage(),
                null,
                true
            );
        }
    }

    /**
     * Test connection to TikTok API
     */
    public function testConnection(TrackingPlatform $platform): AdapterResult
    {
        try {
            $pixelId = $platform->pixel_id;
            $accessToken = $platform->access_token;

            if (empty($pixelId)) {
                return AdapterResult::failure('Pixel ID is missing', null, null, false);
            }

            if (empty($accessToken)) {
                return AdapterResult::failure('Access token is missing', null, null, false);
            }

            $testEventData = [
                'event' => 'TestEvent',
                'event_id' => $this->hashingService->generateEventId(),
                'timestamp' => time() * 1000, // TikTok uses milliseconds
                'context' => [
                    'user' => [
                        'sha256_email' => $this->hashingService->hashEmail('test@example.com'),
                    ],
                ],
                'properties' => [
                    'test' => true,
                ],
            ];

            return $this->sendTestEventInternal($platform, $testEventData);

        } catch (\Exception $e) {
            return AdapterResult::failure('Exception: ' . $e->getMessage(), $e->getMessage(), null, false);
        }
    }

    /**
     * Send test event
     */
    public function sendTestEvent(TrackingPlatform $platform): AdapterResult
    {
        $testEventData = [
            'event' => 'TestEvent',
            'event_id' => $this->hashingService->generateEventId(),
            'timestamp' => time() * 1000,
            'context' => [
                'user' => [
                    'sha256_email' => $this->hashingService->hashEmail('test@example.com'),
                ],
            ],
            'properties' => [
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

            $payload = $this->buildPayload($testEventData, $platform);

            $url = "https://business-api.tiktok.com/open_api/v1.3/pixel/events/";

            $response = Http::timeout(15)->asJson()->post($url, [
                'pixel_id' => $pixelId,
                'events' => [$payload],
                'access_token' => $accessToken,
            ]);

            $httpStatus = $response->status();
            $responseData = $response->json();

            if ($response->failed()) {
                $error = $responseData['message'] ?? $responseData['data']['error_message'] ?? 'Unknown error';
                return AdapterResult::failure('Test event failed', $error, $httpStatus, false);
            }

            return AdapterResult::success(
                'Test event sent successfully',
                $responseData,
                $httpStatus
            );

        } catch (\Exception $e) {
            return AdapterResult::failure('Exception: ' . $e->getMessage(), $e->getMessage(), null, false);
        }
    }

    /**
     * Build TikTok Events API payload
     */
    protected function buildPayload(array $eventData, TrackingPlatform $platform): array
    {
        $payload = [
            'event' => $eventData['event'] ?? 'CustomEvent',
            'event_id' => $eventData['event_id'] ?? $this->hashingService->generateEventId(),
            'timestamp' => $eventData['timestamp'] ?? (time() * 1000),
        ];

        // Add context
        if (!empty($eventData['context'])) {
            $payload['context'] = $eventData['context'];
        } else {
            $payload['context'] = [];
        }

        // Add properties
        if (!empty($eventData['properties'])) {
            $payload['properties'] = $eventData['properties'];
        }

        // Add ttclid if available
        if (!empty($eventData['ttclid'])) {
            $payload['context']['ad']['callback'] = $eventData['ttclid'];
        }

        // Add ttp if available
        if (!empty($eventData['ttp'])) {
            $payload['context']['ad']['ttp'] = $eventData['ttp'];
        }

        // Add IP and user agent if available
        if (!empty($eventData['ip_address'])) {
            $payload['context']['ip'] = $eventData['ip_address'];
        }

        if (!empty($eventData['user_agent'])) {
            $payload['context']['user_agent'] = $eventData['user_agent'];
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

        // Default to retryable
        return true;
    }

    /**
     * Get platform name
     */
    public function getPlatformName(): string
    {
        return 'tiktok';
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
