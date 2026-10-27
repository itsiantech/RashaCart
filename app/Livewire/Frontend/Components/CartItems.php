<?php

namespace App\Livewire\Frontend\Components;

use App\Traits\HandlesCartActions;
use Livewire\Component;

class CartItems extends Component
{
    use HandlesCartActions;

    public $cartItems = [];

    public bool $directBuy = false;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount($cartItems = [], $directBuy = false)
    {
        $this->cartItems = $cartItems;
        $this->directBuy = (bool) $directBuy;
    }

    public function loadCart()
    {
        if ($this->directBuy) {
            return;
        }

        $this->cartItems = session('cart', []);
        $this->dispatch('cart-drawer-ready');
    }

    public function render()
    {
        return view('livewire.frontend.components.cart-items');
    }
}
