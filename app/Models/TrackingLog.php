<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingLog extends Model
{
    protected $fillable = [
        'platform_id',
        'event_name',
        'event_id',
        'order_id',
        'payload',
        'response',
        'http_status',
        'status',
        'attempts',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'http_status' => 'integer',
        'attempts' => 'integer',
        'sent_at' => 'datetime',
    ];

    /**
     * Relationship with platform
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(TrackingPlatform::class, 'platform_id');
    }

    /**
     * Relationship with order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mask PII in payload before saving
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->payload && is_array($model->payload)) {
                $model->payload = self::maskPii($model->payload);
            }
        });
    }

    /**
     * Mask PII data in array
     */
    protected static function maskPii(array $data): array
    {
        $piiFields = [
            'em', 'email', 'ph', 'phone', 'fn', 'first_name',
            'ln', 'last_name', 'ct', 'city', 'st', 'state',
            'zip', 'country', 'external_id', 'client_ip_address',
            'client_user_agent', 'user_data'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $piiFields)) {
                if (is_array($value)) {
                    $data[$key] = self::maskPii($value);
                } else {
                    $data[$key] = '[MASKED]';
                }
            } elseif (is_array($value)) {
                $data[$key] = self::maskPii($value);
            }
        }

        return $data;
    }

    /**
     * Scope for successful logs
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending logs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for a specific event
     */
    public function scopeForEvent($query, string $eventName)
    {
        return $query->where('event_name', $eventName);
    }

    /**
     * Scope for a specific order
     */
    public function scopeForOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Check if log is retryable
     */
    public function isRetryable(): bool
    {
        if ($this->status === 'success' || $this->status === 'duplicate') {
            return false;
        }

        if ($this->attempts >= 5) {
            return false;
        }

        // Permanent errors (4xx except 429)
        if ($this->http_status && $this->http_status >= 400 && $this->http_status < 500 && $this->http_status !== 429) {
            return false;
        }

        return true;
    }
}
