<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @deprecated Use TrackingPlatform instead
 * This model is kept for backward compatibility
 */
class PixelSetting extends Model
{
    protected $fillable = [
        'platform',
        'name',
        'pixel_id',
        'access_token',
        'test_event_code',
        'is_active',
        'is_test_mode',
        'api_version',
        'extra',
        'store_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'extra' => 'array',
        'test_mode_enabled_at' => 'datetime',
        'access_token' => 'encrypted', // Encrypt at rest
    ];

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('pixel_settings.active'));
        static::deleted(fn() => Cache::forget('pixel_settings.active'));
    }

    public static function active()
    {
        return Cache::remember('pixel_settings.active', 3600, function () {
            return self::where('is_active', true)->whereNotNull('pixel_id')->get();
        });
    }

    /**
     * Relationship with event rules
     */
    public function eventRules(): HasMany
    {
        return $this->hasMany(TrackingEventRule::class, 'platform_id');
    }

    /**
     * Relationship with tracking logs
     */
    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class, 'platform_id');
    }

    /**
     * Mask access token for display
     */
    public function getMaskedTokenAttribute(): string
    {
        if (!$this->access_token) {
            return '';
        }

        $token = $this->access_token;
        if (strlen($token) > 10) {
            return substr($token, 0, 8) . '...' . substr($token, -4);
        }

        return '****';
    }
}
