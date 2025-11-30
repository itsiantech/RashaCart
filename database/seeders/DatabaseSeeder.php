<?php

namespace Database\Seeders;

use Database\Seeders\AreaSeeder; 
use Database\Seeders\RolePermissionSeeder; 

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
   
    public function run(): void
    {

     
        $this->call(RolePermissionSeeder::class);
        
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'phone' => '01742201187',
            'password' => bcrypt('password')
        ]);
        
        $role = Role::where('name', 'super-admin')->first(); 
        $user->assignRole($role);
        $permissions = Permission::all();  
        $role->givePermissionTo($permissions);

    


    }

}
