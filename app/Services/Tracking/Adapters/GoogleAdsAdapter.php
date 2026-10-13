<?php

namespace App\Services\Tracking\Adapters;

use App\Models\TrackingPlatform;
use App\Services\Tracking\HashingService;
use Illuminate\Support\Facades\Log;

/**
 * Google Ads Enhanced Conversions / Offline Conversions Adapter
 * Placeholder implementation - can be extended when needed
 */
class GoogleAdsAdapter implements TrackingAdapterInterface
{
    protected HashingService $hashingService;
    protected bool $healthy = true;
    protected ?string $healthError = null;

    public function __construct(HashingService $hashingService)
    {
        $this->hashingService = $hashingService;
    }

    /**
     * Send event to Google Ads
     */
    public function send(array $eventData, TrackingPlatform $platform): AdapterResult
    {
        // Google Ads enhanced conversions typically use click ID (gclid)
        // This is a placeholder for future implementation
        return AdapterResult::failure(
            'Google Ads adapter not yet implemented',
            'This adapter is a placeholder for future enhanced conversions implementation',
            null,
            false
        );
    }

    /**
     * Test connection to Google Ads API
     */
    public function testConnection(TrackingPlatform $platform): AdapterResult
    {
        return AdapterResult::failure(
            'Google Ads adapter not yet implemented',
            'This adapter is a placeholder for future enhanced conversions implementation',
            null,
            false
        );
    }

    /**
     * Send test event
     */
    public function sendTestEvent(TrackingPlatform $platform): AdapterResult
    {
        return AdapterResult::failure(
            'Google Ads adapter not yet implemented',
            'This adapter is a placeholder for future enhanced conversions implementation',
            null,
            false
        );
    }

    /**
     * Get platform name
     */
    public function getPlatformName(): string
    {
        return 'google_ads';
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
