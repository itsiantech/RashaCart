<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'module_key', 'action', 'old_value', 'new_value', 'ip', 'created_at',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
    ];

    /** Secrets are stripped before write — callers must still avoid passing them. */
    public static function record(
        string $moduleKey,
        string $action,
        $old = null,
        $new = null,
        ?int $userId = null
    ): void {
        static::create([
            'user_id' => $userId ?? auth()->id(),
            'module_key' => $moduleKey,
            'action' => $action,
            'old_value' => self::scrub($old),
            'new_value' => self::scrub($new),
            'ip' => request()?->ip(),
            'created_at' => now(),
        ]);
    }

    protected static function scrub($value)
    {
        if (! is_array($value)) {
            return $value;
        }
        foreach (['api_key', 'secret', 'token', 'password', 'access_token'] as $k) {
            if (array_key_exists($k, $value)) {
                $value[$k] = '[REDACTED]';
            }
        }

        return $value;
    }
}
