<?php

namespace App\Models;

use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    protected $casts = [
        'purchase_sent_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'vendor_id',
        'name',
        'phone',
        'shipping_address',
        'delivery_zone',
        'delivery_charge',
        'payment_method',
        'pay_amt',
        'invoice_no',
        'note',
        'status',
        'total_amount',
        'coupon_discount_amount',
        'coupon_code',
        'transaction_id',
        'payment_status',
        // Tracking fields
        'fbp',
        'fbc',
        'fbclid',
        'gclid',
        'ttclid',
        'ttp',
        'ga_client_id',
        'ga_session_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'ip_address',
        'user_agent',
        'event_source_url',
        'tracking_event_id',
        'purchase_sent_at',
    ];

    protected static function booted()
    {
        static::created(function (Order $order) {
            InventoryService::syncOrderMovements($order);
        });

        static::updated(function (Order $order) {
            if ($order->wasChanged('status')) {
                InventoryService::syncOrderMovements($order);
                event(new \App\Events\OrderStatusChanged(
                    $order,
                    $order->getOriginal('status'),
                    $order->status
                ));
            }
        });

        static::saved(function () {
            Cache::forever('dashboard_cache_version', now()->timestamp);
        });

        static::deleted(function () {
            Cache::forever('dashboard_cache_version', now()->timestamp);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Relationship with tracking logs
     */
    public function trackingLogs()
    {
        return $this->hasMany(\App\Models\TrackingLog::class, 'order_id');
    }

    /**
     * Check if purchase event has been sent
     */
    public function hasPurchaseBeenSent(): bool
    {
        return !is_null($this->purchase_sent_at);
    }

    /**
     * Mark purchase as sent
     */
    public function markPurchaseAsSent(): void
    {
        $this->update(['purchase_sent_at' => now()]);
    }

    /**
     * Get the tracking event ID for this order
     */
    public function getTrackingEventId(): string
    {
        if ($this->tracking_event_id) {
            return $this->tracking_event_id;
        }

        // Generate deterministic event ID for purchase
        return 'purchase_' . $this->id;
    }

    /**
     * Check if order should trigger tracking events
     */
    public function shouldTrackPurchase(): bool
    {
        // Don't track cancelled/returned orders
        if (in_array($this->status, ['cancelled', 'returned'])) {
            return false;
        }

        // Don't track if already sent
        if ($this->hasPurchaseBeenSent()) {
            return false;
        }

        return true;
    }
}