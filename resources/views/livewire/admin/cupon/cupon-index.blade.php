<div>
     <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Coupon</a>
            </li>
            <li class="breadcrumb-item active">Index</li>
        </ol>
    </nav>
    <div class="col-xxl">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="col-12 col-md-4 mb-2 mb-md-0 pe-md-2">
                        <input type="text" class="form-control" wire:model.live="search"
                            placeholder="Search attributes...">
                    </div>

                    @can('banner.create')
                        <div class="col-12 col-md-auto ms-md-auto text-md-end">
                            <a href="{{ route('admin.cupon.create') }}" wire:navigate
                                class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                <i class="bx bx-plus me-1"></i> Add New
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
           
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Assigned Products</th>
                            <th>Assigned Categories</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->code }}</td>
                                <td>
                                    {{ $coupon->discount_type == 'percent' ? $coupon->discount_value.'%' : $coupon->discount_value }}
                                </td>
                                <td>
                                    @foreach($coupon->products as $product)
                                        <span class="badge bg-info">{{ $product->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($coupon->categories as $category)
                                        <span class="badge bg-warning">{{ $category->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge {{ $coupon->status ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $coupon->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                     @can('cupon.update')
                                        <a wire:navigate href="{{ route('admin.cupon.create', $coupon->id) }}"
                                            class="btn btn-sm btn-primary me-1">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                    @endcan
                                    @can('cupon.delete')
                                        <button onclick="confirmDelete({{ $coupon->id }})"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No coupons found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
