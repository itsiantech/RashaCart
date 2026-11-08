<?php

namespace Addons\PartialPayment\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $table = 'pp_activities';

    protected $fillable = ['order_id', 'payment_id', 'action', 'admin_id', 'note'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public static function log(?int $orderId, ?int $paymentId, string $action, ?string $note = null): self
    {
        return static::create([
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'action' => $action,
            'admin_id' => auth()->id(),
            'note' => $note,
        ]);
    }

    public static function forOrder(int $orderId)
    {
        return static::where('order_id', $orderId)->with('admin')->latest('id')->get();
    }
}
