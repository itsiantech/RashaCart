<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageOptionStockMovement extends Model
{
    protected $fillable = [
        'image_option_id',
        'product_id',
        'order_item_id',
        'type',
        'quantity',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function option()
    {
        return $this->belongsTo(ImageOption::class, 'image_option_id');
    }
}
