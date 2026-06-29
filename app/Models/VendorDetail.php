<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VendorDetail extends Model
{
    protected $table = 'vendor_details';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'shop_name',
        'shop_address',
        'NID_number',
        'profile_image',
        'trade_license_front',
        'trade_license_back',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'account_holder_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
