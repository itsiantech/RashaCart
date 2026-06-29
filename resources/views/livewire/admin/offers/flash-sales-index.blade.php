{{-- resources/views/livewire/admin/flash-sales/index.blade.php --}}

<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Flash Sales</h4>
            <p class="text-muted mb-0">Manage your flash sale campaigns</p>
        </div>
        @can('flash.sales.create')
            <a wire:navigate href="{{ route('admin.flash.sales.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Create Flash Sale
            </a>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Search flash sales..."
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="live">Live</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Products</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($flashSales as $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $sale->title }}</strong>
                                @if($sale->description)
                                    <br><small class="text-muted">{{ Str::limit($sale->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $sale->items_count }} products</span>
                            </td>
                            <td>{{ $sale->start_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $sale->end_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @if($sale->status === 'live')
                                    <span class="badge bg-success">
                                        <i class="bx bx-radio-circle-marked bx-flashing me-1"></i> Live
                                    </span>
                                @elseif($sale->status === 'upcoming')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bx bx-time me-1"></i> Upcoming
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bx bx-x-circle me-1"></i> Expired
                                    </span>
                                @endif
                            </td>
                            <td>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            wire:click="toggleActive({{ $sale->id }})"
                                            {{ $sale->is_active ? 'checked' : '' }}
                                        />
                                    </div>
                                    <span class="badge bg-label-{{ $sale->is_active ? 'success' : 'secondary' }}">
                                        {{ $sale->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                            </td>
                            {{-- Actions --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                        <a wire:navigate href="{{ route('admin.flash.sales.view', $sale->id) }}" title="View">
                                            <i class="tf-icons bx bx-show-alt" style="font-size:20px; color:#03c3ec;"></i>
                                        </a>

                                        <a wire:navigate href="{{ route('admin.flash.sales.edit', $sale->id) }}" title="Edit">
                                            <i class="tf-icons bx bx-edit" style="font-size:20px; color:#696cff;"></i>
                                        </a>
                                        <a wire:navigate href="{{ route('admin.flash.sales.stock.manage', $sale->id) }}" title="Manage Stock">
                                            <i class="menu-icon tf-icons bx bx-key"></i>
                                        </a>

                                        <a href="javascript:void(0);" wire:click="confirmDelete({{ $sale->id }})" title="Delete">
                                            <i class="tf-icons bx bx-trash" style="font-size:20px; color:#ff3e1d;"></i>
                                        </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bx bx-folder-open fs-3 d-block mb-2"></i>
                                No flash sales found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $flashSales->links() }}
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger"><i class="bx bx-error-circle me-1"></i> Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $deleteName }}</strong>? This will also remove all products in this campaign.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" wire:click="cancelDelete" class="btn btn-outline-secondary btn-sm">Cancel</button>
                    <button type="button" wire:click="delete" class="btn btn-danger btn-sm">
                        <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-1"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        Livewire.on('show-delete-modal', () => deleteModal.show());
        Livewire.on('hide-delete-modal', () => deleteModal.hide());
    });
</script>
@endpush