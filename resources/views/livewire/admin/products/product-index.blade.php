<div>
    <div class="col-xxl">    

    
        <!-- Categories List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Product Manage</h5>
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
                                    placeholder="Search categories..."
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
                            <th>Name</th>
                            <th>Pricing</th>                            
                            <th>Total Order</th>
                            <th>Stoke</th>                            
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($products->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">No products found.</td>
                            </tr>
                        @endif
                        @foreach($products as $product)
                            <tr>
                                
                                <td>
                                    <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png') }}" 
                                         alt="{{ $product->name }}" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                               <td>
                                    {{ Str::limit($product->name, 40) }}

                                    <span class="text-muted d-block">Added: {{ $product->created_at->format('d M Y') }}</span>
                                </td>

                                <td>Price: {{ $product->price }} <br>
                                    @if($product->discount_price) 
                                   <span class="text-danger"> Discount: {{ $product->discount_price }} {{$product->discount_type}} </span> <br>  @endif
                                   <span class="text-info"> <strong> Sale Price:{{ $product->sale_price }} </strong></span>
                                </td>
                              <td>{{ $product->orderitems?->count() ?? 0 }}</td>

                            
                               
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">   
                                        <a wire:navigate href="{{ route('admin.product.view', $product->id) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a wire:navigate href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bx bx-edit"></i>
                                        </a>  
                                        <button 
                                            class="btn btn-sm btn-danger" 
                                            wire:click="deleteProduct({{ $product->id }})"
                                            onclick="return confirm('Are you sure you want to delete this product?')"
                                        >
                                            <i class="bx bx-trash"></i>
                                        </button>                                   
                                        
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>    
                    
                </table>
            </div>
        </div>
    </div>

    
</div>
