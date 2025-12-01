<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Helpers\Settings;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Storage;

class ProductIndex extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $search = '';    
    
    public function render()
    {
        $products = Product::with([
                'orderitems', 
                'inventories', 
                'variants.inventories'
            ])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(setting('items_per_page'));

        return view('livewire.admin.products.product-index', compact('products'))
            ->layout('livewire.admin.layouts.app');
    }

    // Calculate product stock from inventory
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

    // Calculate variant stock from inventory
    private function calculateVariantStock($variant)
    {
        $inventoryIn = $variant->inventories->where('type', 'in')->sum('quantity');
        $inventoryOut = $variant->inventories->where('type', 'out')->sum('quantity');
        return $inventoryIn - $inventoryOut;
    }

    // Calculate total stock including variants
    public function getTotalStock($product)
    {
        $mainStock = $this->calculateProductStock($product);
        
        $variantStock = 0;
        foreach ($product->variants as $variant) {
            $variantStock += $this->calculateVariantStock($variant);
        }
        
        return $mainStock + $variantStock;
    }

    // Get stock badge class based on quantity
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
        $product = Product::findOrFail($id);
        
        // Delete related data
        $product->variants()->delete();
        $product->inventories()->delete();
        
        // Delete images from storage
        if ($product->thumbnail) {
            Storage::delete('public/' . $product->thumbnail);
        }
        
        if ($product->gallery_image) {
            foreach ($product->gallery_image as $image) {
                Storage::delete('public/' . $image);
            }
        }
        
        $product->delete();
        $this->dispatch('success', message: 'Product deleted successfully!');
    }
}