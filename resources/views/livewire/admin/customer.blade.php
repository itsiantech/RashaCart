<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="#">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">customer</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Index</li>
            </ol>
        </nav>

        <div>

            {{-- <a wire:navigate href="{{ route('customer.user.create') }}" class="btn btn-danger btn-sm">
                <i class="bx bx-plus"></i> Add New
            </a> --}}

        </div>
    </div>
    <div class="card py-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 px-3 px-sm-4 mb-4">

            <div class="d-flex flex-column flex-sm-row align-items-center gap-3 w-100 w-sm-auto">
                <form class="flex-grow-1 flex-sm-grow-0" role="search" autocomplete="off">
                    <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" placeholder="Search..." wire:model.live.debounce.300ms="search"
                            class="form-control ps-5" style="max-width: 300px;">
                    </div>
                </form>
            </div>
        </div>

        <!-- customers Table -->
        <div class="table-responsive px-3 px-sm-4">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Name</th>
                        <th class="text-center text-nowrap">Order Success Rate</th>
                        <th class="text-center text-nowrap">Status</th>
                        <th class="text-center text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="text-nowrap">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">

                                        <img src="{{ $customer->image ? asset('storage/' . $customer->image) : asset('assets/admin/img/avatars/user-avater.png') }}"
                                            alt="customer image" class="img-fluid">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $customer->name }}</div>
                                        <small class="text-muted">
                                            <div>Email: {{ $customer->email }}</div>
                                            <div>Phone: {{ $customer->phone }}</div>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="order-stats-container">
                                    <!-- Total Order Count -->
                                    <div class="total-orders mb-2">
                                        <strong>{{ $customer->orders->count() }}</strong>
                                    </div>

                                    <!-- Status Progress Bar -->
                                    <div class="status-progress-bar">
                                        @php
                                            $statuses = [
                                                'pending',
                                                'processing',
                                                'shipped',
                                                'delivered',
                                                'cancelled',
                                                'returned',
                                            ];
                                            $statusCounts = [];
                                            $totalOrders = $customer->orders->count();

                                            foreach ($statuses as $status) {
                                                $statusCounts[$status] = $customer->orders
                                                    ->where('status', $status)
                                                    ->count();
                                            }
                                        @endphp

                                        <div class="progress-bar-container">
                                            @foreach ($statuses as $status)
                                                @if ($statusCounts[$status] > 0)
                                                    @php
                                                        $percentage =
                                                            $totalOrders > 0
                                                                ? ($statusCounts[$status] / $totalOrders) * 100
                                                                : 0;
                                                        $colors = [
                                                            'pending' => '#ffc107',
                                                            'processing' => '#17a2b8',
                                                            'shipped' => '#007bff',
                                                            'delivered' => '#28a745',
                                                            'cancelled' => '#dc3545',
                                                            'returned' => '#6c757d',
                                                        ];
                                                    @endphp
                                                    <div class="progress-segment"
                                                        style="width: {{ $percentage }}%; background-color: {{ $colors[$status] }};"
                                                        title="{{ ucfirst($status) }}: {{ $statusCounts[$status] }} ({{ number_format($percentage, 1) }}%)">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Status Legend -->
                                        <div class="status-legend mt-1">
                                            @foreach ($statuses as $status)
                                                @if ($statusCounts[$status] > 0)
                                                    <span class="status-badge status-{{ $status }} me-1"
                                                        title="{{ ucfirst($status) }}: {{ $statusCounts[$status] }}">
                                                        {{ $statusCounts[$status] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @push('styles')
                            <style>
                                .order-stats-container {
                                    min-width: 120px;
                                }

                                .total-orders {
                                    font-size: 1.1rem;
                                    color: #2c3e50;
                                }

                                .progress-bar-container {
                                    height: 8px;
                                    background-color: #e9ecef;
                                    border-radius: 4px;
                                    display: flex;
                                    overflow: hidden;
                                }

                                .progress-segment {
                                    height: 100%;
                                    transition: all 0.3s ease;
                                    cursor: help;
                                }

                                .progress-segment:hover {
                                    opacity: 0.8;
                                    transform: scaleY(1.2);
                                }

                                .status-legend {
                                    display: flex;
                                    flex-wrap: wrap;
                                    justify-content: center;
                                    gap: 2px;
                                }

                                .status-badge {
                                    font-size: 0.7rem;
                                    padding: 1px 4px;
                                    border-radius: 3px;
                                    color: white;
                                    font-weight: bold;
                                    cursor: help;
                                }

                                .status-pending {
                                    background-color: #ffc107;
                                }

                                .status-processing {
                                    background-color: #17a2b8;
                                }

                                .status-shipped {
                                    background-color: #007bff;
                                }

                                .status-delivered {
                                    background-color: #28a745;
                                }

                                .status-cancelled {
                                    background-color: #dc3545;
                                }

                                .status-returned {
                                    background-color: #6c757d;
                                }
                            </style>
                            @endpush
                            <td class="text-center">

                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="statusSwitch{{ $customer->id }}" @checked($customer->status === 1)
                                        wire:click="toggleStatus({{ $customer->id }})">
                                </div>

                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- <a href="{{ route('customer.user.edit', $customer->id) }}"
                                        class="btn btn-sm btn-primary" aria-label="Edit customer" wire:navigate>
                                        <i class="bx bx-pencil"></i>
                                    </a> --}}
                                    <button type="button" class="btn btn-sm btn-danger" aria-label="Delete customer"
                                        onclick="if(confirm('Are you sure you want to delete this customer?')) { @this.deletecustomer({{ $customer->id }}) }">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4 mb-3">
                {{ $customers->links() }}
            </div>
        </div>

    </div>
</div>
