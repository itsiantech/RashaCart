<div class="col-xxl">
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Order Manage</h5>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <!-- Search -->
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search orders...">
                    </div>

                    <!-- Status Filters -->
                    <div class="btn-group flex-wrap" role="group">
                        <button type="button" wire:click="filterByStatus('all')" class="btn {{ $statusFilter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            All ({{ array_sum($statusCounts) ?? 0 }})
                        </button>

                        @foreach (['pending', 'confiemed', 'shipped', 'delivered', 'cancelled', 'returned'] as $status)
                            <button type="button" wire:click="filterByStatus('{{ $status }}')" class="btn {{ $statusFilter == $status ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ ucfirst($status) }} ({{ $statusCounts[$status] ?? 0 }})
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap position-relative" style="min-height: 200px;">
            <!-- Loader -->
            <div wire:loading.flex wire:target="search,statusFilter,updateStatus,filterByStatus" class="position-absolute top-50 start-50 translate-middle p-3 rounded shadow">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <table class="table table-hover mt-3">
                <thead>
                    <tr>
                        <th>INV</th>
                        <th>Customer</th>
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
                            <td>{{ $order->invoice_no }}</td>
                            <td>
                                {{ $order->name }} <br>
                                <span class="text-muted">
                                    <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                                </span><br>
                                <span class="text-muted">Shipping addr: {{ $order->shipping_address }}</span>
                            </td>
                            <td>{{ $order->items->count() }}</td>
                            <td>{{ setting('currency') }} {{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->payment_method }} @if($order->payment_amt) - ${{ $order->payment_amt }} @endif</td>
                            <td>
                                <select wire:change="updateStatus({{ $order->id }}, $event.target.value)" wire:loading.attr="disabled" class="form-select form-select-sm">
                                    @foreach (['pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned'] as $status)
                                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.order.view', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-sm btn-danger">
                                        <i class='bx bxs-printer'></i>
                                    </a>

                                   @php $courier = new \App\Services\CourierService(); @endphp
                                    @if($courier->canSend() && !in_array($order->status, ['shipped', 'delivered']))
                                        <button wire:click="sendToCourier({{ $order->id }})" 
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

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
