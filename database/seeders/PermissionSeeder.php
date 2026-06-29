<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Dashboard
            'dashboard.view',

            // Category
            'category.view',

            // Attribute
            'attribute.view',

            // Product
            'product.view',
            'product.create',
            'product.update',
            'product.stock.manage',

            // Brand
            'brand.view',
            'brand.create',
            'brand.update',

            // Orders
            'order.manage',
            'order.view.own',
            'order.bulk.invoice.print',

            // Coupon
            'cupon.view',

            // Users
            'user.view',

            // Vendor
            'vendor.view',
            'vendor.payout',
            'vendor.profile',

            // Settings
            'setting.view',
            'settings.view',
            'settings.edit',
            'r&p.view',

            // Profile
            'profile.edit',
            'admin.profile.view',

            // Free Shipping
            'free.shipping.view',

            // Flash Sales
            'flash.sales.view',
            'flash.sales.create',
            'flash.sales.update',
            'flash.sales.stock.manage',

            // New Arrival
            'new.arrival.create',

            // For You
            'for.you.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Admin gets ALL permissions
        $admin->syncPermissions(Permission::all());

        // Vendor limited permissions
        $vendor->syncPermissions([
            'dashboard.view',
            'product.view',
            'product.create',
            'product.update',
            'order.view.own',
            'setting.view',
            'order.view',
            'vendor.payout',
            'vendor.profile',
            'admin.profile.view'
        ]);

        // Customer limited
        $customer->syncPermissions([
            'product.view',
            'order.view.own',
            'profile.edit',
        ]);
    }
}
