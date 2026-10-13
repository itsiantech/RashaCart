<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-category-sidebar d-lg-block"
                x-data="{ open: false }">
                <div class="categories-header" @click="open = !open" style="cursor:pointer;">
                    <h3>
                        <i class="fas fa-sliders-h"></i> Filters
                        <i class="fas fa-chevron-down ms-2 d-lg-none"
                            :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </h3>
                </div>

                <div class="filter-card py-3 sidebar-body" :class="open ? 'd-block' : ''">
                    <ul class="check-list">
                        @include('livewire.frontend.layouts.partial.category.category-list', [
                        'categories' => $categories->where('parent_id', null),
                        ])
                    </ul>
                </div>

                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : ''">
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

                @foreach ($this->filterAttributes as $filterAttr)
                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : ''">
                    <h3 class="filter-title"><i class="fas fa-filter"></i> {{ $filterAttr->name }}</h3>
                    <div class="size-options">
                        @foreach ($filterAttr->values as $filterVal)
                        @php $isOn = in_array($filterVal->id, $selectedAttrs[$filterAttr->id] ?? []); @endphp
                        <button
                            wire:click.prevent="toggleAttrValue({{ $filterAttr->id }}, {{ $filterVal->id }})"
                            class="size-option {{ $isOn ? 'active' : '' }}">
                            @if ($filterVal->color_hex)
                            <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:{{ $filterVal->color_hex }};vertical-align:-1px;"></span>
                            @endif
                            {{ $filterVal->value }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
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
                <div class="pagination-container">
                    {{ $products->links() }}
                </div>
                <style>
                    .products-grid {
                        grid-template-columns: repeat(5, 1fr) !important;
                        gap: 10px;
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

                    /* Mobile: hide sidebar body by default */
                    @media (max-width: 991px) {
                        .categories-category-sidebar {
                            display: block !important;
                            /* override d-lg-block so header stays visible */
                        }

                        .sidebar-body {
                            display: none;
                            /* hidden by default on mobile */
                        }

                        .categories-header {
                            border-bottom: 1px solid #eee;
                            padding-bottom: 0.5rem;
                            margin-bottom: 0;
                            user-select: none;
                        }
                    }

                    /* Desktop: always show sidebar body */
                    @media (min-width: 992px) {
                        .sidebar-body {
                            display: block !important;
                        }

                        .categories-header {
                            cursor: default !important;
                            pointer-events: none;
                        }
                    }
                </style>
            </div>
        </div>
    </div>
</section>