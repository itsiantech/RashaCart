<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovements extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $typeFilter = '';
    public $search = '';
    public $preset = 'this_month';
    public $from = '';
    public $to = '';
    public $groupBy = 'daily';

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPreset()
    {
        $this->resetPage();
    }

    private function dateRange(): array
    {
        return match ($this->preset) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            '7days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'custom' => [
                $this->from ? \Carbon\Carbon::parse($this->from)->startOfDay() : null,
                $this->to ? \Carbon\Carbon::parse($this->to)->endOfDay() : null,
            ],
            default => [null, null], // all
        };
    }

    private function baseQuery()
    {
        [$from, $to] = $this->dateRange();

        return Inventory::query()
            ->with(['product:id,name,sku', 'variant:id,sku', 'user:id,name'])
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->search, fn ($q) => $q->where(function ($w) {
                $w->where('notes', 'like', "%{$this->search}%")
                    ->orWhereHas('product', fn ($p) => $p
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%"))
                    ->orWhereHas('variant', fn ($v) => $v
                        ->where('sku', 'like', "%{$this->search}%"));
            }))
            ->when($from, fn ($q) => $q->where('inventories.created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('inventories.created_at', '<=', $to));
    }

    public function render()
    {
        $base = $this->baseQuery();

        // Summary totals for the selected range
        $totals = (clone $base)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END), 0) AS in_qty,
                COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) AS out_qty,
                COALESCE(SUM(CASE WHEN type = 'return' THEN quantity ELSE 0 END), 0) AS return_qty,
                COALESCE(SUM(CASE WHEN type = 'damage' THEN quantity ELSE 0 END), 0) AS damage_qty,
                COALESCE(SUM(CASE WHEN type = 'adjustment' THEN quantity ELSE 0 END), 0) AS adjust_qty")
            ->first();

        // Time-bucketed series
        $bucket = match ($this->groupBy) {
            'weekly' => "DATE_FORMAT(created_at, '%x-W%v')",
            'monthly' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => 'DATE(created_at)',
        };

        $series = (clone $base)
            ->selectRaw("$bucket AS bucket,
                COALESCE(SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END), 0) AS in_qty,
                COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) AS out_qty,
                COALESCE(SUM(CASE WHEN type = 'damage' THEN quantity ELSE 0 END), 0) AS damage_qty")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->limit(60)
            ->get();

        // Outgoing (out) grouped by product category
        $byCategory = (clone $base)
            ->where('inventories.type', 'out')
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') AS label, SUM(inventories.quantity) AS qty")
            ->groupBy('categories.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // Outgoing grouped by variant attribute (e.g. Color: Red)
        $byAttribute = (clone $base)
            ->where('inventories.type', 'out')
            ->whereNotNull('inventories.variant_id')
            ->join('variant_attribute_values', 'variant_attribute_values.product_variant_id', '=', 'inventories.variant_id')
            ->join('attributes', 'attributes.id', '=', 'variant_attribute_values.attribute_id')
            ->join('attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
            ->selectRaw("CONCAT(attributes.name, ': ', attribute_values.value) AS label, SUM(inventories.quantity) AS qty")
            ->groupBy('attributes.name', 'attribute_values.value')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $movements = (clone $base)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.admin.inventory.stock-movements', [
            'movements' => $movements,
            'totals' => $totals,
            'series' => $series,
            'byCategory' => $byCategory,
            'byAttribute' => $byAttribute,
        ])->layout('livewire.admin.layouts.app');
    }
}
