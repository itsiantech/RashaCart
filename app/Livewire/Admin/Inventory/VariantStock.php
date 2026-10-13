<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class VariantStock extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $productId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectProduct($id)
    {
        $this->productId = $id ?: null;
    }

    public function render()
    {
        $stockMap = InventoryService::stockMap();
        $reservedMap = InventoryService::reservedMap();

        // Product list (has_variant only)
        $products = Product::query()
            ->select('id', 'sku', 'name', 'thumbnail')
            ->where('has_variant', true)
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%")))
            ->withCount('variants')
            ->orderBy('name')
            ->paginate(12);

        $selected = null;
        $matrix = collect();
        $metrics = ['total_variants' => 0, 'total_units' => 0, 'low' => 0, 'out' => 0];

        if ($this->productId) {
            $selected = Product::query()
                ->select('id', 'sku', 'name', 'price', 'thumbnail')
                ->with(['variants' => fn ($q) => $q->select('id', 'product_id', 'sku', 'varient_price')
                    ->with(['variantAttributeValues:id,product_variant_id,attribute_id,attribute_value_id',
                        'variantAttributeValues.attribute:id,name',
                        'variantAttributeValues.attributeValue:id,value'])])
                ->find($this->productId);

            if ($selected) {
                foreach ($selected->variants as $v) {
                    $available = $stockMap['variant'][$v->id] ?? 0;
                    $reserved = $reservedMap['variant'][$v->id] ?? 0;
                    $threshold = $v->low_stock_threshold;

                    $matrix->push([
                        'id' => $v->id,
                        'sku' => $v->sku,
                        'attributes' => $v->variantAttributeValues->map(fn ($vav) => [
                            'name' => $vav->attribute?->name,
                            'value' => $vav->attributeValue?->value,
                        ]),
                        'price' => (float) $selected->price + (float) ($v->varient_price ?? 0),
                        'available' => $available,
                        'reserved' => $reserved,
                        'is_out' => $available <= 0,
                        'is_low' => $available > 0 && $threshold > 0 && $available <= $threshold,
                    ]);
                }

                $metrics['total_variants'] = $matrix->count();
                $metrics['total_units'] = $matrix->sum('available');
                $metrics['low'] = $matrix->where('is_low', true)->count();
                $metrics['out'] = $matrix->where('is_out', true)->count();
            }
        }

        return view('livewire.admin.inventory.variant-stock', [
            'products' => $products,
            'selected' => $selected,
            'matrix' => $matrix,
            'metrics' => $metrics,
        ])->layout('livewire.admin.layouts.app');
    }
}
