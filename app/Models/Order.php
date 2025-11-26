<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
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


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    

}
