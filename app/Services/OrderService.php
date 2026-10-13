<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CombinationResolver;
use App\Services\InventoryService;
use App\Services\PriceResolver;
use App\Services\Tracking\AttributionService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected AttributionService $attributionService;

    public function __construct(AttributionService $attributionService)
    {
        $this->attributionService = $attributionService;
    }

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
            // 1) Resolve every line authoritatively (variant ownership, price, stock)
            $lines = [];
            $itemsTotal = 0;

            foreach ($cartItems as $item) {
                $product = Product::with('activeFlashSaleItem')->findOrFail($item['id']);
                $qty = max(1, (int) ($item['qty'] ?? 1));

                // Authoritative combination resolution (variant + image option,
                // ownership, required layers, price, stock) with row locks.
                try {
                    $combo = CombinationResolver::resolve(
                        $product,
                        $item['variant_id'] ?? null,
                        $item['image_option_id'] ?? null,
                        $qty,
                        true
                    );
                } catch (CombinationException $e) {
                    throw new \Exception($e->getMessage());
                }

                $variant = $combo->variant;
                $option = $combo->option;
                $unitPrice = $combo->unitPrice;

                $lines[] = compact('product', 'variant', 'option', 'unitPrice', 'qty');
                $itemsTotal += $unitPrice * $qty;
            }

            if (empty($lines)) {
                throw new \Exception('Your cart is empty.');
            }

            // 2) Authoritative total (browser total is never trusted)
            $data['total_amount'] = max(0, $itemsTotal
                + (float) ($data['delivery_charge'] ?? 0)
                - (float) ($data['coupon_discount_amount'] ?? 0));

            // Capture attribution data
            $attributionData = $this->attributionService->getAttributionForOrder();

            // Merge attribution data into order data
            $orderData = array_merge($data, [
                'fbp' => $attributionData['fbp'] ?? null,
                'fbc' => $attributionData['fbc'] ?? null,
                'fbclid' => $attributionData['fbclid'] ?? null,
                'gclid' => $attributionData['gclid'] ?? null,
                'ttclid' => $attributionData['ttclid'] ?? null,
                'ttp' => $attributionData['ttp'] ?? null,
                'ga_client_id' => $attributionData['ga_client_id'] ?? null,
                'ga_session_id' => $attributionData['ga_session_id'] ?? null,
                'utm_source' => $attributionData['utm_source'] ?? null,
                'utm_medium' => $attributionData['utm_medium'] ?? null,
                'utm_campaign' => $attributionData['utm_campaign'] ?? null,
                'utm_content' => $attributionData['utm_content'] ?? null,
                'utm_term' => $attributionData['utm_term'] ?? null,
                'ip_address' => $attributionData['ip_address'] ?? null,
                'user_agent' => $attributionData['user_agent'] ?? null,
                'event_source_url' => $attributionData['event_source_url'] ?? null,
                'tracking_event_id' => $this->attributionService->generatePurchaseEventId(0), // Will be updated after order creation
            ]);

            $order = Order::create($orderData);

            // Update tracking_event_id with actual order ID
            $order->update([
                'tracking_event_id' => $this->attributionService->generatePurchaseEventId($order->id)
            ]);

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'vendor_id' => $line['product']->user_id, // 👈 IMPORTANT
                    'product_variant_id' => $line['variant']?->id,
                    'variant_sku' => $line['variant']?->sku,
                    'variant_name' => $line['variant']?->label,
                    'variant_image' => $line['variant']?->image,
                    'image_option_id' => $line['option']?->id,
                    'option_label' => $line['option']?->label,
                    'option_image' => $line['option']?->image,
                    'quantity' => $line['qty'],
                    'unit_price' => $line['unitPrice'],
                ]);
            }

            // Drop relations cached before items existed (e.g. Order::updated →
            // InventoryService::syncOrderMovements lazy-loads empty items),
            // so listeners see the real items.
            $order->unsetRelation('items')->unsetRelation('orderItems');

            // Fire OrderPlaced event for tracking
            event(new OrderPlaced($order));

            return $order;
        });
    }
}
