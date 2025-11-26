<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
// use App\Models\Product;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\Cache;
class SingleView extends Component
{
    use HandlesCartActions;

    public $relatedProducts;
    public $product;
    public $selectedVariant = null;
    public $selectedColour = null;
    public $selectedSize = null;

    // সেটিংস কন্ট্রোল করবে কোন সিস্টেম অন আছে
    public bool $enableVariant = true;
    public bool $enableColor = true;
    public bool $enableSize = true;

   
        public function mount($slug)
    {
        $allProducts = Cache::get('products', collect());

        $this->product = $allProducts->where('slug', $slug)->firstOrFail();

        $this->relatedProducts = $allProducts->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->take(5);
    }    
    

    public function selectVariant($variantId)
    {
        $this->selectedVariant = $variantId;
        // $this->dispatch('success', message: 'Variant selected!');
    }

    public function selectColor($color)
    {
        $this->selectedColour = $color;
    }

    public function selectSize($size)
    {
        $this->selectedSize = $size;
    }

    public function render()
    {
        return view('livewire.frontend.product.single-view')
            ->layout('livewire.frontend.layouts.app');
    }
}
