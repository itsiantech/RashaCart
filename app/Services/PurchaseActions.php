<?php

namespace App\Services;

use App\Models\Product;

class PurchaseActions
{
    public const DEFAULT_ADD_LABEL = 'Add to Cart';

    public const DEFAULT_BUY_LABEL = 'Buy Now';

    public const DEFAULT_DISABLED_TEXT = 'Contact us to order';

    public static function addToCartEnabled(): bool
    {
        return (string) setting('add_to_cart_enabled', '1') === '1';
    }

    public static function buyNowEnabled(): bool
    {
        return (string) setting('buy_now_enabled', '1') === '1';
    }

    public static function cartDrawerEnabled(): bool
    {
        return (string) setting('cart_drawer_enabled', '1') === '1';
    }

    public static function addToCartLabel(): string
    {
        return trim((string) setting('add_to_cart_label')) ?: self::DEFAULT_ADD_LABEL;
    }

    public static function buyNowLabel(): string
    {
        return trim((string) setting('buy_now_label')) ?: self::DEFAULT_BUY_LABEL;
    }

    public static function disabledText(): string
    {
        return trim((string) setting('purchase_disabled_text')) ?: self::DEFAULT_DISABLED_TEXT;
    }

    public static function hasAnyEnabled(): bool
    {
        return self::addToCartEnabled() || self::buyNowEnabled();
    }

    public static function requiresVariantSelection(Product $product): bool
    {
        return (bool) setting('attribute_system', true) && (bool) $product->has_attributes;
    }
}
