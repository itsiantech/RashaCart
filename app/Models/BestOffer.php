<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BestOffer extends Model
{
    protected $fillable = [
        'product_id',
        'offer_price',
        'discount_type',
        'discount_value',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
