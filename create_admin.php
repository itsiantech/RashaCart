<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

try {
    echo "Running PermissionSeeder...\n";
    (new \Database\Seeders\PermissionSeeder())->run();
    echo "Seeding completed.\n";

    echo "Creating admin user...\n";
    $user = User::firstOrCreate(
        ['email' => 'admin@gmail.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'phone' => '01742201187',
            'status' => true,
        ]
    );

    echo "Assigning role 'admin'...\n";
    $user->assignRole('admin');

    echo "Admin user created successfully!\n";
    echo "Email: admin@gmail.com\n";
    echo "Password: password\n";
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
