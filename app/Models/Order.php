<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
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
    ];

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
}
