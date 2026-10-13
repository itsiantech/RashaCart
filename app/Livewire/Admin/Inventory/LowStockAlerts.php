<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class LowStockAlerts extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $severityFilter = '';

    // Inline threshold editing: key 'p-{id}' or 'v-{id}' => value
    public $thresholds = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSeverityFilter()
    {
        $this->resetPage();
    }

    public function saveThreshold(string $key)
    {
        $value = (int) ($this->thresholds[$key] ?? -1);

        if ($value < 0) {
            $this->dispatch('error', message: 'Threshold must be 0 or greater!');

            return;
        }

        [$type, $id] = [$key[0], (int) substr($key, 2)];

        if ($type === 'p') {
            Product::whereKey($id)->update(['low_stock_threshold' => $value]);
        } else {
            ProductVariant::whereKey($id)->update(['low_stock_threshold' => $value]);
        }

        $this->dispatch('success', message: 'Reorder level updated!');
    }

    private function severity(int $stock, int $threshold): array
    {
        if ($stock <= 0) {
            return ['level' => 'out', 'label' => 'Out of Stock', 'badge' => 'bg-danger'];
        }

        if ($stock <= intdiv($threshold, 2)) {
            return ['level' => 'critical', 'label' => 'Critical', 'badge' => 'bg-danger'];
        }

        return ['level' => 'low', 'label' => 'Low', 'badge' => 'bg-warning'];
    }

    public function render()
    {
        $stockMap = InventoryService::stockMap();
        $reservedMap = InventoryService::reservedMap();

        $items = collect();

        // Simple products with threshold
        Product::query()
            ->select('id', 'sku', 'name', 'thumbnail', 'has_variant', 'low_stock_threshold')
            ->with('category:id,name')
            ->where('low_stock_threshold', '>', 0)
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%")))
            ->orderBy('name')
            ->get()
            ->each(function ($p) use (&$items, $stockMap, $reservedMap) {
                if ($p->has_variant) {
                    return;
                }

                $stock = $stockMap['product'][$p->id] ?? 0;
                $severity = $this->severity($stock, $p->low_stock_threshold);

                $items->push([
                    'key' => 'p-'.$p->id,
                    'product_id' => $p->id,
                    'sku' => $p->sku,
                    'name' => $p->name,
                    'category' => $p->category?->name,
                    'available' => $stock,
                    'reserved' => $reservedMap['product'][$p->id] ?? 0,
                    'threshold' => $p->low_stock_threshold,
                    'is_variant' => false,
                    'variant_id' => null,
                    ...$severity,
                ]);
            });

        // Variants with threshold
        ProductVariant::query()
            ->select('id', 'product_id', 'sku', 'low_stock_threshold')
            ->with('product:id,name,sku,category_id')
            ->where('low_stock_threshold', '>', 0)
            ->whereHas('product', fn ($q) => $q->where('has_variant', true)
                ->when($this->search, fn ($w) => $w->where(fn ($x) => $x
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%"))))
            ->orderBy('sku')
            ->get()
            ->each(function ($v) use (&$items, $stockMap, $reservedMap) {
                $stock = $stockMap['variant'][$v->id] ?? 0;
                $severity = $this->severity($stock, $v->low_stock_threshold);

                $items->push([
                    'key' => 'v-'.$v->id,
                    'product_id' => $v->product_id,
                    'sku' => $v->sku,
                    'name' => $v->product?->name,
                    'category' => $v->product?->category?->name,
                    'available' => $stock,
                    'reserved' => $reservedMap['variant'][$v->id] ?? 0,
                    'threshold' => $v->low_stock_threshold,
                    'is_variant' => true,
                    'variant_id' => $v->id,
                    ...$severity,
                ]);
            });

        $metrics = [
            'total' => $items->count(),
            'out' => $items->where('level', 'out')->count(),
            'critical' => $items->where('level', 'critical')->count(),
            'low' => $items->where('level', 'low')->count(),
        ];

        if ($this->severityFilter) {
            $items = $items->where('level', $this->severityFilter)->values();
        }

        // Sort: out first, then critical, then low; closest to threshold first
        $order = ['out' => 0, 'critical' => 1, 'low' => 2];
        $items = $items->sortBy([
            fn ($a, $b) => $order[$a['level']] <=> $order[$b['level']],
            fn ($a, $b) => ($a['available'] <=> $b['available']),
        ])->values();

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $currentPage
        );
        $paginated->withQueryString();

        return view('livewire.admin.inventory.low-stock-alerts', [
            'items' => $paginated,
            'metrics' => $metrics,
        ])->layout('livewire.admin.layouts.app');
    }
}
