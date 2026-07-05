<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 
        'sku', 
        'variant_price',
        'varient_price', // Keep both for compatibility
        'stock',
        'image', 
    ];

    // ── Main Relationships ──
    public function product() 
    {
        return $this->belongsTo(Product::class);
    }

    public function inventories() 
    {
        return $this->hasMany(Inventory::class, 'variant_id');
    }

    // ── FOR ADMIN PANEL (ProductEdit component) ──
    public function values()
    {
        return $this->hasMany(VariantAttributeValue::class, 'product_variant_id')
                    ->with(['attribute', 'attributeValue']);
    }

    // ── FOR FRONTEND (SingleView component) ──
    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'product_variant_id')
                    ->with(['attribute', 'attributeValue']);
    }

    // ── Get Current Stock ──
    public function getCurrentStockAttribute()
    {
        return $this->inventories->sortByDesc('id')->first()?->current_stock ?? 0;
    }

    // ── Get Variant Price ──
    public function getVariantPriceAttribute($value)
    {
        // Return the actual price, not 0
        return $value ?? $this->product?->sale_price ?? 0;
    }
}