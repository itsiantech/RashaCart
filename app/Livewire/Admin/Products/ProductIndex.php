<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\OrderItem;
use App\Helpers\Settings;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
class ProductIndex extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $search = '';    
    
    public function render()
    {
        $products = Product::with('orderitems')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(setting('items_per_page'));

        return view('livewire.admin.products.product-index', compact('products'))
            ->layout('livewire.admin.layouts.app');
    }


    public function deleteProduct($id)
    {       
       
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();
        $this->dispatch('success', message: 'Product deleted!');
    }
}
