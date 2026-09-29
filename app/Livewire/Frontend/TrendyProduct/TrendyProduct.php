<?php

namespace App\Livewire\Frontend\TrendyProduct;

use App\Models\Category;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Livewire\Component;
use Livewire\WithPagination;

class TrendyProduct extends Component
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

    public function nextPage()
    {
        $this->nextPage();
    }

    public function render()
    {
        $query = Product::where('status', 1)
            ->where('is_featured', 1)
            ->with(['category', 'activeFlashSaleItem']);
            
        $all = $query->get();

        // Filter by category
        if ($this->selectedCategory) {
            $all = $all->filter(
                fn($p) => $p->category_id == $this->selectedCategory
            );
        }

        // Filter by price range
        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $all = $all->filter(
                fn($p) => $p->sale_price >= $min &&
                    $p->sale_price <= $max
            );
        }

        // Sort
        $all = match ($this->sortBy) {
            'price_low' => $all->sortBy(fn($p) => $p->sale_price ?? 0),
            'price_high' => $all->sortByDesc(fn($p) => $p->sale_price ?? 0),
            'name_az' => $all->sortBy(fn($p) => $p->name ?? ''),
            default => $all->sortByDesc(fn($p) => $p->created_at),
        };

        $total = $all->count();
        $page = $this->getPage();
        $items = $all->forPage($page, $this->perPage)->values();

        return view('livewire.frontend.trendy-product.trendy-product', [
            'featuredProducts' => $items,
            'total' => $total,
            'hasMore' => ($page * $this->perPage) < $total,
            'categories' => $this->sidebarCategories,
        ])->layout('livewire.frontend.layouts.app');
    }
}
