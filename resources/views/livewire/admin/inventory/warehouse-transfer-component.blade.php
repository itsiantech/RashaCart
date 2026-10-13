<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Warehouse Transfer</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-repeat"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total']) }}</h5>
                    </div>
                    <small class="text-muted">Total Transfers</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-cube"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total_qty']) }} pcs</h5>
                    </div>
                    <small class="text-muted">Total Quantity Moved</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-building-house"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['locations']) }}</h5>
                    </div>
                    <small class="text-muted">Distinct Locations</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Transfer Form -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">New Transfer Log</h6>
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
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" wire:model="formQuantity" min="1" class="form-control @error('formQuantity') is-invalid @enderror">
                        @error('formQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">From Location <span class="text-danger">*</span></label>
                            <input type="text" wire:model="fromLocation" class="form-control @error('fromLocation') is-invalid @enderror" placeholder="e.g. Dhaka Godown">
                            @error('fromLocation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">To Location <span class="text-danger">*</span></label>
                            <input type="text" wire:model="toLocation" class="form-control @error('toLocation') is-invalid @enderror" placeholder="e.g. Chattogram Hub">
                            @error('toLocation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea wire:model="note" rows="2" class="form-control" placeholder="Transfer reason or remarks..."></textarea>
                    </div>

                    <button type="button" wire:click="saveTransfer" class="btn btn-primary w-100">
                        <i class="bx bx-send me-1"></i> Log Transfer
                    </button>
                    <small class="text-muted d-block mt-2">
                        This records the movement log (From → To). Ledger stock stays unchanged.
                    </small>
                </div>
            </div>
        </div>

        <!-- Transfer History -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Transfer History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="13%">Date</th>
                                    <th width="27%">Product</th>
                                    <th width="10%">Variant</th>
                                    <th width="8%">Qty</th>
                                    <th width="22%">Route</th>
                                    <th width="10%">Note</th>
                                    <th width="10%">By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transfers as $t)
                                    <tr>
                                        <td>{{ $t->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ Str::limit($t->product?->name ?? '-', 35) }}</td>
                                        <td>{{ $t->variant?->sku ?? '-' }}</td>
                                        <td><strong>{{ number_format($t->quantity) }}</strong></td>
                                        <td>
                                            <span class="badge bg-label-secondary">{{ $t->from_location }}</span>
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="badge bg-label-primary">{{ $t->to_location }}</span>
                                        </td>
                                        <td>{{ Str::limit($t->note ?? '-', 20) }}</td>
                                        <td>{{ $t->transferredBy?->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No transfers found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
