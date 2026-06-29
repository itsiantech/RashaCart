<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

trait HandlesCartActions
{

    public $quantity = 1;

    /**
     * Get all unique attribute types that exist for a product's variants
     */
    private function getProductAttributeTypes($product)
    {
        $attributeTypes = collect();
        foreach ($product->variants as $variant) {
            foreach ($variant->variantAttributeValues as $vav) {
                $attrName = strtolower($vav->attribute->name ?? '');
                if ($attrName !== '' && !$attributeTypes->contains($attrName)) {
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
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function addToCart($productId)
    {
        $product = Product::with([
            'variants.variantAttributeValues.attribute',
            'variants.variantAttributeValues.attributeValue',
            'variants.inventories',
        ])->findOrFail($productId);

        $cart  = session()->get('cart', []);
        $key   = $productId;
        $qty   = $this->quantity > 0 ? $this->quantity : 1;
        $price = $product->sale_price ?? $product->price;

        $variantImage = null;
        $variantId    = null;
        $color        = null;
        $size         = null;
        $variantLabel = '';

        if (setting('attribute_system', true) && $product->variants->count() > 0) {

            $attributeTypes = $this->getProductAttributeTypes($product);
            $hasColor = $attributeTypes->contains(fn($a) => in_array($a, ['color', 'colour']));
            $hasSize  = $attributeTypes->contains(fn($a) => in_array($a, ['size', 'siize']));

            $selectedVariantId = $this->selectedVariant ?? null;

            if ($selectedVariantId) {
                $variantId = $selectedVariantId;
            } elseif ($attributeTypes->count() === 1) {
                $resolved = null;

                if ($hasColor && !empty($this->selectedColor)) {
                    $resolved = $this->findVariantByAttributes($product, $this->selectedColor, null);
                } elseif ($hasSize && !empty($this->selectedSize)) {
                    $resolved = $this->findVariantByAttributes($product, null, $this->selectedSize);
                }

                $variantId = ($resolved ?? $product->variants->first())?->id;
            } else {
                $colorSelected = !$hasColor || !empty($this->selectedColor);
                $sizeSelected  = !$hasSize  || !empty($this->selectedSize);

                if (!$colorSelected || !$sizeSelected) {
                    $missing = collect();
                    if (!$colorSelected) $missing->push('color');
                    if (!$sizeSelected)  $missing->push('size');

                    $this->dispatch('error', message: 'Please select ' . $missing->implode(' and ') . ' before adding to cart.');
                    $this->dispatch('show-variant-warning');
                    return false;
                }

                $resolved  = $this->findVariantByAttributes($product, $this->selectedColor ?? null, $this->selectedSize ?? null);
                $variantId = $resolved?->id;

                if (!$variantId) {
                    $this->dispatch('error', message: 'The selected combination is not available.');
                    return false;
                }
            }

            if ($variantId) {
                $variant = ProductVariant::with([
                    'variantAttributeValues.attribute',
                    'variantAttributeValues.attributeValue',
                    'inventories',
                ])->find($variantId);

                if (!$variant) {
                    $this->dispatch('error', message: 'Selected variant not found.');
                    return false;
                }

                // Stock check
                $currentStock = $variant->inventories()->orderByDesc('id')->value('current_stock') ?? 0;
                if ($currentStock <= 0) {
                    $this->dispatch('error', message: 'Sorry, this variant is out of stock.');
                    return false;
                }

                $cartKey          = $productId . '-v' . $variantId;
                $currentQtyInCart = $cart[$cartKey]['qty'] ?? 0;

                if (($currentQtyInCart + $qty) > $currentStock) {
                    $this->dispatch('error', message: "Not enough stock available. Only {$currentStock} left.");
                    return false;
                }

                $price        = $variant->variant_price ?? $price;
                $variantImage = !empty($variant->image) ? asset('storage/' . $variant->image) : null;

                foreach ($variant->variantAttributeValues as $vav) {
                    if (!$vav->attribute || !$vav->attributeValue) continue;

                    $attrName  = strtolower($vav->attribute->name ?? '');
                    $attrValue = $vav->attributeValue->value ?? '';

                    if (in_array($attrName, ['color', 'colour'])) $color = $attrValue;
                    if (in_array($attrName, ['size', 'siize']))   $size  = $attrValue;
                }

                if (empty($color) && !empty($this->selectedColor)) $color = $this->selectedColor;
                if (empty($size)  && !empty($this->selectedSize))  $size  = $this->selectedSize;


                if (empty($variantImage) && !empty($color)) {
                    $colorVariant = $product->variants->first(function ($v) use ($color) {
                        foreach ($v->variantAttributeValues as $vav) {
                            if (!$vav->attribute || !$vav->attributeValue) continue;
                            $attrName = strtolower($vav->attribute->name ?? '');
                            if (
                                in_array($attrName, ['color', 'colour'])
                                && $vav->attributeValue->value === $color
                                && !empty($v->image)
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

        if (isset($cart[$finalKey])) {
            $cart[$finalKey]['qty'] += $qty;
        } else {
            $cart[$finalKey] = [
                'id'            => $product->id,
                'product_id'    => $product->id,
                'category_id'   => $product->category_id,
                'variant_id'    => $variantId,
                'variant_label' => $variantLabel ?: null,
                'color'         => $color,
                'size'          => $size,
                'title'         => $product->name,
                'price'         => $price,
                'img'           => $variantImage
                    ?? ($product->thumbnail
                        ? asset('storage/' . $product->thumbnail)
                        : 'https://placehold.co/500x500'),
                'qty'           => $qty,
            ];
        }

        session()->put('cart', $cart);
        $this->quantity = 1;

        $cartCount = array_sum(array_column($cart, 'qty'));
        $this->dispatch('cart-updated', count: $cartCount);
        $this->dispatch('cartUpdated');
        $this->dispatch('success', message: 'Product added to cart!');

        return true;
    }


    private function findVariantByAttributes($product, ?string $colorValue, ?string $sizeValue): ?ProductVariant
    {
        foreach ($product->variants as $variant) {
            $colorMatch = ($colorValue === null);
            $sizeMatch  = ($sizeValue  === null);

            foreach ($variant->variantAttributeValues as $vav) {
                if (!$vav->attribute || !$vav->attributeValue) continue;

                $attrName = strtolower($vav->attribute->name ?? '');
                $val      = $vav->attributeValue->value ?? '';

                if (!$colorMatch && in_array($attrName, ['color', 'colour'])) {
                    $colorMatch = ($val === $colorValue);
                }
                if (!$sizeMatch && in_array($attrName, ['size', 'siize'])) {
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
        $result = $this->addToCart($productId);
        if ($result === false) return;
        $this->redirect(route('frontend.checkout'), navigate: true);
    }

    public function addToWishlist($productId)
    {
        $userId = Auth::check() ? Auth::id() : null;

        Favorite::firstOrCreate([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);

        $this->dispatch('wishlistUpdated');
        $this->dispatch('success', message: 'Product added to favorites!');
    }

    public function refreshCart()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if (!empty($item['variant_id']) && (empty($item['color']) || empty($item['variant_label']))) {
                $variant = ProductVariant::with([
                    'variantAttributeValues.attribute',
                    'variantAttributeValues.attributeValue',
                ])->find($item['variant_id']);

                if ($variant) {
                    $color = null;
                    $size  = null;

                    foreach ($variant->variantAttributeValues as $vav) {
                        $attrName  = strtolower($vav->attribute?->name ?? '');
                        $attrValue = $vav->attributeValue?->value ?? '';

                        if (in_array($attrName, ['color', 'colour'])) $color = $attrValue;
                        if (in_array($attrName, ['size', 'siize']))   $size  = $attrValue;
                    }

                    $cart[$key]['color']         = $color;
                    $cart[$key]['size']          = $size;
                    $cart[$key]['variant_label'] = collect([$color, $size])->filter()->implode(' / ');
                }
            }
        }

        session()->put('cart', $cart);
        $this->cartItems = $cart;

        $cartCount = array_sum(array_column($this->cartItems, 'qty') ?: [0]);
        $this->dispatch('cart-updated', count: $cartCount);
    }
}
