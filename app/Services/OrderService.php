<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function generateInvoice()
    {
        $dateCode = now()->format('ymd');

        $lastOrder = Order::whereDate('created_at', today())
            ->where('invoice_no', 'like', "INV-{$dateCode}%")
            ->latest('id')
            ->first();

        if ($lastOrder) {
            $lastSerial = (int) substr($lastOrder->invoice_no, -2);
            $newSerial = str_pad($lastSerial + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newSerial = '01';
        }

        return 'INV-' . $dateCode . $newSerial;
    }

    public function createOrder($data, $cartItems)
    {
        return DB::transaction(function () use ($data, $cartItems) {
            $order = Order::create($data);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                ]);
            }

            return $order;
        });
    }
}
