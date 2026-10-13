<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryWiseStock extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    #[Url(as: 'category')]
    public $categoryId = null;

    public function selectCategory($id)
    {
        $this->categoryId = $id ?: null;
        $this->resetPage();
    }

    private function productTotals()
    {
        // product_id => ['available' => int, 'value' => float] (variants included)
        $stockMap = InventoryService::stockMap();

        $variantSums = collect($stockMap['variant'])
            ->mapWithKeys(function ($stock, $variantId) {
                return [$variantId => $stock];
            });

        $variantProductIds = ProductVariant::query()
            ->pluck('product_id', 'id');

        $totals = [];

        Product::query()
            ->select('id', 'category_id', 'buying_price', 'has_variant')
            ->chunk(500, function ($products) use (&$totals, $stockMap, $variantSums, $variantProductIds) {
                foreach ($products as $p) {
                    $available = $p->has_variant
                        ? collect($stockMap['variant'])
                            ->filter(fn ($stock, $variantId) => $variantProductIds[$variantId] == $p->id)
                            ->sum()
                        : ($stockMap['product'][$p->id] ?? 0);

                    $totals[$p->id] = [
                        'category_id' => $p->category_id,
                        'available' => $available,
                        'value' => $available * (float) $p->buying_price,
                    ];
                }
            });

        return $totals;
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

    public function render()
    {
        $cats = Category::orderBy('name')->get()->keyBy('id');
        $totals = $this->productTotals();

        // Aggregate per category: direct + descendants
        $aggregate = [];
        foreach ($cats as $cat) {
            $ids = $this->descendantIds($cats, $cat->id);
            $units = 0;
            $value = 0.0;
            $productCount = 0;
            foreach ($totals as $t) {
                if (in_array($t['category_id'], $ids)) {
                    $units += $t['available'];
                    $value += $t['value'];
                    $productCount++;
                }
            }
            $aggregate[$cat->id] = [
                'units' => $units,
                'value' => $value,
                'product_count' => $productCount,
            ];
        }

        // Breadcrumb
        $breadcrumb = collect();
        if ($this->categoryId && isset($cats[$this->categoryId])) {
            $walk = $cats[$this->categoryId];
            while ($walk) {
                $breadcrumb->prepend($walk);
                $walk = $walk->parent_id ? ($cats[$walk->parent_id] ?? null) : null;
            }
        }

        $currentCategory = $this->categoryId ? ($cats[$this->categoryId] ?? null) : null;

        // Categories to display: children of selected, or top-level
        $displayCategories = $this->categoryId
            ? $cats->where('parent_id', (int) $this->categoryId)
            : $cats->whereNull('parent_id');

        // Direct products of the selected category
        $products = collect();
        if ($currentCategory) {
            $query = Product::query()
                ->select('id', 'sku', 'name', 'thumbnail', 'buying_price', 'has_variant')
                ->with('category:id,name')
                ->where('category_id', $currentCategory->id)
                ->orderBy('name');

            $products = $query->paginate(15);

            $variantProductIds = ProductVariant::pluck('product_id', 'id');
            $stockMap = InventoryService::stockMap();
            $reservedMap = InventoryService::reservedMap();

            $products->getCollection()->transform(function ($p) use ($stockMap, $reservedMap, $variantProductIds) {
                if ($p->has_variant) {
                    $available = 0;
                    $reserved = 0;
                    foreach ($variantProductIds as $variantId => $productId) {
                        if ($productId == $p->id) {
                            $available += $stockMap['variant'][$variantId] ?? 0;
                            $reserved += $reservedMap['variant'][$variantId] ?? 0;
                        }
                    }
                } else {
                    $available = $stockMap['product'][$p->id] ?? 0;
                    $reserved = $reservedMap['product'][$p->id] ?? 0;
                }

                $p->available_stock = $available;
                $p->reserved_stock = $reserved;
                $p->stock_value = $available * (float) $p->buying_price;

                return $p;
            });
        }

        return view('livewire.admin.inventory.category-wise-stock', [
            'displayCategories' => $displayCategories,
            'aggregate' => $aggregate,
            'currentCategory' => $currentCategory,
            'breadcrumb' => $breadcrumb,
            'products' => $products,
        ])->layout('livewire.admin.layouts.app');
    }
}
