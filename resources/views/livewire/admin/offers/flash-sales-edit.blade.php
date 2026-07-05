{{-- resources/views/livewire/admin/flash-sales/edit.blade.php --}}

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Flash Sale</h4>
            <p class="text-muted mb-0">Update campaign: <strong>{{ $flashSale->title }}</strong></p>
        </div>
        <a wire:navigate href="{{ route('admin.flash.sales.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form wire:submit="save">
        <div class="row g-4">

            {{-- Left: Campaign Details --}}
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bx bx-info-circle me-2 text-primary"></i>Campaign Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" />
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea wire:model="description" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" wire:model="start_at" class="form-control @error('start_at') is-invalid @enderror" />
                                @error('start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" wire:model="end_at" class="form-control @error('end_at') is-invalid @enderror" />
                                @error('end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActive" />
                                <label class="form-check-label fw-semibold" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Countdown --}}
                <div class="card mb-4 border-primary">
                    <div class="card-body">
                        <p class="text-muted small mb-2"><i class="bx bx-time-five me-1"></i> Live Countdown</p>
                        <div class="d-flex gap-3 justify-content-center py-2">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-primary" id="cd-days">--</div>
                                <small class="text-muted">Days</small>
                            </div>
                            <div class="fs-3 fw-bold text-muted">:</div>
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-primary" id="cd-hours">--</div>
                                <small class="text-muted">Hours</small>
                            </div>
                            <div class="fs-3 fw-bold text-muted">:</div>
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-primary" id="cd-mins">--</div>
                                <small class="text-muted">Mins</small>
                            </div>
                            <div class="fs-3 fw-bold text-muted">:</div>
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-primary" id="cd-secs">--</div>
                                <small class="text-muted">Secs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Products --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bx bx-package me-2 text-primary"></i>Products</h6>
                        <span class="badge bg-primary">{{ count($selectedItems) }} added</span>
                    </div>
                    <div class="card-body">
                        @error('selectedItems') <div class="alert alert-danger py-2 small">{{ $message }}</div> @enderror

                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="productSearch"
                                    class="form-control"
                                    placeholder="Search products to add..."
                                />
                            </div>

                            @if($productSearch && $this->searchResults->count())
                                <div class="position-absolute w-100 bg-white border rounded shadow-sm mt-1" style="z-index:1000; max-height:240px; overflow-y:auto;">
                                    @foreach($this->searchResults as $product)
                                        <div
                                            wire:click="addProduct({{ $product->id }})"
                                            class="d-flex align-items-center gap-2 p-2 border-bottom"
                                            style="cursor:pointer;"
                                        >
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" width="36" height="36" class="rounded object-fit-cover" />
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                    <i class="bx bx-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-fill">
                                                <div class="small fw-semibold">{{ $product->name }}</div>
                                                <div class="small text-muted">৳{{ number_format($product->price, 2) }}</div>
                                            </div>
                                            <i class="bx bx-plus-circle text-primary"></i>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if(count($selectedItems))
                            <div style="max-height:460px; overflow-y:auto;">
                                @foreach($selectedItems as $productId => $item)
                                    <div class="border rounded p-2 mb-2 {{ $item['is_active'] ? 'bg-light' : 'bg-white opacity-75' }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="small fw-semibold">{{ $item['name'] }}</div>
                                            <div class="d-flex gap-1">
                                                <button
                                                    type="button"
                                                    wire:click="toggleItemActive({{ $productId }})"
                                                    class="btn btn-sm p-0 px-1 {{ $item['is_active'] ? 'text-success' : 'text-muted' }}"
                                                    title="{{ $item['is_active'] ? 'Deactivate' : 'Activate' }}"
                                                >
                                                    <i class="bx {{ $item['is_active'] ? 'bx-toggle-right' : 'bx-toggle-left' }} fs-6"></i>
                                                </button>
                                                <button type="button" wire:click="removeProduct({{ $productId }})" class="btn btn-sm text-danger p-0 px-1">
                                                    <i class="bx bx-x fs-6"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row g-2 align-items-center">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Discount %</label>
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        wire:model.live="selectedItems.{{ $productId }}.discount_percent"
                                                        class="form-control"
                                                        min="1" max="99" step="0.5"
                                                    />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Sale Price</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="text" class="form-control bg-white" value="{{ number_format($item['discounted_price'], 2) }}" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            Original: <span class="text-decoration-line-through">৳{{ number_format($item['original_price'], 2) }}</span>
                                            &nbsp;<span class="text-success fw-semibold">Save ৳{{ number_format($item['original_price'] - $item['discounted_price'], 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-package fs-2 d-block mb-2"></i>
                                No products added yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                <i wire:loading.remove wire:target="save" class="bx bx-save me-1"></i>
                Save Changes
            </button>
            <a wire:navigate href="{{ route('admin.flash.sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function startCountdown(endTime) {
        const interval = setInterval(() => {
            const diff = new Date(endTime) - new Date();
            if (diff <= 0) { clearInterval(interval); return; }
            const days  = Math.floor(diff / 86400000);
            const hours = Math.floor((diff % 86400000) / 3600000);
            const mins  = Math.floor((diff % 3600000) / 60000);
            const secs  = Math.floor((diff % 60000) / 1000);
            document.getElementById('cd-days').innerText  = String(days).padStart(2,'0');
            document.getElementById('cd-hours').innerText = String(hours).padStart(2,'0');
            document.getElementById('cd-mins').innerText  = String(mins).padStart(2,'0');
            document.getElementById('cd-secs').innerText  = String(secs).padStart(2,'0');
        }, 1000);
    }

    document.addEventListener('livewire:initialized', () => {
        startCountdown('{{ $flashSale->end_at->toIso8601String() }}');
    });
</script>
@endpush
