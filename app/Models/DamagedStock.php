<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedStock extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'quantity',
        'reason',
        'action_status',
        'action_note',
        'action_at',
        'restocked',
        'reported_by',
    ];

    protected $casts = [
        'action_at' => 'datetime',
        'restocked' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
