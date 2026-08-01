<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'brand_id',
        'user_id',
        'sku',
        'category_id',
        'name',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_tags',
        'short_description',
        'long_description',
        'buying_price',
        'price',
        'discount_type',
        'discount_price',
        'sale_price',
        'stock',
        'has_variant',
        'thumbnail',
        'gallery_image',
        'video',
        'status',
        'is_featured',
        'is_preorder',
        'preorder_start_date',
        'preorder_end_date',
        'expected_release_date',
    ];

    protected $casts = [
        'gallery_image' => 'array',
        'is_preorder' => 'boolean',
        'preorder_start_date' => 'datetime',
        'preorder_end_date' => 'datetime',
        'expected_release_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_id')
            ->whereNull('variant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getStockAttribute()
    {
        $in = $this->inventories()->where('type', 'in')->sum('quantity');
        $out = $this->inventories()->where('type', 'out')->sum('quantity');

        return $in - $out;
    }

    public function getCreatorNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }

        return 'System';
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'product_coupons');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderitems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function newArrival()
    {
        return $this->hasOne(NewArrival::class, 'product_id', 'id');
    }

    public function scopeActivePreorder($query)
    {
        return $query->where('is_preorder', true)
            ->where('preorder_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('preorder_end_date')
                    ->orWhere('preorder_end_date', '>=', now());
            });
    }

    // Helper accessor
    public function getIsPreorderActiveAttribute(): bool
    {
        if (! $this->is_preorder) {
            return false;
        }
        if ($this->preorder_start_date && $this->preorder_start_date > now()) {
            return false;
        }
        if ($this->preorder_end_date && $this->preorder_end_date < now()) {
            return false;
        }

        return true;
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('products');
            Cache::forget('global_cache_ready');
        });

        static::deleted(function () {
            Cache::forget('products');
            Cache::forget('global_cache_ready');
        });
    }

    public function primaryBrand()
    {
        return $this->belongsToMany(Brand::class)->wherePivot('is_primary', true);
    }
}
