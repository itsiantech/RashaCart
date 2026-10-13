<?php

namespace App\Models;

use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_variant_id',
        'variant_sku',
        'variant_name',
        'variant_image',
        'image_option_id',
        'option_label',
        'option_image',
        'quantity',
        'unit_price',
        'is_preorder',
    ];

    public function imageOption()
    {
        return $this->belongsTo(ImageOption::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getTotalPriceAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    protected static function booted()
    {
        static::created(function (OrderItem $item) {
            InventoryService::syncOrderItem($item);
            \App\Services\ImageOptionStock::ensureOrderItemOut($item);
        });

        static::updated(function (OrderItem $item) {
            if ($item->wasChanged('quantity')) {
                InventoryService::syncOrderItem($item);
            }
        });

        static::deleted(function (OrderItem $item) {
            InventoryService::removeOrderItemMovements($item);
        });
    }
}
