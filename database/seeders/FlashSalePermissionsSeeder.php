<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FlashSalePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'flash.sales.view',
            'flash.sales.create',
            'flash.sales.update',
            'flash.sales.delete',
            'flash.sales.stock.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('Flash sale permissions seeded.');
    }
}