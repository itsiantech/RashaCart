<div>
    <!-- Charts and Main Content -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Revenue Overview</h5>
                    <small class="text-muted">Filtered Period</small>
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
                    <small class="text-muted">Filtered Period</small>
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
            <div wire:loading.flex wire:target="filterType, startDate, endDate, applyFilter"
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
                                    Showing data from {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}
                                    to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
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
        <!-- Total Revenue -->
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
                                From {{ $orderStats->total_orders ?? 0 }} Orders
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">
                                    All Time: {{ setting('currency') }}{{ number_format($totalStats['total_revenue_all_time'] ?? 0, 2) }}
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
                                <h4 class="text-info">{{ $orderStats->processing_orders ?? 0 }}</h4>
                                <small class="text-muted">Processing</small>
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
                        
                        <!-- Stock Status -->
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-secondary">{{ $stockStats->total_products ?? 0 }}</h4>
                                <small class="text-muted">Total Products</small>
                                <div class="mt-1">
                                    <small class="text-danger">{{ $stockStats->out_of_stock ?? 0 }} Out</small>
                                    <br>
                                    <small class="text-warning">{{ $stockStats->low_stock ?? 0 }} Low</small>
                                </div>
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
        let googleReady = false;

        google.charts.load("current", {
            packages: ["corechart"]
        });

        google.charts.setOnLoadCallback(() => {
            googleReady = true;
            drawCharts();
        });

        document.addEventListener("livewire:initialized", () => {
            if (googleReady) drawCharts();
        });

        document.addEventListener("livewire:navigated", () => {
            setTimeout(() => {
                if (googleReady) drawCharts();
            }, 200);
        });

        Livewire.hook("morph.updated", () => {
            if (googleReady) {
                setTimeout(drawCharts, 150);
            }
        });

        function drawCharts() {
            drawRevenueChart();
            drawCategoryChart();
        }

        function drawRevenueChart() {
            let el = document.getElementById("revenueChart");
            if (!el) return;

            let rows = @json($revenueChartData);
            if (rows.length < 2) {
                el.innerHTML = "<p class='text-center text-muted mt-5'>No revenue data</p>";
                return;
            }

            let data = google.visualization.arrayToDataTable(rows);

            let options = {
                title: "Revenue Overview",
                curveType: "function",
                legend: {
                    position: "bottom"
                },
                colors: ["#4285F4"],
                backgroundColor: "transparent",
                chartArea: {
                    width: '85%',
                    height: '70%'
                }
            };

            new google.visualization.LineChart(el).draw(data, options);
        }

        function drawCategoryChart() {
            let el = document.getElementById("categoryChart");
            if (!el) return;

            let rows = @json($categoryChartData);
            if (rows.length < 2) {
                el.innerHTML = "<p class='text-center text-muted mt-5'>No category data</p>";
                return;
            }

            let data = google.visualization.arrayToDataTable(rows);

            let options = {
                title: "Sales by Category",
                pieHole: 0.4,
                chartArea: {
                    width: "90%",
                    height: "80%"
                }
            };

            new google.visualization.PieChart(el).draw(data, options);
        }

        window.addEventListener("resize", () => {
            if (googleReady) drawCharts();
        });
    </script>
@endpush