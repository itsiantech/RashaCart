<div>
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
                            <h3 class="card-title mb-2">${{ number_format($orderStats->total_revenue ?? 0, 2) }}</h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i>
                                From {{ $orderStats->total_orders ?? 0 }} Orders
                            </small>
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

        <!-- Products Stock -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1">Products Stock</span>
                            <h3 class="card-title mb-2">{{ $stockStats->total_stock ?? 0 }}</h3>
                            <small class="text-warning fw-semibold">
                                {{ $stockStats->low_stock ?? 0 }} Low, {{ $stockStats->out_of_stock ?? 0 }} Out
                            </small>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Golden Customers -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block mb-1">Golden Customers</span>
                            <h3 class="card-title mb-2">{{ $goldenCustomers->count() }}</h3>
                            <small class="text-success fw-semibold">
                                Top Spenders
                            </small>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Main Content -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Revenue Overview</h5>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Sales by Category</h5>
                </div>
                <div class="card-body">
                    <div id="categoryChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Order Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $orderStats->pending_orders ?? 0 }}</h4>
                                <small class="text-muted">Pending</small>
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
                        <div class="col-md-2 col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-secondary">{{ $stockStats->total_products ?? 0 }}</h4>
                                <small class="text-muted">Total Products</small>
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
                                @foreach ($bestSellingProducts as $product)
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
                                        <td>${{ number_format($product->total_revenue, 2) }}</td>
                                    </tr>
                                @endforeach
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
                                @foreach ($popularCategories as $category)
                                    <tr>
                                        <td class="fw-semibold">{{ $category->name }}</td>
                                        <td>{{ $category->order_count }}</td>
                                        <td>{{ $category->total_sold }}</td>
                                    </tr>
                                @endforeach
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
                                @foreach ($goldenCustomers as $customer)
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
                                        <td>${{ number_format($customer->total_spent, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', initCharts);
        document.addEventListener('livewire:navigated', initCharts);

        Livewire.hook('morph.updated', () => {
            drawCharts();
        });

        function initCharts() {
            if (!google || !google.charts) return;

            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawCharts);
        }

        function drawCharts() {
            drawRevenueChart();
            drawCategoryChart();
        }

        function drawRevenueChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Revenue');

            data.addRows([
                @foreach ($revenueChartData as $item)
                    ['{{ \Carbon\Carbon::parse($item->date)->format('M d') }}', {{ $item->revenue }}],
                @endforeach
            ]);

            var options = {
                title: 'Daily Revenue',
                curveType: 'function',
                legend: {
                    position: 'bottom'
                },
                colors: ['#7367f0'],
                backgroundColor: 'transparent',
                chartArea: {
                    width: '85%',
                    height: '75%'
                }
            };

            var chart = new google.visualization.LineChart(
                document.getElementById('revenueChart')
            );
            chart.draw(data, options);
        }

        function drawCategoryChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Sales');

            data.addRows([
                @foreach ($salesByCategoryData as $category)
                    ['{{ $category->name }}', {{ $category->total_sales }}],
                @endforeach
            ]);

            var options = {
                title: 'Sales Distribution',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                chartArea: {
                    width: '90%',
                    height: '80%'
                },
                legend: {
                    position: 'labeled'
                }
            };

            var chart = new google.visualization.PieChart(
                document.getElementById('categoryChart')
            );
            chart.draw(data, options);
        }
    </script>
@endpush
