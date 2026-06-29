<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-category-sidebar">
                <div class="categories-header">
                    <h3><i class="fas fa-sliders-h "></i> Filters</h3>
                </div>

                <div class="filter-card py-3">
                    <ul class="check-list">
                        @include('livewire.frontend.layouts.partial.category.category-list', [
                        'categories' => $categories->where('parent_id', null),
                        ])
                    </ul>
                </div>

                <div class="filter-card  pb-3">
                    <h3 class="filter-title"><i class="fas fa-mobile-alt"></i> Price</h3>
                    <div class="size-options">
                        @foreach (['500-1000', '1000-2000', '2000-3000', '3000-4000'] as $price)
                        <button
                            wire:click.prevent="togglePrice('{{ $price }}')"
                            class="size-option {{ $selectedPrice === $price ? 'active' : '' }}">
                            {{ $price }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="products-grid-container">
                <div class="section-header">
                    <h2>
                        {{ $brand ? 'Brand - ' . $brand->name : 'All Brands' }}
                    </h2>
                </div>

                <div class="products-grid">
                    @forelse($brands as $brand)
                     <a wire:navigate href="{{ route('frontend.brands.products.view', $brand->slug) }}"
                        class="brand-card">
                        <div class="brand-image">
                            <img src="{{ $brand->logo ? asset('storage/' . $brand->logo) : 'https://placehold.co/500x500' }}"
                                alt="{{ $brand->name }}" loading="lazy">
                        </div>
                        <h3 class="d-none category-name">{{ $brand->name }}</h3>
                    </a>
                    @empty
                    <div class="empty-products">
                        <div class="cart-empty">
                            <i class="fas fa-shopping-basket"></i>
                            <p>No brands found.</p>
                            <p>We couldn’t find any brands matching your criteria. Please adjust your filters or try a different search.</p>
                            <a wire:navigate href="{{ route('home')}}" class="btn-continue-shopping">Continue Shopping</a>
                        </div>
                    </div>
                    @endforelse
                </div>
                <style>
                    .products-grid {
                        grid-template-columns: repeat(5, 1fr) !important;
                        gap: 1.25rem;
                    }

                    @media (max-width: 1400px) {
                        .products-grid {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }
                    }

                    @media (max-width: 1200px) {
                        .products-grid {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }

                    @media (max-width: 900px) {
                        .products-grid {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }

                    .empty-products {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 90%;
                        max-width: 300px;
                    }

                    .empty-icon i {
                        font-size: 64px;
                        color: #ccc;
                    }

                    .categories-category-sidebar {
                        width: 100%;
                        background: white;
                        border-radius: 8px;
                        box-shadow: var(--box-shadow);
                        overflow: hidden;
                        margin-top: 20px;
                        margin-bottom: 20px;
                    }

                    @media screen and (max-width: 768px) {
                        .categories-category-sidebar {
                            overflow: hidden;
                            display: none;
                        }
                    }
                </style>

                <div class="pagination-container">
                    {{ $brands->links() }}
                </div>
            </div>
        </div>
    </div>
</section>