<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCommission extends Model
{
    protected $fillable = [
        'id',
        'order_id',
        'order_item_id',
        'vendor_id',
        'admin_commission',
        'vendor_earning',
        'commission_rate',
        'created_at',
    ];
}
