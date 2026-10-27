<?php

namespace App\Livewire\Frontend\FlashSale;

use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Traits\HandlesCartActions;
use Livewire\Component;
use Livewire\WithPagination;

class FlashSaleProducts extends Component
{
    public $flashSale = null;

    public $selectedPrice = null;

    public $selectedCategory = null;

    public $timeLeft = [];

    use HandlesCartActions;
    use WithPagination;

    protected $queryString = [
        'selectedPrice' => ['except' => null],
        'selectedCategory' => ['except' => null],
    ];

    public function mount()
    {
        // Load the currently active flash sale
        $this->flashSale = FlashSale::active()->live()->latest()->first();
    }

    public function togglePrice($price)
    {
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
        $this->resetPage();
    }

    public function toggleCategory($categoryId)
    {
        $this->selectedCategory = ($this->selectedCategory === $categoryId) ? null : $categoryId;
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        if (! $this->flashSale) {
            return collect()->paginate(40);
        }

        $query = FlashSaleItem::with('product.category')
            ->where('flash_sale_id', $this->flashSale->id)
            ->whereHas('product', function ($q) {
                $q->where('status', true);

                if ($this->selectedPrice) {
                    [$min, $max] = explode('-', $this->selectedPrice);
                    $q->whereBetween('price', [(int) $min, (int) $max]);
                }

                if ($this->selectedCategory) {
                    $category = Category::find($this->selectedCategory);
                    $q->inCategories($category ? $category->selfAndDescendantIds() : [(int) $this->selectedCategory]);
                }
            });

        return $query->paginate(20);
    }

    public function getCategoriesProperty()
    {
        return Category::where('status', 1)
            ->whereHas('products', fn ($q) => $q->where('status', true))
            ->get();
    }

    public function render()
    {
        return view('livewire.frontend.flash-sale.flash-sale-products', [
            'items' => $this->products,
            'categories' => $this->categories,
        ])->layout('livewire.frontend.layouts.app')
            ->layoutData([
                'meta' => [
                    'title' => 'Flash Sale - Best Deals',
                    'description' => 'Grab the best flash sale deals before time runs out!',
                    'keywords' => 'flash sale, deals, discount, offers',
                    'tags' => 'flash sale',
                ],
            ]);
    }
}
