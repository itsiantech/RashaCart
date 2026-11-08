<?php

namespace Addons\PartialPayment\Services;

use Addons\PartialPayment\Models\OrderPlan;
use Addons\PartialPayment\Models\ProductRule;
use Addons\PartialPayment\Support\Options;
use App\Models\Order;

class PlanCalculator
{
    /**
     * Advance in whole taka: percentages always round up, a fixed claim can
     * never exceed the amount it is taken from, and any advance is at least
     * Tk 1 and never more than the total.
     */
    public static function advanceAmount(float $base, ProductRule $rule): float
    {
        $base = max(0.0, $base);
        $value = (float) $rule->advance_value;

        if ($base <= 0 || $value <= 0) {
            return 0.0;
        }

        $raw = $rule->isPercentage() ? ($base * $value / 100) : min($value, $base);
        $whole = (float) ceil($raw - 0.0000001);

        return max(1.0, min($whole, (float) ceil($base - 0.0000001)));
    }

    /** The rule that applies to a preview: subtotal/total come from the server. */
    public static function preview(int $productId, float $unitPrice, int $qty, float $shipping): ?array
    {
        $rule = ProductRule::forProduct($productId);

        return $rule === null ? null : self::build($rule, $unitPrice * max(1, $qty), $shipping);
    }

    public static function build(ProductRule $rule, float $subtotal, float $shipping): array
    {
        $total = max(0.0, $subtotal + $shipping);
        $base = Options::base() === 'subtotal' ? $subtotal : $total;

        $advance = self::advanceAmount($base, $rule);

        if ($advance <= 0 || $advance >= $total) {
            // Nothing left to collect on delivery is not a partial payment.
            return [];
        }

        return [
            'advance' => $advance,
            'due' => max(0.0, $total - $advance),
            'total' => $total,
            'subtotal' => $subtotal,
            'advance_type' => $rule->advance_type,
            'advance_value' => (float) $rule->advance_value,
        ];
    }

    /**
     * Server-side plan from a persisted order. Multi-item orders take the
     * largest single advance among the products that carry a rule.
     */
    public static function fromOrder(Order $order): ?array
    {
        $order->loadMissing('items');

        $subtotal = 0.0;

        foreach ($order->items as $item) {
            $subtotal += (float) $item->unit_price * (int) $item->quantity;
        }

        $best = null;

        foreach ($order->items as $item) {
            $rule = ProductRule::forProduct((int) $item->product_id);

            if ($rule === null) {
                continue;
            }

            $plan = self::build($rule, $subtotal, (float) $order->delivery_charge);

            if (($plan['advance'] ?? 0) > ($best['advance'] ?? 0)) {
                $best = $plan;
            }
        }

        if ($best === null || ! isset($best['advance'])) {
            return null;
        }

        // Always settle against the order's own authoritative total.
        $total = (float) $order->total_amount;

        if ($total <= 1) {
            return null;
        }

        $advance = max(1.0, min((float) $best['advance'], $total - 1));

        return [
            'advance' => $advance,
            'due' => max(0.0, $total - $advance),
            'total' => $total,
        ];
    }

    public static function planExists(int $orderId): bool
    {
        return OrderPlan::forOrder($orderId) !== null;
    }
}
