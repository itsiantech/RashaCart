<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use App\Traits\HandlesCartActions;

class CartView extends Component
{
    use HandlesCartActions;

    public $cartItems = [];
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;
    public $mayLikeProducts;

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    public function mount() {
        $this->refreshCart();
       $allProducts = Cache::get('products', collect());
        $this->mayLikeProducts = $allProducts->shuffle()->take(8);

    }

    public function refreshCart() {
        $this->cartItems = session('cart', []);
        $this->calculateTotals();
    }

    public function calculateTotals() {
        $this->subtotal = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['qty']);
        $this->discount = 0;
        $this->total = $this->subtotal - $this->discount;
    }

    public function render() {
        return view('livewire.frontend.product.cart-view')->layout('livewire.frontend.layouts.app');
    }
}
