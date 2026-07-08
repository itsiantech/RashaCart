<?php

namespace App\Livewire\Admin\Products;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use DB;
use Livewire\Component;

class ProductStockManage extends Component
{
    public $productId;

    public $product;

    // Inventory management
    public $showInventoryModal = false;

    public $inventoryType = 'in';

    public $inventoryQuantity = 1;

    public $inventoryNotes = '';

    public $selectedVariantId = null;

    public $inventoryHistory = [];

    // Stock data
    public $productStock = 0;

    public $variantStocks = [];

    public function mount($id)
    {
        $this->productId = $id;
        $this->product = Product::with([
            'variants.inventories',
            'inventories.user',
            'variants.inventories',
        ])->findOrFail($id);

        $this->loadStockData();
        $this->loadInventoryHistory();
    }

    // Load stock data
    public function loadStockData()
    {
        // Calculate product stock
        $this->productStock = $this->calculateProductStock($this->product);

        // Calculate variant stocks
        $this->variantStocks = [];
        foreach ($this->product->variants as $variant) {
            $this->variantStocks[$variant->id] = $this->calculateVariantStock($variant);
        }
    }

    // Calculate product stock from inventory
    private function calculateProductStock($product)
    {
        $inventoryIn = $product->inventories()
            ->where('variant_id', null)
            ->where('type', 'in')
            ->sum('quantity');

        $inventoryOut = $product->inventories()
            ->where('variant_id', null)
            ->where('type', 'out')
            ->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    // Calculate variant stock from inventory
    private function calculateVariantStock($variant)
    {
        $inventoryIn = $variant->inventories()->where('type', 'in')->sum('quantity');
        $inventoryOut = $variant->inventories()->where('type', 'out')->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    // Inventory Management Methods
    public function showAddInventory($variantId = null)
    {
        $this->selectedVariantId = $variantId;
        $this->inventoryType = 'in';
        $this->inventoryQuantity = 1;
        $this->inventoryNotes = '';
        $this->showInventoryModal = true;
    }

    public function addInventory()
    {
        $this->validate([
            'inventoryType' => 'required|in:in,out',
            'inventoryQuantity' => 'required|integer|min:1',
            'inventoryNotes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $userId = auth()->id();

            if ($this->selectedVariantId) {
                // Variant inventory
                $variant = ProductVariant::find($this->selectedVariantId);
                if ($variant) {
                    $currentStock = $this->calculateVariantStock($variant);

                    if ($this->inventoryType === 'out' && $currentStock < $this->inventoryQuantity) {
                        $this->dispatch('error', message: 'Insufficient stock! Available: '.$currentStock);

                        return;
                    }

                    $newStock = $this->inventoryType === 'in'
                        ? $currentStock + $this->inventoryQuantity
                        : $currentStock - $this->inventoryQuantity;

                    // Create inventory record
                    Inventory::create([
                        'product_id' => $this->productId,
                        'variant_id' => $variant->id,
                        'type' => $this->inventoryType,
                        'quantity' => $this->inventoryQuantity,
                        'current_stock' => $newStock,
                        'notes' => $this->inventoryNotes,
                        'user_id' => $userId,
                    ]);

                    // Update variant stock display
                    $this->variantStocks[$variant->id] = $newStock;
                }
            } else {
                // Main product inventory
                $currentStock = $this->calculateProductStock($this->product);

                if ($this->inventoryType === 'out' && $currentStock < $this->inventoryQuantity) {
                    $this->dispatch('error', message: 'Insufficient stock! Available: '.$currentStock);

                    return;
                }

                $newStock = $this->inventoryType === 'in'
                    ? $currentStock + $this->inventoryQuantity
                    : $currentStock - $this->inventoryQuantity;

                // Update product stock display
                $this->productStock = $newStock;

                // Create inventory record
                Inventory::create([
                    'product_id' => $this->productId,
                    'variant_id' => null,
                    'type' => $this->inventoryType,
                    'quantity' => $this->inventoryQuantity,
                    'current_stock' => $newStock,
                    'notes' => $this->inventoryNotes,
                    'user_id' => $userId,
                ]);
            }

            DB::commit();

            $this->loadInventoryHistory();
            $this->showInventoryModal = false;
            $this->dispatch('success', message: 'Stock updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Error updating stock: '.$e->getMessage());
        }
    }

    public function loadInventoryHistory()
    {
        $this->inventoryHistory = Inventory::with(['user', 'variant'])
            ->where('product_id', $this->productId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    public function closeInventoryModal()
    {
        $this->showInventoryModal = false;
        $this->selectedVariantId = null;
        $this->inventoryQuantity = 1;
        $this->inventoryNotes = '';
    }

    // Get stock badge class
    public function getStockBadgeClass($stock)
    {
        if ($stock > 10) {
            return 'bg-success';
        } elseif ($stock > 0) {
            return 'bg-warning';
        } else {
            return 'bg-danger';
        }
    }

    public function render()
    {
        return view('livewire.admin.products.product-stock-manage')
            ->layout('livewire.admin.layouts.app');
    }
}
