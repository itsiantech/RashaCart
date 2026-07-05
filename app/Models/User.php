<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\VendorCommission;
use App\Models\VendorPayout;
use App\Models\VendorPayoutRequest;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    protected $fillable = [
        'google_id',
        'profile_image',
        'name',
        'role',
        'is_vendor_approved',
        'vendor_shop_name',
        'vendor_description',
        'vendor_phone',
        'vendor_address',
        'address',
        'status',
        'phone',
        'email',
        'password'
    ];

    public function user_profile()
    {
        return $this->hasOne(UserProfile::class);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categoryCommissions()
    {
        return $this->hasMany(CategoryCommission::class, 'vendor_id');
    }

    public function payoutRequests()
    {
        return $this->hasMany(VendorPayoutRequest::class, 'vendor_id');
    }

    public function payouts()
    {
        return $this->hasMany(WithdrawalRequest::class, 'vendor_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function getStockAttribute()
    {
        $inventoryIn = $this->inventories()
            ->whereNull('variant_id')
            ->where('type', 'in')
            ->sum('quantity');

        $inventoryOut = $this->inventories()
            ->whereNull('variant_id')
            ->where('type', 'out')
            ->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }


    //This area for checkBok Loop for select roles permission
    public static function getpermissionGroups()
    {

        $permission_groups = DB::table('permissions')
            ->select('group_name')
            ->groupBy('group_name')
            ->get();

        return $permission_groups;
    }

    public static function getpermissionByGroupName($group_name)
    {

        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }

    //this is for edit roles permission checkbox selected
    public static function roleHasPermissions($roles, $permissions)
    {

        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$roles->hasPermissionTo($permission->name)) {
                $hasPermission = false;
            }
            return $hasPermission;
        }
    }

    public function vendorDetails()
    {
        return $this->hasOne(VendorDetail::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id'); // Adjust based on your product model
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_vendor_approved' => 'boolean',
        'status' => 'boolean',
        'password' => 'hashed',
    ];
}
