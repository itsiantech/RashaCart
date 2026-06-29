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
        $rolesToCreate = ['admin', 'vendor', 'user'];

        foreach ($rolesToCreate as $roleName) {
            Role::create(['name' => $roleName]);
        }

        $permissionsAndGroups = [

            'user' => ['user.view', 'user.create', 'user.update', 'user.delete'],
            'category' => ['category.view', 'category.create', 'category.update', 'category.delete'],
            'product' => ['product.view', 'product.create', 'product.update', 'product.delete', 'product.stock.manage'],
            'order' => ['order.view','order.vendor.view', 'order.create', 'order.update', 'order.delete'],
            'order.bulk.invoice.print' => ['order.bulk.invoice.print'],
            'free-shipping' => ['free.shipping.view'],
            'cupon' => ['cupon.view', 'cupon.create', 'cupon.update', 'cupon.delete'],
            'setting' => ['setting.view',  'setting.update',],
            'RolesAndPermissions' => ['r&p.view', 'r&p.manage','r&p.create','r&p.update','r&p.delete'],
            'flash-sale' => ['flash.sales.view', 'flash.sales.create', 'flash.sales.update', 'flash.sales.stock.manage'],
            'for-you' => ['for.you.view'],
            'vendor' => ['vendor.view', 'vendor.payout', 'vendor.profile'],
            
        ];
    
        foreach ($permissionsAndGroups as $groupName => $permissions) {
            foreach ($permissions as $permissionName) {
                Permission::create(['name' => $permissionName, 'group_name' => $groupName]);
            }
        }
    }
}
