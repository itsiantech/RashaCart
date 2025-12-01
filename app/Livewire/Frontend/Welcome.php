<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use App\Traits\HandlesCartActions;
class Welcome extends Component
{
    use HandlesCartActions;
    public $featuredProducts = [];
    public $categoryProducts = [];
    public $featuredCategories = [];
    public $perPage = 8;

    public function mount()
    {
        $this->featuredCategories = Cache::get('categories', collect())
            ->where('is_featured', 1)
            ->values();

        $this->loadProducts();
    }

    public function loadProducts()
    {
        $allProducts = Cache::get('products', collect());

        $this->featuredProducts = $allProducts
            ->where('is_featured', 1)
            ->take($this->perPage);

        foreach ($this->featuredCategories as $category) {
            $this->categoryProducts[$category->id] = $allProducts
                ->filter(fn($product) => $product['category_id'] == $category->id)
                ->take($this->perPage)
                ->values();
        }
    }

    public function loadMore($categoryId)
    {
        $allProducts = Cache::get('products', collect());
        
        $currentProducts = $this->categoryProducts[$categoryId] ?? collect();
        $currentCount = count($currentProducts);

        $moreProducts = $allProducts
            ->where('category_id', $categoryId)
            ->slice($currentCount, $this->perPage)
            ->values();

        $this->categoryProducts[$categoryId] = $currentProducts->concat($moreProducts);
    }

    public function render()
    {
        return view('livewire.frontend.welcome')
            ->layout('livewire.frontend.layouts.app');
    }
}