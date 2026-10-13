<?php

namespace App\Services\Tracking;

use App\Jobs\SendTrackingEventJob;
use App\Models\Order;
use App\Models\TrackingEventRule;
use App\Models\TrackingLog;
use App\Models\TrackingPlatform;
use App\Services\Tracking\Adapters\Ga4Adapter;
use App\Services\Tracking\Adapters\GoogleAdsAdapter;
use App\Services\Tracking\Adapters\MetaAdapter;
use App\Services\Tracking\Adapters\TikTokAdapter;
use App\Services\Tracking\Adapters\TrackingAdapterInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrackingManager
{
    protected HashingService $hashingService;
    protected AttributionService $attributionService;

    // Platform adapter mapping
    protected array $adapters = [
        'meta' => MetaAdapter::class,
        'tiktok' => TikTokAdapter::class,
        'ga4' => Ga4Adapter::class,
        'google_ads' => GoogleAdsAdapter::class,
    ];

    public function __construct(
        HashingService $hashingService,
        AttributionService $attributionService
    ) {
        $this->hashingService = $hashingService;
        $this->attributionService = $attributionService;
    }

    /**
     * Track an event across all active platforms.
     * Returns the number of jobs dispatched.
     */
    public function trackEvent(string $eventName, array $data = [], ?Order $order = null): int
    {
        $activePlatforms = TrackingPlatform::getActivePlatforms();

        if ($activePlatforms->isEmpty()) {
            Log::info('No active tracking platforms found');
            return 0;
        }

        // Generate event ID
        $eventId = $data['event_id'] ?? $this->hashingService->generateEventId();

        $dispatched = 0;

        foreach ($activePlatforms as $platform) {
            // Check if event is enabled for this platform
            $rule = $this->getEventRule($platform->id, $eventName);

            if (!$rule || !$rule->is_enabled) {
                continue;
            }

            // Exactly-once guard: never send Purchase twice for the same order
            if ($eventName === 'Purchase' && $order && $order->hasPurchaseBeenSent()) {
                continue;
            }

            // Check trigger type
            if ($rule->trigger_type === 'order_status' && $order) {
                if (!$rule->shouldTriggerForStatus($order->status)) {
                    continue;
                }
            }

            // Check if server-side sending is enabled
            if (!$rule->send_server) {
                continue;
            }

            // Circuit breaker: skip platforms marked unhealthy by their adapter
            $adapter = $this->getAdapter($platform->platform);
            if ($adapter && !$adapter->isHealthy()) {
                Log::warning("Skipping {$eventName} for unhealthy platform {$platform->platform} (id {$platform->id})");
                continue;
            }

            // Purchase value follows the rule's value_mode (full|exclude_shipping|custom)
            if ($eventName === 'Purchase' && $order) {
                $data['value'] = $this->calculateOrderValue($order, $rule->value_mode);
            }

            // Build event data
            $eventData = $this->buildEventData($eventName, $eventId, $data, $order, $platform);

            // Check for duplicates
            if ($this->isDuplicate($platform->id, $eventName, $eventId)) {
                Log::info("Duplicate event skipped: {$eventName} for platform {$platform->id}");
                continue;
            }

            // Dispatch job (with delay if configured)
            $delay = $rule->delay_minutes ? now()->addMinutes($rule->delay_minutes) : null;

            SendTrackingEventJob::dispatch($platform->id, $eventName, $eventId, $eventData, $order?->id)
                ->delay($delay)
                ->onQueue('tracking');

            $dispatched++;
        }

        return $dispatched;
    }

    /**
     * Track purchase event for an order
     */
    public function trackPurchase(Order $order): void
    {
        if (!$order->shouldTrackPurchase()) {
            return;
        }

        $eventId = $this->hashingService->generatePurchaseEventId($order->id);

        $data = [
            'event_id' => $eventId,
            'value' => $this->calculateOrderValue($order),
            'currency' => 'BDT',
            'contents' => $this->buildOrderContents($order),
            'num_items' => $order->items->sum('quantity'),
            'order_id' => $order->id,
            'invoice_no' => $order->invoice_no,
        ];

        $dispatched = $this->trackEvent('Purchase', $data, $order);

        // Mark purchase as sent only if at least one platform accepted the
        // event (respects status-based rules: placed-time send is skipped
        // when the rule waits for e.g. confirmed/delivered).
        if ($dispatched > 0) {
            $order->markPurchaseAsSent();
        }
    }

    /**
     * Get event rule for platform and event
     */
    protected function getEventRule(int $platformId, string $eventName): ?TrackingEventRule
    {
        return Cache::remember("tracking.rule.{$platformId}.{$eventName}", 3600, function () use ($platformId, $eventName) {
            return TrackingEventRule::where('platform_id', $platformId)
                ->where('event_name', $eventName)
                ->first();
        });
    }

    /**
     * Build event data for platform
     */
    protected function buildEventData(string $eventName, string $eventId, array $data, ?Order $order, TrackingPlatform $platform): array
    {
        $eventData = [
            'event_name' => $eventName,
            'event_id' => $eventId,
            'event_time' => time(),
        ];

        // Add user data (hashed PII)
        $userData = $this->buildUserData($order);
        if (!empty($userData)) {
            $eventData['user_data'] = $userData;
        }

        // Add custom data
        $customData = $this->buildCustomData($data, $order);
        if (!empty($customData)) {
            $eventData['custom_data'] = $customData;
        }

        // Add platform-specific data
        $eventData = $this->addPlatformSpecificData($eventData, $platform, $order);

        return $eventData;
    }

    /**
     * Build user data with hashed PII
     */
    protected function buildUserData(?Order $order): array
    {
        if (!$order) {
            return [];
        }

        $userData = [
            'email' => $order->user?->email,
            'phone' => $order->phone,
            'first_name' => $order->name, // Assuming full name in "name" field
            'last_name' => '', // Will be split if needed
            'city' => $order->delivery_zone,
            'country' => 'bd',
        ];

        // Hash the data
        return $this->hashingService->buildUserData($userData);
    }

    /**
     * Build custom data for event
     */
    protected function buildCustomData(array $data, ?Order $order): array
    {
        $customData = [];

        // Add value and currency
        if (isset($data['value'])) {
            $customData['value'] = $data['value'];
        }

        if (isset($data['currency'])) {
            $customData['currency'] = $data['currency'];
        }

        // Add contents
        if (isset($data['contents'])) {
            $customData['contents'] = $data['contents'];
        }

        // Add num_items
        if (isset($data['num_items'])) {
            $customData['num_items'] = $data['num_items'];
        }

        // Add order_id
        if (isset($data['order_id'])) {
            $customData['order_id'] = $data['order_id'];
        }

        // Add action_source
        $customData['action_source'] = 'website';

        return $customData;
    }

    /**
     * Add platform-specific data
     */
    protected function addPlatformSpecificData(array $eventData, TrackingPlatform $platform, ?Order $order): array
    {
        // Get attribution data from order
        if ($order) {
            $eventData['fbp'] = $order->fbp;
            $eventData['fbc'] = $order->fbc;
            $eventData['fbclid'] = $order->fbclid;
            $eventData['gclid'] = $order->gclid;
            $eventData['ttclid'] = $order->ttclid;
            $eventData['ttp'] = $order->ttp;
            $eventData['ip_address'] = $order->ip_address;
            $eventData['user_agent'] = $order->user_agent;
            $eventData['event_source_url'] = $order->event_source_url;
        }

        // GA4 specific
        if ($platform->platform === 'ga4') {
            $eventData['client_id'] = $order->ga_client_id ?? null;
            $eventData['session_id'] = $order->ga_session_id ?? null;
            $eventData['timestamp_micros'] = time() * 1000000;
            $eventData['events'] = [
                [
                    'name' => $eventData['event_name'],
                    'params' => $eventData['custom_data'] ?? [],
                ],
            ];
        }

        // TikTok specific
        if ($platform->platform === 'tiktok') {
            $eventData['event'] = $eventData['event_name'];
            $eventData['timestamp'] = time() * 1000;
            $eventData['context'] = [
                'user' => $eventData['user_data'] ?? [],
            ];
            $eventData['properties'] = $eventData['custom_data'] ?? [];
            unset($eventData['user_data']);
            unset($eventData['custom_data']);
        }

        return $eventData;
    }

    /**
     * Calculate order value based on value_mode.
     * full = total_amount, exclude_shipping = total minus delivery charge,
     * custom = total minus delivery charge and coupon discount.
     */
    protected function calculateOrderValue(Order $order, ?string $mode = 'full'): float
    {
        $total = (float) $order->total_amount;

        if ($mode === 'exclude_shipping') {
            return max(0, $total - (float) ($order->delivery_charge ?? 0));
        }

        if ($mode === 'custom') {
            return max(0, $total - (float) ($order->delivery_charge ?? 0) - (float) ($order->coupon_discount_amount ?? 0));
        }

        return $total;
    }

    /**
     * Build order contents for tracking
     */
    protected function buildOrderContents(Order $order): array
    {
        $contents = [];

        foreach ($order->items as $item) {
            // Legacy ids stay untouched; only option combinations use the
            // extended productSKU-variantSKU-optionId form (matches feed).
            if ($item->image_option_id) {
                $parts = [$item->product?->sku ?? $item->product_id];
                if ($item->variant_sku) {
                    $parts[] = $item->variant_sku;
                }
                $parts[] = 'opt'.$item->image_option_id;
                $baseId = implode('-', $parts);
            } else {
                $baseId = $item->variant_sku ?? $item->product?->sku ?? $item->product_id;
            }
            $contents[] = [
                'id' => $baseId,
                'item_id' => $baseId,
                'item_variant' => trim(($item->variant_name ?? '').($item->option_label ? ' | '.$item->option_label : '')) ?: null,
                'quantity' => $item->quantity,
                'item_price' => $item->unit_price,
            ];
        }

        return $contents;
    }

    /**
     * Check if event is duplicate
     */
    protected function isDuplicate(int $platformId, string $eventName, string $eventId): bool
    {
        return TrackingLog::where('platform_id', $platformId)
            ->where('event_name', $eventName)
            ->where('event_id', $eventId)
            ->where('status', '!=', 'failed')
            ->exists();
    }

    /**
     * Get adapter instance for platform
     */
    public function getAdapter(string $platform): ?TrackingAdapterInterface
    {
        $adapterClass = $this->adapters[$platform] ?? null;

        if (!$adapterClass) {
            return null;
        }

        return app($adapterClass);
    }

    /**
     * Test connection for a platform
     */
    public function testConnection(TrackingPlatform $platform): array
    {
        $adapter = $this->getAdapter($platform->platform);

        if (!$adapter) {
            return [
                'success' => false,
                'message' => 'Adapter not found for platform: ' . $platform->platform,
            ];
        }

        $result = $adapter->testConnection($platform);

        return $result->toArray();
    }

    /**
     * Send test event for a platform
     */
    public function sendTestEvent(TrackingPlatform $platform): array
    {
        $adapter = $this->getAdapter($platform->platform);

        if (!$adapter) {
            return [
                'success' => false,
                'message' => 'Adapter not found for platform: ' . $platform->platform,
            ];
        }

        $result = $adapter->sendTestEvent($platform);

        return $result->toArray();
    }

    /**
     * Clear tracking cache
     */
    public function clearCache(): void
    {
        Cache::forget('tracking.platforms.active');
        Cache::forget('tracking.platforms.all');

        // Clear all rule caches
        TrackingPlatform::all()->each(function ($platform) {
            $platform->eventRules->each(function ($rule) use ($platform) {
                Cache::forget("tracking.rule.{$platform->id}.{$rule->event_name}");
            });
        });
    }
}
