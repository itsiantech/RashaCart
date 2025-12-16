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
    $product = Product::findOrFail($productId);
    $cart = session()->get('cart', []);

    $key = $productId;
    $qty = $this->quantity > 0 ? $this->quantity : 1;

    // Default price
    $price = $product->sale_price ?? $product->price;
    $variantImage = null;

    // Handle attribute system (variants)
    if (setting('attribute_system', true)) {
        $variantId = $this->selectedVariant ?? null;

        if ($variantId) {
            $variant = ProductVariant::find($variantId);

            if ($variant) {
                $price = $variant->varient_price;
                $variantImage = !empty($variant->image)
                    ? asset('storage/' . $variant->image)
                    : null;
            }

            // Unique key for variant
            $key .= '-v' . $variantId;
        }
    }

    // Add or update cart item
    if (isset($cart[$key])) {
        $cart[$key]['qty'] += $qty;
    } else {
        $cart[$key] = [
            'id'         => $product->id,
            'variant_id' => $this->selectedVariant ?? null,
            'title'      => $product->name,
            'price'      => $price,
            'img'        => $variantImage 
                             ?? ($product->thumbnail 
                                 ? asset('storage/' . $product->thumbnail) 
                                 : 'https://placehold.co/500x500'),
            'qty'        => $qty,
        ];
    }

    session()->put('cart', $cart);

    // Reset quantity
    $this->quantity = 1;

    // Update UI
    $cartCount = array_sum(array_column($cart, 'qty'));

    $this->dispatch('cart-updated', count: $cartCount);
    $this->dispatch('cartUpdated');
    $this->dispatch('success', message: 'Product added to cart!');
}


    public function buyNow($productId)
    {
        $this->addToCart($productId);
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
        $this->cartItems = session()->get('cart', []);
        $cartCount = array_sum(array_column($this->cartItems, 'qty') ?: [0]);
        $this->dispatch('cart-updated', count: $cartCount);
    }

}
