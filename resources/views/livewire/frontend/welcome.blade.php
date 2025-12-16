<div>
    @include('livewire.frontend.layouts.partial.banner.banner-default')

    <div class="container">
        <!-- Ad Section -->
        <div class="ad-section mt-3">
            @foreach ($ads as $ad)
                <img src="{{ $ad->image ? asset('storage/' . $ad->image) : 'https://placehold.co/1000x500' }}"
                    alt="Ad" loading="lazy">
            @endforeach
        </div>

        <!-- Categories -->
        <section class="categories-section">
            <div class="section-header">
                <h2>Shop by Category</h2>
                <a wire:navigate href="{{ route('frontend.category.view') }}" class="view-all">
                    View All Categories <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="categories-grid">
                @foreach ($categories as $category)
                    <a wire:navigate href="{{ route('frontend.category.view', $category->slug) }}"
                        class="category-card">
                        <div class="category-image">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://placehold.co/500x500' }}"
                                alt="{{ $category->name }}" loading="lazy">
                        </div>
                        <h3 class="category-name">{{ $category->name }}</h3>
                    </a>
                @endforeach
            </div>
        </section>

        <div wire:init="loadProducts">
            <!-- Featured Products -->
            <div class="tab-content active" id="featured">
                <div class="section-header">
                    <div class="section-left">
                        <h2>Featured Products</h2>
                    </div>
                    <a wire:navigate href="{{ route('frontend.category.view') }}" class="view-all">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="products-grid">
                    @if (count($featuredProducts) === 0)
                        <p class="text-center text-muted">Loading featured products...</p>
                    @else
                        @foreach ($featuredProducts as $product)
                            @include('livewire.frontend.layouts.partial.product-card', [
                                'product' => $product,
                            ])
                        @endforeach
                    @endif
                </div>

                @if ($featuredLoadedCount < Cache::get('products', collect())->where('is_featured', 1)->count())
                    <div class="text-center mt-3">
                        <button class="btn-order-now btn-load-more" wire:click="loadMoreFeatured"
                            wire:loading.attr="disabled" wire:target="loadMore({{ $category->id }})">
                            <span wire:loading.remove wire:target="loadMore({{ $category->id }})">
                                Load More..
                            </span>

                            <span wire:loading wire:target="loadMore({{ $category->id }})">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                @endif


            </div>

            <!-- Featured Categories Products -->
            @foreach ($featuredCategories as $category)
                <div class="tab-content active" id="category-{{ $category->id }}">
                    <div class="section-header">
                        <div class="section-left">
                            <h2>{{ $category->name }} Products</h2>
                        </div>
                        <a wire:navigate href="{{ route('frontend.category.view', $category->slug) }}"
                            class="view-all">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="products-grid">
                        @if (!isset($categoryProducts[$category->id]) || count($categoryProducts[$category->id]) === 0)
                            <p class="text-center text-muted">Loading products...</p>
                        @else
                            @foreach ($categoryProducts[$category->id] as $product)
                                @include('livewire.frontend.layouts.partial.product-card', [
                                    'product' => $product,
                                ])
                            @endforeach
                        @endif
                    </div>

                    @if (isset($categoryLoadedCount[$category->id]) &&
                            $categoryLoadedCount[$category->id] <
                                Cache::get('products', collect())->where('category_id', $category->id)->count())
                        <div class="text-center mt-3">
                            <button class="btn-order-now btn-load-more" wire:click="loadMore({{ $category->id }})"
                                wire:loading.attr="disabled" wire:target="loadMore({{ $category->id }})">
                                <span wire:loading.remove wire:target="loadMore({{ $category->id }})">
                                    Load More..
                                </span>

                                <span wire:loading wire:target="loadMore({{ $category->id }})">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            <style>
                                .btn-load-more {
                                    width: auto !important;
                                    /* <-- THIS fixes full width */
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    padding: 6px 18px;
                                    font-size: 14px;
                                    border-radius: 6px;
                                }
                            </style>


                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </div>
</div>
