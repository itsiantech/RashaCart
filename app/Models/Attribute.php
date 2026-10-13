<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public const TYPE_COLOR_SWATCH = 'color_swatch';
    public const TYPE_IMAGE_SWATCH = 'image_swatch';
    public const TYPE_BUTTON = 'button';
    public const TYPE_DROPDOWN = 'dropdown';

    public const TYPES = [
        self::TYPE_COLOR_SWATCH => 'Color Swatch',
        self::TYPE_IMAGE_SWATCH => 'Image Swatch',
        self::TYPE_BUTTON => 'Button',
        self::TYPE_DROPDOWN => 'Dropdown',
    ];

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_filterable',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class)->orderBy('sort_order')->orderBy('id');
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes');
    }

    /** How many variants use this attribute (for safe-delete guard). */
    public function usageCount(): int
    {
        return VariantAttributeValue::where('attribute_id', $this->id)->count();
    }

    public function isUsed(): bool
    {
        return $this->usageCount() > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
