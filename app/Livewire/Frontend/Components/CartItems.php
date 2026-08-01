<?php

namespace App\Livewire\Frontend\Components;

use App\Traits\HandlesCartActions;
use Livewire\Component;

class CartItems extends Component
{
    use HandlesCartActions;

    public $cartItems = [];

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount($cartItems = [])
    {
        $this->cartItems = $cartItems;
    }

    public function loadCart()
    {
        $this->cartItems = session('cart', []);
    }

    public function render()
    {
        return view('livewire.frontend.components.cart-items');
    }
}
