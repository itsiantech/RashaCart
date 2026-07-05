<?php

namespace App\Livewire\Admin\NewArrival;

use App\Models\NewArrival;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class NewArrivalCreate extends Component
{
    use WithPagination;

    public $search        = '';
    public $searchResults = [];
    public $productSearch = '';

    public function updatedProductSearch()
    {
        if (strlen($this->productSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        // Exclude already-added products
        $existingIds = NewArrival::pluck('product_id')->toArray();

        $this->searchResults = Product::where('status', true)
            ->whereNotIn('id', $existingIds)
            ->where('name', 'like', '%' . $this->productSearch . '%')
            ->limit(8)
            ->get(['id', 'name', 'thumbnail', 'price'])
            ->toArray();
    }

    public function addProduct($productId)
    {
        // Prevent duplicates
        if (NewArrival::where('product_id', $productId)->exists()) {
            session()->flash('error', 'Product already in New Arrivals.');
            return;
        }

        $maxOrder = NewArrival::max('sort_order') ?? 0;

        NewArrival::create([
            'product_id' => $productId,
            'sort_order' => $maxOrder + 1,
            'is_active'  => true,
        ]);

        $this->productSearch = '';
        $this->searchResults = [];
        session()->flash('success', 'Product added to New Arrivals.');
    }

    public function toggleActive($id)
    {
        $item = NewArrival::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
    }

    public function remove($id)
    {
        NewArrival::findOrFail($id)->delete();
        session()->flash('success', 'Product removed from New Arrivals.');
    }

    public function getItemsProperty()
    {
        return NewArrival::with('product')
            ->when(
                $this->search,
                fn($q) =>
                $q->whereHas(
                    'product',
                    fn($q2) =>
                    $q2->where('name', 'like', '%' . $this->search . '%')
                )
            )
            ->ordered()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.new-arrival.new-arrival-create', ['items' => $this->items,])
            ->layout('livewire.admin.layouts.app');
    }
}
