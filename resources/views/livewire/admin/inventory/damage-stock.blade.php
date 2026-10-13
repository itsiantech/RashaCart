<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Damage &amp; Returned Stock</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error-circle"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total_qty']) }} pcs</h5>
                    </div>
                    <small class="text-muted">Total Damaged (৳{{ number_format($metrics['total_value'], 2) }})</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['pending']) }}</h5>
                    </div>
                    <small class="text-muted">Pending Action</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-dark"><i class="bx bx-trash"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['scrapped']) }}</h5>
                    </div>
                    <small class="text-muted">Scrapped</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-truck"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['returned']) }}</h5>
                    </div>
                    <small class="text-muted">Returned to Supplier</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-wrench"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['refurbished']) }}</h5>
                    </div>
                    <small class="text-muted">Refurbished</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Report Damage Form -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Report Damaged Stock</h6>
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

                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea wire:model="formReason" rows="3" class="form-control @error('formReason') is-invalid @enderror"
                            placeholder="e.g. expired, damaged in transit, water damage..."></textarea>
                        @error('formReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="button" wire:click="saveDamage" class="btn btn-danger w-100">
                        <i class="bx bx-error-circle me-1"></i> Log Damage
                    </button>
                    <small class="text-muted d-block mt-2">
                        Damaged units are deducted from sellable stock immediately.
                    </small>
                </div>
            </div>
        </div>

        <!-- Damage List -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Damage Log</h6>
                    <select wire:model.live="statusFilter" class="form-select form-select-sm w-auto">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="scrapped">Scrapped</option>
                        <option value="returned_to_supplier">Returned to Supplier</option>
                        <option value="refurbished">Refurbished</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">Date</th>
                                    <th width="22%">Product</th>
                                    <th width="9%">Variant</th>
                                    <th width="8%">Qty</th>
                                    <th width="18%">Reason</th>
                                    <th width="12%">Status</th>
                                    <th width="19%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>{{ Str::limit($item->product?->name ?? '-', 30) }}</td>
                                        <td>{{ $item->variant?->sku ?? '-' }}</td>
                                        <td><strong>{{ $item->quantity }}</strong></td>
                                        <td>
                                            {{ Str::limit($item->reason, 45) }}
                                            <br><small class="text-muted">by {{ $item->reportedBy?->name ?? 'System' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $badge = match ($item->action_status) {
                                                    'pending' => 'bg-warning',
                                                    'scrapped' => 'bg-dark',
                                                    'returned_to_supplier' => 'bg-info',
                                                    'refurbished' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $item->action_status)) }}</span>
                                            @if ($item->action_status === 'refurbished' && $item->restocked)
                                                <br><small class="text-success">Restocked</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->action_status === 'pending')
                                                <select wire:model="actionStatus.{{ $item->id }}" class="form-select form-select-sm mb-1">
                                                    <option value="">-- Action --</option>
                                                    <option value="scrapped">Scrap</option>
                                                    <option value="returned_to_supplier">Return to Supplier</option>
                                                    <option value="refurbished">Refurbish (+restock)</option>
                                                </select>
                                                <div class="d-flex gap-1">
                                                    <input type="text" wire:model="actionNote.{{ $item->id }}" class="form-control form-control-sm" placeholder="Note (optional)">
                                                    <button type="button" wire:click="applyAction({{ $item->id }})" class="btn btn-sm btn-primary px-2">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <small class="text-muted">{{ $item->action_note ?? '-' }}</small>
                                                @if ($item->action_at)
                                                    <br><small class="text-muted">{{ $item->action_at->format('M d, Y') }}</small>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No damage records found</td>
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
    </div>
</div>
