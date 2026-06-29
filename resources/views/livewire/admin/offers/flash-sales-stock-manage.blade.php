{{-- resources/views/livewire/admin/offers/flash-sales-stock-manage.blade.php --}}

<div>
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Manage Stock</h4>
            <p class="text-muted mb-0">
                Campaign: <strong>{{ $flashSale->title }}</strong>
                &nbsp;|&nbsp;
                @if($flashSale->status === 'live')
                <span class="badge bg-success"><i class="bx bx-radio-circle-marked bx-flashing me-1"></i> Live</span>
                @elseif($flashSale->status === 'upcoming')
                <span class="badge bg-warning text-dark"><i class="bx bx-time me-1"></i> Upcoming</span>
                @else
                <span class="badge bg-secondary"><i class="bx bx-x-circle me-1"></i> Expired</span>
                @endif
            </p>
        </div>
        <a wire:navigate href="{{ route('admin.flash.sales.index') }}" class="btn btn-outline-secondary">
            <i class="tf-icons bx bx-arrow-back me-1"></i> Back
        </a>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Total Products</p>
                    <h4 class="mb-0">{{ $flashSale->items->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Out of Stock</p>
                    <h4 class="mb-0 text-danger">
                        {{ $flashSale->items->filter(fn($i) => ($i->product->stock ?? 0) <= 0)->count() }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">Low Stock (≤10)</p>
                    <h4 class="mb-0 text-warning">
                        {{ $flashSale->items->filter(fn($i) => ($i->product->stock ?? 0) > 0 && ($i->product->stock ?? 0) <= 10)->count() }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">In Stock</p>
                    <h4 class="mb-0 text-success">
                        {{ $flashSale->items->filter(fn($i) => ($i->product->stock ?? 0) > 10)->count() }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Table --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">
                <i class="tf-icons bx bx-package me-2 text-primary"></i>
                Product Stock Levels
            </h6>
            <small class="text-muted">Update each product's stock individually</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Sale Price</th>
                        <th>Discount</th>
                        <th>Current Stock</th>
                        <th style="width: 180px;">New Stock</th>
                        <th>Update</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flashSale->items as $item)
                    @php $currentStock = $item->product->stock ?? 0; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- Product --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($item->product->thumbnail ?? false)
                                <img
                                    src="{{ asset('storage/' . $item->product->thumbnail) }}"
                                    width="40" height="40"
                                    class="rounded"
                                    style="object-fit:cover;" />
                                @else
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="bx bx-image"></i>
                                    </span>
                                </div>
                                @endif
                                <div>
                                    <span class="fw-semibold d-block" style="font-size:13px;">{{ $item->product->name }}</span>
                                    <small class="text-muted">ID: {{ $item->product->id }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Sale Price --}}
                        <td>
                            <span class="fw-bold text-success">৳{{ number_format($item->discounted_price, 2) }}</span>
                            <br>
                            <small class="text-muted text-decoration-line-through">৳{{ number_format($item->product->price, 2) }}</small>
                        </td>

                        {{-- Discount --}}
                        <td>
                            <span class="badge bg-label-danger">{{ number_format($item->discount_percent, 0) }}% OFF</span>
                        </td>

                        {{-- Current Stock Badge --}}
                        <td>
                            @if($currentStock <= 0)
                                <span class="badge bg-danger">Out of Stock</span>
                                @elseif($currentStock <= 10)
                                    <span class="badge bg-warning text-dark">
                                    <i class="bx bx-error-circle me-1"></i>{{ $currentStock }} Low
                                    </span>
                                    @else
                                    <span class="badge bg-success">
                                        <i class="bx bx-check-circle me-1"></i>{{ $currentStock }}
                                    </span>
                                    @endif
                        </td>

                        {{-- Stock Input --}}
                        <td>
                            <div class="input-group input-group-sm">
                                <button
                                    type="button"
                                    onclick="decrementStock({{ $item->id }})"
                                    class="btn btn-outline-secondary px-2">
                                    <i class="bx bx-minus"></i>
                                </button>
                                <input
                                    type="number"
                                    wire:model="stocks.{{ $item->id }}"
                                    id="stock-input-{{ $item->id }}"
                                    class="form-control text-center"
                                    min="0"
                                    style="max-width:70px;" />
                                <button
                                    type="button"
                                    onclick="incrementStock({{ $item->id }})"
                                    class="btn btn-outline-secondary px-2">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </div>
                        </td>

                        {{-- Save Button --}}
                        <td>
                            <button
                                wire:click="updateStock({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="updateStock({{ $item->id }})"
                                class="btn btn-info btn-sm">
                                <span wire:loading wire:target="updateStock({{ $item->id }})">
                                    <i class="bx bx-loader-alt bx-spin"></i>
                                </span>
                                <span wire:loading.remove wire:target="updateStock({{ $item->id }})">
                                    <i class="tf-icons bx bx-save me-1"></i> Save
                                </span>
                            </button>
                        </td>
                        <td>
                            <a href="javascript:void(0);"
                                wire:click="confirmRemoveItem({{ $item->id }})"
                                title="Remove from Flash Sale">
                                <button type="button" class="btn btn-danger" style="padding: 12px 10px;">Delete</button>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bx bx-package fs-2 d-block mb-2"></i>
                            No products found in this campaign.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- Remove Product Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bx bx-error-circle me-1"></i> Remove Product
                    </h5>
                </div>
                <div class="modal-body">
                    <p>Remove <strong>{{ $deleteItemName }}</strong> from this flash sale campaign?</p>
                    <small class="text-muted">This only removes it from the campaign, the product itself won't be deleted.</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" wire:click="cancelRemove" class="btn btn-outline-secondary btn-sm">
                        Cancel
                    </button>
                    <button type="button" wire:click="removeItem" class="btn btn-danger btn-sm">
                        <span wire:loading wire:target="removeItem" class="spinner-border spinner-border-sm me-1"></span>
                        <span wire:loading.remove wire:target="removeItem">Remove</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function incrementStock(itemId) {
        const input = document.getElementById('stock-input-' + itemId);
        if (input) {
            input.value = parseInt(input.value || 0) + 1;
            input.dispatchEvent(new Event('input'));
        }
    }

    function decrementStock(itemId) {
        const input = document.getElementById('stock-input-' + itemId);
        if (input) {
            const val = parseInt(input.value || 0);
            if (val > 0) {
                input.value = val - 1;
                input.dispatchEvent(new Event('input'));
            }
        }
    }

    document.addEventListener('livewire:initialized', () => {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        Livewire.on('show-delete-modal', () => deleteModal.show());
        Livewire.on('hide-delete-modal', () => deleteModal.hide());
    });
</script>
@endpush