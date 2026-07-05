<div>
    @include('livewire.frontend.layouts.partial.banner.banner-default')
    <!-- Seller Info Header -->
    <div class="container">
        <div class="d-flex rounded-md shadow p-1 vendor-breadcumb">
            <img src="{{ $vendor->vendorDetails?->profile_image 
            ? Storage::url($vendor->vendorDetails->profile_image) 
            : asset('images/default-avatar.png') }}" class="logo-img">

            <div class="mt-1">
                <div class="ms-4 mt-3 vendor-name">{{ $vendor->name }}'s Products</div>
                <!-- <p class="text-gray-600 ms-4">{{ $products->total() }} products available</p> -->
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            @if (count($products) === 0)
            <p class="text-center text-muted">Loading products...</p>
            @else
            @foreach ($products as $product)
            @include('livewire.frontend.layouts.partial.product-card', [
            'product' => $product,
            ])
            @endforeach
            @endif
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $products->links() }}
        </div>
    </div>

    <style>

        .vendor-name {
            font-size: 24px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }
        .vendor-breadcumb {
            margin: 50px 0px;
            background-color: #D3E3FD;
        }

        .logo-img {
            margin-top: 5px;
            margin-left: 10px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        /* Featured: prefer 5 columns, fall back to 5/4 on smaller screens */
        .products-grid {
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 1.25rem;
        }

        @media (max-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr) !important;
            }
        }

        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }

        @media (max-width: 900px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
    </style>
</div>