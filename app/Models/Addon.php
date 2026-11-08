<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    public const STATUS_INSTALLED = 'installed';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'slug',
        'name',
        'version',
        'description',
        'author',
        'provider',
        'status',
        'provides',
        'last_error',
        'installed_at',
        'activated_at',
    ];

    protected $casts = [
        'provides' => 'array',
        'installed_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
