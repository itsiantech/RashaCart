<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Stock Adjustment (Manual)</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-edit-alt"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total']) }}</h5>
                    </div>
                    <small class="text-muted">Total Adjustments</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-plus-circle"></i></span>
                        </div>
                        <h5 class="mb-0">+{{ number_format($metrics['increase_qty']) }}</h5>
                    </div>
                    <small class="text-muted">Increased Units</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-minus-circle"></i></span>
                        </div>
                        <h5 class="mb-0">-{{ number_format($metrics['decrease_qty']) }}</h5>
                    </div>
                    <small class="text-muted">Decreased Units</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-transfer"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['net']) }}</h5>
                    </div>
                    <small class="text-muted">Net Change</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Adjustment Form -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Manual Stock Adjustment</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select wire:model="formProductId" class="form-select @error('formProductId') is-invalid @enderror">
                            <option value="">-- Select Product --</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                            @endforeach
                        </select>
                        @error('formProductId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if ($formVariants->count())
                        <div class="mb-3">
                            <label class="form-label">Variant</label>
                            <select wire:model="formVariantId" class="form-select">
                                <option value="">-- Main Product --</option>
                                @foreach ($formVariants as $v)
                                    <option value="{{ $v->id }}">{{ $v->sku }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Direction <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="direction" value="increase" id="dir-in">
                                <label class="form-check-label text-success" for="dir-in">Increase (+)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="direction" value="decrease" id="dir-out">
                                <label class="form-check-label text-danger" for="dir-out">Decrease (−)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" wire:model="adjustQuantity" min="1" class="form-control @error('adjustQuantity') is-invalid @enderror">
                        @error('adjustQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason (Mandatory) <span class="text-danger">*</span></label>
                        <textarea wire:model="reason" rows="3" class="form-control @error('reason') is-invalid @enderror"
                            placeholder="e.g. physical count correction, found in warehouse..."></textarea>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="button" wire:click="saveAdjustment" class="btn btn-primary w-100">
                        <i class="bx bx-save me-1"></i> Apply Adjustment
                    </button>
                    <small class="text-muted d-block mt-2">
                        Every adjustment is recorded with the reason and your user ID in the audit trail.
                    </small>
                </div>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Adjustment History (Audit Trail)</h6>
                    <select wire:model.live="typeFilter" class="form-select form-select-sm w-auto">
                        <option value="">All</option>
                        <option value="increase">Increase only</option>
                        <option value="decrease">Decrease only</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="14%">Date</th>
                                    <th width="28%">Product</th>
                                    <th width="10%">Variant</th>
                                    <th width="10%">Change</th>
                                    <th width="24%">Reason</th>
                                    <th width="14%">Adjusted By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($history as $h)
                                    <tr>
                                        <td>{{ $h->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ Str::limit($h->product?->name ?? '-', 35) }}</td>
                                        <td>{{ $h->variant?->sku ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $h->quantity_change > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $h->quantity_change > 0 ? '+' : '' }}{{ $h->quantity_change }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($h->reason, 50) }}</td>
                                        <td>{{ $h->adjustedBy?->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No adjustments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $history->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
