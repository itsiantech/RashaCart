<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = 'this_month';

   
    public $revenueChartData = [];
    public $categoryChartData = [];

    protected function getListeners()
    {
        return [
            'refreshCharts' => 'prepareChartData',
        ];
    }

    public function mount()
    {
        $this->setDefaultDateRange();
        $this->prepareChartData();
    }

    protected function setDefaultDateRange()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
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
        
        $this->prepareChartData();
        $this->dispatch('charts-updated');
    }

    public function applyFilter()
    {
        $this->prepareChartData();
        $this->dispatch('charts-updated');
    }

    
    protected function prepareChartData()
    {
        $this->revenueChartData = $this->formatRevenueChartData();
        $this->categoryChartData = $this->formatCategoryChartData();
        
        // Dispatch browser event to refresh charts
        $this->dispatch('charts-updated');
    }

    protected function formatRevenueChartData()
    {
        $revenueData = $this->getRevenueChartData();
        
        $chartData = [['Date', 'Revenue']];
        foreach ($revenueData as $data) {
            $chartData[] = [
                Carbon::parse($data->date)->format('M d'),
                (float) $data->revenue
            ];
        }
        
        // Ensure we have at least some data
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
                (float) $data->total_sales
            ];
        }
        
        // Ensure we have at least some data
        if (count($chartData) === 1) {
            $chartData[] = ['No Data', 1];
        }
        
        return $chartData;
    }

    
    protected function ensureChartData($chartData)
    {
        if (!$chartData) {
            return [['No Data', 'Value'], ['No Data', 1]];
        }
        return $chartData;
    }

    // New method to get total pending orders (all time)
    public function getTotalPendingOrders()
    {
        return Order::where('status', 'pending')->count();
    }

    // New method to get total data (all time)
    public function getTotalStats()
    {
        // Spatie role এর জন্য customer count - alternative approach
        $totalCustomers = User::whereHas('roles', function($query) {
            $query->where('name', 'customer');
        })->count();

        // Alternative: যদি specific role না থাকে, তাহলে admin/user role ব্যতীত সব user কে customer ধরা
        if ($totalCustomers === 0) {
            $totalCustomers = User::whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->count();
        }

        return [
            'total_revenue_all_time' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'total_orders_all_time' => Order::count(),
            'total_products_all_time' => Product::count(),
            'total_customers_all_time' => $totalCustomers,
        ];
    }

   
    public function getBestSellingProducts()
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.name',
                'products.thumbnail',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.thumbnail')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    }

    public function getPopularCategories()
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'categories.name',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    }

    public function getGoldenCustomers()
    {
        return Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled')
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
    }

    public function getOrderStats()
    {
        return Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_orders'),
                DB::raw('COUNT(CASE WHEN status = "processing" THEN 1 END) as processing_orders'),
                DB::raw('COUNT(CASE WHEN status = "shipped" THEN 1 END) as shipped_orders'),
                DB::raw('COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered_orders'),
                DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_orders')
            )
            ->first();
    }

    // Updated method to get stock stats from inventory
    public function getStockStats()
    {
        // Calculate main product stock from inventory
        $mainProductStock = Inventory::where('variant_id', null)
            ->select(
                DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) - SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_stock')
            )
            ->first();

        // Calculate variant stock from inventory
        $variantStock = Inventory::whereNotNull('variant_id')
            ->select(
                DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) - SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_stock')
            )
            ->first();

        $totalStock = ($mainProductStock->total_stock ?? 0) + ($variantStock->total_stock ?? 0);

        // Get out of stock and low stock products
        $outOfStockProducts = $this->getOutOfStockProductsCount();
        $lowStockProducts = $this->getLowStockProductsCount();

        return (object) [
            'total_products' => Product::count(),
            'total_stock' => $totalStock,
            'out_of_stock' => $outOfStockProducts,
            'low_stock' => $lowStockProducts,
        ];
    }

    // Get count of out of stock products
    protected function getOutOfStockProductsCount()
    {
        $outOfStockCount = 0;
        $products = Product::with(['inventories', 'variants.inventories'])->get();

        foreach ($products as $product) {
            $totalStock = $this->calculateProductTotalStock($product);
            if ($totalStock <= 0) {
                $outOfStockCount++;
            }
        }

        return $outOfStockCount;
    }

    // Get count of low stock products (less than 10)
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

    // Calculate total stock for a product including variants
    protected function calculateProductTotalStock($product)
    {
        // Calculate main product stock
        $mainStock = $product->inventories
            ->where('variant_id', null)
            ->sum(function($inventory) {
                return $inventory->type === 'in' ? $inventory->quantity : -$inventory->quantity;
            });

        // Calculate variant stock
        $variantStock = 0;
        foreach ($product->variants as $variant) {
            $variantStock += $variant->inventories->sum(function($inventory) {
                return $inventory->type === 'in' ? $inventory->quantity : -$inventory->quantity;
            });
        }

        return $mainStock + $variantStock;
    }

    public function getRevenueChartData()
    {
        return Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled')
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
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();
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
        ];
         $this->prepareChartData();

        return view('livewire.admin.dashboard', $data)->layout('livewire.admin.layouts.app');
    }
}