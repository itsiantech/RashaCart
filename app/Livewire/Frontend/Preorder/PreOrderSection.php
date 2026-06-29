<?php

namespace App\Livewire\Frontend\Preorder;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class PreOrderSection extends Component
{
    use WithPagination;

    public string $sortBy = 'preorder_end_date';

    public function addToCart(int $productId): void
    {
        $product = Product::activePreorder()->findOrFail($productId);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name'        => $product->name,
                'price'       => $product->price,
                'quantity'    => 1,
                'image'       => $product->image,
                'is_preorder' => true,
            ];
        }

        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Pre-order added to cart!', type: 'success');
    }

    public function render()
    {
        $products = Product::activePreorder()
            ->latest($this->sortBy)
            ->paginate(12);

        return view('livewire.frontend.preorder.pre-order-section', compact('products'))->layout('livewire.frontend.layouts.app');
    }
}
