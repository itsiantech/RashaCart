<?php

namespace App\Livewire\Frontend\Product;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserProductsView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $vendor;

    public $perPage = 16;

    public function mount($id)
    {
        $this->vendor = User::findOrFail($id);
    }

    public function render()
    {
        $products = $this->vendor->products()
            ->where('status', 1)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.frontend.product.user-products-view', [
            'products' => $products,
        ]);
    }
}
