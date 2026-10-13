<?php

namespace App\Services;

use App\Models\ImageOption;
use App\Models\ImageOptionStockMovement;
use App\Models\OrderItem;

class ImageOptionStock
{
    /** Current sellable stock from the option ledger. */
    public static function currentStock(int $optionId): int
    {
        return (int) ImageOptionStockMovement::where('image_option_id', $optionId)
            ->selectRaw("COALESCE(SUM(CASE type WHEN 'in' THEN quantity WHEN 'return' THEN quantity WHEN 'adjustment' THEN quantity ELSE -quantity END), 0) AS total")
            ->value('total');
    }

    public static function hasOwnStock(int $optionId): bool
    {
        return ImageOptionStockMovement::where('image_option_id', $optionId)->exists();
    }

    /** Record an initial stock entry (admin). */
    public static function setInitial(ImageOption $option, int $qty, ?int $userId = null): void
    {
        self::record([
            'image_option_id' => $option->id,
            'product_id' => $option->product_id,
            'type' => 'in',
            'quantity' => max(0, $qty),
            'notes' => 'Initial stock for image option',
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /** Adjust option stock to an absolute level (admin edit). */
    public static function adjustTo(ImageOption $option, int $target, ?int $userId = null): void
    {
        $diff = $target - self::currentStock($option->id);
        if ($diff === 0) {
            return;
        }
        self::record([
            'image_option_id' => $option->id,
            'product_id' => $option->product_id,
            'type' => 'adjustment',
            'quantity' => $diff,
            'notes' => 'Manual stock edit (option)',
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /** Idempotent OUT movement for an order item (double-deduction safe). */
    public static function ensureOrderItemOut(OrderItem $item): void
    {
        if (! $item->image_option_id || (int) $item->quantity < 1) {
            return;
        }

        $exists = ImageOptionStockMovement::where('order_item_id', $item->id)
            ->where('type', 'out')
            ->exists();

        if ($exists) {
            return;
        }

        self::record([
            'image_option_id' => $item->image_option_id,
            'product_id' => $item->product_id,
            'order_item_id' => $item->id,
            'type' => 'out',
            'quantity' => (int) $item->quantity,
            'notes' => 'Order sale — option stock',
            'user_id' => auth()->id(),
        ]);
    }

    /** Idempotent RETURN movement for a cancelled/returned order item. */
    public static function ensureOrderItemReturn(OrderItem $item): void
    {
        if (! $item->image_option_id || (int) $item->quantity < 1) {
            return;
        }

        $hasOut = ImageOptionStockMovement::where('order_item_id', $item->id)
            ->where('type', 'out')
            ->exists();

        if (! $hasOut) {
            return;
        }

        $hasReturn = ImageOptionStockMovement::where('order_item_id', $item->id)
            ->where('type', 'return')
            ->exists();

        if ($hasReturn) {
            return;
        }

        self::record([
            'image_option_id' => $item->image_option_id,
            'product_id' => $item->product_id,
            'order_item_id' => $item->id,
            'type' => 'return',
            'quantity' => (int) $item->quantity,
            'notes' => 'Order cancelled/returned — option stock released',
            'user_id' => auth()->id(),
        ]);
    }

    protected static function record(array $data): void
    {
        ImageOptionStockMovement::create($data);
    }
}
