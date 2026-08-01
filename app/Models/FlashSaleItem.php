<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'discount_percent',
        'discounted_price',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Calculate and return the discounted price from product's original price.
     */
    public static function calculateDiscountedPrice(float $originalPrice, float $discountPercent): float
    {
        return round($originalPrice - ($originalPrice * $discountPercent / 100), 2);
    }
}
