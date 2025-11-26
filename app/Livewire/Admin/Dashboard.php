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

    public function mount()
    {
        $this->setDefaultDateRange();
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
    }

    public function applyFilter()
    {
        // This method will be called when custom date range is applied
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
        $revenueData = Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $revenueData;
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
            'revenueChartData' => $this->getRevenueChartData(),
            'salesByCategoryData' => $this->getSalesByCategoryData(),
        ];

        return view('livewire.admin.dashboard', $data)->layout('livewire.admin.layouts.app');
    }
}