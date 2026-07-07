<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Running query...\n";
    $orders = App\Models\Order::with(['items', 'vendor'])->latest()->paginate(15);
    echo "Orders retrieved successfully. Count: " . count($orders) . "\n";
    
    foreach ($orders as $order) {
        echo "Order ID: " . $order->id . "\n";
        $vendors = $order->items
            ->map(fn($item) => $item->product?->user)
            ->filter()
            ->unique('id');
        echo "Vendors: " . $vendors->pluck('name')->implode(', ') . "\n";
        echo "Currency setting: " . setting('currency') . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
