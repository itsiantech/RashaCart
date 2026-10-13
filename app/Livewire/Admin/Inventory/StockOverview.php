<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class StockOverview extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $categoryId = '';
    public $subCategoryId = '';
    public $childCategoryId = '';
    public $stockFilter = '';

    protected $queryString = ['search' => ['except' => '']];

    public function updatedCategoryId()
    {
        $this->subCategoryId = '';
        $this->childCategoryId = '';
        $this->resetPage();
    }

    public function updatedSubCategoryId()
    {
        $this->childCategoryId = '';
        $this->resetPage();
    }

    public function updatedChildCategoryId()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    private function buildRows()
    {
        $stockMap = InventoryService::stockMap();
        $reservedMap = InventoryService::reservedMap();

        $variantsByProduct = ProductVariant::query()
            ->select('id', 'product_id', 'low_stock_threshold')
            ->get()
            ->groupBy('product_id');

        $rows = [];

        Product::query()
            ->select('id', 'sku', 'name', 'thumbnail', 'category_id', 'buying_price', 'has_variant', 'low_stock_threshold', 'status')
            ->with('category:id,name,parent_id')
            ->orderBy('name')
            ->chunk(200, function ($products) use (&$rows, $stockMap, $reservedMap, $variantsByProduct) {
                foreach ($products as $p) {
                    if ($p->has_variant) {
                        $available = 0;
                        $reserved = 0;
                        $out = true;
                        $low = false;

                        foreach ($variantsByProduct[$p->id] ?? [] as $v) {
                            $vs = $stockMap['variant'][$v->id] ?? 0;
                            $available += $vs;
                            $reserved += $reservedMap['variant'][$v->id] ?? 0;

                            if ($vs > 0) {
                                $out = false;
                                if ($v->low_stock_threshold > 0 && $vs <= $v->low_stock_threshold) {
                                    $low = true;
                                }
                            }
                        }
                    } else {
                        $available = $stockMap['product'][$p->id] ?? 0;
                        $reserved = $reservedMap['product'][$p->id] ?? 0;
                        $threshold = $p->low_stock_threshold;
                        $out = $available <= 0;
                        $low = ! $out && $threshold > 0 && $available <= $threshold;
                    }

                    $rows[] = [
                        'id' => $p->id,
                        'sku' => $p->sku,
                        'name' => $p->name,
                        'thumbnail' => $p->thumbnail,
                        'category_id' => $p->category_id,
                        'category' => $p->category?->name,
                        'buying_price' => (float) $p->buying_price,
                        'has_variant' => (bool) $p->has_variant,
                        'available' => $available,
                        'reserved' => $reserved,
                        'is_low' => $low,
                        'is_out' => $out,
                    ];
                }
            });

        return $rows;
    }

    public function render()
    {
        $rows = collect($this->buildRows());

        // Metric cards — full dataset
        $metrics = [
            'total_skus' => $rows->count(),
            'total_units' => $rows->sum('available'),
            'total_value' => $rows->sum(fn ($r) => $r['available'] * $r['buying_price']),
            'low_count' => $rows->where('is_low', true)->count(),
            'out_count' => $rows->where('is_out', true)->count(),
        ];

        // Filters
        $search = strtolower(trim($this->search));
        $selectedCategoryId = $this->childCategoryId ?: $this->subCategoryId ?: $this->categoryId;

        $allowedCategoryIds = null;
        if ($selectedCategoryId) {
            $cats = Category::all(['id', 'parent_id']);
            $allowedCategoryIds = $this->descendantIds($cats, (int) $selectedCategoryId);
        }

        $filtered = $rows->filter(function ($r) use ($search, $allowedCategoryIds) {
            if ($search && ! str_contains(strtolower($r['name']), $search) && ! str_contains(strtolower((string) $r['sku']), $search)) {
                return false;
            }

            if ($allowedCategoryIds !== null && ! in_array($r['category_id'] ?? null, $allowedCategoryIds)) {
                return false;
            }

            if ($this->stockFilter === 'low' && ! $r['is_low']) {
                return false;
            }

            if ($this->stockFilter === 'out' && ! $r['is_out']) {
                return false;
            }

            if ($this->stockFilter === 'in_stock' && $r['available'] <= 0) {
                return false;
            }

            return true;
        })->values();

        // Keep category_id for filter check — add it inside buildRows? Already have category via name only.
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $paginated = new LengthAwarePaginator(
            $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $currentPage
        );
        $paginated->withQueryString();

        $topCategories = Category::whereNull('parent_id')->orderBy('name')->get(['id', 'name']);
        $subCategories = $this->categoryId
            ? Category::where('parent_id', $this->categoryId)->orderBy('name')->get(['id', 'name'])
            : collect();
        $childCategories = $this->subCategoryId
            ? Category::where('parent_id', $this->subCategoryId)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('livewire.admin.inventory.stock-overview', [
            'rows' => $paginated,
            'metrics' => $metrics,
            'topCategories' => $topCategories,
            'subCategories' => $subCategories,
            'childCategories' => $childCategories,
        ])->layout('livewire.admin.layouts.app');
    }

    private function descendantIds($cats, int $id): array
    {
        $ids = [$id];
        $stack = [$id];

        while ($stack) {
            $current = array_pop($stack);
            foreach ($cats as $c) {
                if ($c->parent_id == $current && ! in_array($c->id, $ids)) {
                    $ids[] = $c->id;
                    $stack[] = $c->id;
                }
            }
        }

        return $ids;
    }
}
