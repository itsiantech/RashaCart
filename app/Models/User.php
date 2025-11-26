<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use DB;
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
        'address',
        'status',
        'phone',
        'email',
        'role',
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


    
    //This area for checkBok Loop for select roles permission
    public static function getpermissionGroups(){
        
        $permission_groups = DB::table('permissions')
                                ->select('group_name')
                                ->groupBy('group_name')
                                ->get();

        return $permission_groups;
    }

    public static function getpermissionByGroupName($group_name){
        
        $permissions = DB::table('permissions')
                            ->select('name','id')
                            ->where('group_name', $group_name)
                            ->get();
        return $permissions;
    }

    //this is for edit roles permission checkbox selected
    public static function roleHasPermissions($roles, $permissions){

       $hasPermission = true;
       foreach($permissions as $permission){
        if (!$roles->hasPermissionTo($permission->name)){
            $hasPermission = false;
        }
        return $hasPermission;
       }
       
    }


    public function products() {
        return $this->hasMany(Product::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function cart() {
        return $this->hasMany(Cart::class);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }


}
