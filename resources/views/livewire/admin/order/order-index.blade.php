@php
$courier = new \App\Services\CourierService();
@endphp
<style>
    /* Order Manage: use the full content width instead of the narrow xxl container */
    .content-wrapper > .container-xxl:has(.order-manage-page) {
        max-width: 100% !important;
        width: 100% !important;
    }

    .order-manage-page .order-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.82rem;
    }

    .order-manage-page .order-table > :not(caption) > * > * {
        padding: 0.6rem 0.45rem;
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
    }

    .order-manage-page .order-table thead th {
        white-space: nowrap;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        color: #6b7280;
    }

    .order-manage-page .order-table .form-select {
        min-width: 125px;
    }

    /* Below xl: turn every row into a readable card (no horizontal scrolling) */
    @media (max-width: 1199.98px) {
        .order-manage-page .order-table thead {
            display: none;
        }

        .order-manage-page .order-table,
        .order-manage-page .order-table tbody,
        .order-manage-page .order-table tr,
        .order-manage-page .order-table td {
            display: block;
            width: 100%;
        }

        .order-manage-page .order-table tr {
            border: 1px solid #e5e7eb;
            border-radius: 0.65rem;
            margin-bottom: 0.75rem;
            padding: 0.35rem 0.75rem;
            background: #fff;
        }

        .order-manage-page .order-table td {
            display: flex;
            gap: 0.75rem;
            border: 0;
            padding: 0.35rem 0;
        }

        .order-manage-page .order-table td::before {
            content: attr(data-label);
            flex: 0 0 110px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            padding-top: 0.15rem;
        }

        .order-manage-page .order-table td[data-label="Actions"] {
            align-items: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .order-manage-page .order-table td {
            flex-direction: column;
            gap: 0.15rem;
        }

        .order-manage-page .order-table td::before {
            flex: none;
        }

        .order-manage-page .order-table .form-select {
            min-width: 0;
            width: 100%;
        }
    }
</style>
<div class="col-xxl order-manage-page">
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Order Manage</h5>

                <div class="d-flex flex-wrap gap-2 align-items-center">

                    <!-- Search -->
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <input type="text"
                            class="form-control"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Search orders...">
                    </div>

                    <!-- Status Filters -->
                    <div class="btn-group flex-wrap" role="group">
                        <button type="button"
                            wire:click="filterByStatus('all')"
                            class="btn {{ $statusFilter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            All ({{ array_sum($statusCounts) ?? 0 }})
                        </button>

                        @foreach ($statuses as $status)
                        <button type="button"
                            wire:click="filterByStatus('{{ $status }}')"
                            class="btn {{ $statusFilter == $status ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ str_replace('_', ' ', ucfirst($status)) }} ({{ $statusCounts[$status] ?? 0 }})
                        </button>
                        @endforeach
                    </div>

                    <!-- Channel Filter -->
                    <select class="form-select" style="max-width: 160px;"
                        wire:model.live="channelFilter" aria-label="Filter by channel">
                        <option value="all">All Channels</option>
                        <option value="checkout">Website</option>
                        <option value="cod">Popup COD</option>
                        <option value="whatsapp">Popup WhatsApp</option>
                    </select>

                    @hook('admin.orders.filters', ['af' => $af])

                    <button type="button" class="btn btn-outline-secondary" wire:click="exportCsv">
                        <i class="bx bx-download"></i> Export CSV
                    </button>

                </div>
            </div>
        </div>

        <div class="position-relative" style="min-height: 200px;">

            <!-- Loader -->
            <div wire:loading.flex
                wire:target="search,statusFilter,channelFilter,updateStatus,filterByStatus,sendToCourier,exportCsv"
                class="position-absolute top-50 start-50 translate-middle p-3 rounded shadow">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <table class="table table-hover mt-3 order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Customer</th>
                        <th>Origin</th>
                        <th>Vendor</th>
                        <th>Items</th>
                        <th>Total AMT</th>
                        <th>Payment Method</th>
                        <th>Channel</th>
                        @foreach($addonColumns as $col)
                        <th>{{ $col['label'] }}</th>
                        @endforeach
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <!-- Order ID -->
                        <td data-label="Order ID">
                            <strong>#{{ $order->id }}</strong><br>
                            <small class="text-muted">{{ $order->invoice_no }}</small>
                        </td>

                        <!-- Order Date -->
                        <td data-label="Order Date" title="{{ $order->created_at?->format('d M Y, h:i A') }}">
                            {{ $order->order_date_display }}
                        </td>

                        <!-- Customer -->
                        <td data-label="Customer">
                            {{ $order->name }} <br>
                            <span class="text-muted">
                                <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                            </span><br>
                            @if($order->email)
                            <span class="text-muted">{{ $order->email }}</span><br>
                            @endif
                            <span class="text-muted">
                                Shipping addr: {{ $order->shipping_address }}
                            </span>
                        </td>

                        <!-- Origin -->
                        <td data-label="Origin">
                            @php
                            $originBadge = match ($order->origin) {
                                'Google' => 'bg-danger',
                                'Facebook' => 'bg-primary',
                                'TikTok' => 'bg-dark',
                                'Direct' => 'bg-secondary',
                                default => 'bg-info',
                            };
                            @endphp
                            <span class="badge {{ $originBadge }}">{{ $order->origin }}</span>
                        </td>

                        <!-- Vendor -->
                        <td data-label="Vendor">
                            @php
                            $vendors = $order->items
                            ->map(fn($item) => $item->product?->user)
                            ->filter()
                            ->unique('id');
                            @endphp

                            @forelse($vendors as $vendor)
                            <span class="badge bg-info">{{ $vendor->name }}</span>
                            @empty
                            <span class="badge bg-secondary">Admin</span>
                            @endforelse
                        </td>

                        <!-- Items -->
                        <td data-label="Items">{{ $order->items->count() }}</td>

                        <!-- Total -->
                        <td data-label="Total">{{ setting('currency') }} {{ number_format($order->total_amount, 2) }}</td>

                        <!-- Payment -->
                        <td data-label="Payment Method">
                            {{ $order->payment_method }}
                            @if($order->payment_amt)
                            - {{ setting('currency') }} {{ $order->payment_amt }}
                            @endif
                        </td>

                        <!-- Channel -->
                        <td data-label="Channel">
                            @php
                            $channelBadge = match ($order->channel) {
                                'cod' => ['Popup COD', 'bg-primary'],
                                'whatsapp' => ['Popup WhatsApp', 'bg-success'],
                                default => ['Website', 'bg-secondary'],
                            };
                            @endphp
                            <span class="badge {{ $channelBadge[1] }}">{{ $channelBadge[0] }}</span>
                        </td>

                        @foreach($addonColumns as $col)
                        <td data-label="{{ $col['label'] }}">@hook('admin.orders.cell.'.$col['key'], ['order' => $order])</td>
                        @endforeach

                        <!-- Status -->
                        <td data-label="Order Status">
                            <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                wire:loading.attr="disabled"
                                class="form-select form-select-sm">
                                @foreach (['pending','Call Not Received','completed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Actions -->
                        <td data-label="Actions">
                            <div class="d-flex gap-2 flex-wrap">

                                <!-- View -->
                                <a href="{{ route('admin.order.view', $order->id) }}"
                                    class="btn btn-sm btn-info"
                                    title="View Order">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Edit -->
                                <button type="button"
                                    wire:click="openEdit({{ $order->id }})"
                                    class="btn btn-sm btn-warning"
                                    title="Edit Order">
                                    <i class="bx bx-edit"></i>
                                </button>

                                <!-- Print -->
                                <a href="{{ route('admin.orders.print', $order->id) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-danger"
                                    title="Print Invoice">
                                    <i class='bx bxs-printer'></i>
                                </a>

                                <!-- Delete -->
                                <button type="button"
                                    wire:click="confirmDelete({{ $order->id }})"
                                    class="btn btn-sm btn-danger"
                                    title="Delete Order">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <!-- Courier -->
                                @if($courier->canSend() && !in_array($order->status, ['shipped', 'delivered']))
                                <button type="button"
                                    wire:click="sendToCourier({{ $order->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="sendToCourier({{ $order->id }})"
                                    class="btn btn-sm btn-primary">
                                    <span wire:loading.remove wire:target="sendToCourier({{ $order->id }})">
                                        Send to Courier <i class='bx bxs-plane-alt'></i>
                                    </span>
                                    <span wire:loading wire:target="sendToCourier({{ $order->id }})">
                                        Sending...
                                    </span>
                                </button>
                                @endif

                                <!-- Mark Completed (Vendor Orders Only) -->
                                @if($order->vendor)
                                @if($order->status !== 'completed')
                                <button type="button"
                                    wire:click="markCompleted({{ $order->id }})"
                                    class="btn btn-sm btn-success">
                                    Mark Completed
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-success" disabled>Completed</button>
                                @endif
                                @else
                                <span class="badge bg-warning">Admin Order</span>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 10 + count($addonColumns) }}" class="text-center">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-3">{{ $orders->links() }}</div>
        </div>
    </div>

    {{-- Always in DOM, visibility controlled by Livewire property --}}
    @if($showEditModal)
    <div class="modal-backdrop fade show"></div>
    @endif
    <div class="modal {{ $showEditModal ? 'show' : 'fade' }}"
        tabindex="-1"
        aria-modal="{{ $showEditModal ? 'true' : 'false' }}"
        role="dialog"
        style="{{ $showEditModal ? 'display:block;' : 'display:none;' }}">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Order #{{ $editingOrder }}</h5>
                    <button type="button" class="btn-close" wire:click="closeEdit"></button>
                </div>

                <div class="modal-body">
                    @if($editingOrder) {{-- Only render fields when data is loaded --}}
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <input type="text"
                                class="form-control @error('editForm.name') is-invalid @enderror"
                                wire:model="editForm.name"
                                placeholder="Enter customer name">
                            @error('editForm.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text"
                                class="form-control @error('editForm.phone') is-invalid @enderror"
                                wire:model="editForm.phone"
                                placeholder="Enter phone number">
                            @error('editForm.phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Shipping Address -->
                        <div class="col-12">
                            <label class="form-label">Shipping Address</label>
                            <textarea class="form-control @error('editForm.shipping_address') is-invalid @enderror"
                                wire:model="editForm.shipping_address"
                                placeholder="Enter shipping address"
                                rows="3"></textarea>
                            @error('editForm.shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <input type="text"
                                class="form-control @error('editForm.payment_method') is-invalid @enderror"
                                wire:model="editForm.payment_method"
                                placeholder="e.g., Cash on Delivery">
                            @error('editForm.payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Amount -->
                        <div class="col-md-6">
                            <label class="form-label">Payment Amount</label>
                            <input type="number"
                                class="form-control @error('editForm.payment_amt') is-invalid @enderror"
                                wire:model="editForm.payment_amt"
                                placeholder="0.00"
                                step="0.01">
                            @error('editForm.payment_amt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Total Amount -->
                        <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number"
                                class="form-control @error('editForm.total_amount') is-invalid @enderror"
                                wire:model="editForm.total_amount"
                                placeholder="0.00"
                                step="0.01">
                            @error('editForm.total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Order Status</label>
                            <select class="form-select @error('editForm.status') is-invalid @enderror"
                                wire:model="editForm.status">
                                <option value="">Select Status</option>
                                @foreach (['pending', 'Call Not Received', 'completed', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned', 'Pending Return', 'Pending Cancelled'] as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            @error('editForm.status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEdit">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveEdit">
                        <i class="bx bx-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>