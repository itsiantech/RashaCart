<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\Cache;

class Welcome extends Component
{
    use HandlesCartActions;

    public $products = [];
    public $featuredProducts = [];
    public $bestSaleProducts = [];

    public function loadProducts()
    {
      
        $allProducts = Cache::get('products', collect());

        // filter collection
        $this->products = $allProducts;
        $this->featuredProducts = $allProducts->where('is_featured', 1);
        $this->bestSaleProducts = $allProducts;
    }

    public function render()
    {
        if (empty($this->products)) {
            $this->loadProducts();
        }

        return view('livewire.frontend.welcome')
            ->layout('livewire.frontend.layouts.app');
    }
}
