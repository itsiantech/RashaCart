<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEventRule extends Model
{
    protected $fillable = [
        'platform_id',
        'event_name',
        'is_enabled',
        'trigger_type',
        'trigger_status',
        'value_mode',
        'send_browser',
        'send_server',
        'delay_minutes',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'send_browser' => 'boolean',
        'send_server' => 'boolean',
        'delay_minutes' => 'integer',
    ];

    /**
     * Relationship with platform
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(TrackingPlatform::class, 'platform_id');
    }

    /**
     * Check if rule should trigger for given order status
     */
    public function shouldTriggerForStatus(?string $orderStatus): bool
    {
        if ($this->trigger_type !== 'order_status') {
            return false;
        }

        if (!$this->trigger_status) {
            return false;
        }

        return $orderStatus === $this->trigger_status;
    }

    /**
     * Ensure every platform has a sensible default rule set so events are
     * never silently skipped on a fresh setup. Existing rules are kept.
     */
    public static function ensureDefaults(int $platformId): void
    {
        $defaults = [
            'PageView' => ['trigger_type' => 'frontend', 'send_browser' => true, 'send_server' => false],
            'ViewContent' => ['trigger_type' => 'frontend', 'send_browser' => true, 'send_server' => true],
            'AddToCart' => ['trigger_type' => 'frontend', 'send_browser' => true, 'send_server' => true],
            'InitiateCheckout' => ['trigger_type' => 'frontend', 'send_browser' => true, 'send_server' => true],
            'AddPaymentInfo' => ['trigger_type' => 'frontend', 'send_browser' => true, 'send_server' => false],
            'Purchase' => ['trigger_type' => 'order_placed', 'send_browser' => true, 'send_server' => true, 'value_mode' => 'full'],
        ];

        foreach ($defaults as $event => $opts) {
            self::firstOrCreate(
                ['platform_id' => $platformId, 'event_name' => $event],
                array_merge([
                    'is_enabled' => true,
                    'trigger_status' => null,
                    'value_mode' => 'full',
                    'delay_minutes' => null,
                ], $opts)
            );
        }
    }

    /**
     * Get rule description
     */
    public function getDescriptionAttribute(): string
    {
        $parts = [];

        if ($this->send_browser && $this->send_server) {
            $parts[] = 'Browser + Server';
        } elseif ($this->send_browser) {
            $parts[] = 'Browser only';
        } elseif ($this->send_server) {
            $parts[] = 'Server only';
        }

        if ($this->trigger_type === 'order_status' && $this->trigger_status) {
            $parts[] = "On status: {$this->trigger_status}";
        }

        if ($this->delay_minutes) {
            $parts[] = "Delay: {$this->delay_minutes}min";
        }

        return implode(' | ', $parts);
    }
}
