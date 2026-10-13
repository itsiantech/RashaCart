<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function add(Product $product)
    {
        $product->load([
            'variants.variantAttributeValues.attribute',
            'variants.variantAttributeValues.attributeValue',
            'variants.inventories',
            'activeFlashSaleItem',
        ]);

        $cart = session()->get('cart', []);
        $price = $product->price_info['final'];

        $variantId = null;
        $variantImage = null;
        $color = null;
        $size = null;
        $variantLabel = '';

        if (setting('attribute_system', true) && $product->has_attributes && $product->variants->count() > 0) {
            $variant = $product->variants->first();

            $currentStock = $variant->inventories()->orderByDesc('id')->value('current_stock') ?? 0;
            if ($currentStock <= 0) {
                return redirect()->back()->with('error', 'Sorry, this product is out of stock.');
            }

            $variantId = $variant->id;

            $variantBase = $variant->variant_price ?? null;
            if ($variantBase !== null) {
                $flash = $product->activeFlashSaleItem;
                $price = ($flash && $flash->discount_percent > 0)
                    ? round($variantBase - ($variantBase * $flash->discount_percent / 100), 2)
                    : $variantBase;
            }

            $variantImage = ! empty($variant->image) ? asset('storage/' . $variant->image) : null;

            foreach ($variant->variantAttributeValues as $vav) {
                if (! $vav->attribute || ! $vav->attributeValue) {
                    continue;
                }
                $attrName = strtolower($vav->attribute->name ?? '');
                $attrValue = $vav->attributeValue->value ?? '';

                if (in_array($attrName, ['color', 'colour'])) {
                    $color = $attrValue;
                }
                if (in_array($attrName, ['size', 'siize'])) {
                    $size = $attrValue;
                }
            }

            $variantLabel = collect([$color, $size])->filter()->implode(' / ');
        }

        $finalKey = $variantId ? ($product->id . '-v' . $variantId) : $product->id;

        // Combination guard (required image option, stock, tampering)
        try {
            $combo = \App\Services\CombinationResolver::resolve($product, $variantId, null, 1);
            $price = $combo->unitPrice;
        } catch (\App\Services\CombinationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (isset($cart[$finalKey])) {
            $cart[$finalKey]['qty'] += 1;
        } else {
            $cart[$finalKey] = [
                'id' => $product->id,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'variant_id' => $variantId,
                'variant_label' => $variantLabel ?: null,
                'color' => $color,
                'size' => $size,
                'title' => $product->name,
                'price' => $price,
                'img' => $variantImage
                    ?? ($product->thumbnail
                        ? asset('storage/' . $product->thumbnail)
                        : 'https://placehold.co/500x500'),
                'qty' => 1,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }
}
