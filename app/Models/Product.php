<?php

namespace App\Models;
use Cache;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $fillable = [
        'user_id',
        'category_id',        
        'name',
        'slug',
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
    ];

    protected $casts = [
        'gallery_image' => 'array',
    ];



    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
 

    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }   

    public function coupons() {
        return $this->belongsToMany(Coupon::class, 'product_coupons');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function orderitems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }



    protected static function booted()
    {
        static::saved(fn() => Cache::forget('products'));
        static::deleted(fn() => Cache::forget('products'));
    }


}
