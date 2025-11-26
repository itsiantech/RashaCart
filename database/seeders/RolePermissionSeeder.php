<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\HasFactory;
class RolePermissionSeeder extends Seeder
{
    
   
    public function run(): void
    {
        $rolesToCreate = ['super-admin', 'admin','user'];

        foreach ($rolesToCreate as $roleName) {
            Role::create(['name' => $roleName]);
        }

        $permissionsAndGroups = [

            'user' => ['user.view', 'user.create', 'user.update', 'user.delete'],
            'category' => ['category.view', 'category.create', 'category.update', 'category.delete'],
            'product' => ['product.view', 'product.create', 'product.update', 'product.delete'],
            'order' => ['order.view', 'order.create', 'order.update', 'order.delete'],
            'cupon' => ['cupon.view', 'cupon.create', 'cupon.update', 'cupon.delete'],
            'setting' => ['setting.view',  'setting.update',],
            'RolesAndPermissions' => ['r&p.view', 'r&p.manage','r&p.create','r&p.update','r&p.delete'],
            
        ];
    
        foreach ($permissionsAndGroups as $groupName => $permissions) {
            foreach ($permissions as $permissionName) {
                Permission::create(['name' => $permissionName, 'group_name' => $groupName]);
            }
        }
    }
}
