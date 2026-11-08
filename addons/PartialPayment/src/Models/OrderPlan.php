<?php

namespace Addons\PartialPayment\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPlan extends Model
{
    public const PENDING = 'advance_pending';

    public const ADVANCE_PAID = 'advance_paid';

    public const PAID = 'paid';

    public const REFUND_DUE = 'refund_due';

    public const REFUNDED = 'refunded';

    protected $table = 'pp_order_plans';

    protected $fillable = [
        'order_id', 'advance_amount', 'due_amount', 'payment_status', 'expires_at', 'due_override',
    ];

    protected $casts = [
        'advance_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'due_override' => 'boolean',
    ];

    /** @var array<int, self|null> */
    protected static array $memo = [];

    protected static function booted(): void
    {
        // Keep the read-through cache honest for the rest of the request.
        static::saved(function (self $plan) {
            static::$memo[$plan->order_id] = $plan;
        });

        static::deleted(function (self $plan) {
            unset(static::$memo[$plan->order_id]);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }

    public static function forOrder(int $orderId): ?self
    {
        if (! array_key_exists($orderId, static::$memo)) {
            static::$memo[$orderId] = static::where('order_id', $orderId)->first();
        }

        return static::$memo[$orderId];
    }

    public static function flushMemo(): void
    {
        static::$memo = [];
    }

    public function advanceVerified(): bool
    {
        return in_array($this->payment_status, [self::ADVANCE_PAID, self::PAID, self::REFUND_DUE], true);
    }

    public function hasOutstandingDue(): bool
    {
        return (float) $this->due_amount > 0
            && ! $this->due_override
            && $this->payment_status !== self::PAID;
    }

    public function isExpired(): bool
    {
        return $this->payment_status === self::PENDING
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }
}
