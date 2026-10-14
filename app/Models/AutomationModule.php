<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationModule extends Model
{
    protected $fillable = [
        'key', 'is_enabled', 'mode', 'settings', 'last_enabled_at', 'enabled_by',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'array',
        'last_enabled_at' => 'datetime',
    ];

    public const MODE_OFF = 'off';
    public const MODE_SHADOW = 'shadow';
    public const MODE_LIVE = 'live';

    protected static function booted()
    {
        static::saved(fn () => \App\Services\AutomationRegistry::clearCache());
        static::deleted(fn () => \App\Services\AutomationRegistry::clearCache());
    }

    public function isLive(): bool
    {
        return $this->is_enabled && $this->mode === self::MODE_LIVE;
    }

    public function isShadow(): bool
    {
        return $this->is_enabled && $this->mode === self::MODE_SHADOW;
    }
}
