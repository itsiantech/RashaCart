<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class)->where('is_active', true);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    /**
     * Status label: upcoming | live | expired
     */
    public function getStatusAttribute(): string
    {
        $now = Carbon::now();

        if ($now->lt($this->start_at)) {
            return 'upcoming';
        }

        if ($now->between($this->start_at, $this->end_at)) {
            return 'live';
        }

        return 'expired';
    }

    /**
     * Remaining seconds until end_at (used by countdown JS)
     */
    public function getRemainingSecondsAttribute(): int
    {
        $now = Carbon::now();

        if ($now->gt($this->end_at)) {
            return 0;
        }

        return (int) $now->diffInSeconds($this->end_at);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLive($query)
    {
        $now = Carbon::now();

        return $query->where('start_at', '<=', $now)->where('end_at', '>=', $now);
    }
}
