<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;

use App\Traits\HandlesCartActions;
class CategoryView extends Component
{
    use WithPagination;
    use HandlesCartActions;

    public $category = null;
    public $selectedPrice = null;

    protected $queryString = [
        'selectedPrice' => ['except' => null],
    ];

    public function mount($slug = null)
    {
        if ($slug) {
            $this->category = Category::where('slug', $slug)->first();
        }
    }

    public function togglePrice($price)
    {
        // যদি একই প্রাইস আবার ক্লিক করা হয় → আনসিলেক্ট
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
    }

    public function getProductsProperty()
    {
        $query = Product::query();

        // Category filter
        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        // Single price filter
        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $query->whereBetween('price', [(int)$min, (int)$max]);
        }

        return $query->paginate(12);
    }

    public function render()
    {
        return view('livewire.frontend.product.category-view', [
            'products' => $this->products,
        ])->layout('livewire.frontend.layouts.app');
    }
}

