<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class TrackingPlatform extends Model
{
    protected $table = 'pixel_settings';

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
        'access_token' => 'encrypted',
    ];

    /**
     * Encrypt access_token automatically
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->access_token && !$model->isDirty('access_token')) {
                // Token already encrypted, don't re-encrypt
                return;
            }
        });

        static::saved(function () {
            Cache::forget('tracking.platforms.active');
            Cache::forget('tracking.platforms.all');
        });

        static::deleted(function () {
            Cache::forget('tracking.platforms.active');
            Cache::forget('tracking.platforms.all');
        });
    }

    /**
     * Get all active platforms from cache
     */
    public static function getActivePlatforms()
    {
        return Cache::remember('tracking.platforms.active', 3600, function () {
            return self::where('is_active', true)
                ->whereNotNull('pixel_id')
                ->get();
        });
    }

    /**
     * Get active platforms by platform type
     */
    public static function getActiveByPlatform(string $platform)
    {
        return self::getActivePlatforms()
            ->where('platform', $platform)
            ->values();
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
     * Relationship with audit logs
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(TrackingAuditLog::class, 'platform_id');
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

    /**
     * Get extra field value
     */
    public function getExtraValue(string $key, $default = null)
    {
        return data_get($this->extra, $key, $default);
    }

    /**
     * Set extra field value
     */
    public function setExtraValue(string $key, $value): void
    {
        $extra = $this->extra ?? [];
        data_set($extra, $key, $value);
        $this->extra = $extra;
    }
}
