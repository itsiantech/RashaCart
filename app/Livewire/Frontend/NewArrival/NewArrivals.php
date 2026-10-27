<?php

namespace App\Livewire\Frontend\NewArrival;

use App\Models\Category;
use App\Models\NewArrival;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Livewire\Component;
use Livewire\WithPagination;

class NewArrivals extends Component
{
    use HandlesCartActions, WithPagination;

    public $perPage = 40;

    public $sortBy = 'latest';

    public $selectedCategory = null;

    public $selectedPrice = null;

    public $sidebarCategories = [];

    public function mount()
    {
        $this->refreshCart();

        $this->sidebarCategories = Category::with('allChildren')
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function togglePrice($price)
    {
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
        $this->resetPage();
    }

    public function setCategory($id)
    {
        $this->selectedCategory = ($this->selectedCategory == $id) ? null : $id;
        $this->resetPage();
    }

    public function render()
    {
        $with = ['product.category', 'product.activeFlashSaleItem'];
        if (Product::hasCategoryPivot()) {
            $with[] = 'product.categories';
        }

        $query = NewArrival::with($with)->active();

        // Get all, then filter/sort on collection
        $all = $query->ordered()->get();

        // Filter by category
        if ($this->selectedCategory) {
            $category = Category::find($this->selectedCategory);
            $categoryIds = $category ? $category->selfAndDescendantIds() : [(int) $this->selectedCategory];
            $all = $all->filter(fn ($a) => $a->product && array_intersect($a->product->categoryIds(), $categoryIds));
        }

        // Filter by price range
        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $all = $all->filter(fn ($a) => $a->product &&
                $a->product->price >= $min &&
                $a->product->price <= $max
            );
        }

        // Sort
        $all = match ($this->sortBy) {
            'price_low' => $all->sortBy(fn ($a) => $a->product?->price ?? 0),
            'price_high' => $all->sortByDesc(fn ($a) => $a->product?->price ?? 0),
            'name_az' => $all->sortBy(fn ($a) => $a->product?->name ?? ''),
            default => $all->values(),
        };

        $total = $all->count();
        $page = $this->getPage();
        $items = $all->forPage($page, $this->perPage)->values();

        return view('livewire.frontend.new-arrival.new-arrivals', [
            'newArrivals' => $items,
            'total' => $total,
            'hasMore' => ($page * $this->perPage) < $total,
            'categories' => $this->sidebarCategories,
        ])->layout('livewire.frontend.layouts.app');
    }
}
