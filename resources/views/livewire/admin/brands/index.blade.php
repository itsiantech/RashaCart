<div>
    <div class="col-xxl">
        <!-- Brand Form -->
        @if($showForm)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ $isEdit ? 'Edit Brand' : 'Add New Brand' }}</h5>
                <button type="button" class="btn-close" wire:click="cancelForm"></button>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveBrand">
                    <!-- Name Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="name">Brand Name *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    wire:model.blur="name"
                                    placeholder="Enter brand name"
                                    autofocus>
                            </div>
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Slug Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="slug">Slug *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-link"></i></span>
                                <input
                                    type="text"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    id="slug"
                                    wire:model="slug"
                                    placeholder="brand-slug">
                            </div>
                            <small class="text-muted">Used in URLs. Auto-generated from name.</small>
                            @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="description">Description</label>
                        <div class="col-sm-10">
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                wire:model.blur="description"
                                placeholder="Enter brand description"
                                rows="3"></textarea>
                            @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Logo Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="logo">Brand Logo</label>
                        <div class="col-sm-10">
                            <input
                                type="file"
                                class="form-control @error('logo') is-invalid @enderror"
                                id="logo"
                                wire:model="logo"
                                accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.pdf,.doc,.docx">
                            <small class="text-muted">
                                Allowed: JPG, PNG, GIF, BMP, WEBP, SVG, PDF, DOC, DOCX (Max: 5MB)
                            </small>
                            @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <!-- Logo Preview Area -->
                            <div class="mt-3">
                                <!-- Current Logo -->
                                @if($current_logo_path)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3">
                                        <img
                                            src="{{ Storage::url($current_logo_path) }}"
                                            alt="Current logo"
                                            style="max-height: 80px; max-width: 200px;"
                                            class="rounded border p-1">
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="$set('logo', 'delete')">
                                        <i class="bx bx-trash me-1"></i> Remove Logo
                                    </button>
                                </div>
                                @endif

                                <!-- New Logo Preview -->
                                @if($logo && !is_string($logo) && $logo !== 'delete')
                                <div class="mt-2 p-2 border rounded">
                                    <p class="mb-1 fw-semibold">New Logo Preview:</p>
                                    <img
                                        src="{{ $logo->temporaryUrl() }}"
                                        alt="Preview"
                                        style="max-height: 80px; max-width: 200px;"
                                        class="rounded">
                                    <p class="small text-muted mt-1 mb-0">
                                        File: {{ $logo->getClientOriginalName() }}
                                        ({{ round($logo->getSize() / 1024, 2) }} KB)
                                    </p>
                                </div>
                                @endif

                                <!-- Logo Delete Indicator -->
                                @if($logo === 'delete')
                                <div class="alert alert-warning py-2 mt-2">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Current logo will be removed upon saving.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sort Order Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="sort_order">Sort Order</label>
                        <div class="col-sm-10">
                            <input
                                type="number"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                id="sort_order"
                                wire:model.blur="sort_order"
                                placeholder="0"
                                min="0">
                            <small class="text-muted">Lower numbers appear first in listings.</small>
                            @error('sort_order')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input @error('is_active') is-invalid @enderror"
                                    type="checkbox"
                                    id="is_active"
                                    wire:model="is_active"
                                    role="switch">
                                <label class="form-check-label" for="is_active">
                                    {{ $is_active ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                            @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="bx bx-save me-1"></i>
                                        {{ $isEdit ? 'Update Brand' : 'Create Brand' }}
                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Saving...
                                    </span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm" wire:loading.attr="disabled">
                                    <i class="bx bx-x me-1"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <!-- Brands List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Brands</h5>
                        <p class="text-muted small mb-0">Manage your product brands</p>
                    </div>

                    <!-- Search and Add Button Column -->
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Search Input -->
                            <div class="flex-grow-1" style="min-width: 200px;">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        wire:model.live.debounce.300ms="search"
                                        placeholder="Search by name, slug, or description...">
                                    @if($search)
                                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                        <i class="bx bx-x"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            @if(count($selectedBrands) > 0)
                            <div class="flex-shrink-0">
                                <button
                                    class="btn btn-danger d-flex align-items-center"
                                    wire:click="confirmDelete"
                                    wire:loading.attr="disabled">
                                    <i class="bx bx-trash me-1"></i>
                                    <span>Delete ({{ count($selectedBrands) }})</span>
                                </button>
                            </div>
                            @endif

                            <!-- Add New Button -->
                            <div class="flex-shrink-0">
                                <button
                                    class="btn btn-primary d-flex align-items-center"
                                    wire:click="showAddForm">
                                    <i class="bx bx-plus me-1"></i>
                                    <span>Add Brand</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        wire:model="selectAll"
                                        id="selectAll">
                                </div>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('name')" class="text-dark text-decoration-none">
                                    Name
                                    @if($sortField === 'name')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Logo</th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('products_count')" class="text-dark text-decoration-none">
                                    Products
                                    @if($sortField === 'products_count')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('sort_order')" class="text-dark text-decoration-none">
                                    Sort
                                    @if($sortField === 'sort_order')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('is_active')" class="text-dark text-decoration-none">
                                    Status
                                    @if($sortField === 'is_active')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($brands as $brand)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        value="{{ $brand->id }}"
                                        wire:model="selectedBrands"
                                        id="brand_{{ $brand->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($brand->logo)
                                    <div class="flex-shrink-0 me-3">
                                        <img
                                            src="{{ Storage::url($brand->logo) }}"
                                            alt="{{ $brand->name }}"
                                            style="height: 40px; width: 40px; object-fit: contain;"
                                            class="rounded border">
                                    </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">
                                            <i class="bx bx-store me-1 text-primary"></i>
                                            {{ $brand->name }}
                                        </div>
                                        <div class="text-muted small">
                                            <code>/{{ $brand->slug }}</code>
                                            @if($brand->website)
                                            <span class="ms-2">
                                                <i class="bx bx-link-external"></i>
                                                <a href="{{ $brand->website }}" target="_blank" class="text-decoration-none">Website</a>
                                            </span>
                                            @endif
                                        </div>
                                        @if($brand->description)
                                        <p class="mb-0 small text-muted mt-1">{{ Str::limit($brand->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($brand->logo)
                                <img src="{{ Storage::url($brand->logo) }}"
                                    alt="{{ $brand->name }}"
                                    style="height: 40px; width: 40px; object-fit: contain;"
                                    class="rounded border">
                                @else
                                <span class="badge bg-label-secondary">No logo</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-primary rounded-pill px-3 py-1">
                                    {{ $brand->products_count }} products
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-info rounded-pill px-3 py-1">
                                    {{ $brand->sort_order }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch d-inline-block">
                                    <input
                                        class="form-check-input cursor-pointer"
                                        type="checkbox"
                                        wire:change="toggleStatus({{ $brand->id }})"
                                        @checked($brand->is_active)
                                    id="status_{{ $brand->id }}"
                                    data-bs-toggle="tooltip"
                                    title="Toggle Status">
                                    <label class="form-check-label" for="status_{{ $brand->id }}">
                                        <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                            {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-icon btn-outline-primary"
                                        wire:click="showEditForm({{ $brand->id }})"
                                        data-bs-toggle="tooltip"
                                        title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-outline-danger"
                                        wire:click="deleteBrands({{ $brand->id }})"
                                        data-bs-toggle="tooltip"
                                        title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bx bx-package bx-lg mb-3 opacity-25"></i>
                                    <h5 class="mb-2">No brands found</h5>
                                    @if($search)
                                    <p class="mb-3">No results found for "{{ $search }}"</p>
                                    <button class="btn btn-sm btn-outline-secondary" wire:click="$set('search', '')">
                                        <i class="bx bx-x me-1"></i> Clear search
                                    </button>
                                    @else
                                    <p class="mb-3">Get started by adding your first brand</p>
                                    <button class="btn btn-primary" wire:click="showAddForm">
                                        <i class="bx bx-plus me-1"></i> Add Your First Brand
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($brands->hasPages())
            <div class="card-footer">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div class="text-muted small">
                        Showing {{ $brands->firstItem() }} to {{ $brands->lastItem() }} of {{ $brands->total() }} brands

                        @if(count($selectedBrands) > 0)
                        <span class="ms-2">
                            • <strong>{{ count($selectedBrands) }}</strong> selected
                        </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 120px;">
                            <label class="input-group-text" for="perPage">Show:</label>
                            <select class="form-select form-select-sm" id="perPage" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div>
                            {{ $brands->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed top-0 end-0 m-4">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <strong>Processing...</strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Listen for delete confirmation - FIXED SYNTAX
        Livewire.on('show-confirmation', (data) => {
            Swal.fire({
                title: data[0].title || 'Are you sure?',
                text: data[0].message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: data[0].confirmText || 'Delete',
                cancelButtonText: data[0].cancelText || 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (data[0].confirmMethod) {
                        // In Livewire 3, you need to call the method directly
                        Livewire.dispatch(data[0].confirmMethod, {
                            ids: data[0].confirmParams
                        });
                    }
                }
            });
        });

        // Listen for toast notifications - FIXED SYNTAX
        Livewire.on('show-toast', (data) => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: data[0].type,
                title: data[0].message
            });
        });
    });
</script>
@endpush