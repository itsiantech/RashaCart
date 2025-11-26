<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id', // Foreign key to the Attribute model
        'value', // e.g. Red, Large
    ];

    public function attribute() {
        return $this->belongsTo(Attribute::class);
    }

    public function variantAttributeValues() {
        return $this->hasMany(VariantAttributeValue::class);
    }

}
