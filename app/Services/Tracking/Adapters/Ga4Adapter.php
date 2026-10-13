<?php

namespace App\Services\Tracking\Adapters;

use App\Models\TrackingPlatform;
use App\Services\Tracking\HashingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Ga4Adapter implements TrackingAdapterInterface
{
    protected HashingService $hashingService;
    protected bool $healthy = true;
    protected ?string $healthError = null;

    public function __construct(HashingService $hashingService)
    {
        $this->hashingService = $hashingService;
    }

    /**
     * Send event to GA4 Measurement Protocol
     */
    public function send(array $eventData, TrackingPlatform $platform): AdapterResult
    {
        if (!$this->healthy) {
            return AdapterResult::failure('Platform marked as unhealthy (circuit breaker)', $this->healthError, null, false);
        }

        try {
            $measurementId = $platform->pixel_id; // GA4 uses measurement ID (G-XXXXXXXXXX)
            $apiSecret = $platform->getExtraValue('ga4_api_secret');

            if (empty($measurementId)) {
                return AdapterResult::failure('Measurement ID is missing', null, null, false);
            }

            if (empty($apiSecret)) {
                return AdapterResult::failure('API Secret is missing', null, null, false);
            }

            // Build GA4 Measurement Protocol payload
            $payload = $this->buildPayload($eventData, $platform);

            // Send to GA4 API
            $url = "https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}";

            $response = Http::timeout(15)->asJson()->post($url, $payload);

            $httpStatus = $response->status();
            $responseData = $response->json();

            // GA4 returns 204 No Content on success
            if ($httpStatus === 204) {
                return AdapterResult::success(
                    'Event sent successfully',
                    [],
                    $httpStatus
                );
            }

            // Check for errors
            if ($response->failed()) {
                $error = $responseData['message'] ?? 'Unknown error';

                // Determine if retryable
                $retryable = $this->isErrorRetryable($httpStatus);

                if (!$retryable) {
                    $this->markAsUnhealthy();
                    $this->healthError = $error;
                }

                return AdapterResult::failure(
                    'GA4 API request failed',
                    $error,
                    $httpStatus,
                    $retryable
                );
            }

            return AdapterResult::success('Event sent successfully', $responseData, $httpStatus);

        } catch (\Exception $e) {
            Log::error('Ga4Adapter error: ' . $e->getMessage(), [
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
     * Test connection to GA4 API
     */
    public function testConnection(TrackingPlatform $platform): AdapterResult
    {
        try {
            $measurementId = $platform->pixel_id;
            $apiSecret = $platform->getExtraValue('ga4_api_secret');

            if (empty($measurementId)) {
                return AdapterResult::failure('Measurement ID is missing', null, null, false);
            }

            if (empty($apiSecret)) {
                return AdapterResult::failure('API Secret is missing', null, null, false);
            }

            $testEventData = [
                'event_name' => 'test_event',
                'event_id' => $this->hashingService->generateEventId(),
                'client_id' => 'test_client_id',
                'timestamp_micros' => time() * 1000000,
                'events' => [
                    [
                        'name' => 'test_event',
                        'params' => [
                            'test' => true,
                        ],
                    ],
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
            'event_name' => 'test_event',
            'event_id' => $this->hashingService->generateEventId(),
            'client_id' => 'test_client_id',
            'timestamp_micros' => time() * 1000000,
            'events' => [
                [
                    'name' => 'test_event',
                    'params' => [
                        'test' => true,
                        'value' => 1.00,
                        'currency' => 'BDT',
                    ],
                ],
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
            $measurementId = $platform->pixel_id;
            $apiSecret = $platform->getExtraValue('ga4_api_secret');

            $payload = $this->buildPayload($testEventData, $platform);

            $url = "https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}";

            $response = Http::timeout(15)->asJson()->post($url, $payload);

            $httpStatus = $response->status();

            if ($httpStatus === 204) {
                return AdapterResult::success(
                    'Test event sent successfully',
                    [],
                    $httpStatus
                );
            }

            $error = 'Test event failed';
            return AdapterResult::failure($error, $error, $httpStatus, false);

        } catch (\Exception $e) {
            return AdapterResult::failure('Exception: ' . $e->getMessage(), $e->getMessage(), null, false);
        }
    }

    /**
     * Build GA4 Measurement Protocol payload
     */
    protected function buildPayload(array $eventData, TrackingPlatform $platform): array
    {
        $payload = [
            'client_id' => $eventData['client_id'] ?? $this->generateClientId(),
            'events' => $eventData['events'] ?? [],
        ];

        // Add user_id if available
        if (!empty($eventData['user_id'])) {
            $payload['user_id'] = $eventData['user_id'];
        }

        // Add timestamp_micros if available
        if (!empty($eventData['timestamp_micros'])) {
            $payload['timestamp_micros'] = $eventData['timestamp_micros'];
        }

        // Add session_id if available
        if (!empty($eventData['session_id'])) {
            $payload['session_id'] = $eventData['session_id'];
        }

        // Add user_properties if available
        if (!empty($eventData['user_properties'])) {
            $payload['user_properties'] = $eventData['user_properties'];
        }

        // Add non_personalized_ads if test mode
        if ($platform->is_test_mode) {
            $payload['non_personalized_ads'] = true;
        }

        return $payload;
    }

    /**
     * Generate a random client ID
     */
    protected function generateClientId(): string
    {
        return (string) rand(1000000000, 9999999999) . '.' . (string) rand(1000000000, 9999999999);
    }

    /**
     * Determine if error is retryable
     */
    protected function isErrorRetryable(?int $httpStatus): bool
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
        return 'ga4';
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
