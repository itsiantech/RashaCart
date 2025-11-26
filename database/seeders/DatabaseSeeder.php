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
            'password' => bcrypt('password')
        ]);
        
        $role = Role::where('name', 'super-admin')->first(); 
        $user->assignRole($role);
        $permissions = Permission::all();  
        $role->givePermissionTo($permissions);


        $user = User::factory()->create([
            'name' => 'rider',
            'email' => 'rider@gmail.com',
            'password' => bcrypt('password')
        ]);

        $role = Role::where('name', 'rider')->first(); 
        $user->assignRole($role);
        $permissions = Permission::where('group_name', 'rider')->get();  
        $role->givePermissionTo($permissions);


        $user = User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password')
        ]);

        $role = Role::where('name', 'user')->first(); 
        $user->assignRole($role);
        $permissions = Permission::where('group_name', 'user')->get();  
        $role->givePermissionTo($permissions);



    }

}
