<?php

namespace App\Livewire\Admin\Products;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $search = '';

    public function render()
    {
        $query = Product::with([
            'orderitems',
            'inventories',
            'variants.inventories',
        ]);

        $user = Auth::user();

        if (! $user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('short_description', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            });
        }

        $products = $query
            ->latest()
            ->paginate(setting('items_per_page'));

        return view('livewire.admin.products.product-index', compact('products'))
            ->layout('livewire.admin.layouts.app');
    }

    private function calculateProductStock($product)
    {
        $inventoryIn = $product->inventories
            ->where('variant_id', null)
            ->where('type', 'in')
            ->sum('quantity');

        $inventoryOut = $product->inventories
            ->where('variant_id', null)
            ->where('type', 'out')
            ->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    private function calculateVariantStock($variant)
    {
        $inventoryIn = $variant->inventories->where('type', 'in')->sum('quantity');
        $inventoryOut = $variant->inventories->where('type', 'out')->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    public function getTotalStock($product)
    {
        $mainStock = $this->calculateProductStock($product);

        $variantStock = 0;
        foreach ($product->variants as $variant) {
            $variantStock += $this->calculateVariantStock($variant);
        }

        return $mainStock + $variantStock;
    }

    public function getStockBadgeClass($totalStock)
    {
        if ($totalStock > 10) {
            return 'bg-success';
        } elseif ($totalStock > 0) {
            return 'bg-warning';
        } else {
            return 'bg-danger';
        }
    }

    public function deleteProduct($id)
    {
        $user = Auth::user();
        $product = Product::where('id', $id)
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        $variantIds = $product->variants()->pluck('id');
        if ($variantIds->isNotEmpty()) {
            OrderItem::whereIn('product_variant_id', $variantIds)
                ->update(['product_variant_id' => null]);
        }

        // Delete related data
        $product->variants()->delete();
        $product->inventories()->delete();

        if ($product->thumbnail) {
            Storage::delete('public/'.$product->thumbnail);
        }

        if ($product->gallery_image) {
            foreach ($product->gallery_image as $image) {
                Storage::delete('public/'.$image);
            }
        }

        $product->delete();

        $this->dispatch('success', message: 'Product deleted successfully!');
    }
}
