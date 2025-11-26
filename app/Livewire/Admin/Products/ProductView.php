<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Component;

class ProductView extends Component
{
    public Product $product;
    
    public function mount($id)
    {
        $this->product = Product::with([
                'category',                 
                'user', 
                'variants',                 
                'coupons',
                'reviews'
            ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.products.product-view')
            ->layout('livewire.admin.layouts.app')
            ->title('View Product - ' . $this->product->name);
    }
}