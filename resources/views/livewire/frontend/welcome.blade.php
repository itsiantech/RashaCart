<div class="homepage-layout">
    <main class="homepage-content">
        <div class="mt-md-2">
            <div class="row g-2">
                <!-- LEFT -->
                <div class="col-12 col-lg-3 d-none d-lg-block categories-sidebar">
                    <ul class="categories-list">
                        @foreach ($categories->take(7) as $category)
                        <li class="{{ $category->allChildren->count() ? 'cat-submenu' : '' }}">
                            <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}">
                                @if($category->image)
                                <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" width="20" height="20" style="object-fit:cover; margin-right:5px;">
                                @endif
                                {{ $category->name }}

                                @if ($category->allChildren->count())
                                <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </a>

                            @if ($category->allChildren->count())
                            <ul class="submenu">
                                @include('livewire.frontend.layouts.partial.category.subcategory-list', ['categories' => $category->allChildren])
                            </ul>
                            @endif
                        </li>
                        @endforeach
                        @if ($categories->count() > 6)
                        <li><a wire:navigate href="{{ route('frontend.category.view') }}"><i class="fas fa-ellipsis-h"></i> View All Categories</a></li>
                        @endif
                    </ul>
                </div>

                <!-- CENTER (BANNER) -->
                <div class="col-12 col-lg-9 banner-middle">
                    @include('livewire.frontend.layouts.partial.banner.banner-default')
                </div>

                <!-- RIGHT -->
                <div class="col-12 col-lg-2 right-side-cards d-none">
                    <div class="row g-2">

                        <!-- Card 1 -->
                        <div class="col-6 col-lg-12">
                            <div class="card skybuy-card text-center shadow-sm">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-2">
                                        <img src="https://skybuybd.com/_next/static/media/logo.2d8160b9.svg"
                                            class="skybuy-logo-img">
                                    </div>
                                    <h2 class="skybuy-text">হোলসেল মার্কেটপ্লেস</h2>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="col-6 col-lg-12">
                            <div class="card skyship-card text-center shadow-sm">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-2">
                                        <img src="https://skyshipbd.com/_next/static/media/logo.28c56c27.svg"
                                            class="skybuy-logo-img">
                                    </div>
                                    <h3 class="skybuy-text">দ্রুত ও নির্ভরযোগ্য শিপমেন্ট</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="">

            <section class="categories-section">
                <div class="categories-grid">
                    @foreach ($featuredCategories->take(20) as $index => $category)
                    <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}"
                        class="category-card item-{{ $index + 1 }}">
                        <div class="category-image">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://placehold.co/500x500' }}"
                                alt="{{ $category->name }}" loading="lazy">
                        </div>
                        <h3 class="category-name">{{ $category->name }}</h3>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- BEST OFFER / FLASH SALE (3 Products in mobile screen) -->
            @if($flashSale && $flashSaleItems->count())
            <section class="flash-sale-section">

                {{-- Header --}}
                <div class="flash-sale-header">
                    <div class="flash-sale-left">
                        <span class="flash-icon">⚡</span>
                        <div>
                            <h2 class="flash-title">FLASH SALE</h2>
                            <p class="flash-subtitle">{{ $flashSale->title }}</p>
                        </div>
                    </div>

                    <div class="flash-sale-center">
                        <span class="ends-label">Ends in:</span>
                        <div class="countdown" id="flash-countdown">
                            <div class="countdown-block">
                                <span class="countdown-num" id="fs-hours">00</span>
                                <span class="countdown-label">HR</span>
                            </div>
                            <span class="countdown-sep">:</span>
                            <div class="countdown-block">
                                <span class="countdown-num" id="fs-mins">00</span>
                                <span class="countdown-label">MIN</span>
                            </div>
                            <span class="countdown-sep">:</span>
                            <div class="countdown-block">
                                <span class="countdown-num" id="fs-secs">00</span>
                                <span class="countdown-label">SEC</span>
                            </div>
                        </div>
                    </div>

                    <div class="flash-sale-right">
                        <a wire:navigate href="{{ route('frontend.flash.sale.products') }}" class="see-more-link">
                            See More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Products --}}
                <div class="flash-products-grid">
                    @foreach($flashSaleItems->take(6) as $item)
                    @if($item->product && $item->product->slug)
                    <a wire:navigate href="{{ route('frontend.product.single.view', $item->product->slug) }}" class="flash-product-card">
                        
                        {{-- Discount Badge --}}
                        <div class="flash-discount-badge d-none">{{ number_format($item->discount_percent, 0) }}% OFF</div>

                        {{-- Product Image --}}
                        <div class="flash-product-image">
                            <img
                                src="{{ $item->product->thumbnail ? asset('storage/' . $item->product->thumbnail) : 'https://placehold.co/200x200' }}"
                                alt="{{ $item->product->name }}"
                                loading="lazy" />
                        </div>

                        {{-- Product Info --}}
                        <div class="flash-product-info">
                            <p class="flash-product-name">{{ Str::limit($item->product->name, 30) }}</p>
                            <div class="flash-product-prices">
                                <span class="flash-sale-price">৳{{ number_format($item->discounted_price, 0) }}</span>
                                <span class="flash-original-price d-none">৳{{ number_format($item->product->price, 0) }}</span>
                            </div>
                        </div>

                    </a>
                    @endif
                    @endforeach
                </div>

            </section>
            {{-- ✅ Move script INSIDE the @if block --}}
            <script>
                (function() {
                    const endTime = new Date("{{ $flashSale->end_at }}").getTime();

                    function updateCountdown() {
                        const now = Date.now();
                        const diff = endTime - now;

                        if (diff <= 0) {
                            document.getElementById('fs-hours').textContent = '00';
                            document.getElementById('fs-mins').textContent = '00';
                            document.getElementById('fs-secs').textContent = '00';
                            return;
                        }

                        const h = Math.floor(diff / 3600000);
                        const m = Math.floor((diff % 3600000) / 60000);
                        const s = Math.floor((diff % 60000) / 1000);

                        document.getElementById('fs-hours').textContent = String(h).padStart(2, '0');
                        document.getElementById('fs-mins').textContent = String(m).padStart(2, '0');
                        document.getElementById('fs-secs').textContent = String(s).padStart(2, '0');
                    }

                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                })();
            </script>
            @endif

            <!-- NEW Arrivals Section -->
            @include('livewire.frontend.layouts.partial.new-arrivals-section', [
            'newArrivals' => $newArrivals
            ])

            <!-- Trendy Products Section -->
            @include('livewire.frontend.layouts.partial.trendy-products-section',[
            'featuredProducts' => $featuredProducts
            ])

            <section class="categories-section d-none">
                <div class=" d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                        <span class="na-section-icon">✨</span>
                        <h2 style="font-weight: 800; font-size: 20px; margin-left: 10px;">BRANDS</h2>
                    </div>
                    <a wire:navigate href="{{ route('frontend.brand.view') }}" class="na-view-all">
                        See More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="categories-grid">
                    @foreach ($featuredBrands->take(20) as $index => $brand)
                    <a wire:navigate href="{{ route('frontend.brands.products.view', $brand->slug) }}"
                        class="brand-card item-{{ $index + 1 }}">
                        <div class="brand-image">
                            <img src="{{ $brand->logo ? asset('storage/' . $brand->logo) : 'https://placehold.co/500x500' }}"
                                alt="{{ $brand->name }}" loading="lazy">
                        </div>
                        <h3 class="d-none category-name">{{ $brand->name }}</h3>
                    </a>
                    @endforeach
                </div>
            </section>

            <div wire:init="loadProducts">

                <!-- All Products -->
                <div class="tab-content active" id="all-products">
                    <div class="section-header">
                        <div class="section-left">
                            <h2>FOR YOU</h2>
                        </div>
                    </div>

                    <div class="products-grid">
                        @if (count($allProducts) === 0)
                        <p class="text-center text-muted">Loading products...</p>
                        @else
                        @foreach ($allProducts as $product)
                        @include('livewire.frontend.layouts.partial.product-card', [
                        'product' => $product,
                        ])
                        @endforeach
                        @endif
                    </div>

                    @if ($allProductsLoadedCount < Cache::get('products', collect())->count())
                        <div class="text-center my-3">
                            <button class="btn-order-now btn-load-more" wire:click="loadMoreAllProducts"
                                wire:loading.attr="disabled" wire:target="loadMoreAllProducts">
                                <span wire:loading.remove wire:target="loadMoreAllProducts">
                                    Load More..
                                </span>
                                <span wire:loading wire:target="loadMoreAllProducts">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </div>
                        @endif
                </div>

            </div>
        </div>

        <script>
            function toggleCategory(id) {

                let element = document.getElementById("cat-" + id);

                if (element.style.display === "block") {
                    element.style.display = "none";
                } else {
                    element.style.display = "block";
                }

            }

            function toggleBrands() {

                document.getElementById("brands-dropdown").classList.toggle("show");

                if (element.style.display === "block") {
                    element.style.display = "none";
                } else {
                    element.style.display = "block";
                }

            }
        </script>
        <style>

            .tab-content {
                padding-bottom: 25px;
                margin-top: 10px;
            }
            .homepage-content {
                padding: 0px 10px;
                background: white;
            }

            @media (max-width: 576px) {
                .homepage-content {
                    padding: 0px 10px;
                    padding-bottom: 10px;
                }
            }

            .homepage-layout {
                max-width: 1600px;
                margin: auto;
            }

            .btn-load-more {
                width: auto !important;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 18px;
                font-size: 14px;
                border-radius: 1px;
                margin-bottom: 35px;
            }

            .categories-sidebar {
                background: rgb(241, 240, 240);
                border-radius: 5px;
                overflow: hidden;
            }

            .categories-list a {
                font-size: 14px !important;
                font-weight: 600 !important;
                font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            }

            .category-list {
                list-style: none;
                padding: 0;
            }

            .category-item {
                border-bottom: 1px solid #f1f1f1;
            }

            .brand-header {
                display: flex;
                justify-content: space-between;
                cursor: pointer;
                font-weight: 500;
            }

            .brand-header i {
                margin-top: 2px;
            }

            .brand-list {
                display: none;
                font-size: 16px !important;
                font-weight: 500 !important;
                margin-top: 5px;
            }

            .brands-item {
                border-bottom: 1px solid #f1f1f1;
                padding: 5px 0px;
            }

            .brand-list.show {
                display: block;
            }

            .brand-item {
                border-top: 1px solid black;
                padding-top: 5px;
            }

            @media (max-width: 767px) {
                .banner-middle {
                    max-height: none;
                    width: 100%;
                }
            }

            .skybuy-card {
                background-color: #1a7a8b;
                border-radius: 10px;
                padding: 8px 5px;
                border: none;
            }

            .card-body {
                height: 133px;
            }

            .skyship-card {
                background-color: rgb(58, 86, 247);
                border-radius: 10px;
                padding: 8px 5px;
                border: none;
            }

            @media (min-width: 2180px) {
                .skybuy-card {
                    padding: 30px 0;
                }

                .skyship-card {
                    padding: 30px 0px;
                }
            }

            @media (max-width: 767px) {
                .right-side-cards .card {
                    border-radius: 10px;
                    height: 100%;
                    min-height: 100px;
                    /* add this */
                }

                .right-side-cards .card-body {
                    padding: 0px 8px;
                    height: auto;
                    /* let it grow naturally */
                }

                .skybuy-logo-img {
                    max-width: 55px;
                }

                .skybuy-text {
                    font-size: 11px;
                    margin-top: 4px;
                }
            }

            .skybuy-card {
                background-color: #1a7a8b;
                border-radius: 10px;
                padding: 8px 5px;
                border: none;
            }

            .skyship-card {
                background-color: rgb(58, 86, 247);
                border-radius: 10px;
                padding: 8px 5px;
                border: none;
            }

            .skybuy-logo-img {
                max-width: 70px;
                height: auto;
            }

            .skybuy-text {
                color: #ffffff;
                font-size: 16px;
                font-weight: 500;
                margin-top: 5px;
                font-family: 'Hind Siliguri', 'Segoe UI', Tahoma, sans-serif;
            }
        </style>
</div>