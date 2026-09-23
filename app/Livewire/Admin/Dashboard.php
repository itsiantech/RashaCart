<?php

namespace App\Livewire\Admin;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $startDate;

    public $endDate;

    public $filterType = 'this_month';

    public $revenueChartData = [];

    public $categoryChartData = [];

    protected $cachePrefix = 'dashboard_';

    protected $cacheDuration = 300;

    public $lastUpdated;

    public $isRefreshing = false;

    protected function getListeners()
    {
        return [
            'refreshCharts' => '$refresh',
            'refreshDashboard' => 'refreshDashboardData',
        ];
    }

    public function mount()
    {
        $this->setDefaultDateRange();
        $this->initializeCharts();
        $this->lastUpdated = Cache::get('dashboard_last_updated', now());
    }

    protected function setDefaultDateRange()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function initializeCharts()
    {
        // Initialize with loading state
        $this->revenueChartData = [['Date', 'Revenue'], ['Loading...', 0]];
        $this->categoryChartData = [['Category', 'Sales'], ['Loading...', 1]];

        // Load actual data
        $this->prepareChartData();
    }

    public function updatedFilterType($value)
    {
        switch ($value) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->format('Y-m-d');
                $this->endDate = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $this->startDate = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Keep current custom dates
                break;
        }

        $this->refreshDashboardData();
    }

    public function applyFilter()
    {
        $this->refreshDashboardData();
    }

    public function refreshDashboardData()
    {
        $this->isRefreshing = true;

        $this->clearAllCache();

        $this->prepareChartData();

        $this->lastUpdated = Cache::remember('dashboard_last_updated', $this->cacheDuration, function () {
            return now();
        });

        $this->dispatch('charts-updated');
        $this->dispatch('refresh-complete');

        $this->isRefreshing = false;
    }

    protected function clearAllCache()
    {
        $cacheKeys = [
            'chart_data',
            'stats',
            'pending_orders',
            'total_stats',
            'revenue_by_status',
            'best_selling',
            'popular_categories',
            'golden_customers',
            'order_stats',
            'stock_stats',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($this->getCacheKey($key));
        }

        Cache::forget('dashboard_last_updated');
    }

    protected function getCacheKey($type)
    {
        $userPart = $this->isVendor() ? '_vendor_' . $this->vendorId() : '_admin';

        // Bumped automatically (Order::booted()) whenever any order is
        // created, updated, or deleted anywhere in the app — so a status
        // change (e.g. to "returned") invalidates these cached stats
        // immediately instead of waiting for $cacheDuration to expire.
        $version = Cache::get('dashboard_cache_version', 1);

        return $this->cachePrefix . $type . '_' . $this->filterType . '_' . $this->startDate . '_' . $this->endDate . $userPart . '_v' . $version;
    }

    protected function prepareChartData()
    {
        $chartDataCacheKey = $this->getCacheKey('chart_data');

        $chartData = Cache::remember($chartDataCacheKey, $this->cacheDuration, function () {
            return [
                'revenue' => $this->formatRevenueChartData(),
                'category' => $this->formatCategoryChartData(),
            ];
        });

        $this->revenueChartData = $chartData['revenue'];
        $this->categoryChartData = $chartData['category'];

        // Dispatch event for charts
        $this->dispatch('charts-data-updated', [
            'revenue' => $this->revenueChartData,
            'category' => $this->categoryChartData,
        ]);
    }

    protected function formatRevenueChartData()
    {
        $revenueData = $this->getRevenueChartData();

        $chartData = [['Date', 'Revenue']];
        foreach ($revenueData as $data) {
            $chartData[] = [
                Carbon::parse($data->date)->format('M d'),
                (float) $data->revenue,
            ];
        }

        if (count($chartData) === 1) {
            $chartData[] = [Carbon::now()->format('M d'), 0];
        }

        return $chartData;
    }

    protected function formatCategoryChartData()
    {
        $categoryData = $this->getSalesByCategoryData();

        $chartData = [['Category', 'Sales']];
        foreach ($categoryData as $data) {
            $chartData[] = [
                $data->name,
                (float) $data->total_sales,
            ];
        }

        if (count($chartData) === 1) {
            $chartData[] = ['No Data', 1];
        }

        return $chartData;
    }

    // Get all data with caching
    public function getTotalPendingOrders()
    {
        $query = Order::where('status', 'pending');

        if ($this->isVendor()) {
            $query->whereHas('orderItems.product', function ($q) {
                $q->where('user_id', $this->vendorId());
            });
        }

        return $query->count();
    }

    public function getTotalStats()
    {
        $cacheKey = $this->getCacheKey('total_stats');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $totalCustomers = User::whereHas('roles', function ($query) {
                $query->where('name', 'customer');
            })->count();

            if ($totalCustomers === 0) {
                $totalCustomers = User::whereDoesntHave('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'vendor']);
                })->count();
            }

            return [
                // Revenue only counts once an order is marked "completed" —
                // "delivered" isn't final because a delivered order can still
                // come back as a return.
                'total_revenue_all_time' => Order::where('status', 'completed')->sum('total_amount'),
                'total_orders_all_time' => Order::count(),
                'total_products_all_time' => Product::count(),
                'total_customers_all_time' => $totalCustomers,
            ];
        });
    }

    // NEW: Get revenue breakdown by status
    public function getRevenueByStatus()
    {
        $cacheKey = $this->getCacheKey('revenue_by_status');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $query = Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);

            if ($this->isVendor()) {
                $query->whereHas('orderItems.product', function ($q) {
                    $q->where('user_id', $this->vendorId());
                });
            }

            return $query->select(
                DB::raw('SUM(CASE WHEN status = "pending" THEN total_amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN status = "confirmed" THEN total_amount ELSE 0 END) as confirmed_amount'),
                DB::raw('SUM(CASE WHEN status = "shipped" THEN total_amount ELSE 0 END) as shipped_amount'),
                DB::raw('SUM(CASE WHEN status = "delivered" THEN total_amount ELSE 0 END) as delivered_amount'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN total_amount ELSE 0 END) as cancelled_amount'),
                DB::raw('SUM(CASE WHEN status = "returned" THEN total_amount ELSE 0 END) as returned_amount'),
                DB::raw('SUM(total_amount) as total_amount')
            )->first();
        });
    }

    public function getBestSellingProducts()
    {
        $cacheKey = $this->getCacheKey('best_selling');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $query = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                ->where('orders.status', '!=', 'cancelled')
                ->where('orders.status', '!=', 'returned');

            if ($this->isVendor()) {
                $query->where('products.user_id', $this->vendorId());
            }

            return $query->select(
                'products.name',
                'products.thumbnail',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
                ->groupBy('products.id', 'products.name', 'products.thumbnail')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get();
        });
    }

    public function getPopularCategories()
    {
        $cacheKey = $this->getCacheKey('popular_categories');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $query = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                ->where('orders.status', '!=', 'cancelled')
                ->where('orders.status', '!=', 'returned');

            if ($this->isVendor()) {
                $query->where('products.user_id', $this->vendorId());
            }

            return $query->select(
                'categories.name',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_sold')
                ->get();
        });
    }

    public function getGoldenCustomers()
    {
        $cacheKey = $this->getCacheKey('golden_customers');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            return Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'returned')
                ->select(
                    'user_id',
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total_amount) as total_spent')
                )
                ->with('user:id,name,email')
                ->groupBy('user_id')
                ->orderByDesc('total_spent')
                ->limit(10)
                ->get()
                ->filter(function ($order) {
                    return $order->user !== null;
                });
        });
    }

    public function getOrderStats()
    {
        $cacheKey = $this->getCacheKey('order_stats');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $query = Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);

            if ($this->isVendor()) {
                $query->whereHas('orderItems.product', function ($q) {
                    $q->where('user_id', $this->vendorId());
                });
            }

            return $query->select(
                DB::raw('COUNT(*) as total_orders'),
                // Revenue only counts once an order is "completed" — see
                // getTotalStats() for why "delivered" isn't final.
                DB::raw('SUM(CASE WHEN status = "completed" THEN total_amount ELSE 0 END) as total_revenue'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_orders'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_orders'),
                DB::raw('COUNT(CASE WHEN status = "shipped" THEN 1 END) as shipped_orders'),
                DB::raw('COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered_orders'),
                DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_orders'),
                DB::raw('COUNT(CASE WHEN status = "returned" THEN 1 END) as returned_orders')
            )->first();
        });
    }

    public function getStockStats()
    {
        $cacheKey = $this->getCacheKey('stock_stats');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $mainProductStock = Inventory::where('variant_id', null)
                ->select(
                    DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) - SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_stock')
                )
                ->first();

            $variantStock = Inventory::whereNotNull('variant_id')
                ->select(
                    DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) - SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_stock')
                )
                ->first();

            $totalStock = ($mainProductStock->total_stock ?? 0) + ($variantStock->total_stock ?? 0);

            $outOfStockProducts = $this->getOutOfStockProductsCount();
            $lowStockProducts = $this->getLowStockProductsCount();

            return (object) [
                'total_products' => $this->isVendor()
                    ? Product::where('user_id', $this->vendorId())->count()
                    : Product::count(),
                'total_stock' => $totalStock,
                'out_of_stock' => $outOfStockProducts,
                'low_stock' => $lowStockProducts,
            ];
        });
    }

    protected function getOutOfStockProductsCount()
    {
        $outOfStockCount = 0;
        $products = $this->isVendor()
            ? Product::where('user_id', $this->vendorId())
            ->with(['inventories', 'variants.inventories'])
            ->get()
            : Product::with(['inventories', 'variants.inventories'])->get();

        foreach ($products as $product) {
            $totalStock = $this->calculateProductTotalStock($product);
            if ($totalStock <= 0) {
                $outOfStockCount++;
            }
        }

        return $outOfStockCount;
    }

    protected function getLowStockProductsCount()
    {
        $lowStockCount = 0;
        $products = Product::with(['inventories', 'variants.inventories'])->get();

        foreach ($products as $product) {
            $totalStock = $this->calculateProductTotalStock($product);
            if ($totalStock > 0 && $totalStock < 10) {
                $lowStockCount++;
            }
        }

        return $lowStockCount;
    }

    protected function calculateProductTotalStock($product)
    {
        $mainStock = $product->inventories
            ->where('variant_id', null)
            ->sum(function ($inventory) {
                return $inventory->type === 'in' ? $inventory->quantity : -$inventory->quantity;
            });

        $variantStock = 0;
        foreach ($product->variants as $variant) {
            $variantStock += $variant->inventories->sum(function ($inventory) {
                return $inventory->type === 'in' ? $inventory->quantity : -$inventory->quantity;
            });
        }

        return $mainStock + $variantStock;
    }

    public function getRevenueChartData()
    {
        $query = Order::whereBetween('created_at', [
            $this->startDate . ' 00:00:00',
            $this->endDate . ' 23:59:59',
        ])
            // Revenue only counts once an order is "completed" — see
            // getTotalStats() for why "delivered" isn't final.
            ->where('status', 'completed');

        if ($this->isVendor()) {
            $query->whereHas('orderItems.product', function ($q) {
                $q->where('user_id', $this->vendorId());
            });
        }

        return $query
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getSalesByCategoryData()
    {
        $query = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ])
            // Revenue only counts once an order is "completed" — see
            // getTotalStats() for why "delivered" isn't final.
            ->where('orders.status', 'completed');

        if ($this->isVendor()) {
            $query->where('products.user_id', $this->vendorId());
        }

        return $query->select(
            'categories.name',
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
        )
            ->groupBy('categories.id', 'categories.name')
            ->get();
    }

    protected function isVendor()
    {
        return Auth::user()->hasRole('vendor');
    }

    protected function vendorId()
    {
        return Auth::id();
    }

    public function render()
    {
        $data = [
            'bestSellingProducts' => $this->getBestSellingProducts(),
            'popularCategories' => $this->getPopularCategories(),
            'goldenCustomers' => $this->getGoldenCustomers(),
            'orderStats' => $this->getOrderStats(),
            'stockStats' => $this->getStockStats(),
            'revenueChartData' => $this->revenueChartData,
            'categoryChartData' => $this->categoryChartData,
            'totalPendingOrders' => $this->getTotalPendingOrders(),
            'totalStats' => $this->getTotalStats(),
            'revenueByStatus' => $this->getRevenueByStatus(),
            'isRefreshing' => $this->isRefreshing,
        ];

        return view('livewire.admin.dashboard', $data)->layout('livewire.admin.layouts.app');
    }
}