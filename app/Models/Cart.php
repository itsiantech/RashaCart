<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id', // Foreign key to the User model
        'product_id', // Foreign key to the Product model
        'product_variant_id', // Foreign key to the ProductVariant model
        'quantity', // Quantity of the product in the cart
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
