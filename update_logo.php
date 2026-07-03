<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
App\Models\Settings::updateOrCreate(['key'=>'app_logo'], ['value'=>'logo.png']);
App\Models\Settings::clearCache();
echo 'Updated!';
