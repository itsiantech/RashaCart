<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'status',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('banners');
            Cache::forget('ads');
            Cache::forget('popup');
            Cache::forget('global_cache_ready');
        });

        static::deleted(function ($model) {
            Cache::forget('banners');
            Cache::forget('ads');
            Cache::forget('popup');
            Cache::forget('global_cache_ready');
        });
    }
}
