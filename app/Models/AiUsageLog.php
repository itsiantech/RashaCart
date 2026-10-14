<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'module_key', 'tokens_in', 'tokens_out', 'cost', 'status', 'created_at',
    ];

    protected $casts = [
        'tokens_in' => 'integer',
        'tokens_out' => 'integer',
        'cost' => 'decimal:4',
        'created_at' => 'datetime',
    ];
}
