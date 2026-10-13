<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Low Stock Alerts</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-bell"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total']) }}</h5>
                    </div>
                    <small class="text-muted">Total Alerts</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x-circle"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['out']) }}</h5>
                    </div>
                    <small class="text-muted">Out of Stock</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-dark"><i class="bx bx-error"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['critical']) }}</h5>
                    </div>
                    <small class="text-muted">Critical (≤50% of threshold)</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-down-arrow-alt"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['low']) }}</h5>
                    </div>
                    <small class="text-muted">Low</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="Search product name or SKU...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="severityFilter" class="form-select">
                        <option value="">All Severity</option>
                        <option value="out">Out of Stock</option>
                        <option value="critical">Critical</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Reorder Alerts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">SKU</th>
                            <th width="28%">Product</th>
                            <th width="12%">Category</th>
                            <th width="11%">Available</th>
                            <th width="10%">Reserved</th>
                            <th width="12%">Severity</th>
                            <th width="10%">Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $i => $item)
                            <tr @class(['table-danger' => $item['level'] === 'out'])>
                                <td>{{ ($items->currentPage() - 1) * $items->perPage() + $i + 1 }}</td>
                                <td><strong>{{ $item['sku'] }}</strong></td>
                                <td>
                                    {{ Str::limit($item['name'], 45) }}
                                    @if ($item['is_variant'])
                                        <span class="badge bg-label-secondary ms-1">Variant</span>
                                    @endif
                                </td>
                                <td>{{ $item['category'] ?? '-' }}</td>
                                <td><strong>{{ number_format($item['available']) }}</strong> pcs</td>
                                <td><span class="badge bg-label-info">{{ number_format($item['reserved']) }}</span></td>
                                <td><span class="badge {{ $item['badge'] }}">{{ $item['label'] }}</span></td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center">
                                        <input type="number" min="0" class="form-control form-control-sm"
                                            style="width: 70px" wire:model="thresholds.{{ $item['key'] }}">
                                        <button type="button" class="btn btn-sm btn-label-primary px-2"
                                            wire:click="saveThreshold('{{ $item['key'] }}')" title="Save reorder level">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bx bx-check-circle text-success me-1"></i> No low stock alerts — everything is above reorder level!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
