<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-sidebar">
                <div class="categories-header">
                    <h3><i class="fas fa-sliders-h "></i> Filters</h3>
                </div>

                <!-- Category Filter -->
                <div class="filter-card py-3">
                    <h3 class="filter-title"><i class="fas fa-microchip"></i> Category</h3>
                    <ul class="check-list">
                        @include('livewire.frontend.layouts.partial.category.category-list', [
                            'categories' => $categories->where('parent_id', null),
                        ])
                    </ul>
                </div>

                <!-- Price Filter -->
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
                        {{ $category ? 'Category - ' . $category->name : 'All Categories' }}
                    </h2>
                </div>

                <div class="products-grid">
                    @forelse($products as $product)
                        @include('livewire.frontend.layouts.partial.product-card', ['product' => $product])
                    @empty
                        <div class="empty-products">
                            <div class="cart-empty">
                                <i class="fas fa-shopping-basket"></i>
                                <p>No products found.</p>
                                <p>We couldn’t find any products matching your criteria. Please adjust your filters or try a different search.</p>
                                <a wire:navigate href="{{ route('home')}}" class="btn-continue-shopping">Continue Shopping</a>
                            </div>
                        </div>
                    @endforelse
                </div>
                <style>
                    .products-grid {
                        position: relative;
                        min-height: 400px;
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
                </style>

                <div class="pagination">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
