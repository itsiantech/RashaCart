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

    // Track loaded count per category for "Load More"
    public $categoryLoadedCount = [];
    public $featuredLoadedCount = 0;

    public function mount()
    {
        $this->refreshCart();

        // Load featured categories
        $this->featuredCategories = Cache::get('categories', collect())
            ->where('is_featured', 1)
            ->values();

        $this->loadProducts();
    }

    public function loadProducts()
    {
        $allProducts = Cache::get('products', collect());

        // Featured products
        $this->featuredProducts = $allProducts
            ->where('is_featured', 1)
            ->take($this->perPage)
            ->values();
        $this->featuredLoadedCount = $this->featuredProducts->count();

        // Category products
        foreach ($this->featuredCategories as $category) {
            $products = $allProducts
                ->where('category_id', $category->id)
                ->take($this->perPage)
                ->values();

            $this->categoryProducts[$category->id] = $products;
            $this->categoryLoadedCount[$category->id] = $products->count();
        }
    }

    public function loadMoreFeatured()
    {
        $allProducts = Cache::get('products', collect());

        $more = $allProducts->where('is_featured', 1)
            ->slice($this->featuredLoadedCount, $this->perPage)
            ->values();

        $this->featuredProducts = $this->featuredProducts->concat($more);
        $this->featuredLoadedCount = $this->featuredProducts->count();
    }

    public function loadMore($categoryId)
    {
        $allProducts = Cache::get('products', collect());

        $currentProducts = $this->categoryProducts[$categoryId] ?? collect();
        $currentCount = $this->categoryLoadedCount[$categoryId] ?? 0;

        $moreProducts = $allProducts
            ->where('category_id', $categoryId)
            ->slice($currentCount, $this->perPage)
            ->values();

        $this->categoryProducts[$categoryId] = $currentProducts->concat($moreProducts);
        $this->categoryLoadedCount[$categoryId] = $this->categoryProducts[$categoryId]->count();
    }

    public function render()
    {
        return view('livewire.frontend.welcome')
            ->layout('livewire.frontend.layouts.app');
    }
}
