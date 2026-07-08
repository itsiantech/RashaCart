<?php

namespace App\Livewire\Admin\Products;

use App\Models\Inventory;
use App\Models\Product;
use Livewire\Component;

class ProductView extends Component
{
    public Product $product;

    public $productStock;

    public $variantStocks = [];

    public function mount($id)
    {
        $this->product = Product::with([
            'category',
            'user',
            'variants.variantAttributeValues.attribute',
            'variants.variantAttributeValues.attributeValue',
            'variants.inventories',
            'inventories',
            'coupons',
            'reviews',
        ])->findOrFail($id);

        // Calculate product stock from inventory
        $this->productStock = $this->calculateProductStock();

        // Calculate variant stocks from inventory
        foreach ($this->product->variants as $variant) {
            $this->variantStocks[$variant->id] = $this->calculateVariantStock($variant);
        }
    }

    private function calculateProductStock()
    {
        $inventoryIn = $this->product->inventories()
            ->where('variant_id', null)
            ->where('type', 'in')
            ->sum('quantity');

        $inventoryOut = $this->product->inventories()
            ->where('variant_id', null)
            ->where('type', 'out')
            ->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    // Calculate variant stock from inventory
    private function calculateVariantStock($variant)
    {
        $inventoryIn = $variant->inventories()
            ->where('type', 'in')
            ->sum('quantity');

        $inventoryOut = $variant->inventories()
            ->where('type', 'out')
            ->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    public function render()
    {
        return view('livewire.admin.products.product-view')
            ->layout('livewire.admin.layouts.app')
            ->title('View Product - '.$this->product->name);
    }
}
