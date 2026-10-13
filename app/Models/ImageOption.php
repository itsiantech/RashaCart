<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageOption extends Model
{
    protected $fillable = [
        'product_id',
        'label',
        'image',
        'price',
        'sale_price',
        'sku',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(ImageOptionStockMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Current sellable stock from the option ledger (in + return + adjustment − out). */
    public function currentStock(): int
    {
        return (int) $this->movements()
            ->selectRaw("COALESCE(SUM(CASE type WHEN 'in' THEN quantity WHEN 'return' THEN quantity WHEN 'adjustment' THEN quantity ELSE -quantity END), 0) AS total")
            ->value('total');
    }

    /** Effective own price (sale first), null when the option has no own price. */
    public function getEffectivePriceAttribute(): ?float
    {
        if ($this->sale_price !== null) {
            return (float) $this->sale_price;
        }
        if ($this->price !== null) {
            return (float) $this->price;
        }

        return null;
    }
}
