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

        <section class="categories-section">
            <div class="section-header">
                <h2>Shop by Category</h2>
                <a wire:navigate href="{{ route('frontend.category.view') }}" class="view-all">View All Categories <i
                        class="fas fa-arrow-right"></i></a>
            </div>
            <div class="categories-grid">
                @foreach ($categories as $category)
                    <a wire:navigate href="{{ route('frontend.category.view', $category->slug) }}" class="category-card">
                        <div class="category-image">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://placehold.co/500x500' }}"
                                alt="{{ $category->name }}" loading="lazy">
                        </div>
                        <h3 class="category-name">{{ $category->name }}</h3>
                    </a>
                @endforeach
            </div>
        </section>


       <!-- Best Sale Products -->
        <div wire:init="loadProducts">
            <div class="tab-content active" id="featured">
                <div class="section-header">
                    <div class="section-left">
                        <h2>Best Sale Products</h2>
                        {{-- <div class="deal-timer" id="featuredTimer" aria-label="Deal ends in">
                            <div class="time-box">
                                <div class="time-num" data-unit="hours">00</div>
                                <div class="time-label">Hours</div>
                            </div>
                            <div class="time-box">
                                <div class="time-num" data-unit="minutes">00</div>
                                <div class="time-label">Min</div>
                            </div>
                            <div class="time-box">
                                <div class="time-num" data-unit="seconds">00</div>
                                <div class="time-label">Sec</div>
                            </div>
                        </div> --}}
                    </div>

                    <a href="products.html" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="products-grid">
                    @if(count($bestSaleProducts) === 0)
                        <!-- Loading Placeholder -->
                        <p class="text-center text-muted">Loading best sale products...</p>
                    @else
                        @foreach ($bestSaleProducts as $product)
                            @include('livewire.frontend.layouts.partial.product-card', ['product' => $product])
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Featured Products -->
            <div class="tab-content active" id="featured">
                <div class="section-header">
                    <div class="section-left">
                        <h2>Featured Products</h2>                        
                    </div>

                    <a href="products.html" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="products-grid">
                    @if(count($featuredProducts) === 0)
                        <!-- Loading Placeholder -->
                        <p class="text-center text-muted">Loading featured products...</p>
                    @else
                        @foreach ($featuredProducts as $product)
                            @include('livewire.frontend.layouts.partial.product-card', ['product' => $product])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>




    </div>
</div>
