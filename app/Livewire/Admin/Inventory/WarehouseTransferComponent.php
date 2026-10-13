<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WarehouseTransfer;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseTransferComponent extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    // Form
    public $formProductId = '';
    public $formVariantId = '';
    public $formQuantity = 1;
    public $fromLocation = '';
    public $toLocation = '';
    public $note = '';

    protected function rules(): array
    {
        return [
            'formProductId' => 'required',
            'formQuantity' => 'required|integer|min:1',
            'fromLocation' => 'required|string|max:100',
            'toLocation' => 'required|string|max:100|different:fromLocation',
        ];
    }

    public function updatedFormProductId()
    {
        $this->formVariantId = '';
    }

    public function saveTransfer()
    {
        $this->validate();

        $product = Product::find($this->formProductId);
        $variant = $this->formVariantId ? ProductVariant::find($this->formVariantId) : null;

        try {
            InventoryService::transfer($product, $variant, (int) $this->formQuantity, trim($this->fromLocation), trim($this->toLocation), $this->note ?: null);
            $this->dispatch('success', message: 'Transfer logged successfully!');
            $this->reset(['formProductId', 'formVariantId', 'formQuantity', 'fromLocation', 'toLocation', 'note']);
            $this->formQuantity = 1;
        } catch (\Throwable $e) {
            $this->dispatch('error', message: 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $metrics = [
            'total' => WarehouseTransfer::count(),
            'total_qty' => WarehouseTransfer::sum('quantity'),
            'locations' => WarehouseTransfer::select('from_location')
                ->union(WarehouseTransfer::select('to_location'))
                ->distinct()->count('from_location'),
        ];

        $transfers = WarehouseTransfer::query()
            ->with(['product:id,name,sku', 'variant:id,sku', 'transferredBy:id,name'])
            ->orderByDesc('id')
            ->paginate(15);

        $products = Product::query()
            ->select('id', 'name', 'sku', 'has_variant')
            ->orderBy('name')
            ->get();

        $formVariants = $this->formProductId
            ? ProductVariant::where('product_id', $this->formProductId)->orderBy('sku')->get(['id', 'sku'])
            : collect();

        return view('livewire.admin.inventory.warehouse-transfer-component', [
            'transfers' => $transfers,
            'metrics' => $metrics,
            'products' => $products,
            'formVariants' => $formVariants,
        ])->layout('livewire.admin.layouts.app');
    }
}
