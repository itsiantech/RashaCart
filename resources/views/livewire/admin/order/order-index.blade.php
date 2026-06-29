<div class="col-xxl">
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

                        @foreach (['pending','Call Not Received', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled'] as $status)
                        <button type="button"
                            wire:click="filterByStatus('{{ $status }}')"
                            class="btn {{ $statusFilter == $status ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ ucfirst($status) }} ({{ $statusCounts[$status] ?? 0 }})
                        </button>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap position-relative" style="min-height: 200px;">

            <!-- Loader -->
            <div wire:loading.flex
                wire:target="search,statusFilter,updateStatus,filterByStatus,sendToCourier"
                class="position-absolute top-50 start-50 translate-middle p-3 rounded shadow">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <table class="table table-hover mt-3">
                <thead>
                    <tr>
                        <th>INV</th>
                        <th>Customer</th>
                        <th>Vendor</th>
                        <th>Items</th>
                        <th>Total AMT</th>
                        <th>Payment Method</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <!-- Invoice -->
                        <td>{{ $order->invoice_no }}</td>

                        <!-- Customer -->
                        <td>
                            {{ $order->name }} <br>
                            <span class="text-muted">
                                <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                            </span><br>
                            <span class="text-muted">
                                Shipping addr: {{ $order->shipping_address }}
                            </span>
                        </td>

                        <!-- Vendor -->
                        <td>
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
                        <td>{{ $order->items->count() }}</td>

                        <!-- Total -->
                        <td>{{ setting('currency') }} {{ number_format($order->total_amount, 2) }}</td>

                        <!-- Payment -->
                        <td>
                            {{ $order->payment_method }}
                            @if($order->payment_amt)
                            - {{ setting('currency') }} {{ $order->payment_amt }}
                            @endif
                        </td>

                        <!-- Status -->
                        <td>
                            <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                wire:loading.attr="disabled"
                                class="form-select form-select-sm">
                                @foreach (['pending','Call Not Received','completed', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Actions -->
                        <td>
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
                                @php $courier = new \App\Services\CourierService(); @endphp
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
                        <td colspan="8" class="text-center">No orders found.</td>
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