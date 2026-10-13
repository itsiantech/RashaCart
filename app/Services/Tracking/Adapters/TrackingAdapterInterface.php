<?php

namespace App\Services\Tracking\Adapters;

use App\Models\TrackingPlatform;

interface TrackingAdapterInterface
{
    /**
     * Send tracking event to platform
     *
     * @param array $eventData Event data including event_name, event_id, user_data, custom_data, etc.
     * @param TrackingPlatform $platform Platform configuration
     * @return AdapterResult
     */
    public function send(array $eventData, TrackingPlatform $platform): AdapterResult;

    /**
     * Test connection to platform API
     *
     * @param TrackingPlatform $platform Platform configuration
     * @return AdapterResult
     */
    public function testConnection(TrackingPlatform $platform): AdapterResult;

    /**
     * Send test event to platform
     *
     * @param TrackingPlatform $platform Platform configuration
     * @return AdapterResult
     */
    public function sendTestEvent(TrackingPlatform $platform): AdapterResult;

    /**
     * Get platform name
     *
     * @return string
     */
    public function getPlatformName(): string;

    /**
     * Check if platform is healthy (for circuit breaker)
     *
     * @return bool
     */
    public function isHealthy(): bool;

    /**
     * Mark platform as unhealthy (circuit breaker)
     *
     * @return void
     */
    public function markAsUnhealthy(): void;

    /**
     * Mark platform as healthy (circuit breaker recovery)
     *
     * @return void
     */
    public function markAsHealthy(): void;
}
