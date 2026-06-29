<div>
    <div class="col-xxl">
        <!-- Free Shipping List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Free Shipping Management</h5>
                    </div>

                    <!-- Search and Add Button Column -->
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Search Input -->
                            <div class="flex-grow-1" style="min-width: 150px;">
                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="search"
                                    placeholder="Search free shipping...">
                            </div>

                            <div class="flex-shrink-0">
                                <button
                                    wire:click="openForm(false)"
                                    class="btn btn-primary d-flex align-items-center"
                                    style="white-space: nowrap;">
                                    <i class="bx bx-plus me-1"></i>
                                    <span>Add New</span>
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
                            <th>
                                <a href="#" wire:click.prevent="setSortBy('name')" class="text-decoration-none">
                                    Name
                                    @if($sortBy === 'name')
                                    <i class="bx {{ $sortDir === 'ASC' ? 'bx-sort-up' : 'bx-sort-down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Details</th>
                            <th>
                                <a href="#" wire:click.prevent="setSortBy('sort_order')" class="text-decoration-none">
                                    Order
                                    @if($sortBy === 'sort_order')
                                    <i class="bx {{ $sortDir === 'ASC' ? 'bx-sort-up' : 'bx-sort-down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($freeShippings->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-package fs-1"></i>
                                    <p class="mt-2">No free shipping rules found</p>
                                </div>
                            </td>
                        </tr>
                        @endif

                        @foreach($freeShippings as $rule)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $rule->name }}</div>
                            </td>

                            <td>
                                <div class="text-muted small">
                                    {{ Str::limit($rule->description, 50) ?? 'N/A' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-info">{{ ucfirst($rule->type) }}</span>
                            </td>

                            <td>
                                @if($rule->type === 'amount')
                                @if($rule->minimum_amount)
                                <div class="small">
                                    <strong>Min Amount:</strong> ৳{{ number_format($rule->minimum_amount, 2) }}
                                </div>
                                @endif
                                @elseif($rule->type === 'category')
                                <div class="small">
                                    <strong>Category:</strong> {{ $rule->category->name ?? 'N/A' }}
                                </div>
                                @elseif($rule->type === 'product')
                                <div class="small">
                                    <strong>Product:</strong> {{ Str::limit($rule->product->name, 30) ?? 'N/A' }}
                                </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary">{{ $rule->sort_order }}</span>
                            </td>

                            <td>
                                <button
                                    wire:click="toggleActive({{ $rule->id }})"
                                    class="btn btn-sm {{ $rule->is_active ? 'btn-success' : 'btn-danger' }}"
                                    title="{{ $rule->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                    {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            <td>
                                <div class="d-flex gap-1">
                                    <button
                                        wire:click="openForm(true, {{ $rule->id }})"
                                        class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button
                                        wire:click="delete({{ $rule->id }})"
                                        onclick="return confirm('Are you sure you want to delete this free shipping rule?')"
                                        class="btn btn-sm btn-danger"
                                        title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    {{ $freeShippings->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal Form -->
    @if($showForm)
    <div class="modal" style="display: block; background-color: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditing ? 'Edit Free Shipping Rule' : 'Add Free Shipping Rule' }}
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        wire:click="closeForm()"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form>
                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                wire:model="name"
                                placeholder="e.g., Free Shipping on Orders Over 2000">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                wire:model="description"
                                rows="3"
                                placeholder="Optional description"></textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                            <select
                                class="form-select @error('type') is-invalid @enderror"
                                id="type"
                                wire:model="type"
                                wire:change="$set('category_id', null); $set('product_id', null);">
                                <option value="amount">By Minimum Amount</option>
                                <option value="category">By Category</option>
                                <option value="product">By Product</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Minimum Amount (for amount type) -->
                        @if($type === 'amount')
                        <div class="mb-3">
                            <label class="form-label" for="minimum_amount">
                                Minimum Order Amount
                                <span class="text-muted small">(In Taka)</span>
                            </label>
                            <input
                                type="number"
                                class="form-control @error('minimum_amount') is-invalid @enderror"
                                id="minimum_amount"
                                wire:model="minimum_amount"
                                step="0.01"
                                min="0"
                                placeholder="e.g., 2000">
                            @error('minimum_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Category (for category type) -->
                        @if($type === 'category')
                        <div class="mb-3">
                            <label class="form-label" for="category_id">
                                Select Category <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id"
                                wire:model="category_id">
                                <option value="">-- Select a Category --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Product (for product type) -->
                        @if($type === 'product')
                        <div class="mb-3">
                            <label class="form-label" for="product_id">
                                Select Product <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select @error('product_id') is-invalid @enderror"
                                id="product_id"
                                wire:model="product_id">
                                <option value="">-- Select a Product --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label class="form-label" for="sort_order">Sort Order</label>
                            <input
                                type="number"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                id="sort_order"
                                wire:model="sort_order"
                                min="0"
                                placeholder="0">
                            <small class="text-muted">Lower numbers appear first</small>
                            @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Is Active Toggle -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    wire:model="is_active">
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        wire:click="closeForm()">
                        Close
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="save()">
                        {{ $isEditing ? 'Update' : 'Create' }} Free Shipping
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    // Initialization script
    document.addEventListener('livewire:initialized', () => {
        // Bootstrap tooltips will be initialized here if needed
    });
</script>
@endscript