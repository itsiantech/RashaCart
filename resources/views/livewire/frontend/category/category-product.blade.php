<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-category-sidebar d-lg-block"
                x-data="{ open: window.innerWidth >= 992 }">
                <div class="categories-header" @click="open = !open" style="cursor:pointer;">
                    <h3>
                        Filters
                        @if ($this->activeFilterCount)
                        <span class="badge bg-danger ms-1">{{ $this->activeFilterCount }}</span>
                        @endif
                        <i class="fas fa-chevron-down ms-2"
                            :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </h3>
                </div>

                @if ($this->activeFilterCount)
                <div class="px-1 mb-2 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <button wire:click="clearAllFilters" class="flt-clear">✕ Clear all filters</button>
                </div>
                @endif

                <div class="filter-card py-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <ul class="check-list">
                        @include('livewire.frontend.layouts.partial.category.category-list', [
                        'categories' => $categories->where('parent_id', null),
                        ])
                    </ul>
                </div>

                @if ($category && $this->subcategories->isNotEmpty())
                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <h3 class="filter-title">More in {{ $category->name }}</h3>
                    <div class="flt-pills">
                        <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}"
                            class="flt-pill on">All</a>
                        @foreach ($this->subcategories as $sub)
                        <a wire:navigate href="{{ route('frontend.category.products.view', $sub->slug) }}"
                            class="flt-pill">{{ $sub->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <h3 class="filter-title">Price range</h3>
                    <div class="flt-minmax">
                        <input type="number" min="0" placeholder="Min" wire:model.defer="minPrice">
                        <span class="text-muted">—</span>
                        <input type="number" min="0" placeholder="Max" wire:model.defer="maxPrice">
                        <button wire:click="applyCustomPrice">Go</button>
                        @if (($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== ''))
                        <button wire:click="clearPrice" class="flt-x" title="Clear price">✕</button>
                        @endif
                    </div>
                    <input type="range" min="0" max="100000" step="500" class="flt-range"
                        wire:model.live.debounce.500ms="maxPrice" title="Max price">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">৳0</small>
                        <small class="text-muted">{{ ($maxPrice !== null && $maxPrice !== '') ? '৳'.number_format($maxPrice) : '৳100000+' }}</small>
                    </div>
                    <div class="size-options mt-2">
                        @foreach (['500-1000', '1000-2000', '2000-3000', '3000-4000'] as $price)
                        <button
                            wire:click.prevent="togglePrice('{{ $price }}')"
                            class="size-option {{ $selectedPrice === $price ? 'active' : '' }}">
                            {{ $price }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'" x-data="{ q: '', more: false }">
                    <h3 class="filter-title">Brands</h3>
                    <input class="flt-search" placeholder="Search brands" x-model="q">
                    <div class="flt-pills">
                        @foreach ($this->filterBrands as $item)
                        @php $b = $item['brand']; @endphp
                        <span x-show="(!q || '{{ str_replace("'", '', mb_strtolower($b->name)) }}'.includes(q.toLowerCase())) && (more || {{ $loop->index }} < 8)">
                            <button wire:click.prevent="toggleBrand({{ $b->id }})"
                                class="flt-pill {{ in_array($b->id, $selectedBrands ?? []) ? 'on' : '' }}">
                                {{ $b->name }} <small>({{ $item['count'] }})</small>
                            </button>
                        </span>
                        @endforeach
                    </div>
                    @if ($this->filterBrands->count() > 8)
                    <button class="flt-more" @click="more = !more" x-text="more ? 'Show less' : 'Show all ({{ $this->filterBrands->count() }})'"></button>
                    @endif
                </div>

                @foreach ($this->filterAttributes as $filterAttr)
                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'" x-data="{ q: '', more: false }">
                    <h3 class="filter-title">{{ $filterAttr->name }}</h3>
                    <input class="flt-search" placeholder="Search {{ strtolower($filterAttr->name) }}" x-model="q">
                    <div class="flt-pills">
                        @foreach ($filterAttr->values as $filterVal)
                        @php $isOn = in_array($filterVal->id, $selectedAttrs[$filterAttr->id] ?? []); @endphp
                        <span x-show="(!q || '{{ str_replace("'", '', mb_strtolower($filterVal->value)) }}'.includes(q.toLowerCase())) && (more || {{ $loop->index }} < 8)">
                            <button
                                wire:click.prevent="toggleAttrValue({{ $filterAttr->id }}, {{ $filterVal->id }})"
                                class="flt-pill {{ $isOn ? 'on' : '' }}">
                                @if ($filterVal->color_hex)
                                <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:{{ $filterVal->color_hex }};vertical-align:-1px;"></span>
                                @endif
                                {{ $filterVal->value }}
                            </button>
                        </span>
                        @endforeach
                    </div>
                    @if ($filterAttr->values->count() > 8)
                    <button class="flt-more" @click="more = !more" x-text="more ? 'Show less' : 'Show all ({{ $filterAttr->values->count() }})'"></button>
                    @endif
                </div>
                @endforeach
            </aside>

            <!-- Products Grid -->
            <div class="products-grid-container">
                <div class="section-header">
                    <h2>
                        {{ $category ? 'Category - ' . $category->name : 'All Categories' }}
                    </h2>
                    <div class="flt-sort">
                        @foreach (['popular' => 'Popular', 'price_asc' => 'Price ↑', 'price_desc' => 'Price ↓', 'newest' => 'Newest'] as $key => $label)
                        <button wire:click="setSort('{{ $key }}')" class="{{ $sort === $key ? 'on' : '' }}">{{ $label }}</button>
                        @endforeach
                    </div>
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

                    /* Desktop: show sidebar body unless collapsed */
                    @media (min-width: 992px) {
                        .sidebar-body:not(.collapsed) {
                            display: block !important;
                        }
                    }

                    /* Collapsed (all screens, beats the rules above) */
                    .sidebar-body.collapsed {
                        display: none !important;
                    }

                    /* Daraz-style filter groups */
                    .flt-search {
                        width: 100%; border: 1px solid #e0e0e0; border-radius: 8px;
                        padding: 7px 10px; font-size: .82rem; margin-bottom: 8px; outline: none;
                    }
                    .flt-search:focus { border-color: #7A0000; }
                    .flt-pills { display: flex; flex-wrap: wrap; gap: 6px; }
                    .flt-pill {
                        border: 1px solid #e0e0e0; background: #fff; border-radius: 14px;
                        padding: 4px 12px; font-size: .78rem; cursor: pointer; color: #374151;
                    }
                    .flt-pill small { color: #9ca3af; }
                    .flt-pill.on { background: #111827; color: #fff; border-color: #111827; }
                    .flt-pill.on small { color: #d1d5db; }
                    .flt-more {
                        background: none; border: none; color: #7A0000; font-size: .8rem;
                        font-weight: 600; cursor: pointer; padding: 6px 0 0;
                    }
                    .flt-minmax { display: flex; gap: 6px; margin-top: 8px; }
                    .flt-minmax input {
                        width: 100%; border: 1px solid #e0e0e0; border-radius: 6px;
                        padding: 6px 8px; font-size: .8rem;
                    }
                    .flt-minmax button {
                        border: 1px solid #e0e0e0; background: #fff; border-radius: 6px;
                        padding: 6px 12px; font-size: .8rem; font-weight: 600; cursor: pointer;
                    }
                    .flt-x { color: #b00 !important; }
                    .flt-range { width: 100%; accent-color: #12225B; margin: 8px 0 2px; }
                    .flt-clear {
                        background: none; border: none; color: #b00; font-size: .82rem;
                        font-weight: 600; cursor: pointer;
                    }
                    .flt-sort { display: flex; gap: 6px; flex-wrap: wrap; }
                    .flt-sort button {
                        border: 1px solid #e0e0e0; background: #fff; border-radius: 14px;
                        padding: 4px 14px; font-size: .78rem; cursor: pointer; color: #374151;
                    }
                    .flt-sort button.on { background: #12225B; color: #fff; border-color: #12225B; }
                </style>
            </div>
        </div>
    </div>
</section>

