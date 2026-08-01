<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PixelSetting extends Model
{
    protected $fillable = ['platform', 'pixel_id', 'access_token', 'test_event_code', 'is_active', 'extra'];

    protected $casts = [
        'is_active' => 'boolean',
        'extra'     => 'array',
    ];

    public static function active()
    {
        return Cache::remember('pixel_settings.active', 3600, function () {
            return self::where('is_active', true)->whereNotNull('pixel_id')->get();
        });
    }

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('pixel_settings.active'));
        static::deleted(fn() => Cache::forget('pixel_settings.active'));
    }
}
