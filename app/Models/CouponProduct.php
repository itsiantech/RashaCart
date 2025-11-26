<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponProduct extends Model
{
    protected $fillable = [
        'coupon_id',
        'product_id',
    ];
}
