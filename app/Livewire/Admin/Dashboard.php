<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = 'this_month';

    // Add these properties for chart data
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

    // Add this method to prepare chart data
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

    // Add a method to handle empty data scenarios
    protected function ensureChartData($chartData)
    {
        if (!$chartData) {
            return [['No Data', 'Value'], ['No Data', 1]];
        }
        return $chartData;
    }

    // Your existing methods remain the same...
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

    public function getStockStats()
    {
        return Product::select(
                DB::raw('COUNT(*) as total_products'),
                DB::raw('SUM(stock) as total_stock'),
                DB::raw('COUNT(CASE WHEN stock = 0 THEN 1 END) as out_of_stock'),
                DB::raw('COUNT(CASE WHEN stock < 10 AND stock > 0 THEN 1 END) as low_stock')
            )
            ->first();
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
        ];
         $this->prepareChartData();

        return view('livewire.admin.dashboard', $data)->layout('livewire.admin.layouts.app');
    }
}