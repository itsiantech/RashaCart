<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // Ensure roles & permissions and an admin user exist before seeding products
        $this->call([
            RolePermissionSeeder::class,
            ProductSeeder::class,
            PermissionSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '01742201187',
            'password' => bcrypt('password'),
        ]);

        $role = Role::where('name', 'admin')->first();
        $user->assignRole($role);
        $permissions = Permission::all();
        $role->givePermissionTo($permissions);

        // Seed products after a user exists so foreign keys are satisfied
        $this->call(ProductSeeder::class);

    }
}
