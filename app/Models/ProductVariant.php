<?php

namespace App\Models;

use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'variant_price',
        'varient_price', // Keep both for compatibility
        'sale_price',
        'cost_price',
        'stock',
        'low_stock_threshold',
        'weight',
        'barcode',
        'is_active',
        'is_default',
        'image',
    ];

    protected $casts = [
        'variant_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function priceSlabs()
    {
        return $this->hasMany(VariantPriceSlab::class, 'product_variant_id')->orderBy('min_qty');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Authoritative sell price: variant sale_price, else variant_price (falls back to product). */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->variant_price ?? $this->product?->sale_price ?? 0);
    }

    /** Human label e.g. "Color: Red, Size: M". */
    public function getLabelAttribute(): string
    {
        return $this->variantAttributeValues
            ->map(fn ($vav) => ($vav->attribute->name ?? '?').': '.($vav->attributeValue->value ?? '?'))
            ->implode(', ');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= ($this->low_stock_threshold ?? 5);
    }

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
        if ($this->relationLoaded('inventories')) {
            return $this->inventories->sum(fn ($i) => match ($i->type) {
                'in', 'return', 'adjustment' => (int) $i->quantity,
                default => - (int) $i->quantity,
            });
        }

        return InventoryService::computeStock($this->inventories());
    }

    // ── Get Variant Price ──
    public function getVariantPriceAttribute($value)
    {
        // Return the actual price, not 0
        return $value ?? $this->product?->sale_price ?? 0;
    }
}
