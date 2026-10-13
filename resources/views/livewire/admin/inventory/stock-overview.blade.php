<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Stock Overview</h4>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-package"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total_skus']) }}</h5>
                    </div>
                    <small class="text-muted">Total SKUs</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-cube"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['total_units']) }}</h5>
                    </div>
                    <small class="text-muted">Total In-Stock Units</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-taka"></i></span>
                        </div>
                        <h5 class="mb-0">৳{{ number_format($metrics['total_value'], 2) }}</h5>
                    </div>
                    <small class="text-muted">Total In-Stock Value</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-bell"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['low_count']) }}</h5>
                    </div>
                    <small class="text-muted">Low Stock Products</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x-circle"></i></span>
                        </div>
                        <h5 class="mb-0">{{ number_format($metrics['out_count']) }}</h5>
                    </div>
                    <small class="text-muted">Out of Stock</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="Product name or SKU...">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Category (Cascading)</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <select wire:model.live="categoryId" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($topCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select wire:model.live="subCategoryId" class="form-select" @disabled(!$subCategories->count())>
                                <option value="">All Sub Categories</option>
                                @foreach ($subCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select wire:model.live="childCategoryId" class="form-select" @disabled(!$childCategories->count())>
                                <option value="">All Child Categories</option>
                                @foreach ($childCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock Status</label>
                    <select wire:model.live="stockFilter" class="form-select">
                        <option value="">All</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Products</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">SKU</th>
                            <th width="30%">Product</th>
                            <th width="15%">Category</th>
                            <th width="12%">Available Stock</th>
                            <th width="12%">Reserved Stock</th>
                            <th width="14%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $row)
                            <tr>
                                <td>{{ ($rows->currentPage() - 1) * $rows->perPage() + $i + 1 }}</td>
                                <td><strong>{{ $row['sku'] }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($row['thumbnail'])
                                            <img src="{{ asset($row['thumbnail']) }}" class="rounded me-2" width="36" height="36" style="object-fit: cover">
                                        @endif
                                        <div>
                                            <span>{{ Str::limit($row['name'], 45) }}</span>
                                            @if ($row['has_variant'])
                                                <span class="badge bg-label-secondary ms-1">Variant</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $row['category'] ?? '-' }}</td>
                                <td>
                                    <strong>{{ number_format($row['available']) }}</strong> pcs
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ number_format($row['reserved']) }}</span>
                                </td>
                                <td>
                                    @if ($row['is_out'])
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif ($row['is_low'])
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No products found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</div>
