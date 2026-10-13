<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ImageOption;

/**
 * Single source of truth for sell prices. Used by cart AND order
 * creation so a tampered browser price can never change what is charged.
 * Supports per-variant wholesale quantity slabs.
 *
 * Precedence: image_option price ?? variant price ?? product price.
 * Variant slabs (when a variant is present) apply on the resolved base.
 */
class PriceResolver
{
    public static function resolve(
        Product $product,
        ?ProductVariant $variant = null,
        int $qty = 1,
        ?ImageOption $option = null
    ): float {
        $base = null;

        if ($option && $option->effective_price !== null) {
            $base = $option->effective_price;
        }

        if ($base === null && $variant) {
            $base = (float) $variant->effective_price;
        }

        if ($base === null) {
            return (float) $product->price_info['final'];
        }

        // Wholesale slab: best (highest) min_qty at or below qty (variant slabs)
        if ($variant) {
            $slab = $variant->priceSlabs
                ->where('min_qty', '<=', max(1, $qty))
                ->sortByDesc('min_qty')
                ->first();

            if ($slab) {
                $base = (float) $slab->price;
            }
        }

        $flash = $product->activeFlashSaleItem;

        if ($flash && (float) $flash->discount_percent > 0) {
            return round($base - ($base * (float) $flash->discount_percent / 100), 2);
        }

        return (float) $base;
    }
}
