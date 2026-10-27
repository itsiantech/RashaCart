<?php

namespace App\Livewire\Frontend\Product;

use App\Models\Category;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryView extends Component
{
    use HandlesCartActions;
    use WithPagination;

    public $category = null;

    public $selectedPrice = null;

    public $allCategories = []; // For sidebar filters

    protected $queryString = [
        'selectedPrice' => ['except' => null],
    ];

    public function mount($slug = null)
    {

        if ($slug) {
            $this->category = Category::where('slug', $slug)->firstOrFail();
        }

        // Load all categories for sidebar filters (not paginated).
        // Eager-load the tree so submenu/sub-submenu render without N+1.
        $this->allCategories = Category::with('allChildren')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function togglePrice($price)
    {
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
    }

    public function getCategoriesProperty()
    {
        return Category::with('allChildren')->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(16);
    }

    public function getProductsProperty()
    {
        $query = Product::query();

        if ($this->category) {
            $query->inCategories($this->category->selfAndDescendantIds());
        }

        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $query->whereBetween('price', [(int) $min, (int) $max]);
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.frontend.product.category-view', [
            'category' => $this->category,
            'categories' => $this->categories,
            'allCategories' => $this->allCategories,
            'products' => $this->products,
        ])->layout('livewire.frontend.layouts.store');
    }
}
