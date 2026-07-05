{{--
    IMPORTANT: This blade file works in tandem with the Livewire component.
    To guarantee that vendor orders never appear, make sure the component's render()
    method uses `whereNull('vendor_id')` (or equivalent). The @if condition below
    is an extra safety net.
--}}

<div class="col-xxl">
    <div class="card">

        <!-- Card Header -->
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Inhouse Order Manage</h5>

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

                        @foreach (['pending','Call Not Received','completed', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled'] as $status)
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

        <!-- Table Section -->
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
                        <th>Items</th>
                        <th>Total AMT</th>
                        <th>Payment Method</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                    {{-- Only show orders that truly belong to the system (admin) --}}
                    @if(is_null($order->vendor_id))
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

                        <!-- Items -->
                        <td>{{ $order->items->count() }}</td>

                        <!-- Total -->
                        <td>
                            {{ setting('currency') }}
                            {{ number_format($order->total_amount, 2) }}
                        </td>

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
                                <option value="{{ $status }}"
                                    {{ $order->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach

                            </select>
                        </td>

                        <!-- Actions -->
                        <td>
                            <div class="d-flex gap-2">

                                <!-- View -->
                                <a href="{{ route('admin.order.view', $order->id) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Print -->
                                <a href="{{ route('admin.orders.print', $order->id) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-danger">
                                    <i class='bx bxs-printer'></i>
                                </a>

                                <!-- Courier -->
                                @php
                                $pathaoDriver = new \App\Services\Couriers\PathaoDriver();
                                $steadfastDriver = new \App\Services\Couriers\SteadfastDriver();
                                @endphp

                                @if(!in_array($order->status, ['shipped', 'delivered']))

                                @if($pathaoDriver->canSend())
                                <button wire:click="sendToPathao('{{ $order->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="sendToPathao({{ $order->id }})"
                                    class="btn btn-sm btn-warning">
                                    <span wire:loading.remove wire:target="sendToPathao({{ $order->id }})">
                                        Pathao <i class='bx bxs-plane-alt'></i>
                                    </span>
                                    <span wire:loading wire:target="sendToPathao({{ $order->id }})">
                                        Sending...
                                    </span>
                                </button>
                                @endif

                                @if($steadfastDriver->canSend())
                                <button wire:click="sendToSteadfast('{{ $order->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="sendToSteadfast({{ $order->id }})"
                                    class="btn btn-sm btn-success">
                                    <span wire:loading.remove wire:target="sendToSteadfast({{ $order->id }})">
                                        Steadfast <i class='bx bxs-plane-alt'></i>
                                    </span>
                                    <span wire:loading wire:target="sendToSteadfast({{ $order->id }})">
                                        Sending...
                                    </span>
                                </button>
                                @endif

                                @endif

                                <!-- Mark Completed -->
                                @if($order->status !== 'confirmed')
                                <button wire:click="markCompleted({{ $order->id }})"
                                    class="btn btn-sm btn-success">
                                    Mark Completed
                                </button>
                                @else
                                <button class="btn btn-sm btn-success" disabled>
                                    Completed
                                </button>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @endif {{-- end of in‑house check --}}
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-3">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
    <div
        x-data="{ show: false, message: '', type: '' }"
        x-on:success.window="message = $event.detail.message; type = 'success'; show = true; setTimeout(() => show = false, 4000)"
        x-on:error.window="message = $event.detail.message; type = 'danger'; show = true; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        x-cloak
        :class="`alert alert-${type} shadow`"
        style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        <span x-text="message"></span>
    </div>
</div>