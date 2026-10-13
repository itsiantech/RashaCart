<?php

namespace App\Models;

use App\Services\InventoryService;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
        'has_attributes',
        'has_image_options',
        'require_image_option',
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
        'has_attributes' => 'boolean',
        'has_image_options' => 'boolean',
        'require_image_option' => 'boolean',
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

    public function images()
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    public function imageOptions()
    {
        return $this->hasMany(ImageOption::class)->ordered();
    }

    public function activeImageOptions()
    {
        return $this->hasMany(ImageOption::class)->active()->ordered();
    }

    public function productAttributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes')->withPivot('is_required');
    }

    public function getStockAttribute()
    {
        return InventoryService::computeStock($this->inventories());
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

    public function getDiscountPercentAttribute()
    {
        if (! $this->sale_price || $this->sale_price >= $this->price || ! $this->price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
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

    public function activeFlashSaleItem()
    {
        return $this->hasOne(FlashSaleItem::class)
            ->whereHas('flashSale', fn($q) => $q
                ->where('is_active', true)
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now()))
            ->latestOfMany();
    }

    public function getPriceInfoAttribute(): array
    {
        $original = (float) $this->price;
        $flash    = $this->activeFlashSaleItem;

        // Flash sale er priority shobar upore
        if ($flash && $flash->discount_percent > 0) {
            $percent = (float) $flash->discount_percent;
            $final   = round($original - ($original * $percent / 100), 2);
        } elseif ($this->sale_price && (float) $this->sale_price < $original) {
            $final   = (float) $this->sale_price;
            $percent = $original > 0 ? round((($original - $final) / $original) * 100) : 0;
        } else {
            $final   = $original;
            $percent = 0;
        }

        $hasDiscount = $original > 0 && $final < $original;

        return [
            'original'     => $original,
            'final'        => $final,
            'percent'      => $hasDiscount ? (int) round($percent) : 0,
            'has_discount' => $hasDiscount,
            'is_flash'     => (bool) $flash,
        ];
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
