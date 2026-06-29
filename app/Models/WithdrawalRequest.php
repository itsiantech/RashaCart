<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'status'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'paid'     => 'Paid',
            default    => 'Unknown',
        };
    }
}
