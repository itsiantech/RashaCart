<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'module_key', 'event', 'status', 'message', 'meta', 'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public static function log(
        string $moduleKey,
        string $event,
        string $status = 'info',
        ?string $message = null,
        array $meta = []
    ): void {
        // Never store PII in activity logs
        $meta = \App\Services\MaskPii::maskArray($meta);

        static::create([
            'module_key' => $moduleKey,
            'event' => $event,
            'status' => $status,
            'message' => $message,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
