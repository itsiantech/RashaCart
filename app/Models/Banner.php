<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
class Banner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'status',
    ];

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('banners'));
        static::deleted(fn() => Cache::forget('banners'));
    }

}
