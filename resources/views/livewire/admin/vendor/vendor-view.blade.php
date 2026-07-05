@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="container mt-4">

    <!-- Vendor Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-danger">{{ $vendor->name }}'s Profile</h4>
            <p class="text-muted mb-0">Detailed information about this seller</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vendor.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="bx bx-arrow-back"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Products</h6>
                    <h4 class="text-danger">{{ $totalProducts }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Orders</h6>
                    <h4 class="text-danger">{{ $totalOrders }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Completed Orders</h6>
                    <h4 class="text-danger">{{ $completedOrders }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pending Orders</h6>
                    <h4 class="text-danger">{{ $pendingOrders }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Vendor Earnings</h6>
                    <h4 class="text-success">{{ $vendorEarnings }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Admin Commission</h6>
                    <h4 class="text-danger">{{ $adminCommission }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Info & Shop -->
    <div class="row mb-4">
        <div class="col-lg-4 text-center">
            <div class="card border-0 shadow-sm p-3">
                <img src="{{ Storage::url($vendor->vendorDetails->profile_image) }}"
                    class=" img-fluid mb-3" style="border: 3px solid #dc3545;">
                <p>
                    Status:
                    @if($vendor->status)
                    <span class="badge bg-danger">Active</span>
                    @else
                    <span class="badge bg-secondary">Inactive</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="text-danger">{{ $vendor->name }}</h5>
                <p><i class="bx bx-envelope text-danger"></i> {{ $vendor->email }}</p>
                <p><i class="bx bx-phone text-danger"></i> {{ $vendor->phone ?? 'N/A' }}</p>
                <p>Joined: {{ $vendor->created_at->format('d M Y') }}</p>

                @if($vendor->vendorDetails)
                <hr class="border-danger">
                <h6 class="text-danger">Shop Info</h6>
                <p><strong>Shop Name:</strong> {{ $vendor->vendorDetails->shop_name }}</p>
                <p><strong>Address:</strong> {{ $vendor->vendorDetails->shop_address }}</p>
                <p><strong>NID:</strong> {{ $vendor->vendorDetails->NID_number }}</p>
                <p><strong>Trade License (Front):</strong></p>
                @if($vendor->vendorDetails)

                <p><strong>Trade License (Front):</strong></p>
                @if($vendor->vendorDetails->trade_license_front)
                <a href="{{ Storage::url($vendor->vendorDetails->trade_license_front) }}" target="_blank">
                    <img src="{{ Storage::url($vendor->vendorDetails->trade_license_front) }}"
                        width="200"
                        class="mb-3 border rounded"
                        style="cursor:pointer;">
                </a>
                @endif

                <p><strong>Trade License (Back):</strong></p>
                @if($vendor->vendorDetails->trade_license_back)
                <a href="{{ Storage::url($vendor->vendorDetails->trade_license_back) }}" target="_blank">
                    <img src="{{ Storage::url($vendor->vendorDetails->trade_license_back) }}"
                        width="200"
                        class="mb-3 border rounded"
                        style="cursor:pointer;">
                </a>
                @endif

                @endif
                <p><strong>Payment Method:</strong> {{ ucfirst($vendor->vendorDetails->payment_method) }}</p>
                @if($vendor->vendorDetails->payment_method == 'bank')
                <p><strong>Bank Name:</strong> {{ $vendor->vendorDetails->bank_name }}</p>
                <p><strong>Account No:</strong> {{ $vendor->vendorDetails->bank_account_number }}</p>
                <p><strong>Account Holder:</strong> {{ $vendor->vendorDetails->account_holder_name }}</p>
                @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-danger">
            <h6 class="mb-0 text-white">Products</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Buying Price</th>
                        <th>Showing Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendor->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->buying_price }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <span class="badge {{ $product->status ? 'bg-danger' : 'bg-secondary' }}">
                                {{ $product->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-3">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>