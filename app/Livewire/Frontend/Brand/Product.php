<?php

namespace App\Livewire\Frontend\Brands;

use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public Brand $brand;

    public $perPage = 12;

    public $sortBy = 'newest';

    public function mount($brand)
    {
        $this->brand = Brand::where('slug', $brand)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        $products = Product::query()
            ->where('brand_id', $this->brand->id)
            ->where('status', 'active')
            ->when($this->sortBy, function ($query) {
                match ($this->sortBy) {
                    'price_low' => $query->orderBy('price'),
                    'price_high' => $query->orderByDesc('price'),
                    'popular' => $query->orderByDesc('views'),
                    'name_asc' => $query->orderBy('name'),
                    'name_desc' => $query->orderByDesc('name'),
                    default => $query->orderByDesc('created_at'),
                };
            })
            ->paginate($this->perPage);

        return view('livewire.frontend.brand.index', [
            'products' => $products,
        ])->layout('livewire.frontend.layouts.app', [
            'title' => $this->brand->meta_title ?? $this->brand->name,
            'description' => $this->brand->meta_description,
        ]);
    }
}
