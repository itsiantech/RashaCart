<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\DamagedStock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DamageStock extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    // Report form
    public $formProductId = '';
    public $formVariantId = '';
    public $formQuantity = 1;
    public $formReason = '';

    // Per-row action state: [damaged_id => value]
    public $actionStatus = [];
    public $actionNote = [];

    public $statusFilter = '';

    protected function rules(): array
    {
        return [
            'formProductId' => 'required',
            'formQuantity' => 'required|integer|min:1',
            'formReason' => 'required|string|max:500',
        ];
    }

    public function updatedFormProductId()
    {
        $this->formVariantId = '';
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function saveDamage()
    {
        $this->validate();

        $product = Product::find($this->formProductId);
        $variant = $this->formVariantId ? ProductVariant::find($this->formVariantId) : null;

        try {
            InventoryService::logDamage($product, $variant, (int) $this->formQuantity, $this->formReason);
            $this->dispatch('success', message: 'Damage logged successfully!');
            $this->reset(['formProductId', 'formVariantId', 'formQuantity', 'formReason']);
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('error', message: $e->getMessage());
        } catch (\Throwable $e) {
            $this->dispatch('error', message: 'Error: '.$e->getMessage());
        }
    }

    public function applyAction($damagedId)
    {
        $action = $this->actionStatus[$damagedId] ?? '';

        if (! in_array($action, ['scrapped', 'returned_to_supplier', 'refurbished'])) {
            $this->dispatch('error', message: 'Please select an action first!');

            return;
        }

        $damaged = DamagedStock::find($damagedId);

        if (! $damaged || $damaged->action_status !== 'pending') {
            return;
        }

        try {
            InventoryService::resolveDamage($damaged, $action, $this->actionNote[$damagedId] ?? null);
            $this->dispatch('success', message: 'Action applied successfully!');

            unset($this->actionStatus[$damagedId], $this->actionNote[$damagedId]);
        } catch (\Throwable $e) {
            $this->dispatch('error', message: 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $damagedValue = DamagedStock::query()
            ->join('products', 'products.id', '=', 'damaged_stocks.product_id')
            ->sum(DB::raw('damaged_stocks.quantity * products.buying_price'));

        $metrics = [
            'total_qty' => DamagedStock::sum('quantity'),
            'pending' => DamagedStock::where('action_status', 'pending')->count(),
            'scrapped' => DamagedStock::where('action_status', 'scrapped')->count(),
            'returned' => DamagedStock::where('action_status', 'returned_to_supplier')->count(),
            'refurbished' => DamagedStock::where('action_status', 'refurbished')->count(),
            'total_value' => $damagedValue,
        ];

        $items = DamagedStock::query()
            ->with(['product:id,name,sku,buying_price', 'variant:id,sku', 'reportedBy:id,name'])
            ->when($this->statusFilter, fn ($q) => $q->where('action_status', $this->statusFilter))
            ->orderByDesc('id')
            ->paginate(15);

        $products = Product::query()
            ->select('id', 'name', 'sku', 'has_variant')
            ->orderBy('name')
            ->get();

        $formVariants = $this->formProductId
            ? ProductVariant::where('product_id', $this->formProductId)->orderBy('sku')->get(['id', 'sku'])
            : collect();

        return view('livewire.admin.inventory.damage-stock', [
            'items' => $items,
            'metrics' => $metrics,
            'products' => $products,
            'formVariants' => $formVariants,
        ])->layout('livewire.admin.layouts.app');
    }
}
