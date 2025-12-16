<div>
    <!-- Full Page Loader during Refresh -->
    @if($isRefreshing)
    <div class="position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 9999;">
        <div class="text-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Refreshing Dashboard...</span>
            </div>
            <p class="mt-3 text-primary fw-semibold">Refreshing dashboard data...</p>
        </div>
    </div>
    @endif

    <!-- Refresh Button and Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Dashboard Overview</h4>
                            <small class="text-muted">Showing data from
                                {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to
                                {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</small>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <!-- Last updated indicator -->
                            <small class="text-muted me-2">
                                <i class="bx bx-time"></i>
                                Updated {{ $lastUpdated->diffForHumans() }}
                            </small>

                            <!-- Refresh Button -->
                            <button wire:click="refreshDashboardData" 
                                    wire:loading.attr="disabled"
                                    wire:loading.class="disabled"
                                    class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                                <span wire:loading.remove wire:target="refreshDashboardData">
                                    <i class="bx bx-refresh"></i> Refresh Data
                                </span>
                                <span wire:loading wire:target="refreshDashboardData">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Refreshing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Revenue Overview</h5>
                    <small class="text-muted">Only delivered orders count as revenue</small>
                </div>
                <div class="card-body">
                    <div wire:ignore id="revenueChart" style="height: 300px;">
                        <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading chart...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Sales by Category</h5>
                    <small class="text-muted">Only delivered orders</small>
                </div>
                <div class="card-body">
                    <div wire:ignore id="categoryChart" style="height: 300px;">
                        <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading chart...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Filter Section -->
    <div class="row mb-4">
        <div class="col-12 position-relative">
            <!-- Loader -->
            <div wire:loading.flex wire:target="filterType, startDate, endDate, applyFilter, refreshDashboardData"
                class="loader-overlay justify-content-center align-items-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <style>
                .loader-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.6);
                    z-index: 50;
                }
            </style>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Filter Type</label>
                            <select class="form-select" wire:model.live="filterType">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month" selected>This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>

                        @if ($filterType === 'custom')
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" wire:model="startDate">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" wire:model="endDate">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary" wire:click="applyFilter">Apply</button>
                            </div>
                        @else
                            <div class="col-md-6 d-flex align-items-end">
                                <small class="text-muted">
                                    Automatic filter applied. Use custom range for specific dates.
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Top Cards -->
    <div class="row">
        <!-- Total Revenue (Delivered Orders Only) -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Revenue</span>
                            <h3 class="card-title mb-2">
                                {{ setting('currency') }}{{ number_format($orderStats->total_revenue ?? 0, 2) }}
                            </h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i>
                                From {{ $orderStats->delivered_orders ?? 0 }} Delivered Orders
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">
                                    All Time:
                                    {{ setting('currency') }}{{ number_format($totalStats['total_revenue_all_time'] ?? 0, 2) }}
                                </small>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-dollar"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Orders</span>
                            <h3 class="card-title mb-2">{{ $orderStats->total_orders ?? 0 }}</h3>
                            <small class="text-info fw-semibold">
                                {{ $orderStats->delivered_orders ?? 0 }} Delivered
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">
                                    All Time: {{ $totalStats['total_orders_all_time'] ?? 0 }}
                                </small>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-shopping-bag"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1">Pending Orders</span>
                            <h3 class="card-title mb-2 text-warning">{{ $totalPendingOrders }}</h3>
                            <small class="text-warning fw-semibold">
                                Need Immediate Attention
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">
                                    In Filter: {{ $orderStats->pending_orders ?? 0 }}
                                </small>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-time"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products & Stock -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1">Products & Stock</span>
                            <h3 class="card-title mb-2">{{ $stockStats->total_products ?? 0 }}</h3>
                            <small class="text-success fw-semibold">
                                {{ $stockStats->total_stock ?? 0 }} Total Stock
                            </small>
                            <div class="mt-2">
                                <small class="text-danger">
                                    {{ $stockStats->out_of_stock ?? 0 }} Out of Stock
                                </small>
                                <br>
                                <small class="text-warning">
                                    {{ $stockStats->low_stock ?? 0 }} Low Stock
                                </small>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown by Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Revenue Breakdown by Order Status</h5>
                    <small class="text-muted">Filtered Period</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Pending Revenue -->
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card border border-warning">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <span class="avatar avatar-sm rounded bg-warning">
                                            <i class="bx bx-time text-white"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-warning mb-1">
                                        {{ setting('currency') }}{{ number_format($revenueByStatus->pending_amount ?? 0, 2) }}
                                    </h4>
                                    <small class="text-muted d-block">Pending</small>
                                    <small class="text-warning fw-semibold">Unconfirmed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmed Revenue -->
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card border border-primary">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <span class="avatar avatar-sm rounded bg-primary">
                                            <i class="bx bx-check-circle text-white"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-primary mb-1">
                                        {{ setting('currency') }}{{ number_format($revenueByStatus->confirmed_amount ?? 0, 2) }}
                                    </h4>
                                    <small class="text-muted d-block">Confirmed</small>
                                    <small class="text-primary fw-semibold">Earned Revenue</small>
                                </div>
                            </div>
                        </div>

                        <!-- Shipped Revenue -->
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card border border-info">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <span class="avatar avatar-sm rounded bg-info">
                                            <i class="bx bx-package text-white"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-info mb-1">
                                        {{ setting('currency') }}{{ number_format($revenueByStatus->shipped_amount ?? 0, 2) }}
                                    </h4>
                                    <small class="text-muted d-block">Shipped</small>
                                    <small class="text-info fw-semibold">In Transit</small>
                                </div>
                            </div>
                        </div>

                        <!-- Delivered Revenue -->
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card border border-success">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <span class="avatar avatar-sm rounded bg-success">
                                            <i class="bx bx-check text-white"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-success mb-1">
                                        {{ setting('currency') }}{{ number_format($revenueByStatus->delivered_amount ?? 0, 2) }}
                                    </h4>
                                    <small class="text-muted d-block">Delivered</small>
                                    <small class="text-success fw-semibold">Completed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Cancelled Revenue -->
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card border border-danger">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <span class="avatar avatar-sm rounded bg-danger">
                                            <i class="bx bx-x-circle text-white"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-danger mb-1">
                                        {{ setting('currency') }}{{ number_format($revenueByStatus->cancelled_amount ?? 0, 2) }}
                                    </h4>
                                    <small class="text-muted d-block">Cancelled</small>
                                    <small class="text-danger fw-semibold">Lost Revenue</small>
                                </div>
                            </div>
                        </div>

                        <!-- Returned Revenue -->
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <div class="card border border-secondary">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <span class="avatar avatar-sm rounded bg-secondary">
                                            <i class="bx bx-undo text-white"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-secondary mb-1">
                                        {{ setting('currency') }}{{ number_format($revenueByStatus->returned_amount ?? 0, 2) }}
                                    </h4>
                                    <small class="text-muted d-block">Returned</small>
                                    <small class="text-secondary fw-semibold">Refunded</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Row -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <h6 class="mb-0">Total Revenue in Filter Period:</h6>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="progress" style="height: 20px;">
                                                @php
                                                    $totalFilteredRevenue = $revenueByStatus->total_amount ?? 0;
                                                    $deliveredPercent = $totalFilteredRevenue > 0 ? (($revenueByStatus->delivered_amount ?? 0) / $totalFilteredRevenue * 100) : 0;
                                                    $confirmedPercent = $totalFilteredRevenue > 0 ? (($revenueByStatus->confirmed_amount ?? 0) / $totalFilteredRevenue * 100) : 0;
                                                    $shippedPercent = $totalFilteredRevenue > 0 ? (($revenueByStatus->shipped_amount ?? 0) / $totalFilteredRevenue * 100) : 0;
                                                    $pendingPercent = $totalFilteredRevenue > 0 ? (($revenueByStatus->pending_amount ?? 0) / $totalFilteredRevenue * 100) : 0;
                                                @endphp

                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $deliveredPercent }}%"
                                                    title="Delivered: {{ number_format($deliveredPercent, 1) }}%">
                                                </div>
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: {{ $confirmedPercent }}%"
                                                    title="Confirmed: {{ number_format($confirmedPercent, 1) }}%">
                                                </div>
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: {{ $shippedPercent }}%"
                                                    title="Shipped: {{ number_format($shippedPercent, 1) }}%">
                                                </div>
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: {{ $pendingPercent }}%"
                                                    title="Pending: {{ number_format($pendingPercent, 1) }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <h5 class="text-primary mb-0">
                                                {{ setting('currency') }}{{ number_format($totalFilteredRevenue, 2) }}
                                            </h5>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="d-flex flex-wrap gap-3">
                                                <small>
                                                    <span class="badge bg-success">●</span>
                                                    Delivered:
                                                    {{ setting('currency') }}{{ number_format($revenueByStatus->delivered_amount ?? 0, 2) }}
                                                </small>
                                                <small>
                                                    <span class="badge bg-primary">●</span>
                                                    Confirmed:
                                                    {{ setting('currency') }}{{ number_format($revenueByStatus->confirmed_amount ?? 0, 2) }}
                                                </small>
                                                <small>
                                                    <span class="badge bg-info">●</span>
                                                    Shipped:
                                                    {{ setting('currency') }}{{ number_format($revenueByStatus->shipped_amount ?? 0, 2) }}
                                                </small>
                                                <small>
                                                    <span class="badge bg-warning">●</span>
                                                    Pending:
                                                    {{ setting('currency') }}{{ number_format($revenueByStatus->pending_amount ?? 0, 2) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  

    <!-- Order Status & Stock Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Order Status & Stock Overview</h5>
                    <small class="text-muted">Filtered Period</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Status -->
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $orderStats->pending_orders ?? 0 }}</h4>
                                <small class="text-muted">Pending</small>
                                <div class="mt-1">
                                    <small class="text-danger">Total {{ $totalPendingOrders }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $orderStats->confirmed_orders ?? 0 }}</h4>
                                <small class="text-muted">Confirmed</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">{{ $orderStats->shipped_orders ?? 0 }}</h4>
                                <small class="text-muted">Shipped</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ $orderStats->delivered_orders ?? 0 }}</h4>
                                <small class="text-muted">Delivered</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">{{ $orderStats->cancelled_orders ?? 0 }}</h4>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-secondary">{{ $orderStats->returned_orders ?? 0 }}</h4>
                                <small class="text-muted">Returned</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Three Column Data -->
    <div class="row">
        <!-- Best Selling Products -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Best Selling Products</h5>
                    <small class="text-muted">Filtered Period</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bestSellingProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png') }}"
                                                    alt="{{ $product->name }}" class="rounded me-2" width="30"
                                                    height="30">
                                                <span class="fw-semibold">{{ \Str::limit($product->name, 20) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $product->total_sold }}</td>
                                        <td>{{ setting('currency') }}{{ number_format($product->total_revenue, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No data found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Categories -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Popular Categories</h5>
                    <small class="text-muted">Filtered Period</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Orders</th>
                                    <th>Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($popularCategories as $category)
                                    <tr>
                                        <td class="fw-semibold">{{ $category->name }}</td>
                                        <td>{{ $category->order_count }}</td>
                                        <td>{{ $category->total_sold }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No data found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Golden Customers -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Golden Customers</h5>
                    <small class="text-muted">Filtered Period</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Orders</th>
                                    <th>Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($goldenCustomers as $customer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($customer->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <span
                                                    class="fw-semibold">{{ \Str::limit($customer->user->name, 15) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $customer->total_orders }}</td>
                                        <td>{{ setting('currency') }}{{ number_format($customer->total_spent, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No data found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    // Store chart instances globally
    let revenueChart = null;
    let categoryChart = null;
    let chartsInitialized = false;

    // Initialize Google Charts
    google.charts.load("current", {
        packages: ["corechart"]
    });

    google.charts.setOnLoadCallback(() => {
        chartsInitialized = true;
        drawCharts();
    });

    // Listen for Livewire events
    document.addEventListener("livewire:initialized", () => {
        if (chartsInitialized) {
            setTimeout(drawCharts, 300);
        }
    });

    document.addEventListener("livewire:navigated", () => {
        if (chartsInitialized) {
            setTimeout(drawCharts, 300);
        }
    });

    // Handle chart updates from Livewire
    Livewire.on('charts-data-updated', (data) => {
        if (chartsInitialized) {
            updateCharts(data);
        }
    });

    // Listen for refresh complete
    Livewire.on('refresh-complete', () => {
        if (chartsInitialized) {
            setTimeout(() => {
                drawCharts();
            }, 500);
        }
    });

    // Draw all charts
    function drawCharts() {
        drawRevenueChart();
        drawCategoryChart();
    }

    // Update charts with new data
    function updateCharts(data) {
        if (data.revenue) {
            drawRevenueChartWithData(data.revenue);
        }
        if (data.category) {
            drawCategoryChartWithData(data.category);
        }
    }

    // Revenue chart function
    function drawRevenueChart() {
        let el = document.getElementById("revenueChart");
        if (!el) return;

        let rows = @json($revenueChartData);
        drawRevenueChartWithData(rows);
    }

    function drawRevenueChartWithData(rows) {
        let el = document.getElementById("revenueChart");
        if (!el) return;

        // Clear previous chart
        if (revenueChart) {
            try {
                revenueChart.clearChart();
            } catch (e) {
                // Chart might not be initialized yet
            }
        }

        if (!rows || rows.length < 2 || rows[1][0] === 'Loading...') {
            el.innerHTML = "<div class='text-center text-muted mt-5'>Loading revenue data...</div>";
            return;
        }

        if (rows.length === 2 && rows[1][1] === 0) {
            el.innerHTML = "<div class='text-center text-muted mt-5'>No revenue data available</div>";
            return;
        }

        try {
            let data = google.visualization.arrayToDataTable(rows);

            let options = {
                title: "Revenue Overview (Delivered Orders Only)",
                curveType: "function",
                legend: {
                    position: "bottom"
                },
                colors: ["#4285F4"],
                backgroundColor: "transparent",
                chartArea: {
                    width: '85%',
                    height: '70%'
                },
                vAxis: {
                    format: 'currency',
                    title: 'Revenue'
                },
                hAxis: {
                    title: 'Date'
                }
            };

            revenueChart = new google.visualization.LineChart(el);
            revenueChart.draw(data, options);
        } catch (error) {
            console.error("Error drawing revenue chart:", error);
            el.innerHTML = "<div class='text-center text-danger mt-5'>Error loading chart</div>";
        }
    }

    // Category chart function
    function drawCategoryChart() {
        let el = document.getElementById("categoryChart");
        if (!el) return;

        let rows = @json($categoryChartData);
        drawCategoryChartWithData(rows);
    }

    function drawCategoryChartWithData(rows) {
        let el = document.getElementById("categoryChart");
        if (!el) return;

        // Clear previous chart
        if (categoryChart) {
            try {
                categoryChart.clearChart();
            } catch (e) {
                // Chart might not be initialized yet
            }
        }

        if (!rows || rows.length < 2 || rows[1][0] === 'Loading...') {
            el.innerHTML = "<div class='text-center text-muted mt-5'>Loading category data...</div>";
            return;
        }

        if (rows.length === 2 && rows[1][0] === 'No Data') {
            el.innerHTML = "<div class='text-center text-muted mt-5'>No category data available</div>";
            return;
        }

        try {
            let data = google.visualization.arrayToDataTable(rows);

            let options = {
                title: "Sales by Category (Delivered Orders Only)",
                pieHole: 0.4,
                chartArea: {
                    width: "90%",
                    height: "80%"
                },
                legend: {
                    position: 'labeled'
                }
            };

            categoryChart = new google.visualization.PieChart(el);
            categoryChart.draw(data, options);
        } catch (error) {
            console.error("Error drawing category chart:", error);
            el.innerHTML = "<div class='text-center text-danger mt-5'>Error loading chart</div>";
        }
    }

    // Handle window resize
    window.addEventListener("resize", () => {
        if (chartsInitialized) {
            setTimeout(drawCharts, 100);
        }
    });

    // Handle page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && chartsInitialized) {
            setTimeout(drawCharts, 100);
        }
    });
</script>
@endpush