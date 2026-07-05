{{-- resources/views/livewire/admin/flash-sales/stock-manage.blade.php --}}

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Manage Stock</h4>
            <p class="text-muted mb-0">Campaign: <strong>{{ $flashSale->title }}</strong></p>
        </div>
        <a wire:navigate href="{{ route('admin.flash.sales.view', $flashSale->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Campaign
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Sale Price</th>
                        <th>Current Stock</th>
                        <th>Update Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flashSale->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" width="40" height="40" class="rounded object-fit-cover" />
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                            <i class="bx bx-image text-muted"></i>
                                        </div>
                                    @endif
                                    <span class="fw-semibold small">{{ $item->product->name }}</span>
                                </div>
                            </td>
                            <td class="text-success fw-bold">৳{{ number_format($item->discounted_price, 2) }}</td>
                            <td>
                                <span class="badge bg-label-{{ $item->product->stock > 10 ? 'success' : ($item->product->stock > 0 ? 'warning' : 'danger') }} fs-6">
                                    {{ $item->product->stock }}
                                </span>
                            </td>
                            <td style="width: 150px;">
                                <input
                                    type="number"
                                    wire:model="stocks.{{ $item->id }}"
                                    class="form-control form-control-sm"
                                    min="0"
                                    placeholder="Stock"
                                />
                            </td>
                            <td>
                                <button
                                    wire:click="updateStock({{ $item->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="updateStock({{ $item->id }})"
                                    class="btn btn-primary btn-sm"
                                >
                                    <span wire:loading wire:target="updateStock({{ $item->id }})" class="spinner-border spinner-border-sm me-1"></span>
                                    <i wire:loading.remove wire:target="updateStock({{ $item->id }})" class="bx bx-save me-1"></i>
                                    Update
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No products in this campaign.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
