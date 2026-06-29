<div>
    <section class="hero-section">
        <div class="container">

            {{-- Flash Sale Banner Header --}}
            @if($flashSale)
            <div class="flash-sale-page-header">
                <div class="flash-sale-page-left">
                    <span class="flash-icon-lg">⚡</span>
                    <div>
                        <h1 class="flash-page-title">FLASH SALE</h1>
                        <p class="flash-page-subtitle">{{ $flashSale->title }}</p>
                    </div>
                </div>
                <div class="flash-sale-page-countdown">
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
            </div>
            @endif

            <div class="hero-container">

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
                </aside>

                {{-- Products Grid --}}
                <div class="products-grid-container">
                    <div class="section-header">
                        <h2>⚡ Flash Sale Products</h2>
                    </div>

                    <div class="products-grid">
                        @forelse($items as $item)
                        @if($item->product && $item->product->slug)
                        {{-- Reuse product card with flash sale data injected --}}
                        @php
                        $product = $item->product;
                        $product->flash_discount = $item->discount_percent;
                        $product->flash_price = $item->discounted_price;
                        @endphp
                        @include('livewire.frontend.layouts.partial.product-card', ['product' => $product])
                        @endif
                        @empty
                        <div class="empty-products">
                            <div class="cart-empty">
                                <i class="fas fa-bolt"></i>
                                <p>No flash sale products found.</p>
                                <p>Try adjusting your filters or check back soon for new deals.</p>
                                <a wire:navigate href="{{ route('home') }}" class="btn-continue-shopping">Back to Home</a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <div class="pagination-container">
                        {{ $items->links() }}
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Countdown Script --}}
    @if($flashSale)
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

    <style>
        /* ── Flash Sale Page Header ───────────────────────── */
        .flash-sale-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ff4e00, #ff9900);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .flash-sale-page-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .flash-icon-lg {
            font-size: 2.5rem;
            line-height: 1;
        }

        .flash-page-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
        }

        .flash-page-subtitle {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Countdown (reuses existing styles + overrides for page) */
        .flash-sale-page-countdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .flash-sale-page-countdown .ends-label {
            font-size: 0.85rem;
            opacity: 0.85;
        }

        .flash-sale-page-countdown .countdown {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .flash-sale-page-countdown .countdown-block {
            background: rgba(0, 0, 0, 0.25);
            border-radius: 6px;
            padding: 4px 10px;
            text-align: center;
            min-width: 48px;
        }

        .flash-sale-page-countdown .countdown-num {
            display: block;
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .flash-sale-page-countdown .countdown-label {
            font-size: 0.65rem;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .flash-sale-page-countdown .countdown-sep {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 6px;
        }

        /* ── Category filter buttons ──────────────────────── */
        .category-filter-btn {
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 6px 4px;
            cursor: pointer;
            color: inherit;
            font-size: 0.9rem;
            border-radius: 4px;
            transition: background 0.15s, color 0.15s;
        }

        .category-filter-btn:hover {
            background: #f5f5f5;
        }

        .category-filter-btn.active {
            color: #ff4e00;
            font-weight: 600;
        }

        /* ── Products grid ────────────────────────────────── */
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

        /* ── Empty state ──────────────────────────────────── */
        .empty-products {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem 0;
        }

        .cart-empty {
            text-align: center;
        }

        .cart-empty i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
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