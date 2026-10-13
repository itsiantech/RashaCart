<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingAuditLog extends Model
{
    protected $fillable = [
        'platform_id',
        'user_id',
        'action',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Relationship with platform
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(TrackingPlatform::class, 'platform_id');
    }

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Create audit log entry
     */
    public static function log(TrackingPlatform $platform, string $action, array $changes = [], ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return self::create([
            'platform_id' => $platform->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => self::sanitizeChanges($changes),
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Remove sensitive data from changes
     */
    protected static function sanitizeChanges(array $changes): array
    {
        $sensitiveKeys = ['access_token', 'token', 'password', 'secret', 'api_key'];

        foreach ($changes as $key => $value) {
            if (in_array($key, $sensitiveKeys)) {
                $changes[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $changes[$key] = self::sanitizeChanges($value);
            }
        }

        return $changes;
    }

    /**
     * Scope for a specific action
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
