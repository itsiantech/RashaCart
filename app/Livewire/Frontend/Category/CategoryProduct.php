<?php

namespace App\Livewire\Frontend\Category;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProduct extends Component
{
    public $category = null;

    public $selectedPrice = null;

    public $selectedAttrs = [];

    public $selectedBrands = [];

    public $minPrice = null;

    public $maxPrice = null;

    public $sort = 'popular';

    public $meta_title;

    public $meta_description;

    public $meta_keywords;

    public $meta_tags;

    public $relatedProducts;

    use HandlesCartActions;
    use WithPagination;

    protected $queryString = [
        'selectedPrice' => ['except' => null],
        'selectedAttrs' => ['except' => []],
        'selectedBrands' => ['except' => []],
        'sort' => ['except' => 'popular'],
    ];

    public function mount($category = null)
    {
        if ($category) {
            $this->category = Category::where('slug', $category)->first();

            $this->meta_title = $this->category->meta_title;
            $this->meta_description = $this->category->meta_description;
            $this->meta_keywords = $this->category->meta_keywords;
            $this->meta_tags = $this->category->meta_tags;
        }

        // Load products
        $this->relatedProducts = Product::where('status', true)
            ->latest()
            ->take(5)
            ->get();

        // Attach total_sold
        $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id')
            ->pluck('total_sold', 'product_id');

        $this->relatedProducts = $this->relatedProducts->map(function ($product) use ($totalSold) {
            $product->total_sold = $totalSold[$product->id] ?? 0;

            return $product;
        });
    }

    public function togglePrice($price)
    {
        $this->selectedPrice = ($this->selectedPrice === $price) ? null : $price;
        if ($this->selectedPrice) {
            $this->minPrice = null;
            $this->maxPrice = null;
        }
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->selectedPrice = null;
    }

    public function updatedMaxPrice(): void
    {
        $this->selectedPrice = null;
    }

    public function applyCustomPrice(): void
    {
        $min = $this->minPrice !== null && $this->minPrice !== '' ? (float) $this->minPrice : null;
        $max = $this->maxPrice !== null && $this->maxPrice !== '' ? (float) $this->maxPrice : null;

        if ($min === null && $max === null) {
            return;
        }
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
            $this->minPrice = $min;
            $this->maxPrice = $max;
        }

        $this->selectedPrice = null;
        $this->resetPage();
    }

    public function clearPrice(): void
    {
        $this->selectedPrice = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function toggleBrand($brandId)
    {
        $brandId = (int) $brandId;
        $current = $this->selectedBrands ?? [];
        $this->selectedBrands = in_array($brandId, $current)
            ? array_values(array_diff($current, [$brandId]))
            : array_values(array_merge($current, [$brandId]));
        $this->resetPage();
    }

    public function setSort(string $sort): void
    {
        if (in_array($sort, ['popular', 'price_asc', 'price_desc', 'newest'], true)) {
            $this->sort = $sort;
            $this->resetPage();
        }
    }

    public function clearAllFilters(): void
    {
        $this->selectedPrice = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->selectedAttrs = [];
        $this->selectedBrands = [];
        $this->resetPage();
    }

    public function toggleAttrValue($attrId, $valueId)
    {
        $attrId = (string) $attrId;
        $valueId = (int) $valueId;
        $current = $this->selectedAttrs[$attrId] ?? [];

        if (in_array($valueId, $current)) {
            $current = array_values(array_diff($current, [$valueId]));
        } else {
            $current[] = $valueId;
        }

        if (empty($current)) {
            unset($this->selectedAttrs[$attrId]);
        } else {
            $this->selectedAttrs[$attrId] = $current;
        }

        $this->resetPage();
    }

    public function getMetaData()
    {
        if ($this->category) {
            $this->meta_title = $this->category->meta_title ?? $this->category->name;
            $this->meta_description = $this->category->meta_description ?? '';
            $this->meta_keywords = $this->category->meta_keywords ?? '';
            $this->meta_tags = $this->category->meta_tags ?? '';
        }
    }

    public function getProductsProperty()
    {
        $query = Product::query();

        // Category filter
        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        if ($this->selectedPrice) {
            [$min, $max] = explode('-', $this->selectedPrice);
            $query->whereBetween('sale_price', [(int) $min, (int) $max]);
        }

        if (($this->minPrice !== null && $this->minPrice !== '') || ($this->maxPrice !== null && $this->maxPrice !== '')) {
            $min = $this->minPrice !== null && $this->minPrice !== '' ? (float) $this->minPrice : 0;
            $max = $this->maxPrice !== null && $this->maxPrice !== '' ? (float) $this->maxPrice : 99999999;
            $query->whereBetween('sale_price', [$min, $max]);
        }

        if (! empty($this->selectedBrands)) {
            $query->whereIn('brand_id', array_map('intval', $this->selectedBrands));
        }

        // Attribute filters: variant must match one selected value per attribute (AND across attributes)
        foreach ($this->selectedAttrs as $attrId => $valueIds) {
            $valueIds = array_map('intval', (array) $valueIds);
            if (empty($valueIds)) {
                continue;
            }
            $query->whereHas('variants.variantAttributeValues', function ($q) use ($attrId, $valueIds) {
                $q->where('variant_attribute_values.attribute_id', (int) $attrId)
                    ->whereIn('variant_attribute_values.attribute_value_id', $valueIds);
            });
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('sale_price')->orderBy('id', 'desc'),
            'price_desc' => $query->orderByDesc('sale_price')->orderBy('id', 'desc'),
            'newest' => $query->orderBy('id', 'desc'),
            default => $query->orderByDesc('is_featured')->orderBy('id', 'desc'),
        };

        return $query->with([
            'variants.variantAttributeValues.attribute',
            'variants.variantAttributeValues.attributeValue',
        ])->paginate(20);
    }

    /** Brands present in the current category scope, with product counts. */
    public function getFilterBrandsProperty()
    {
        $base = Product::query()->where('status', true);
        if ($this->category) {
            $base->where('category_id', $this->category->id);
        }

        $counts = (clone $base)->selectRaw('brand_id, COUNT(*) as total')
            ->whereNotNull('brand_id')
            ->groupBy('brand_id')
            ->pluck('total', 'brand_id');

        if ($counts->isEmpty()) {
            return collect();
        }

        return \App\Models\Brand::whereIn('id', $counts->keys())
            ->where('is_active', 1)
            ->orderBy('name')->get()
            ->map(fn ($b) => ['brand' => $b, 'count' => (int) ($counts[$b->id] ?? 0)]);
    }

    /** Children of the current category ("More in X" pills). */
    public function getSubcategoriesProperty()
    {
        if (! $this->category) {
            return collect();
        }

        return \App\Models\Category::where('parent_id', $this->category->id)
            ->where('status', 1)
            ->orderBy('sort_order')->orderBy('name')->get();
    }

    public function getActiveFilterCountProperty(): int
    {
        return ($this->selectedPrice ? 1 : 0)
            + (($this->minPrice !== null && $this->minPrice !== '') || ($this->maxPrice !== null && $this->maxPrice !== '') ? 1 : 0)
            + count($this->selectedBrands)
            + collect($this->selectedAttrs)->map(fn ($v) => count((array) $v))->sum();
    }

    public function getCategoriesProperty()
    {
        return Category::with('allChildren')->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getFilterAttributesProperty()
    {
        if (! setting('attribute_system', true)) {
            return collect();
        }

        return \App\Models\Attribute::active()
            ->where('is_filterable', true)
            ->with(['values' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->get();
    }

    public function render()
    {
        return view('livewire.frontend.category.category-product', [
            'products' => $this->products,
            'categories' => $this->categories,
        ])->layout('livewire.frontend.layouts.store')
            ->layoutData([
                'meta' => [
                    'title' => $this->meta_title ?? '',
                    'description' => $this->meta_description ?? '',
                    'keywords' => $this->meta_keywords ?? '',
                    'tags' => $this->meta_tags ?? '',
                ],
            ]);
    }
}
