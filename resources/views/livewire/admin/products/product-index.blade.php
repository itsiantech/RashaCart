<div>
    <div class="col-xxl">    
        <!-- Products List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Product Management</h5>
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
                                    placeholder="Search products..."
                                >
                            </div>                       
                           
                            <div class="flex-shrink-0">
                                <a wire:navigate href="{{ route('admin.product.create') }}"
                                    class="btn btn-primary d-flex align-items-center"                                    
                                    style="white-space: nowrap;"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    <span>Add New</span>
                                </a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Info</th>
                            <th>Pricing</th>                            
                            <th>Orders</th>
                            <th>Stock</th>                            
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($products->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-package fs-1"></i>
                                        <p class="mt-2">No products found</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach($products as $product)
                            @php
                                $totalStock = $this->getTotalStock($product);
                                $stockBadgeClass = $this->getStockBadgeClass($totalStock);
                                $mainStock = $this->calculateProductStock($product);
                            @endphp
                            <tr>
                                <td>
                                    <div class="position-relative">
                                        <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png') }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @if($product->is_featured)
                                            <span class="badge bg-info position-absolute top-0 start-0 mt-1 ms-1" style="font-size: 8px;">
                                                <i class="bx bx-star"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                               
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($product->name, 35) }}</div>
                                    <div class="text-muted small mt-1">
                                        SKU: {{ $product->sku ?? 'N/A' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $product->category->name ?? 'No Category' }}
                                    </div>
                                    <div class="text-muted small">
                                        Added: {{ $product->created_at->format('d M Y') }}
                                    </div>
                                    @if($product->has_variant)
                                        <span class="badge bg-primary mt-1" style="font-size: 10px;">
                                            {{ $product->variants->count() }} Variants
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold text-primary">
                                        ৳{{ number_format($product->sale_price, 2) }}
                                    </div>
                                    @if($product->discount_price) 
                                        <div class="text-danger small mt-1">
                                            <i class="bx bx-discount"></i>
                                            @if($product->discount_type == 'percentage')
                                                {{ $product->discount_price }}% OFF
                                            @else
                                                ৳{{ number_format($product->discount_price, 2) }} OFF
                                            @endif
                                        </div> 
                                    @endif
                                    <div class="text-muted small">
                                        Cost: ৳{{ number_format($product->buying_price, 2) }}
                                    </div>
                                </td>
                              
                                <td>
                                    <div class="text-center">
                                        <span class="fw-semibold fs-6">{{ $product->orderitems->count() }}</span>
                                        <div class="text-muted small">Orders</div>
                                    </div>
                                </td>

                                <td>
                                    <div class="text-center">
                                        <span class="badge {{ $stockBadgeClass }} fs-6 px-2 py-1">
                                            {{ $totalStock }}
                                        </span>
                                        @if($product->has_variant)
                                            <div class="text-muted small mt-1">
                                                Main: {{ $mainStock }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->status ? 'Active' : 'Inactive' }}
                                    </span>
                                    
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-1">  
                                        @if(Auth()->user()->can('admin.product.stock.manage')) 
                                         <a wire:navigate href="{{ route('admin.product.stock.manage', $product->id) }}" 
                                           class="btn btn-sm btn-success"
                                           title="Edit Product"
                                           data-bs-toggle="tooltip">
                                            <i class="bx bxs-analyse"></i>inventory
                                        </a> 
                                        @endif

                                        <a wire:navigate href="{{ route('admin.product.view', $product->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details"
                                           data-bs-toggle="tooltip">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        
                                        <a wire:navigate href="{{ route('admin.product.edit', $product->id) }}" 
                                           class="btn btn-sm btn-warning"
                                           title="Edit Product"
                                           data-bs-toggle="tooltip">
                                            <i class="bx bx-edit"></i>
                                        </a> 
                                        <button 
                                            class="btn btn-sm btn-danger" 
                                            wire:click="deleteProduct({{ $product->id }})"
                                            onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')"
                                            title="Delete Product"
                                            data-bs-toggle="tooltip"
                                        >
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
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Initialize tooltips
    document.addEventListener('livewire:initialized', () => {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endscript