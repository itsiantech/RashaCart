<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function generateInvoice()
    {
        $dateCode = now()->format('ymd');
        $attempt = 1;

        while ($attempt <= 100) {

            $lastOrder = Order::where('invoice_no', 'like', "INV-{$dateCode}%")
                ->orderByDesc('id')
                ->first();

            if ($lastOrder) {
                $lastNumber = (int) substr($lastOrder->invoice_no, -2);
                $nextNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '01';
            }

            $invoice = "INV-{$dateCode}{$nextNumber}";

            $exists = Order::where('invoice_no', $invoice)->exists();

            if (! $exists) {
                return $invoice;
            }

            $attempt++;
        }

        throw new \Exception('Failed to generate unique invoice after 100 attempts.');
    }

    public function createOrder($data, $cartItems)
    {
        return DB::transaction(function () use ($data, $cartItems) {

            $order = Order::create($data);

            foreach ($cartItems as $item) {

                $product = Product::findOrFail($item['id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'vendor_id' => $product->user_id, // 👈 IMPORTANT
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                ]);
            }

            return $order;
        });
    }
}
