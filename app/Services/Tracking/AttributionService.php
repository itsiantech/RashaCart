<?php

namespace App\Services\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AttributionService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Capture attribution data from request
     */
    public function captureAttribution(): array
    {
        $data = [];

        // Capture click IDs from URL
        $data['fbclid'] = $this->request->query('fbclid');
        $data['gclid'] = $this->request->query('gclid');
        $data['ttclid'] = $this->request->query('ttclid');

        // Capture UTM parameters
        $data['utm_source'] = $this->request->query('utm_source');
        $data['utm_medium'] = $this->request->query('utm_medium');
        $data['utm_campaign'] = $this->request->query('utm_campaign');
        $data['utm_content'] = $this->request->query('utm_content');
        $data['utm_term'] = $this->request->query('utm_term');

        // Capture GA4 client_id and session_id from cookies
        $data['ga_client_id'] = $this->extractGaClientId();
        $data['ga_session_id'] = $this->extractGaSessionId();

        // Capture fbp and fbc cookies
        $data['fbp'] = Cookie::get('_fbp');
        $data['fbc'] = Cookie::get('_fbc');

        // Capture ttp cookie (TikTok)
        $data['ttp'] = Cookie::get('ttp');

        // Capture IP address
        $data['ip_address'] = $this->getClientIp();

        // Capture user agent
        $data['user_agent'] = $this->request->userAgent();

        // Capture event source URL
        $data['event_source_url'] = $this->request->fullUrl();

        // Manage _fbc cookie if fbclid is present
        if ($data['fbclid']) {
            $this->setFbcCookie($data['fbclid']);
        }

        // Store first-touch and last-touch in session
        $this->storeAttributionInSession($data);

        return $data;
    }

    /**
     * Get stored attribution data from session
     */
    public function getStoredAttribution(): array
    {
        return session()->get('tracking.attribution', []);
    }

    /**
     * Clear stored attribution data
     */
    public function clearStoredAttribution(): void
    {
        session()->forget('tracking.attribution');
    }

    /**
     * Build _fbc cookie from fbclid
     * Format: fb.1.{timestamp_ms}.{fbclid}
     */
    protected function setFbcCookie(string $fbclid): void
    {
        // Check if _fbc already exists and is valid (90 days)
        $existingFbc = Cookie::get('_fbc');
        if ($existingFbc && $this->isFbcValid($existingFbc)) {
            return; // Don't overwrite existing valid _fbc
        }

        // Build new _fbc
        $timestamp = now()->getTimestampMs();
        $fbc = "fb.1.{$timestamp}.{$fbclid}";

        // Set cookie for 90 days
        Cookie::queue('_fbc', $fbc, 90 * 24 * 60);
    }

    /**
     * Check if _fbc cookie is still valid (within 90 days)
     */
    protected function isFbcValid(string $fbc): bool
    {
        $parts = explode('.', $fbc);
        if (count($parts) < 3) {
            return false;
        }

        $timestampMs = (int) $parts[2];
        $daysSinceCreation = (time() - (int) ($timestampMs / 1000)) / (60 * 60 * 24);

        return $daysSinceCreation < 90;
    }

    /**
     * Extract GA4 client_id from _ga cookie
     */
    protected function extractGaClientId(): ?string
    {
        $gaCookie = Cookie::get('_ga');
        if (!$gaCookie) {
            return null;
        }

        // _ga format: GA1.2.123456789.1234567890
        $parts = explode('.', $gaCookie);
        if (count($parts) >= 4) {
            return $parts[2] . '.' . $parts[3];
        }

        return null;
    }

    /**
     * Extract GA4 session_id from _ga_* cookies
     */
    protected function extractGaSessionId(): ?string
    {
        // GA4 stores session info in _ga_<property_id> cookies
        // This is a simplified implementation
        foreach ($_COOKIE as $key => $value) {
            if (str_starts_with($key, '_ga_')) {
                // Parse session from cookie
                $parts = explode('.', $value);
                if (count($parts) >= 2) {
                    return $parts[1];
                }
            }
        }

        return null;
    }

    /**
     * Get client IP address (respecting trusted proxies)
     */
    protected function getClientIp(): string
    {
        // Check for Cloudflare headers
        if ($this->request->hasHeader('CF-Connecting-IP')) {
            return $this->request->header('CF-Connecting-IP');
        }

        // Check for other proxy headers
        $headers = [
            'X-Forwarded-For',
            'X-Real-IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
        ];

        foreach ($headers as $header) {
            if ($this->request->hasHeader($header)) {
                $ip = $this->request->header($header);
                // Get first IP if multiple
                $ips = explode(',', $ip);
                return trim($ips[0]);
            }
        }

        return $this->request->ip();
    }

    /**
     * Store attribution data in session (first-touch and last-touch)
     */
    protected function storeAttributionInSession(array $data): void
    {
        $current = session()->get('tracking.attribution', []);

        // Store first-touch (only if not already set)
        if (empty($current['first_touch'])) {
            $current['first_touch'] = $this->filterAttributionData($data);
            $current['first_touch']['timestamp'] = now()->toIso8601String();
        }

        // Always update last-touch
        $current['last_touch'] = $this->filterAttributionData($data);
        $current['last_touch']['timestamp'] = now()->toIso8601String();

        session()->put('tracking.attribution', $current);
    }

    /**
     * Filter attribution data to remove null values
     */
    protected function filterAttributionData(array $data): array
    {
        return array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });
    }

    /**
     * Get attribution data for order creation
     * Prioritizes last-touch, falls back to first-touch
     */
    public function getAttributionForOrder(): array
    {
        $stored = $this->getStoredAttribution();

        if (empty($stored)) {
            // If nothing stored, capture current
            return $this->captureAttribution();
        }

        // Use last-touch data
        return $stored['last_touch'] ?? $stored['first_touch'] ?? [];
    }

    /**
     * Generate event ID for browser events
     */
    public function generateEventId(): string
    {
        return app(HashingService::class)->generateEventId();
    }

    /**
     * Generate deterministic event ID for purchase
     */
    public function generatePurchaseEventId(int $orderId): string
    {
        return app(HashingService::class)->generatePurchaseEventId($orderId);
    }
}
