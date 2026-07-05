@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="container-fluid py-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ $vendor->name }}</h3>
            <span class="text-muted">Vendor Profile Overview</span>
        </div>

        <a href="/"
            class="btn btn-outline-secondary btn-sm px-3">
            <i class="bx bx-arrow-back"></i> Back
        </a>
    </div>


    {{-- ================= STATS ================= --}}
    <div class="row g-3 mb-4">

        @php
        $stats = [
        ['label' => 'Total Products', 'value' => $totalProducts, 'color' => 'primary'],
        ['label' => 'Total Orders', 'value' => $totalOrders, 'color' => 'info'],
        ['label' => 'Completed', 'value' => $completedOrders, 'color' => 'success'],
        ['label' => 'Pending', 'value' => $pendingOrders, 'color' => 'warning'],
        ['label' => 'Earnings', 'value' => $vendorEarnings, 'color' => 'dark'],
        ['label' => 'Admin Commission', 'value' => $adminCommission, 'color' => 'danger'],
        ];
        @endphp

        @foreach($stats as $stat)
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <small class="text-muted d-block mb-1">{{ $stat['label'] }}</small>
                    <h4 class="fw-bold text-{{ $stat['color'] }} mb-0">
                        {{ $stat['value'] }}
                    </h4>
                </div>
            </div>
        </div>
        @endforeach

    </div>


    <div class="row g-4">

        {{-- ================= LEFT PANEL ================= --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">

                    <img src="{{ $vendor->vendorDetails?->profile_image 
                        ? Storage::url($vendor->vendorDetails->profile_image) 
                        : asset('images/default-avatar.png') }}"
                        class="rounded-circle mb-3"
                        width="150"
                        height="150"
                        style="object-fit: cover; border:4px solid #f8f9fa;">

                    <h5 class="fw-semibold mb-1">{{ $vendor->name }}</h5>
                    <p class="text-muted small mb-2">{{ $vendor->email }}</p>

                    <div class="d-flex justify-content-center w-100">
                        <span class="badge position-static px-3 py-2 {{ $vendor->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $vendor->status ? 'Active Vendor' : 'Inactive Vendor' }}
                        </span>
                    </div>

                    <hr>

                    <div class="text-start small">
                        <p class="mb-1">
                            <strong>Phone:</strong> {{ $vendor->phone ?? 'N/A' }}
                        </p>
                        <p class="mb-1">
                            <strong>Joined:</strong> {{ $vendor->created_at->format('d M Y') }}
                        </p>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small">Update Profile Image</label>
                        <input type="file" class="form-control form-control-sm" wire:model="profile_image">
                    </div>

                </div>
            </div>

        </div>


        {{-- ================= RIGHT PANEL ================= --}}
        <div class="col-lg-8">

            <form wire:submit.prevent="updateProfile">
                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-light fw-semibold">
                        Shop Information
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Shop Name</label>
                                <input type="text" class="form-control" wire:model="shop_name">
                                @error('shop_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NID Number</label>
                                <input type="text" class="form-control" wire:model="NID_number">
                                @error('NID_number') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Shop Address</label>
                                <textarea class="form-control" rows="3" wire:model="shop_address"></textarea>
                                @error('shop_address') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Trade License (Front)</label>
                                <input type="file" class="form-control" wire:model="trade_license_front">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Trade License (Back)</label>
                                <input type="file" class="form-control" wire:model="trade_license_back">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" wire:model="payment_method">
                                    <option value="">Select</option>
                                    <option value="bank">Bank</option>
                                    <option value="bkash">Bkash</option>
                                    <option value="nagad">Nagad</option>
                                </select>
                            </div>

                            @if($payment_method === 'bank')
                            <div class="col-md-6">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" wire:model="bank_name">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" wire:model="bank_account_number">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Account Holder</label>
                                <input type="text" class="form-control" wire:model="account_holder_name">
                            </div>
                            @endif

                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-dark px-4">
                                Save Changes
                            </button>
                        </div>

                        @if (session()->has('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                        @endif

                    </div>
                </div>
            </form>

        </div>

    </div>


    {{-- ================= PRODUCTS ================= --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-black">
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
                            <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }} text-white">
                                {{ $product->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <style>
        table .badge, .card-body .badge {
            position: static !important;
            transform: none !important;
            width: auto !important;
            height: auto !important;
            border-radius: 0.25rem !important;
            padding: 0.35em 0.65em !important;
            display: inline-block !important;
        }

        
    </style>
</div>