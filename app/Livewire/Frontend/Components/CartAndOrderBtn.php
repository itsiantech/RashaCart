<?php

namespace App\Livewire\Frontend\Components;

use Livewire\Component;
use App\Models\Product;

class CartAndOrderBtn extends Component
{
    public $product;

    public function mount($product)
    {
        $this->product = $product;
    }

    public function addToCart()
    {
        $id = $this->product->id;
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                'id' => $this->product->id,
                'title' => $this->product->name,
                'price' => $this->product->sale_price ?? $this->product->price,
                'img' => $this->product->thumbnail ? asset('storage/' . $this->product->thumbnail) : 'https://placehold.co/500x500',
                'qty' => 1,
            ];
        }

        session()->put('cart', $cart);        
        $this->dispatch('cartUpdated');        
        $this->dispatch('success', message: 'Product added to cart!');        
    }

    public function buyNow()
    {
        $this->addToCart();        
        $this->redirect(route('frontend.checkout'), navigate: true);
    }

    public function render()
    {
        return view('livewire.frontend.components.cart-and-order-btn');
    }
}
