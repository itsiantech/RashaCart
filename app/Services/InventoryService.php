<?php

namespace App\Services;

use App\Models\DamagedStock;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockAdjustment;
use App\Models\WarehouseTransfer;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    // Orders that hold (reserve) stock — shown in Reserved column
    public const OPEN_ORDER_STATUSES = [
        'pending', 'Call Not Received', 'confirmed', 'shipped', 'Pending Return', 'Pending Cancelled',
    ];

    // Orders whose quantities are recorded as stock OUT movements
    public const SOLD_ORDER_STATUSES = [
        'pending', 'Call Not Received', 'confirmed', 'shipped', 'delivered', 'completed', 'Pending Return', 'Pending Cancelled',
    ];

    public const RELEASED_ORDER_STATUSES = ['cancelled', 'returned'];

    // Canonical stock formula: in + return + signed adjustment - out - damage
    public static function computeStock($inventoryQuery): int
    {
        $sum = (clone $inventoryQuery)
            ->selectRaw("COALESCE(SUM(CASE type WHEN 'in' THEN quantity WHEN 'return' THEN quantity WHEN 'adjustment' THEN quantity ELSE -quantity END), 0) AS total")
            ->value('total');

        return (int) $sum;
    }

    public static function currentStock(int $productId, ?int $variantId = null): int
    {
        return self::computeStock(
            Inventory::where('product_id', $productId)->where('variant_id', $variantId)
        );
    }

    public static function stockMap(): array
    {
        $rows = Inventory::query()
            ->selectRaw("product_id, variant_id, COALESCE(SUM(CASE type WHEN 'in' THEN quantity WHEN 'return' THEN quantity WHEN 'adjustment' THEN quantity ELSE -quantity END), 0) AS total")
            ->groupBy('product_id', 'variant_id')
            ->get();

        $map = ['product' => [], 'variant' => []];
        foreach ($rows as $row) {
            if ($row->variant_id === null) {
                $map['product'][$row->product_id] = (int) $row->total;
            } else {
                $map['variant'][$row->variant_id] = (int) $row->total;
            }
        }

        return $map;
    }

    public static function reservedMap(): array
    {
        $rows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', self::OPEN_ORDER_STATUSES)
            ->where('order_items.quantity', '>', 0)
            ->selectRaw('order_items.product_id, order_items.product_variant_id, SUM(order_items.quantity) AS qty')
            ->groupBy('order_items.product_id', 'order_items.product_variant_id')
            ->get();

        $map = ['product' => [], 'variant' => []];
        foreach ($rows as $row) {
            if ($row->product_variant_id === null) {
                $map['product'][$row->product_id] = (int) $row->qty;
            } else {
                $map['variant'][$row->product_variant_id] = (int) $row->qty;
            }
        }

        return $map;
    }

    // Single write path for the ledger. Keeps the legacy stock column
    // in sync so ledger available == products.stock / product_variants.stock.
    public static function recordMovement(array $data): Inventory
    {
        $type = $data['type'];
        $quantity = (int) $data['quantity'];
        $productId = (int) $data['product_id'];
        $variantId = isset($data['variant_id']) ? (int) $data['variant_id'] : null;

        $effect = match ($type) {
            'in', 'return' => abs($quantity),
            'out', 'damage' => -abs($quantity),
            default => $quantity, // adjustment carries its own sign
        };

        $data['current_stock'] = self::currentStock($productId, $variantId) + $effect;

        $inventory = Inventory::create($data);

        if ($variantId) {
            ProductVariant::whereKey($variantId)->increment('stock', $effect);
        } else {
            Product::whereKey($productId)->increment('stock', $effect);
        }

        return $inventory;
    }

    public static function adjustStock(Product $product, ?ProductVariant $variant, int $change, string $reason, ?int $userId = null): StockAdjustment
    {
        return DB::transaction(function () use ($product, $variant, $change, $reason, $userId) {
            $current = self::currentStock($product->id, $variant?->id);

            if ($current + $change < 0) {
                throw new \InvalidArgumentException('Insufficient stock! Available: '.$current);
            }

            $adjustment = StockAdjustment::create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity_change' => $change,
                'reason' => $reason,
                'adjusted_by' => $userId ?? auth()->id(),
            ]);

            self::recordMovement([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'type' => 'adjustment',
                'quantity' => $change,
                'notes' => $reason,
                'user_id' => $userId ?? auth()->id(),
            ]);

            return $adjustment;
        });
    }

    public static function logDamage(Product $product, ?ProductVariant $variant, int $quantity, string $reason, ?int $userId = null): DamagedStock
    {
        return DB::transaction(function () use ($product, $variant, $quantity, $reason, $userId) {
            $current = self::currentStock($product->id, $variant?->id);

            if ($quantity > $current) {
                throw new \InvalidArgumentException('Insufficient stock! Available: '.$current);
            }

            $damaged = DamagedStock::create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
                'reason' => $reason,
                'reported_by' => $userId ?? auth()->id(),
            ]);

            self::recordMovement([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'type' => 'damage',
                'quantity' => $quantity,
                'notes' => 'Damage reported: '.$reason,
                'user_id' => $userId ?? auth()->id(),
            ]);

            return $damaged;
        });
    }

    public static function resolveDamage(DamagedStock $damaged, string $action, ?string $note = null): void
    {
        if ($damaged->action_status !== 'pending') {
            return;
        }

        DB::transaction(function () use ($damaged, $action, $note) {
            $damaged->update([
                'action_status' => $action,
                'action_note' => $note,
                'action_at' => now(),
            ]);

            // refurbishing puts the units back into sellable stock (once)
            if ($action === 'refurbished' && ! $damaged->restocked) {
                self::recordMovement([
                    'product_id' => $damaged->product_id,
                    'variant_id' => $damaged->variant_id,
                    'type' => 'in',
                    'quantity' => $damaged->quantity,
                    'notes' => 'Refurbished damaged stock #'.$damaged->id.' restocked',
                    'user_id' => auth()->id(),
                ]);

                $damaged->forceFill(['restocked' => true])->save();
            }
        });
    }

    public static function transfer(Product $product, ?ProductVariant $variant, int $quantity, string $from, string $to, ?string $note = null, ?int $userId = null): WarehouseTransfer
    {
        return WarehouseTransfer::create([
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'quantity' => $quantity,
            'from_location' => $from,
            'to_location' => $to,
            'note' => $note,
            'transferred_by' => $userId ?? auth()->id(),
        ]);
    }

    // ── Order → ledger sync (idempotent, keyed on order_item reference) ──

    public static function syncOrderMovements(Order $order): void
    {
        $sold = in_array($order->status, self::SOLD_ORDER_STATUSES);

        foreach ($order->items as $item) {
            if ($sold) {
                self::ensureOrderItemOut($item);
                self::dropOrderItemReversal($item);
            } else {
                self::ensureOrderItemReversal($item);
            }

            // Image-option layer (separate ledger, same idempotency rules)
            if ($item->image_option_id) {
                if ($sold) {
                    \App\Services\ImageOptionStock::ensureOrderItemOut($item);
                } else {
                    \App\Services\ImageOptionStock::ensureOrderItemReturn($item);
                }
            }
        }
    }

    public static function syncOrderItem(OrderItem $item): void
    {
        $order = $item->order;
        if (! $order) {
            return;
        }

        if (in_array($order->status, self::SOLD_ORDER_STATUSES)) {
            self::ensureOrderItemOut($item);
            if ($item->image_option_id) {
                \App\Services\ImageOptionStock::ensureOrderItemOut($item);
            }
        } else {
            self::ensureOrderItemReversal($item);
            if ($item->image_option_id) {
                \App\Services\ImageOptionStock::ensureOrderItemReturn($item);
            }
        }
    }

    public static function removeOrderItemMovements(OrderItem $item): void
    {
        Inventory::where('reference_type', 'order_item')
            ->where('reference_id', $item->id)
            ->delete();
    }

    private static function ensureOrderItemOut(OrderItem $item): void
    {
        if ((int) $item->quantity < 1) {
            return;
        }

        $out = Inventory::where('reference_type', 'order_item')
            ->where('reference_id', $item->id)
            ->where('type', 'out')
            ->first();

        if ($out) {
            if ((int) $out->quantity !== (int) $item->quantity) {
                $out->update(['quantity' => (int) $item->quantity]);
            }

            return;
        }

        $order = $item->order;

        self::recordMovement([
            'product_id' => $item->product_id,
            'variant_id' => $item->product_variant_id,
            'type' => 'out',
            'quantity' => (int) $item->quantity,
            'notes' => 'Order sale — '.($order?->invoice_no ?? ('#'.$item->order_id)),
            'reference_type' => 'order_item',
            'reference_id' => $item->id,
            'user_id' => auth()->id(),
        ]);
    }

    private static function ensureOrderItemReversal(OrderItem $item): void
    {
        if ((int) $item->quantity < 1) {
            return;
        }

        $hasOut = Inventory::where('reference_type', 'order_item')
            ->where('reference_id', $item->id)
            ->where('type', 'out')
            ->exists();

        if (! $hasOut) {
            return;
        }

        $hasReversal = Inventory::where('reference_type', 'order_item')
            ->where('reference_id', $item->id)
            ->where('type', 'return')
            ->exists();

        if ($hasReversal) {
            return;
        }

        $order = $item->order;

        self::recordMovement([
            'product_id' => $item->product_id,
            'variant_id' => $item->product_variant_id,
            'type' => 'return',
            'quantity' => (int) $item->quantity,
            'notes' => 'Order '.($order?->status ?? 'cancelled').' — stock released ('.($order?->invoice_no ?? ('#'.$item->order_id)).')',
            'reference_type' => 'order_item',
            'reference_id' => $item->id,
            'user_id' => auth()->id(),
        ]);
    }

    private static function dropOrderItemReversal(OrderItem $item): void
    {
        Inventory::where('reference_type', 'order_item')
            ->where('reference_id', $item->id)
            ->where('type', 'return')
            ->delete();
    }
}
