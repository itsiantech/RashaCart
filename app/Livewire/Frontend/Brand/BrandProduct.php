<?php

namespace App\Livewire\Frontend\Brand;

use App\Models\Brand;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Livewire\Component;
use Livewire\WithPagination;

class BrandProduct extends Component
{
    use HandlesCartActions;
    use WithPagination;

    public $brand = null;

    public $selectedPrice = null;

    protected $queryString = [
        'selectedPrice' => ['except' => null],
    ];

    public function mount($brand = null)
    {
        if ($brand) {
            $this->brand = Brand::where('slug', $brand)->firstOrFail();
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

        // Brand filter
        if ($this->brand) {
            $query->where('brand_id', $this->brand->id);
        }

        // Single price filter
        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $query->whereBetween('price', [(int) $min, (int) $max]);
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.frontend.brand.brand-product', [
            'products' => $this->products,
        ])
            ->layout('livewire.frontend.layouts.app');
    }
}
