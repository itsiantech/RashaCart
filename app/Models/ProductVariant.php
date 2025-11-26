<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 
        'sku', 
        'price_diff', 
        'stock',
        'image', 
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues() {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values')
                    ->withPivot('attribute_id');
    }

   
    public function values()
    {
        return $this->hasMany(VariantAttributeValue::class);
    }

   
    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'product_variant_id', 'id')
                    ->with(['attribute', 'attributeValue']);
    }


}
