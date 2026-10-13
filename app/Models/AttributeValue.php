<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id', // Foreign key to the Attribute model
        'value', // e.g. Red, Large
        'slug',
        'color_hex',
        'swatch_image',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function (AttributeValue $model) {
            if (empty($model->slug) && ! empty($model->value)) {
                $model->slug = Str::slug($model->value);
            }
        });
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class);
    }

    /** How many variants use this value (for safe-delete guard). */
    public function usageCount(): int
    {
        return $this->variantAttributeValues()->count();
    }

    public function isUsed(): bool
    {
        return $this->usageCount() > 0;
    }
}
