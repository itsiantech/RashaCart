<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPaymentSetting extends Model
{
    protected $table = 'manual_payment_settings';
    protected $fillable = [
        // bKash
        'bkash_enabled',
        'bkash_number',
        'bkash_account_type',

        // Nagad
        'nagad_enabled',
        'nagad_number',
        'nagad_account_type',

        // Rocket
        'rocket_enabled',
        'rocket_number',

        // Bank
        'bank_enabled',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_routing_number',
        'bank_branch',
    ];

    protected $casts = [
        'bkash_enabled'  => 'boolean',
        'nagad_enabled'  => 'boolean',
        'rocket_enabled' => 'boolean',
        'bank_enabled'   => 'boolean',
    ];

    /**
     * Always work with a single settings row.
     * Returns the existing record or creates a blank one.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], []);
    }
}
