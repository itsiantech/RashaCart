<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

trait HandlesCartActions
{
    // Quantity এখন পাবলিক property, যাতে Livewire থেকে bind করা যায়
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

        // ✅ সঠিক quantity নাও
        $qty = $this->quantity > 0 ? $this->quantity : 1;

        // ✅ price সেট
        $price = $product->sale_price ?? $product->price;
        $variantImage = null;

        if (setting('attribute_system', true)) {
            $variantId = $this->selectedVariant ?? null;
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $price = $variant->price_diff; // direct price_diff (final price)
                    if (!empty($variant->image)) {
                        $variantImage = asset('storage/' . $variant->image);
                    }
                }
                $key .= '-v' . $variantId;
            }
        } elseif ($this->selectedColour || $this->selectedSize) {
            $key .= '-c' . ($this->selectedColour ?? '') . '-s' . ($this->selectedSize ?? '');
        }

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'id' => $product->id,
                'variant_id' => $this->selectedVariant ?? null,
                'title' => $product->name,
                'price' => $price,
                'img' => $variantImage ?? ($product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/500x500'),
                'qty' => $qty,
                'color' => $this->selectedColour ?? null,
                'size' => $this->selectedSize ?? null,
            ];
        }

        session()->put('cart', $cart);

        // ✅ quantity reset না করে রাখতে চাইলে নিচের লাইন comment করে রাখো
        $this->quantity = 1;

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
}
