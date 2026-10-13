<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantPriceSlab extends Model
{
    protected $fillable = [
        'product_variant_id',
        'min_qty',
        'price',
    ];

    protected $casts = [
        'min_qty' => 'integer',
        'price' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
