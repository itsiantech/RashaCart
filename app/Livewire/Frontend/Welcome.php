<?php

namespace App\Livewire\Frontend;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\NewArrival;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\HandlesCartActions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Welcome extends Component
{
    use HandlesCartActions;

    public $featuredProducts = [];

    public $categoryProducts = [];

    public $featuredCategories = [];

    public $featuredBrands = [];

    public $brandProducts = [];

    public $perPage = 12;

    public $banners = [];

    public array $allProducts = [];

    public int $allProductsLoadedCount = 0;

    public int $allProductsPerPage = 12;

    public $sidebarCategories = [];

    public $categoryLoadedCount = [];

    public $featuredLoadedCount = 0;

    // Flash Sale
    public $flashSale = null;

    public $flashSaleItems = [];

    public $flashSaleEndTime = null;

    public $newArrivals;

    public function mount()
    {
        $this->refreshCart();

        $this->sidebarCategories = Category::with('allChildren')
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $this->featuredCategories = Cache::get('categories', collect())
            ->where('is_featured', 1)
            ->values();

        $this->featuredBrands = Brand::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $this->newArrivals = NewArrival::with('product')->active()->ordered()->get();

        $this->loadFlashSale();
        $this->loadProducts();

        $this->banners = Banner::where('status', 1)->get();
    }

    public function loadFlashSale(): void
    {
        $now = Carbon::now();

        $this->flashSale = FlashSale::where('is_active', true)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->with(['activeItems.product' => function ($q) {
                $q->select('id', 'name', 'price', 'stock', 'thumbnail');
            }])
            ->latest()
            ->first();

        if ($this->flashSale) {
            $this->flashSaleItems = $this->flashSale->activeItems;
            $this->flashSaleEndTime = $this->flashSale->end_at->toIso8601String();
        }
    }

    public function loadProducts()
    {
        $allCachedProducts = Cache::get('products', collect());

        // Featured products
        $this->featuredProducts = $allCachedProducts
            ->where('is_featured', 1)
            ->take($this->perPage)
            ->values();

        // Attach total_sold
        $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id')
            ->pluck('total_sold', 'product_id');

        $this->featuredProducts = $this->featuredProducts->map(function ($product) use ($totalSold) {
            $product->total_sold = $totalSold[$product->id] ?? 0;

            return $product;
        });

        $this->featuredLoadedCount = $this->featuredProducts->count();

        // Brand products
        foreach ($this->featuredBrands as $brand) {
            $this->brandProducts[$brand->id] = $allCachedProducts
                ->where('brand_id', $brand->id)
                ->take($this->perPage)
                ->values();
        }

        // All products (from DB directly)
        $this->allProducts = Product::where('status', 1)
            ->latest()
            ->take($this->allProductsPerPage)
            ->get()
            ->all();
        $this->allProductsLoadedCount = count($this->allProducts);
    }

    public function loadMoreFeatured()
    {
        $allProducts = Cache::get('products', collect());

        $more = $allProducts->where('is_featured', 1)
            ->slice($this->featuredLoadedCount, $this->perPage)
            ->values();

        $this->featuredProducts = $this->featuredProducts->concat($more);
        $this->featuredLoadedCount = $this->featuredProducts->count();
    }

    public function loadMoreAllProducts(): void
    {
        $this->allProductsPerPage += 12;
        $this->allProducts = Product::where('status', 1)
            ->latest()
            ->take($this->allProductsPerPage)
            ->get()
            ->all();
        $this->allProductsLoadedCount = count($this->allProducts);
    }

    public function render()
    {
        return view('livewire.frontend.welcome', [
            'categories' => $this->sidebarCategories,
            'featuredCategories' => $this->featuredCategories,
            'featuredBrands' => $this->featuredBrands,
            'featuredProducts' => $this->featuredProducts,
            'categoryProducts' => $this->categoryProducts,
            'brandProducts' => $this->brandProducts,
            'newArrivals' => $this->newArrivals,
            'allProducts' => $this->allProducts,
            'allProductsLoadedCount' => $this->allProductsLoadedCount,
        ])->layout('livewire.frontend.layouts.app');
    }
}
