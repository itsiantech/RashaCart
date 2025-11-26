<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Cupon</a>
            </li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card">

         <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Cupon Add/Edit</h5>
            <div class="col-12 col-md-auto ms-md-auto text-md-end">
                <a href="{{ route('admin.cupon.index') }}" wire:navigate
                    class="btn btn-primary btn-sm d-inline-flex align-items-center">
                    <i class='bx bx-arrow-back'></i> Go back
                </a>
            </div>

        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Coupon Code</label>
                        <input type="text" class="form-control" wire:model="code">
                        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Discount Type</label>
                        <select class="form-control" wire:model="discount_type">
                            <option value="">Select</option>
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed</option>
                        </select>
                        @error('discount_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Discount Value</label>
                        <input type="number" class="form-control" wire:model="discount_value">
                        @error('discount_value') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    
                    <div class="col-md-4">
                        <label>Min Order Amount</label>
                        <input type="number" class="form-control" wire:model="min_order_amount">
                    </div>
                    <div class="col-md-4">
                        <label>Start Date</label>
                        <input type="date" class="form-control" wire:model="start_date">
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>End Date</label>
                        <input type="date" class="form-control" wire:model="end_date">
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Assign Products</label>
                        <select class="form-control" wire:model="selected_products" multiple>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Assign Categories</label>
                        <select class="form-control" wire:model="selected_categories" multiple>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save Coupon</button>
            </form>
        </div>
    </div>
</div>
