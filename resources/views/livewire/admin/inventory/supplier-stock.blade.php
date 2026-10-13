<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Supplier Stock (Purchase Orders)</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-receipt"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total_pos']) }}</h5>
                    </div>
                    <small class="text-muted">Total Purchase Orders</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-cube"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total_qty']) }}</h5>
                    </div>
                    <small class="text-muted">Total Received Quantity</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-taka"></i></span>
                        </div>
                        <h5 class="mb-0">৳{{ number_format($metrics['total_value'], 2) }}</h5>
                    </div>
                    <small class="text-muted">Total Purchase Value (COGS)</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-credit-card"></i></span>
                        </div>
                        <h5 class="mb-0">৳{{ number_format($metrics['total_due'], 2) }}</h5>
                    </div>
                    <small class="text-muted">Total Due</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="PO number or supplier...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select wire:model.live="supplierId" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date Range</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" wire:model.live="from" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="date" wire:model.live="to" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Purchase Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="4%"></th>
                            <th width="12%">PO Number</th>
                            <th width="18%">Supplier</th>
                            <th width="11%">Date</th>
                            <th width="11%">Received Qty</th>
                            <th width="12%">Purchase Cost</th>
                            <th width="12%">Net Total</th>
                            <th width="10%">Due</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $po)
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-sm btn-label-secondary p-0 px-1"
                                        wire:click="toggleExpand({{ $po->id }})">
                                        <i class="bx {{ in_array($po->id, $expanded) ? 'bx-chevron-up' : 'bx-chevron-down' }}"></i>
                                    </button>
                                </td>
                                <td><strong>{{ $po->invoice_no }}</strong></td>
                                <td>{{ $po->supplier?->name ?? '-' }}</td>
                                <td>{{ optional($po->purchase_date)->format('M d, Y') }}</td>
                                <td>{{ number_format($po->total_quantity) }} pcs</td>
                                <td>৳{{ number_format($po->purchase_cost, 2) }}</td>
                                <td>৳{{ number_format($po->net_total, 2) }}</td>
                                <td>
                                    <span class="badge {{ $po->due_amount > 0 ? 'bg-danger' : 'bg-success' }}">
                                        ৳{{ number_format($po->due_amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @php $statusClass = match ($po->status) { 'received' => 'bg-success', 'ordered' => 'bg-warning', default => 'bg-secondary' }; @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($po->status ?? '-') }}</span>
                                </td>
                            </tr>
                            @if (in_array($po->id, $expanded))
                                <tr>
                                    <td colspan="9" class="bg-light">
                                        <h6 class="mb-2">PO Items — {{ $po->invoice_no }}</h6>
                                        <table class="table table-sm table-bordered mb-0 bg-white">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Variant / Specs</th>
                                                    <th width="12%">Quantity</th>
                                                    <th width="15%">Unit Cost</th>
                                                    <th width="15%">Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($po->items as $item)
                                                    <tr>
                                                        <td>{{ $item->product?->name ?? $item->product_name }}</td>
                                                        <td>{{ $item->variant?->sku ?? ($item->specs ?? '-') }}</td>
                                                        <td>{{ number_format($item->quantity) }}</td>
                                                        <td>৳{{ number_format($item->unit_cost, 2) }}</td>
                                                        <td>৳{{ number_format($item->total_cost, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">No items</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No purchase orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</div>
