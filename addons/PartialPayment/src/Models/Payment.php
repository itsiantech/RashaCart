<?php

namespace Addons\PartialPayment\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'pp_payments';

    protected $fillable = [
        'order_id', 'type', 'method', 'amount', 'sender_number', 'trx_id',
        'status', 'verified_by', 'verified_at', 'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function pendingAdvance(int $orderId): ?self
    {
        return static::where('order_id', $orderId)
            ->where('type', 'advance')
            ->where('status', 'pending')
            ->latest('id')
            ->first();
    }

    public static function verifiedAdvance(int $orderId): ?self
    {
        return static::where('order_id', $orderId)
            ->where('type', 'advance')
            ->where('status', 'verified')
            ->latest('id')
            ->first();
    }

    public static function hasVerifiedBalance(int $orderId): bool
    {
        return static::where('order_id', $orderId)
            ->where('type', 'balance')
            ->where('status', 'verified')
            ->exists();
    }
}
