<?php

namespace App\Traits;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

trait HandlesCartActions
{
    public $quantity = 1;


    private function getProductAttributeTypes($product)
    {
        $attributeTypes = collect();
        foreach ($product->variants as $variant) {
            foreach ($variant->variantAttributeValues as $vav) {
                $attrName = strtolower($vav->attribute->name ?? '');
                if ($attrName !== '' && ! $attributeTypes->contains($attrName)) {
                    $attributeTypes->push($attrName);
                }
            }
        }

        return $attributeTypes;
    }

    public function increaseQty($id = null)
    {
        if ($id && isset($this->cartItems[$id])) {
            $this->cartItems[$id]['qty']++;
            $this->updateCart();
        } else {
            $this->quantity++;
        }
    }

    public function decreaseQty($id = null)
    {
        if ($id && isset($this->cartItems[$id]) && $this->cartItems[$id]['qty'] > 1) {
            $this->cartItems[$id]['qty']--;
            $this->updateCart();
        } else {
            $this->quantity = max(1, $this->quantity - 1);
        }
    }

    public function removeItem($id)
    {
        if (isset($this->cartItems[$id])) {
            unset($this->cartItems[$id]);
            $this->updateCart();
        }
    }

    public function updateCart()
    {
        session(['cart' => $this->cartItems]);
        $this->dispatch('cartUpdated');

        $cartCount = array_sum(array_column($this->cartItems, 'qty'));
        $cartTotal = $this->cartTotal($this->cartItems);
        $this->dispatch('cart-updated', count: $cartCount, total: $cartTotal);
    }

    private function cartTotal(array $cart): float
    {
        return array_sum(array_map(
            fn ($item) => ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 1)),
            $cart
        ));
    }

    public function addToCart($productId)
    {
        if (! \App\Services\PurchaseActions::addToCartEnabled()) {
            $this->dispatch('error', message: 'Add to Cart is currently disabled.');

            return false;
        }

        session()->forget('buy_now_item');

        $resolved = $this->buildPurchaseItem($productId);
        if ($resolved === false) {
            return false;
        }

        $cart = session()->get('cart', []);
        $key = $resolved['key'];
        $qty = $resolved['qty'];
        $currentQtyInCart = (int) ($cart[$key]['qty'] ?? 0);

        if (($currentQtyInCart + $qty) > $resolved['available']) {
            $this->dispatch('error', message: "Not enough stock available. Only {$resolved['available']} left.");

            return false;
        }

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = $resolved['item'];
        }

        session()->put('cart', $cart);
        $this->quantity = 1;

        $cartCount = array_sum(array_column($cart, 'qty'));
        $cartTotal = $this->cartTotal($cart);
        $this->dispatch('cart-updated', count: $cartCount, total: $cartTotal);
        $this->dispatch('cartUpdated');
        $this->dispatch('cart-drawer-open', count: $cartCount, total: $cartTotal);
        $this->dispatch('success', message: 'Product added to cart!');

        $this->dispatch('track-event', eventName: 'AddToCart', data: [
            'content_id'   => $resolved['content_id'],
            'content_name' => $resolved['item']['title'],
            'content_type' => 'product',
            'variant'      => trim(($resolved['variant_label'] ?: '').($resolved['option_label'] ? ' | '.$resolved['option_label'] : '')) ?: null,
            'value'        => $resolved['item']['price'] * $qty,
            'currency'     => 'BDT',
        ]);

        return true;
    }

    /**
     * Resolve a single purchasable line without writing to the cart.
     * Used by both Add to Cart and the independent Buy Now flow.
     */
    private function buildPurchaseItem($productId)
    {
        // CHANGED (1): 'activeFlashSaleItem' added to eager load
        $product = Product::with([
            'variants.variantAttributeValues.attribute',
            'variants.variantAttributeValues.attributeValue',
            'variants.inventories',
            'activeFlashSaleItem',
        ])->findOrFail($productId);

        $qty = max(1, (int) ($this->quantity ?? 1));

        // CHANGED (2): flash sale + normal discount, both handled
        $price = $product->price_info['final'];

        $variantImage = null;
        $variantId = null;
        $variant = null;
        $color = null;
        $size = null;
        $variantLabel = '';

        if (setting('attribute_system', true) && $product->has_attributes && $product->variants->count() > 0) {

            $attributeTypes = $this->getProductAttributeTypes($product);
            $hasColor = $attributeTypes->contains(fn($a) => in_array($a, ['color', 'colour']));
            $hasSize = $attributeTypes->contains(fn($a) => in_array($a, ['size', 'siize']));

            $selectedVariantId = $this->selectedVariant ?? null;

            if ($selectedVariantId) {
                $variantId = $selectedVariantId;
            } elseif ($attributeTypes->count() === 1) {
                $resolved = null;

                if ($hasColor && ! empty($this->selectedColor)) {
                    $resolved = $this->findVariantByAttributes($product, $this->selectedColor, null);
                } elseif ($hasSize && ! empty($this->selectedSize)) {
                    $resolved = $this->findVariantByAttributes($product, null, $this->selectedSize);
                }

                $variantId = ($resolved ?? $product->variants->first())?->id;
            } else {
                $colorSelected = ! $hasColor || ! empty($this->selectedColor);
                $sizeSelected = ! $hasSize || ! empty($this->selectedSize);

                if (! $colorSelected || ! $sizeSelected) {
                    $missing = collect();
                    if (! $colorSelected) {
                        $missing->push('color');
                    }
                    if (! $sizeSelected) {
                        $missing->push('size');
                    }

                    $this->dispatch('error', message: 'Please select ' . $missing->implode(' and ') . ' before adding to cart.');
                    $this->dispatch('show-variant-warning');

                    return false;
                }

                $resolved = $this->findVariantByAttributes($product, $this->selectedColor ?? null, $this->selectedSize ?? null);
                $variantId = $resolved?->id;

                if (! $variantId) {
                    $this->dispatch('error', message: 'The selected combination is not available.');

                    return false;
                }
            }

            if ($variantId) {
                $variant = ProductVariant::with([
                    'variantAttributeValues.attribute',
                    'variantAttributeValues.attributeValue',
                    'inventories',
                    'priceSlabs',
                ])->find($variantId);

                if (! $variant) {
                    $this->dispatch('error', message: 'Selected variant not found.');

                    return false;
                }

                // Tamper guard: variant must belong to this product and be active
                if ((int) $variant->product_id !== (int) $product->id) {
                    $this->dispatch('error', message: 'Selected variant is not available for this product.');

                    return false;
                }

                if (! $variant->is_active) {
                    $this->dispatch('error', message: 'Sorry, this variant is no longer available.');

                    return false;
                }

                // Stock check (authoritative qty check runs after CombinationResolver)
                $currentStock = $variant->inventories()->orderByDesc('id')->value('current_stock') ?? 0;
                if ($currentStock <= 0) {
                    $this->dispatch('error', message: 'Sorry, this variant is out of stock.');

                    return false;
                }

                // Authoritative price (same resolver used at order time — browser can't tamper)
                $price = \App\Services\PriceResolver::resolve($product, $variant, $qty);

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

                if (empty($color) && ! empty($this->selectedColor)) {
                    $color = $this->selectedColor;
                }
                if (empty($size) && ! empty($this->selectedSize)) {
                    $size = $this->selectedSize;
                }

                if (empty($variantImage) && ! empty($color)) {
                    $colorVariant = $product->variants->first(function ($v) use ($color) {
                        foreach ($v->variantAttributeValues as $vav) {
                            if (! $vav->attribute || ! $vav->attributeValue) {
                                continue;
                            }
                            $attrName = strtolower($vav->attribute->name ?? '');
                            if (
                                in_array($attrName, ['color', 'colour'])
                                && $vav->attributeValue->value === $color
                                && ! empty($v->image)
                            ) {
                                return true;
                            }
                        }

                        return false;
                    });

                    if ($colorVariant) {
                        $variantImage = asset('storage/' . $colorVariant->image);
                    }
                }

                $variantLabel = collect([$color, $size])->filter()->implode(' / ');
            }
        }

        $finalKey = $variantId ? ($productId . '-v' . $variantId) : $productId;

        // ── Image-option layer: authoritative re-resolution (price/stock/ownership) ──
        // (property_exists: not every component using this trait has the selector)
        $optionId = property_exists($this, 'selectedOption') ? $this->selectedOption : null;
        $optionLabel = null;
        $optionImage = null;

        try {
            $combo = \App\Services\CombinationResolver::resolve($product, $variantId, $optionId, $qty);
            $price = $combo->unitPrice;
            $optionLabel = $combo->optionLabel;
            $optionImage = $combo->option?->image;
        } catch (\App\Services\CombinationException $e) {
            $this->dispatch('error', message: $e->getMessage());

            return false;
        }

        if ($optionId && ($combo->option?->id !== (int) $optionId)) {
            $this->dispatch('error', message: 'ভুল image option সিলেক্ট হয়েছে।');

            return false;
        }

        $resolvedOptionId = $combo->option?->id;

        if ($resolvedOptionId) {
            $finalKey .= '-o' . $resolvedOptionId;
        }

        $item = [
            'id' => $product->id,
            'product_id' => $product->id,
            'category_id' => $product->category_id,
            'variant_id' => $variantId,
            'image_option_id' => $resolvedOptionId,
            'option_label' => $optionLabel,
            'variant_label' => $variantLabel ?: null,
            'color' => $color,
            'size' => $size,
            'title' => $product->name,
            'price' => $price,
            'img' => $optionImage
                ? asset('storage/' . $optionImage)
                : ($variantImage
                    ?? ($product->thumbnail
                        ? asset('storage/' . $product->thumbnail)
                        : 'https://placehold.co/500x500')),
            'qty' => $qty,
        ];

        return [
            'key' => $finalKey,
            'item' => $item,
            'qty' => $qty,
            'available' => $combo->available,
            'content_id' => $combo->contentId,
            'variant_label' => $variantLabel,
            'option_label' => $optionLabel,
            'product' => $product,
        ];
    }

    private function findVariantByAttributes($product, ?string $colorValue, ?string $sizeValue): ?ProductVariant
    {
        foreach ($product->variants as $variant) {
            $colorMatch = ($colorValue === null);
            $sizeMatch = ($sizeValue === null);

            foreach ($variant->variantAttributeValues as $vav) {
                if (! $vav->attribute || ! $vav->attributeValue) {
                    continue;
                }

                $attrName = strtolower($vav->attribute->name ?? '');
                $val = $vav->attributeValue->value ?? '';

                if (! $colorMatch && in_array($attrName, ['color', 'colour'])) {
                    $colorMatch = ($val === $colorValue);
                }
                if (! $sizeMatch && in_array($attrName, ['size', 'siize'])) {
                    $sizeMatch = ($val === $sizeValue);
                }
            }

            if ($colorMatch && $sizeMatch) {
                return $variant;
            }
        }

        return null;
    }

    public function buyNow($productId)
    {
        if (! \App\Services\PurchaseActions::buyNowEnabled()) {
            $this->dispatch('error', message: 'Buy Now is currently disabled.');

            return;
        }

        session()->forget('buy_now_item');

        $resolved = $this->buildPurchaseItem($productId);
        if ($resolved === false) {
            return;
        }

        if ($resolved['qty'] > $resolved['available']) {
            $this->dispatch('error', message: "Not enough stock available. Only {$resolved['available']} left.");

            return;
        }

        // Direct purchase: keep the cart untouched.
        $item = $resolved['item'];
        $item['_key'] = $resolved['key'];
        $item['product_slug'] = $resolved['product']->slug;

        session()->put('buy_now_item', $item);
        $this->quantity = 1;

        $this->redirect(route('frontend.checkout'), navigate: true);
    }

    public function addToWishlist($productId)
    {
        $userId = Auth::check() ? Auth::id() : null;

        Favorite::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $this->dispatch('wishlistUpdated');
        $this->dispatch('success', message: 'Product added to favorites!');
    }

    public function refreshCart()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if (! empty($item['variant_id']) && (empty($item['color']) || empty($item['variant_label']))) {
                $variant = ProductVariant::with([
                    'variantAttributeValues.attribute',
                    'variantAttributeValues.attributeValue',
                ])->find($item['variant_id']);

                if ($variant) {
                    $color = null;
                    $size = null;

                    foreach ($variant->variantAttributeValues as $vav) {
                        $attrName = strtolower($vav->attribute?->name ?? '');
                        $attrValue = $vav->attributeValue?->value ?? '';

                        if (in_array($attrName, ['color', 'colour'])) {
                            $color = $attrValue;
                        }
                        if (in_array($attrName, ['size', 'siize'])) {
                            $size = $attrValue;
                        }
                    }

                    $cart[$key]['color'] = $color;
                    $cart[$key]['size'] = $size;
                    $cart[$key]['variant_label'] = collect([$color, $size])->filter()->implode(' / ');
                }
            }
        }

        session()->put('cart', $cart);
        $this->cartItems = $cart;

        $cartCount = array_sum(array_column($this->cartItems, 'qty') ?: [0]);
        $this->dispatch('cart-updated', count: $cartCount, total: $this->cartTotal($this->cartItems));
    }
}