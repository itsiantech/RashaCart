<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
       'product_id',
       'variant_id',
       'type',
       'quantity',
       'current_stock',
       'notes',
       'user_id',
   ];

    public function product()
    {
         return $this->belongsTo(Product::class);
    }
    
    public function variant()
    {
         return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }
}