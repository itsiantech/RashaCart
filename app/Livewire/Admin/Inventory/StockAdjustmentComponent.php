<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockAdjustment;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class StockAdjustmentComponent extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    // Form
    public $formProductId = '';
    public $formVariantId = '';
    public $direction = 'increase';
    public $adjustQuantity = 1;
    public $reason = '';

    // History filter
    public $typeFilter = '';

    protected function rules(): array
    {
        return [
            'formProductId' => 'required',
            'adjustQuantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ];
    }

    public function updatedFormProductId()
    {
        $this->formVariantId = '';
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function saveAdjustment()
    {
        $this->validate();

        $product = Product::find($this->formProductId);
        $variant = $this->formVariantId ? ProductVariant::find($this->formVariantId) : null;

        $change = $this->direction === 'decrease'
            ? -abs((int) $this->adjustQuantity)
            : abs((int) $this->adjustQuantity);

        try {
            InventoryService::adjustStock($product, $variant, $change, $this->reason);
            $this->dispatch('success', message: 'Stock adjusted successfully!');
            $this->reset(['formProductId', 'formVariantId', 'direction', 'adjustQuantity', 'reason']);
            $this->direction = 'increase';
            $this->adjustQuantity = 1;
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('error', message: $e->getMessage());
        } catch (\Throwable $e) {
            $this->dispatch('error', message: 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $metrics = [
            'total' => StockAdjustment::count(),
            'increase_qty' => StockAdjustment::where('quantity_change', '>', 0)->sum('quantity_change'),
            'decrease_qty' => abs(StockAdjustment::where('quantity_change', '<', 0)->sum('quantity_change')),
            'net' => StockAdjustment::sum('quantity_change'),
        ];

        $history = StockAdjustment::query()
            ->with(['product:id,name,sku', 'variant:id,sku', 'adjustedBy:id,name'])
            ->when($this->typeFilter === 'increase', fn ($q) => $q->where('quantity_change', '>', 0))
            ->when($this->typeFilter === 'decrease', fn ($q) => $q->where('quantity_change', '<', 0))
            ->orderByDesc('id')
            ->paginate(15);

        $products = Product::query()
            ->select('id', 'name', 'sku', 'has_variant')
            ->orderBy('name')
            ->get();

        $formVariants = $this->formProductId
            ? ProductVariant::where('product_id', $this->formProductId)->orderBy('sku')->get(['id', 'sku'])
            : collect();

        return view('livewire.admin.inventory.stock-adjustment', [
            'history' => $history,
            'metrics' => $metrics,
            'products' => $products,
            'formVariants' => $formVariants,
        ])->layout('livewire.admin.layouts.app');
    }
}
