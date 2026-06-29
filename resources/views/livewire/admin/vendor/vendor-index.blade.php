<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Seller Management</h4>
            <p class="text-muted mb-0">Manage your sellers and their shops</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="exportVendors" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-export"></i> Export
            </button>
            <button wire:click="openModal" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> Add Seller
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Seller</h6>
                            <h4 class="mb-0">{{ $totalVendors }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="bx bx-store-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Sellers</h6>
                            <h4 class="mb-0">{{ $activeVendors }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="bx bx-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Inactive Sellers</h6>
                            <h4 class="mb-0">{{ $inactiveVendors }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="bx bx-x-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bx bx-search"></i>
                        </span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control border-start-0"
                            placeholder="Search Sellers by name, email, phone, or shop name...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Seller</th>
                            <th>Shop Info</th>
                            <th>Contact</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $vendor->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">
                                            Joined: {{ optional($vendor->created_at)->format('M d, Y') ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($vendor->vendorDetails)
                                <h6 class="mb-0">{{ $vendor->vendorDetails->shop_name }}</h6>
                                <small class="text-muted">
                                    <i class="bx bx-map-pin"></i> {{ Str::limit($vendor->vendorDetails->shop_address, 30) }}
                                </small>
                                @else
                                <span class="text-muted">No shop info</span>
                                @endif
                            </td>
                            <td>
                                <div class="mb-1">
                                    <i class="bx bx-envelope text-muted"></i>
                                    <small>{{ $vendor->email }}</small>
                                </div>
                                @if($vendor->phone)
                                <div>
                                    <i class="bx bx-phone text-muted"></i>
                                    <small>{{ $vendor->phone }}</small>
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $vendor->products_count ?? $vendor->products()->count() }} Products
                                </span>
                            </td>
                            <td>
                                <!-- Vendor Approval Button -->
                                <button
                                    wire:click="toggleApproval({{ $vendor->id }})"
                                    class="btn btn-sm {{ $vendor->is_vendor_approved ? 'btn-success' : 'btn-warning' }}">
                                    {{ $vendor->is_vendor_approved ? 'Approved (Click to Pending)' : 'Pending (Click to Approve)' }}
                                </button>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.vendors.view', $vendor->id) }}"
                                        class="btn btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <button wire:click="openModal({{ $vendor->id }})"
                                        class="btn btn-outline-primary">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button type="button"
                                        wire:click="deleteVendor('{{ $vendor->id }}')"
                                        wire:confirm="Are you sure you want to delete {{ $vendor->name }}?"
                                        class="btn btn-outline-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-5">
                                    <i class="bx bx-store-alt display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">No Sellers found</h5>
                                    <p class="text-muted mb-0">Start by adding your first Seller</p>
                                    <button wire:click="openModal" class="btn btn-primary mt-3">
                                        <i class="bx bx-plus"></i> Add Seller
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($vendors->hasPages())
            <div class="card-footer border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted">
                            Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} vendors
                        </p>
                    </div>
                    <div>
                        {{ $vendors->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $modalTitle }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveVendor">
                        <div class="row g-3">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3"></h6>
                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" wire:model="name">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" wire:model="email">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" wire:model="phone">
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $vendorId ? 'New Password (Leave blank to keep current)' : 'Password *' }}
                                    </label>
                                    <input type="password" class="form-control" wire:model="password">
                                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                @if(!$vendorId || $password)
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" wire:model="password_confirmation">
                                    @error('password_confirmation') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                @endif

                            </div>

                            <!-- Shop Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3"></h6>
                                <div class="mb-3">
                                    <label class="form-label">Shop Name *</label>
                                    <input type="text" class="form-control" wire:model="shop_name">
                                    @error('shop_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Shop Address *</label>
                                    <textarea class="form-control" rows="2" wire:model="shop_address"></textarea>
                                    @error('shop_address') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">NID Number *</label>
                                    <input type="text" class="form-control" wire:model="NID_number">
                                    @error('NID_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Trade License Number *</label>
                                    <input type="text" class="form-control" wire:model="trade_license_number">
                                    @error('trade_license_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                            </div>

                            <!-- Payment Information -->
                            <div class="col-12">
                                <hr>
                                <h6 class="mb-3">Payment Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Payment Method</label>
                                        <select class="form-select" wire:model="payment_method">
                                            <option value="">Select Method</option>
                                            <option value="bank">Bank Transfer</option>
                                            <option value="bkash">Bkash</option>
                                            <option value="nagad">Nagad</option>
                                            <option value="rocket">Rocket</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                    @if($payment_method == 'bank')
                                    <div class="col-md-4">
                                        <label class="form-label">Bank Name *</label>
                                        <input type="text" class="form-control" wire:model="bank_name">
                                        @error('bank_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Account Number *</label>
                                        <input type="text" class="form-control" wire:model="bank_account_number">
                                        @error('bank_account_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Account Holder Name *</label>
                                        <input type="text" class="form-control" wire:model="account_holder_name">
                                        @error('account_holder_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> {{ $vendorId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @endif

    @script
    <script>
        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete vendor "${name}"? This action cannot be undone.`)) {
                $wire.deleteVendor(id);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.querySelector('.modal.show');
            if (modal && event.target === modal) {
                @this.closeModal();
            }
        });
    </script>
    @endscript

    <!-- Toast notifications -->
    <div wire:ignore class="toast-container position-fixed top-0 end-0 p-3">
        @if(session()->has('message'))
        <div class="toast align-items-center text-white bg-{{ session('type') ?? 'success' }} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('message') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif
    </div>
</div>