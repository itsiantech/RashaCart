<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saved(fn () => Cache::forget('products'));
        static::deleted(fn () => Cache::forget('products'));

    }
}
