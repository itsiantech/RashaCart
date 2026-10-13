<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Category Wise Stock</h4>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="#" wire:click="selectCategory(0)" wire:navigate.prevent
                    @if (!$categoryId) class="text-primary fw-semibold" @endif>All Categories</a>
            </li>
            @foreach ($breadcrumb as $bc)
                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                    @if (!$loop->last)
                        <a href="#" wire:click="selectCategory({{ $bc->id }})">{{ $bc->name }}</a>
                    @else
                        {{ $bc->name }}
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- Category Cards -->
    <div class="row g-4 mb-4">
        @forelse ($displayCategories as $cat)
            @php $agg = $aggregate[$cat->id] ?? ['units' => 0, 'value' => 0, 'product_count' => 0]; @endphp
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 cursor-pointer" wire:click="selectCategory({{ $cat->id }})" style="cursor: pointer">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-sitemap"></i></span>
                            </div>
                            <span class="badge bg-label-secondary">{{ $agg['product_count'] }} products</span>
                        </div>
                        <h6 class="mb-1">{{ $cat->name }}</h6>
                        <p class="text-muted mb-2 small">Total Units: <strong>{{ number_format($agg['units']) }}</strong></p>
                        <p class="text-success mb-0 small fw-semibold">Stock Value: ৳{{ number_format($agg['value'], 2) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-muted text-center mb-0">No sub categories found</div>
            </div>
        @endforelse
    </div>

    @if ($currentCategory)
        <!-- Products in this category -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products in "{{ $currentCategory->name }}"</h5>
                <button type="button" wire:click="selectCategory({{ $currentCategory->parent_id ?? 0 }})" class="btn btn-sm btn-label-secondary">
                    <i class="bx bx-left-arrow-alt me-1"></i> Back
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">SKU</th>
                                <th width="35%">Product</th>
                                <th width="14%">Available</th>
                                <th width="12%">Reserved</th>
                                <th width="22%">Stock Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $i => $p)
                                <tr>
                                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $i + 1 }}</td>
                                    <td><strong>{{ $p->sku }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($p->thumbnail)
                                                <img src="{{ asset($p->thumbnail) }}" class="rounded me-2" width="36" height="36" style="object-fit: cover">
                                            @endif
                                            <span>{{ Str::limit($p->name, 50) }}</span>
                                            @if ($p->has_variant)
                                                <span class="badge bg-label-secondary ms-1">Variant</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><strong>{{ number_format($p->available_stock) }}</strong> pcs</td>
                                    <td><span class="badge bg-label-info">{{ number_format($p->reserved_stock) }}</span></td>
                                    <td>৳{{ number_format($p->stock_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No products directly in this category</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
