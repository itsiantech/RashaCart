<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\PurchaseActions;

class CartController extends Controller
{
    public function add(Product $product)
    {
        if (! PurchaseActions::addToCartEnabled()) {
            return redirect()->back()->with('error', 'Add to Cart is currently disabled.');
        }

        session()->forget('buy_now_item');

        $product->load([
            'variants.variantAttributeValues.attribute',
            'variants.variantAttributeValues.attributeValue',
            'variants.inventories',
            'activeFlashSaleItem',
        ]);

        // Variant products must be configured on the product page, never
        // silently added with an arbitrary first variant.
        if (setting('attribute_system', true) && $product->has_attributes && $product->variants->count() > 0) {
            return redirect()
                ->route('frontend.product.single.view', $product->slug)
                ->with('error', 'Please select your variant before adding to cart.');
        }

        $cart = session()->get('cart', []);
        $price = $product->price_info['final'];

        $finalKey = $product->id;

        // Combination guard (required image option, stock, tampering)
        try {
            $price = \App\Services\CombinationResolver::resolve($product, null, null, 1)->unitPrice;
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
                'variant_id' => null,
                'variant_label' => null,
                'color' => null,
                'size' => null,
                'title' => $product->name,
                'price' => $price,
                'img' => $product->thumbnail
                    ? asset('storage/' . $product->thumbnail)
                    : 'https://placehold.co/500x500',
                'qty' => 1,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()
            ->with('success', 'Product added to cart!')
            ->with('open_cart_drawer', true);
    }
}
