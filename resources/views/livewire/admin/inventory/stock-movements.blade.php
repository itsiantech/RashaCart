<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Stock Movements &amp; Analytics</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Movement Type</label>
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="return">Return</option>
                        <option value="damage">Damage</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Timeframe</label>
                    <select wire:model.live="preset" class="form-select">
                        <option value="today">Today</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
                @if ($preset === 'custom')
                    <div class="col-md-3">
                        <label class="form-label">From — To</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" wire:model.live="from" class="form-control">
                            </div>
                            <div class="col-6">
                                <input type="date" wire:model.live="to" class="form-control">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label">Group By</label>
                    <select wire:model.live="groupBy" class="form-select">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="Search product, SKU or notes...">
                </div>
            </div>
        </div>
    </div>

    <!-- Range Totals -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge bg-label-success mb-2"><i class="bx bx-down-arrow-alt"></i></span>
                    <h5 class="mb-0">{{ number_format($totals->in_qty ?? 0) }}</h5>
                    <small class="text-muted">Stock In</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge bg-label-danger mb-2"><i class="bx bx-up-arrow-alt"></i></span>
                    <h5 class="mb-0">{{ number_format($totals->out_qty ?? 0) }}</h5>
                    <small class="text-muted">Stock Out (Sales)</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge bg-label-info mb-2"><i class="bx bx-undo"></i></span>
                    <h5 class="mb-0">{{ number_format($totals->return_qty ?? 0) }}</h5>
                    <small class="text-muted">Returns</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge bg-label-dark mb-2"><i class="bx bx-error-circle"></i></span>
                    <h5 class="mb-0">{{ number_format($totals->damage_qty ?? 0) }}</h5>
                    <small class="text-muted">Damaged</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge bg-label-warning mb-2"><i class="bx bx-edit-alt"></i></span>
                    <h5 class="mb-0">{{ number_format($totals->adjust_qty ?? 0) }}</h5>
                    <small class="text-muted">Adjustment Net</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Outgoing by Category ({{ ucfirst($groupBy) }} range)</h6>
                </div>
                <div class="card-body">
                    @forelse ($byCategory as $row)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $row->label }}</span>
                            <span class="badge bg-label-danger">{{ number_format($row->qty) }} pcs</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No outgoing stock in this range</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Outgoing by Variant Attribute</h6>
                </div>
                <div class="card-body">
                    @forelse ($byAttribute as $row)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $row->label }}</span>
                            <span class="badge bg-label-primary">{{ number_format($row->qty) }} pcs</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No variant sales in this range</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Time Series -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">{{ ucfirst($groupBy) }} Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 420px">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light" style="position: sticky; top: 0">
                                <tr>
                                    <th>Period</th>
                                    <th>In</th>
                                    <th>Out</th>
                                    <th>Damage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($series as $row)
                                    <tr>
                                        <td>{{ $row->bucket }}</td>
                                        <td class="text-success">+{{ number_format($row->in_qty) }}</td>
                                        <td class="text-danger">-{{ number_format($row->out_qty) }}</td>
                                        <td class="text-warning">-{{ number_format($row->damage_qty) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No movements in this range</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Movement Ledger</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="14%">Date</th>
                                    <th width="26%">Product</th>
                                    <th width="10%">Variant</th>
                                    <th width="12%">Type</th>
                                    <th width="10%">Qty</th>
                                    <th width="12%">Stock After</th>
                                    <th width="16%">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movements as $m)
                                    <tr>
                                        <td>{{ $m->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            {{ Str::limit($m->product?->name ?? '-', 35) }}
                                            <br>
                                            <small class="text-muted">{{ $m->notes }}</small>
                                        </td>
                                        <td>{{ $m->variant?->sku ?? '-' }}</td>
                                        <td>
                                            @php
                                                $typeBadge = match ($m->type) {
                                                    'in' => 'bg-success',
                                                    'out' => 'bg-danger',
                                                    'return' => 'bg-info',
                                                    'damage' => 'bg-dark',
                                                    default => 'bg-warning',
                                                };
                                            @endphp
                                            <span class="badge {{ $typeBadge }}">{{ ucfirst($m->type) }}</span>
                                        </td>
                                        <td class="{{ in_array($m->type, ['in', 'return']) || ($m->type === 'adjustment' && $m->quantity > 0) ? 'text-success' : 'text-danger' }} fw-semibold">
                                            {{ in_array($m->type, ['in', 'return']) ? '+' : ($m->type === 'adjustment' ? ($m->quantity > 0 ? '+' : '') : '-') }}{{ number_format(abs($m->quantity)) }}
                                        </td>
                                        <td><strong>{{ $m->current_stock }}</strong></td>
                                        <td>{{ $m->user?->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No movements found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body pt-0">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
