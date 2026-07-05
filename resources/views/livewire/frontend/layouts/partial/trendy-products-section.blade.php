{{-- NEW ARRIVALS SECTION --}}
@if($newArrivals->count())
<section class="new-arrivals-section">

    {{-- Header --}}
    <div class="na-section-header">
        <div class="na-section-left">
            <span class="na-section-icon">✨</span>
            <div>
                <h2 class="na-section-title">TRENDY PRODUCTS</h2>
            </div>
        </div>
        <a wire:navigate href="{{ route('frontend.trendy-products.view') }}" class="na-view-all">
            See More <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    {{-- Slider --}}
    <div class="na-slider-wrap">
        <button class="na-arrow na-arrow-left" id="naArrowLeft">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="na-slider" id="naSlider">
            @foreach ($featuredProducts as $product)
             @if($product->slug)
            <a wire:navigate href="{{ route('frontend.product.single.view', $product->slug) }}" class="flash-product-card">

                {{-- Discount Badge --}}
                <div class="flash-discount-badge">{{ number_format($product->discount_percent, 0) }}% OFF</div>

                {{-- Product Image --}}
                <div class="flash-product-image">
                    <img
                        src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/200x200' }}"
                        alt="{{ $product->name }}"
                        loading="lazy" />
                </div>

                {{-- Product Info --}}
                <div class="flash-product-info">
                    <p class="flash-product-name">{{ Str::limit($product->name, 30) }}</p>
                    <div class="flash-product-prices">
                        <span class="flash-original-price-new-arrival">৳{{ number_format($product->price, 0) }}</span>
                    </div>
                </div>

            </a>
            @endif
            @endforeach
        </div>

        <button class="na-arrow na-arrow-right" id="naArrowRight">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

</section>

<style>
    /* ── Section wrapper ───────────────────────────── */
    .new-arrivals-section {
        padding: 0.5rem 0;
    }

    /* ── Header ────────────────────────────────────── */
    .na-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .na-section-left {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .na-section-icon {
        font-size: 1.75rem;
        line-height: 1;
    }

    .na-section-title {
        font-size: 1rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .na-section-sub {
        margin: 0;
        font-size: 0.8rem;
        color: #888;
    }

    .na-view-all:hover {
        gap: 8px;
    }

    /* ── Slider wrapper ────────────────────────────── */
    .na-slider-wrap {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ── Arrow buttons ─────────────────────────────── */
    .na-arrow {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #e0e0e0;
        background: #fff;
        color: #444;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s, color 0.2s, border-color 0.2s;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .na-arrow:hover {
        background: #f97316;
        color: #fff;
        border-color: #f97316;
    }

    .na-arrow:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    /* ── Slider track ──────────────────────────────── */
    .na-slider {
        display: flex;
        gap: 0.7rem;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
        flex: 1;
        padding: 4px 2px 8px;
    }

    .na-slider::-webkit-scrollbar {
        display: none;
    }

    /* ── Product card ──────────────────────────────── */
    .flash-product-card {
        flex: 0 0 240px;
        min-width: 0;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        transition: box-shadow 0.2s, transform 0.2s;
        position: relative;
    }

    .flash-product-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.10);
        transform: translateY(-2px);
    }

    /* ── Discount badge ────────────────────────────── */
    .flash-discount-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #f97316;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 7px;
        border-radius: 5px;
        z-index: 1;
    }

    /* ── Image container ───────────────────────────── */
    /* ✅ Fixed: use padding-top aspect ratio trick so height is never 0 */
    .flash-product-image {
        position: relative;
        width: 100%;
        padding-top: 115%;
        /* 1:1 square — change to 110% for slightly taller */
        background: #f9f9f9;
        overflow: hidden;
    }

    .flash-product-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* changed from contain */
        /* padding: 8px; */
        /* remove this */
        transition: transform 0.3s;
    }

    .flash-product-card:hover .flash-product-image img {
        transform: scale(1.05);
    }

    /* ── Product info ──────────────────────────────── */
    .flash-product-info {
        padding: 8px 10px 10px;
    }

    .flash-product-name {
        font-size: 12px;
        font-weight: 600;
        color: #222;
        margin: 0 0 4px;
        line-height: 1.4;
        /* clamp to 2 lines */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .flash-original-price-new-arrival {
        font-size: 14px;
        font-weight: 700;
        color: #e63946;
    }

    /* ── Responsive card widths ────────────────────── */
    /* Desktop: 200px — already set on .flash-product-card */

    @media (max-width: 768px) {
        .flash-product-card {
            /* ✅ Fixed: wider cards on tablet so more fit in view */
            flex: 0 0 185px;
        }

        .na-arrow {
            width: 30px;
            height: 30px;
            font-size: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .flash-product-card {
            flex: 0 0 170px;
        }

        /* Hide arrows on very small screens — swipe works better */
        .na-arrow {
            display: none;
            /* width: 32px;
            height: 32px;
            font-size: 0.7rem;
            flex-shrink: 0; */
        }

        .na-slider {
            scroll-snap-type: x mandatory;
        }

        .flash-product-card {
            scroll-snap-align: start;
        }
    }
</style>

<script>
    (function() {
        const slider = document.getElementById('naSlider');
        const btnLeft = document.getElementById('naArrowLeft');
        const btnRight = document.getElementById('naArrowRight');

        if (!slider || !btnLeft || !btnRight) return;

        // ✅ Fixed: scroll by one card width + gap instead of fixed 220px
        function getScrollStep() {
            const card = slider.querySelector('.flash-product-card');
            if (!card) return 220;
            const gap = 16; // 1rem gap
            return card.offsetWidth + gap;
        }

        function updateArrows() {
            btnLeft.disabled = slider.scrollLeft <= 0;
            btnRight.disabled = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 1;
        }

        btnLeft.addEventListener('click', () => {
            slider.scrollBy({
                left: -getScrollStep(),
                behavior: 'smooth'
            });
        });

        btnRight.addEventListener('click', () => {
            slider.scrollBy({
                left: getScrollStep(),
                behavior: 'smooth'
            });
        });

        slider.addEventListener('scroll', updateArrows);

        // ✅ Re-check arrows on resize
        window.addEventListener('resize', updateArrows);

        updateArrows();
    })();
</script>
@endif