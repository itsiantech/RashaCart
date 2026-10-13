@php
    // ── Dynamic helpers ──
    $cur = setting('currency') ?: '৳';
    $fmt = function ($n) {
        $n = (float) $n;
        return number_format($n, fmod($n, 1) ? 2 : 0);
    };

    $showDiscount = $strikePrice > $displayPrice && $strikePrice > 0;
    $discountPercent = $showDiscount ? (int) round((($strikePrice - $displayPrice) / $strikePrice) * 100) : 0;

    $phoneRaw = setting('contact_phone') ?: '';
    $waNumber = preg_replace('/\D+/', '', $phoneRaw);
    $waText = rawurlencode(
        'Hello, I want to order: ' .
            $product->name .
            ($product->sku ? ' (SKU: ' . $product->sku . ')' : '') .
            ' - ' .
            $cur .
            $fmt($displayPrice),
    );

    $soldBy = $product->user->name ?? 'Admin';
    $brandName = $product->brand->name ?? null;

    // Category ancestor chain for breadcrumb
    $categoryChain = collect();
    $catCursor = $product->category;
    while ($catCursor) {
        $categoryChain->prepend($catCursor);
        $catCursor = $catCursor->parent;
    }

    // ── Image options own the gallery when present ──
    $optionItems = [];
    $hasOptions = false;
    if ($product->relationLoaded('imageOptions')) {
        $activeOpts = $product->imageOptions->where('is_active', true)->sortBy('sort_order')->values();
        $hasOptions = $activeOpts->isNotEmpty();
        foreach ($activeOpts as $ao) {
            $optionItems[] = ['src' => asset('storage/'.$ao->image), 'value' => null, 'option_id' => $ao->id];
        }
    }

    // ── Gallery images (each item: src + optional swatch value/option for thumb→select sync) ──
    $galleryItems = [];

    if ($hasOptions) {
        // Options own the gallery: selected option's image first
        if (!empty($selectedOption)) {
            usort($optionItems, fn ($a, $b) => (($b['option_id'] ?? null) == $selectedOption) <=> (($a['option_id'] ?? null) == $selectedOption));
        }
        $galleryItems = $optionItems;
    } else {
        $legacyUrls = !empty($product->gallery_image) && is_array($product->gallery_image)
            ? collect($product->gallery_image)->map(fn($img) => ['src' => asset('storage/' . $img), 'value' => null, 'option_id' => null])->values()->all()
            : [];
        $galleryItems = $legacyUrls;

        $initialImage = $activeVariant && $activeVariant->image
            ? asset('storage/' . $activeVariant->image)
            : ($product->thumbnail
                ? asset('storage/' . $product->thumbnail)
                : null);

        if ($initialImage) {
            $galleryItems = collect($galleryItems)->reject(fn($it) => $it['src'] === $initialImage)->values()->all();
            array_unshift($galleryItems, ['src' => $initialImage, 'value' => null, 'option_id' => null]);
        }

        // ── Swatch-linked gallery (product_images): matched value's images first ──
        if ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
            $selValues = array_filter([$selectedColor ?? null, $selectedSize ?? null]);
            [$matched, $rest] = $product->images->sortBy('sort_order')->partition(
                fn ($im) => $im->attributeValue && in_array($im->attributeValue->value, $selValues, true)
            );
            $extra = $matched->merge($rest)
                ->map(fn ($im) => ['src' => asset('storage/'.$im->path), 'value' => $im->attributeValue?->value, 'option_id' => null])
                ->reject(fn ($it) => collect($galleryItems)->contains('src', $it['src']))->values()->all();
            array_splice($galleryItems, 1, 0, $extra);
        }
    }

    $galleryUrls = array_column($galleryItems, 'src');

    if (empty($galleryItems)) {
        $galleryItems = [['src' => 'https://placehold.co/600x600', 'value' => null]];
        $galleryUrls = ['https://placehold.co/600x600'];
    }
    $galleryFirst = $galleryItems[0]['src'];

    // ── Reviews summary ──
    $reviewCount = (int) ($product->reviews_count ?? 0);
@endphp

<div class="bg-white">
    <style>
        /* ===== Product Details Page ===== */
        .pd-crumb-bar {
            background: #fff;
            border-bottom: 1px solid var(--line, #e5e7eb);
        }

        .pd-crumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            font-size: .8rem;
            color: #6b7280;
            padding: 10px 0;
            margin: 0;
            list-style: none;
        }

        .pd-crumb a {
            color: #6b7280;
            text-decoration: none;
        }

        .pd-crumb a:hover {
            color: var(--navy, #12225B);
        }

        .pd-crumb .sep {
            font-size: .6rem;
            color: #9ca3af;
        }

        .pd-crumb .cur {
            color: #111827;
            font-weight: 600;
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pd-wrap {
            margin-top: 16px;
        }

        .pd-box {
            background: #fff;
            border: 1px solid var(--line, #e5e7eb);
            border-radius: 12px;
            padding: 20px;
        }

        .pd-box + .pd-box {
            margin-top: 16px;
        }

        /* ---- Top product box (3 cols on desktop) ---- */
        .pd-product {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 992px) {
            .pd-product {
                grid-template-columns: minmax(300px, 400px) 1fr;
            }

            .pd-aside {
                grid-column: 1 / -1;
            }
        }

        @media (min-width: 1200px) {
            .pd-product {
                grid-template-columns: minmax(320px, 420px) 1fr 260px;
            }

            .pd-aside {
                grid-column: auto;
            }
        }

        /* ---- Gallery / zoom ---- */
        .pd-gallery {
            min-width: 0;
        }

        .pd-main-image {
            position: relative;
            aspect-ratio: 1 / 1;
            background: #fafafa;
            border: 1px solid var(--line, #e5e7eb);
            border-radius: 10px;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .pd-main-image {
                cursor: zoom-in;
            }
        }

        .pd-main-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform-origin: var(--ox, 50%) var(--oy, 50%);
            transition: transform .18s ease;
            will-change: transform;
        }

        .pd-main-image.pd-zoomed img {
            transform: scale(2.2);
        }

        @media (max-width: 991.98px) {
            .pd-main-image.pd-zoomed img {
                transform: none;
            }
        }

        .pd-zoom-hint {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background: rgba(17, 24, 39, .72);
            color: #fff;
            font-size: .68rem;
            padding: 4px 9px;
            border-radius: 20px;
            pointer-events: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 991.98px) {
            .pd-zoom-hint {
                display: none !important;
            }
        }

        .pd-thumbs {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .pd-thumb {
            flex: 0 0 64px;
            width: 64px;
            height: 64px;
            border: 2px solid var(--line, #e5e7eb);
            border-radius: 8px;
            background: #fff;
            padding: 2px;
            cursor: pointer;
        }

        .pd-thumb.active {
            border-color: #111827;
        }

        .pd-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 5px;
        }

        /* ---- Info column ---- */
        .pd-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.4;
            margin-bottom: 6px;
        }

        .stars {
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .stars i {
            font-size: .62rem;
            color: #111827;
        }

        .stars .rating-count {
            margin-left: 5px;
            font-size: .72rem;
            color: #6b7280;
        }

        .pd-price-row {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0 6px;
        }

        .pd-price {
            font-size: 1.55rem;
            font-weight: 800;
            color: #111827;
        }

        .pd-price-old {
            font-size: .95rem;
            color: #9ca3af;
            text-decoration: line-through;
        }

        .pd-off {
            color: #dc2626;
            font-weight: 700;
            font-size: .9rem;
        }

        .pd-flash {
            background: #dc2626;
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .pd-stock {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .pd-stock--in {
            background: #ecfdf5;
            color: #047857;
        }

        .pd-stock--out {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* ---- Variants ---- */
        .pd-variant-group + .pd-variant-group {
            margin-top: 14px;
        }

        .pd-opt-label {
            font-size: .78rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 7px;
        }

        .pd-opt-label .sel {
            color: #6b7280;
            font-weight: 500;
        }

        .pd-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pd-chip {
            border: 1px solid var(--line, #e5e7eb);
            background: #fff;
            color: #374151;
            font-size: .78rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all .15s;
        }

        .pd-chip:hover {
            border-color: #111827;
        }

        .pd-chip.active {
            border: 2px solid #111827;
            padding: 5px 13px;
        }

        .pd-chip:disabled {
            opacity: .4;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .pd-chip img {
            width: 26px;
            height: 26px;
            border-radius: 4px;
            object-fit: cover;
        }

        /* ---- 64x64 image swatches (color section) ---- */
        .pd-swatch {
            border: 2px solid var(--line, #e5e7eb);
            background: #fff;
            border-radius: 8px;
            padding: 3px;
            cursor: pointer;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            transition: all .15s;
        }

        .pd-swatch img {
            width: 64px;
            height: 64px;
            border-radius: 5px;
            object-fit: cover;
            display: block;
        }

        .pd-swatch-fill {
            width: 64px;
            height: 64px;
            border-radius: 5px;
            display: block;
        }

        .pd-swatch:hover {
            border-color: #111827;
        }

        .pd-swatch.active {
            border-color: #f97316;
            box-shadow: 0 0 0 1px #f97316;
        }

        .pd-swatch:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        /* ---- Quantity ---- */
        .pd-qty {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--line, #e5e7eb);
            border-radius: 6px;
            overflow: hidden;
        }

        .pd-qty button {
            width: 38px;
            height: 40px;
            border: 0;
            background: #f9fafb;
            color: #111827;
            font-size: .85rem;
            cursor: pointer;
        }

        .pd-qty button:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .pd-qty input {
            width: 58px;
            height: 40px;
            border: 0;
            border-left: 1px solid var(--line, #e5e7eb);
            border-right: 1px solid var(--line, #e5e7eb);
            text-align: center;
            font-weight: 700;
            color: #111827;
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .pd-qty input::-webkit-outer-spin-button,
        .pd-qty input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* ---- Action buttons ---- */
        .pd-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 16px;
        }

        .pd-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            border: 0;
            border-radius: 6px;
            font-size: .86rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: opacity .15s;
        }

        .pd-btn:hover {
            opacity: .9;
            color: #fff;
        }

        .pd-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .pd-btn--cart {
            background: var(--orange, #f97316);
        }

        .pd-btn--buy {
            background: var(--navy, #12225B);
        }

        .pd-btn--wa {
            background: #25D366;
        }

        .pd-btn--call {
            background: var(--navy, #12225B);
        }

        .pd-warning {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: .78rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 9px 12px;
            margin-top: 12px;
        }

        .pd-warning .warning-close {
            border: 0;
            background: transparent;
            color: #b91c1c;
            cursor: pointer;
            font-size: .8rem;
        }

        /* ---- Right meta column ---- */
        .pd-aside {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .pd-meta-row {
            display: flex;
            gap: 10px;
            font-size: .78rem;
            line-height: 1.5;
        }

        .pd-meta-ic {
            flex: 0 0 18px;
            color: #6b7280;
            text-align: center;
            margin-top: 2px;
        }

        .pd-meta-label {
            color: #6b7280;
            display: block;
            font-size: .72rem;
        }

        .pd-meta-value {
            color: #111827;
            font-weight: 600;
        }

        .pd-seller {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pd-seller-av {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid var(--line, #e5e7eb);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--navy, #12225B);
            background: #fff;
        }

        .pd-seller-badges {
            display: flex;
            flex-direction: column;
            gap: 1px;
            font-size: .68rem;
            color: #059669;
            font-weight: 600;
        }

        .pd-brand-chip {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: var(--navy, #12225B);
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            letter-spacing: .5px;
        }

        /* ---- Sections ---- */
        .pd-sec-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--line, #e5e7eb);
        }

        .pd-desc-body {
            color: #374151;
            font-size: .86rem;
            line-height: 1.75;
        }

        .pd-desc-body img {
            max-width: 100%;
            height: auto;
        }

        /* ---- Reviews ---- */
        .pd-rev-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
        }

        @media (min-width: 768px) {
            .pd-rev-grid {
                grid-template-columns: 220px 1fr;
            }
        }

        .pd-rev-avg {
            text-align: center;
        }

        .pd-rev-avg .num {
            font-size: 2.6rem;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .pd-rev-avg .lbl {
            font-size: .74rem;
            color: #6b7280;
            margin-top: 6px;
        }

        .pd-bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: .74rem;
            color: #6b7280;
        }

        .pd-bar-row .lbl {
            flex: 0 0 34px;
        }

        .pd-bar {
            flex: 1;
            height: 9px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
        }

        .pd-bar span {
            display: block;
            height: 100%;
            background: #f5a623;
            border-radius: 20px;
        }

        .pd-bar-row .cnt {
            flex: 0 0 24px;
            text-align: right;
        }

        .pd-no-reviews {
            color: #9ca3af;
            font-size: .84rem;
            padding: 14px 0 4px;
        }

        .pd-review-item {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-top: 1px solid var(--line, #e5e7eb);
        }

        .pd-review-av {
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--navy, #12225B);
            color: #fff;
            font-weight: 700;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pd-review-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pd-review-name {
            font-size: .82rem;
            font-weight: 700;
            color: #111827;
        }

        .pd-review-date {
            font-size: .7rem;
            color: #9ca3af;
        }

        .pd-review-comment {
            font-size: .82rem;
            color: #4b5563;
            margin: 4px 0 0;
            line-height: 1.6;
        }

        /* ---- Review form ---- */
        .pd-rev-form {
            border-top: 1px solid var(--line, #e5e7eb);
            margin-top: 6px;
            padding-top: 16px;
        }

        .pd-star-pick {
            display: inline-flex;
            gap: 4px;
        }

        .pd-star-pick button {
            border: 0;
            background: transparent;
            padding: 0 1px;
            color: #d1d5db;
            font-size: 1.1rem;
            cursor: pointer;
            line-height: 1;
        }

        .pd-star-pick button.on {
            color: #f5a623;
        }

        .pd-input {
            width: 100%;
            border: 1px solid var(--line, #e5e7eb);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: .84rem;
            color: #111827;
            resize: vertical;
        }

        .pd-input:focus {
            outline: none;
            border-color: var(--navy, #12225B);
        }

        .pd-submit {
            background: var(--navy, #12225B);
            color: #fff;
            border: 0;
            border-radius: 6px;
            height: 40px;
            padding: 0 22px;
            font-size: .84rem;
            font-weight: 700;
            cursor: pointer;
        }

        /* ---- Related products grid ---- */
        .pd-rel-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        @media (min-width: 576px) {
            .pd-rel-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 992px) {
            .pd-rel-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        .pd-rel-card {
            border: 1px solid var(--line, #e5e7eb);
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .pd-rel-img {
            position: relative;
            aspect-ratio: 1 / 1;
            background: #fafafa;
            display: block;
        }

        .pd-rel-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }

        .pd-sale-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--navy, #12225B);
            color: #fff;
            font-size: .6rem;
            font-weight: 800;
            letter-spacing: .4px;
            padding: 3px 7px;
            border-radius: 4px;
            z-index: 2;
        }

        .pd-sale-badge--flash {
            background: #dc2626;
        }

        .pd-rel-body {
            padding: 10px 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .pd-rel-title {
            font-size: .8rem;
            font-weight: 600;
            color: #111827;
            text-decoration: none;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.24em;
        }

        .pd-rel-title:hover {
            color: var(--navy, #12225B);
        }

        .pd-rel-price {
            font-size: .88rem;
            font-weight: 800;
            color: var(--navy, #12225B);
            display: flex;
            align-items: baseline;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pd-rel-price .old {
            font-size: .72rem;
            color: #9ca3af;
            text-decoration: line-through;
            font-weight: 500;
        }

        .pd-rel-price .pct {
            font-size: .72rem;
            color: #dc2626;
            font-weight: 700;
        }

        .pd-rel-cart {
            margin-top: auto;
            width: 100%;
            height: 34px;
            border: 1px solid var(--navy, #12225B);
            border-radius: 6px;
            background: #fff;
            color: var(--navy, #12225B);
            font-size: .74rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all .15s;
        }

        .pd-rel-cart:hover {
            background: var(--navy, #12225B);
            color: #fff;
        }

        /* ---- Sticky mobile bar ---- */
        .pd-sticky-bar {
            display: none;
        }

        .pd-sticky-spacer {
            display: none;
        }

        @media (max-width: 768px) {
            .pd-sticky-bar {
                display: flex;
                gap: 8px;
                position: fixed;
                left: 0;
                right: 0;
                bottom: 58px;
                z-index: 999;
                background: #fff;
                border-top: 1px solid var(--line, #e5e7eb);
                padding: 8px 12px;
            }

            .pd-sticky-bar .pd-fav {
                flex: 0 0 46px;
                height: 44px;
                border: 1px solid var(--line, #e5e7eb);
                border-radius: 6px;
                background: #fff;
                color: #111827;
                font-size: 1rem;
                cursor: pointer;
            }

            .pd-sticky-bar .pd-btn {
                flex: 1;
            }

            .pd-sticky-spacer {
                display: block;
                height: 66px;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- ===== Breadcrumb ===== --}}
    <div class="pd-crumb-bar">
        <div class="hp-container">
            <nav class="pd-crumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                @foreach ($categoryChain as $chainCat)
                    <i class="fas fa-chevron-right sep"></i>
                    <a href="{{ route('frontend.category.view', $chainCat->slug) }}">{{ $chainCat->name }}</a>
                @endforeach
                <i class="fas fa-chevron-right sep"></i>
                <span class="cur">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <div class="hp-container pd-wrap">

        {{-- ===== Top Box: Gallery + Info + Meta ===== --}}
        <div class="pd-box">
            <div class="pd-product">

                {{-- Gallery --}}
                <div class="pd-gallery" x-data="{
                    images: @js($galleryItems),
                    activeIndex: 0,
                    zoom: false,
                    ox: 50,
                    oy: 50,
                    addOrJumpTo(src) {
                        if (!src) return;
                        let idx = this.images.findIndex(i => i.src === src);
                        if (idx === -1) {
                            this.images.unshift({src: src, value: null});
                            idx = 0;
                        }
                        this.activeIndex = idx;
                    },
                    thumbClick(index) {
                        this.activeIndex = index;
                        let it = this.images[index];
                        if (it && it.option_id) $wire.selectOption(it.option_id);
                        else if (it && it.value) $wire.selectColor(it.value);
                    },
                    onMove(e) {
                        const r = e.currentTarget.getBoundingClientRect();
                        this.ox = ((e.clientX - r.left) / r.width) * 100;
                        this.oy = ((e.clientY - r.top) / r.height) * 100;
                    }
                }"
                    @set-main-image.window="if ($event.detail.src) addOrJumpTo($event.detail.src)">

                    <div class="pd-main-image" :class="{ 'pd-zoomed': zoom }"
                        :style="'--ox:' + ox + '%; --oy:' + oy + '%;'"
                        @mouseenter="zoom = window.innerWidth >= 992" @mouseleave="zoom = false"
                        @mousemove="onMove($event)">
                        <img src="{{ $galleryFirst }}" :src="images[activeIndex].src" alt="{{ $product->name }}">
                        <span class="pd-zoom-hint" x-show="!zoom" x-cloak>
                            <i class="fas fa-search-plus"></i> Hover to zoom
                        </span>
                    </div>

                    <div class="pd-thumbs" x-show="images.length > 1" x-cloak>
                        <template x-for="(img, index) in images" :key="index">
                            <button type="button" class="pd-thumb" :class="{ 'active': activeIndex === index }"
                                @click="thumbClick(index)" :aria-label="'View image ' + (index + 1)">
                                <img :src="img.src" :alt="'{{ $product->name }} thumbnail ' + (index + 1)">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Info --}}
                <div class="pd-info">
                    <h1 class="pd-title">{{ $product->name }}</h1>

                    <x-rating-stars :rating="$product->reviews_avg_rating ?? 0" :count="$reviewCount" />

                    <div class="pd-price-row">
                        <span class="pd-price">{{ $cur }}{{ $fmt($displayPrice) }}</span>

                        @if ($showDiscount)
                            <span class="pd-price-old">{{ $cur }}{{ $fmt($strikePrice) }}</span>
                            <span class="pd-off">-{{ $discountPercent }}%</span>
                        @endif

                        @if ($flashSaleItem)
                            <span class="pd-flash">Flash Sale -{{ $flashSaleItem->discount_percent }}%</span>
                        @endif
                    </div>

                    <div>
                        <span class="pd-stock {{ $isInStock ? 'pd-stock--in' : 'pd-stock--out' }}">
                            <i class="fas {{ $isInStock ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $isInStock ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>

                    @if ($product->short_description)
                        <p class="text-muted mt-2 mb-0" style="font-size:.82rem;">
                            {{ $product->short_description }}</p>
                    @endif

                    {{-- ===== Variants ===== --}}
                    @if (setting('attribute_system', true) && $product->has_attributes && $enableVariant && $product->variants->count())
                        @php
                            $allAttributeNames = collect();
                            foreach ($product->variants as $variant) {
                                foreach ($variant->variantAttributeValues as $vav) {
                                    $attrName = $vav->attribute?->name ?? 'Variant';
                                    if (!$allAttributeNames->contains($attrName)) {
                                        $allAttributeNames->push($attrName);
                                    }
                                }
                            }
                            $allAttributeNames = $allAttributeNames
                                ->sortBy(function ($name) {
                                    $lower = strtolower($name);
                                    if (str_contains($lower, 'color') || str_contains($lower, 'colour')) {
                                        return 0;
                                    }
                                    if (str_contains($lower, 'size')) {
                                        return 1;
                                    }
                                    return 2;
                                })
                                ->values();
                        @endphp

                        <div class="mt-3">
                            @php $reqAttrs = $this->requiredAttributeNames(); @endphp
                            @foreach ($allAttributeNames as $attributeName)
                                @php
                                    $attributeValues = collect();
                                    foreach ($product->variants as $variant) {
                                        foreach ($variant->variantAttributeValues as $vav) {
                                            if ($vav->attribute?->name === $attributeName) {
                                                $value = $vav->attributeValue?->value ?? 'Variant';
                                                if (!$attributeValues->contains('value', $value)) {
                                                    // Gallery-linked image for this value? (drives thumbnail
                                                    // selection + auto-hide of the color row)
                                                    $hasGallery = false;
                                                    if ($vav->attributeValue && $product->relationLoaded('images')) {
                                                        $hasGallery = $product->images->contains(
                                                            fn ($im) => (int) $im->attribute_value_id === (int) $vav->attributeValue->id
                                                        );
                                                    }
                                                    $attributeValues->push([
                                                        'value' => $value,
                                                        'value_id' => $vav->attributeValue?->id,
                                                        'variant_id' => $variant->id,
                                                        'image' => $variant->image,
                                                        'color_hex' => $vav->attributeValue?->color_hex,
                                                        'swatch_image' => $vav->attributeValue?->swatch_image,
                                                        'has_gallery' => $hasGallery,
                                                    ]);
                                                }
                                                break;
                                            }
                                        }
                                    }

                                    $attrNameLower = strtolower($attributeName);
                                    $isColorAttribute =
                                        str_contains($attrNameLower, 'color') || str_contains($attrNameLower, 'colour');
                                    $isSizeAttribute = str_contains($attrNameLower, 'size');
                                    // Hide the color row when EVERY value has a linked gallery image:
                                    // thumbnails then act as the selector (clicking one auto-selects).
                                    $hideColorRow = $isColorAttribute
                                        && $attributeValues->isNotEmpty()
                                        && $attributeValues->every(fn ($o) => !empty($o['has_gallery']));
                                @endphp

                                @if (!$hideColorRow)
                                <div class="pd-variant-group">
                                    <div class="pd-opt-label">
                                        {{ $attributeName }}
                                        @if (in_array($attributeName, $reqAttrs))<span class="text-danger" title="বাছতেই হবে">*</span>
                                        @else<span class="sel">(optional)</span>@endif
                                        @if ($isColorAttribute && !empty($selectedColor))
                                            <span class="sel">(Selected: {{ $selectedColor }})</span>
                                        @elseif ($isSizeAttribute && !empty($selectedSize))
                                            <span class="sel">(Selected: {{ $selectedSize }})</span>
                                        @endif
                                    </div>

                                    <div class="pd-chips">
                                        @foreach ($attributeValues as $attrOption)
                                            @php
                                                $matchingVariant = null;
                                                foreach ($product->variants as $v) {
                                                    foreach ($v->variantAttributeValues as $vav) {
                                                        if (
                                                            $vav->attribute?->name === $attributeName &&
                                                            $vav->attributeValue?->value === $attrOption['value']
                                                        ) {
                                                            $matchingVariant = $v;
                                                            break 2;
                                                        }
                                                    }
                                                }
                                                $variantStock =
                                                    $matchingVariant?->inventories->sortByDesc('id')->first()
                                                        ?->current_stock ?? 0;
                                                $variantLabel = $attrOption['value'];
                                                $hasImage = !empty($attrOption['image']);
                                                // Image swatch source: variant image → value swatch image
                                                // → swatch-gallery image linked to this value → null
                                                $linkedGallery = null;
                                                if (!empty($attrOption['value_id']) && $product->relationLoaded('images')) {
                                                    $linkedGallery = $product->images->first(
                                                        fn ($im) => (int) $im->attribute_value_id === (int) $attrOption['value_id']
                                                    );
                                                }
                                                $swatchSrc = $hasImage
                                                    ? asset('storage/' . $attrOption['image'])
                                                    : (!empty($attrOption['swatch_image'])
                                                        ? asset('storage/' . $attrOption['swatch_image'])
                                                        : ($linkedGallery
                                                            ? asset('storage/' . $linkedGallery->path)
                                                            : null));
                                                $isOut = $variantStock <= 0;

                                                if ($isColorAttribute) {
                                                    $isSelected = $selectedColor == $variantLabel;
                                                } elseif ($isSizeAttribute) {
                                                    $isSelected = $selectedSize == $variantLabel;
                                                } else {
                                                    $isSelected = $selectedVariant == $attrOption['variant_id'];
                                                }
                                            @endphp

                                            @if ($isColorAttribute)
                                            {{-- Uniform 64x64 swatches: picture if available, else color fill.
                                                No text on/under swatches; name in tooltip + Selected header. --}}
                                            @php $fillHex = $attrOption['color_hex'] ?? '#e5e7eb'; @endphp
                                            <button type="button" class="pd-swatch {{ $isSelected ? 'active' : '' }}"
                                                @if (!$isOut) wire:click="selectColor('{{ $variantLabel }}')"
                                                    @if ($swatchSrc) @click="$dispatch('set-main-image', { src: '{{ $swatchSrc }}' })" @endif
                                                @endif
                                                @disabled($isOut) aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                                title="{{ $variantLabel }}" aria-label="Select color {{ $variantLabel }}">
                                                @if ($swatchSrc)
                                                <img src="{{ $swatchSrc }}" alt="{{ $variantLabel }}"
                                                    width="64" height="64" loading="lazy">
                                                @else
                                                <span class="pd-swatch-fill" style="background:{{ $fillHex }};"></span>
                                                @endif
                                            </button>
                                            @else
                                            <button type="button" class="pd-chip {{ $isSelected ? 'active' : '' }}"
                                                @if (!$isOut && $isSizeAttribute)
                                                    wire:click="selectSize('{{ $variantLabel }}')"
                                                @elseif (!$isOut)
                                                    wire:click="selectVariant({{ $attrOption['variant_id'] }})"
                                                @endif
                                                @disabled($isOut) aria-pressed="{{ $isSelected ? 'true' : 'false' }}">
                                                {{ $variantLabel }}
                                            </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach

                            {{-- ===== Image options (independent layer) ===== --}}
                            @if ($hasOptions)
                            <div class="pd-variant-group mt-3">
                                <div class="pd-opt-label">
                                    Design / View
                                    @if ($product->require_image_option)<span class="text-danger" title="বাছতেই হবে">*</span>
                                    @else<span class="sel">(optional)</span>@endif
                                    @if (!empty($selectedOption))
                                        @php $selOptLabel = $product->imageOptions->firstWhere('id', $selectedOption)?->label; @endphp
                                        @if ($selOptLabel)<span class="sel">(Selected: {{ $selOptLabel }})</span>@endif
                                    @endif
                                </div>
                                <div class="pd-chips">
                                    @foreach ($product->imageOptions->where('is_active', true)->sortBy('sort_order')->values() as $io)
                                    @php
                                        $oa = $optionAvailability[$io->id] ?? ['out' => false, 'reason' => null];
                                        $ioSel = (int) $selectedOption === (int) $io->id;
                                    @endphp
                                    <button type="button" class="pd-swatch {{ $ioSel ? 'active' : '' }}"
                                        @if (!$oa['out']) wire:click="selectOption({{ $io->id }})"
                                            @click="$dispatch('set-main-image', { src: '{{ asset('storage/' . $io->image) }}' })"
                                        @endif
                                        @disabled($oa['out']) aria-pressed="{{ $ioSel ? 'true' : 'false' }}"
                                        title="{{ $io->label }}{{ $oa['out'] && $oa['reason'] ? ' — ' . $oa['reason'] : '' }}"
                                        aria-label="Select {{ $io->label }}">
                                        <img src="{{ asset('storage/' . $io->image) }}" alt="{{ $io->label }}"
                                            width="64" height="64" loading="lazy">
                                    </button>
                                    @endforeach
                                </div>
                                @if ($comboReason)
                                <div class="alert alert-warning py-1 px-2 mt-2 mb-0"><small>{{ $comboReason }}</small></div>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif

                    @if ($comboReason && !$hasOptions)
                    <div class="alert alert-warning py-1 px-2 mt-2 mb-0"><small>{{ $comboReason }}</small></div>
                    @endif

                    {{-- ===== Quantity ===== --}}
                    <div class="pd-variant-group mt-3">
                        <div class="pd-opt-label">Quantity</div>
                        <div class="pd-qty">
                            <button type="button" wire:click="decreaseQty" @disabled($quantity <= 1)
                                aria-label="Decrease quantity">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" min="1" max="{{ max((int) $currentStock, 1) }}"
                                wire:model="quantity" aria-label="Quantity">
                            <button type="button" wire:click="increaseQty"
                                @disabled($quantity >= max((int) $currentStock, 1)) aria-label="Increase quantity">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ===== Action buttons ===== --}}
                    <div class="pd-actions">
                        <button type="button" class="pd-btn pd-btn--cart" wire:click="addToCart({{ $product->id }})"
                            @disabled(!$isInStock)>
                            <i class="fas fa-shopping-cart"></i>
                            <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                            <span wire:loading wire:target="addToCart">Adding...</span>
                        </button>

                        <button type="button" class="pd-btn pd-btn--buy" wire:click="buyNow({{ $product->id }})"
                            @disabled(!$isInStock)>
                            <i class="fas fa-bolt"></i>
                            <span wire:loading.remove wire:target="buyNow">Buy Now</span>
                            <span wire:loading wire:target="buyNow">Please wait...</span>
                        </button>

                        @if ($waNumber)
                            <a class="pd-btn pd-btn--wa"
                                href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank"
                                rel="noopener">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        @endif

                        @if ($phoneRaw)
                            <a class="pd-btn pd-btn--call" href="tel:{{ $phoneRaw }}">
                                <i class="fas fa-phone-alt"></i> Call to Order
                            </a>
                        @endif
                    </div>

                    <div class="pd-warning" id="variant-select-warning">
                        <span><i class="fas fa-exclamation-circle me-1"></i> Please select all variant options first.</span>
                        <button type="button" class="warning-close"
                            onclick="this.closest('.pd-warning').style.display='none'" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Meta aside --}}
                <div class="pd-aside">
                    <div class="pd-meta-row">
                        <span class="pd-meta-ic"><i class="fas fa-barcode"></i></span>
                        <span>
                            <span class="pd-meta-label">SKU</span>
                            <span class="pd-meta-value">{{ $product->sku ?: 'N/A' }}</span>
                        </span>
                    </div>

                    <div class="pd-meta-row">
                        <span class="pd-meta-ic"><i class="fas fa-truck"></i></span>
                        <span>
                            <span class="pd-meta-label">Delivery</span>
                            <span class="pd-meta-value">{{ setting('delivery_time') ?: '2 to 4 Days' }}</span>
                        </span>
                    </div>

                    <div class="pd-meta-row">
                        <span class="pd-meta-ic"><i class="fas fa-undo"></i></span>
                        <span>
                            <span class="pd-meta-label">Return Policy</span>
                            <span class="pd-meta-value">{{ setting('return_policy') ?: 'Return the product before the delivery boy from your door.' }}</span>
                        </span>
                    </div>

                    <div class="pd-meta-row">
                        <span class="pd-meta-ic"><i class="fas fa-store"></i></span>
                        <span style="flex:1;">
                            <span class="pd-meta-label">Sold By</span>
                            <span class="pd-seller">
                                <span class="pd-seller-av"><i class="fas fa-store"></i></span>
                                <span>
                                    <span class="pd-meta-value d-block">{{ $soldBy }}</span>
                                    <span class="pd-seller-badges">
                                        <span><i class="fas fa-check-circle"></i> 100% Positive</span>
                                        <span><i class="fas fa-check-circle"></i> 100% Shipping</span>
                                    </span>
                                </span>
                            </span>
                        </span>
                    </div>

                    @if ($brandName)
                        <div class="pd-meta-row">
                            <span class="pd-meta-ic"><i class="fas fa-tag"></i></span>
                            <span style="flex:1;">
                                <span class="pd-meta-label">Brand</span>
                                <span class="pd-seller">
                                    <span class="pd-brand-chip">{{ strtoupper(substr($brandName, 0, 2)) }}</span>
                                    <span class="pd-meta-value">{{ $brandName }}</span>
                                </span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Description ===== --}}
        <div class="pd-box">
            <h2 class="pd-sec-title">Description</h2>
            <div class="pd-desc-body">
                @if ($product->long_description)
                    {!! $product->long_description !!}
                @else
                    {!! nl2br(e($product->short_description ?? 'No description available.')) !!}
                @endif
            </div>
        </div>

        {{-- ===== Ratings & Reviews ===== --}}
        <div class="pd-box">
            <h2 class="pd-sec-title">Ratings & Reviews</h2>

            <div class="pd-rev-grid">
                <div class="pd-rev-avg">
                    <div class="num">{{ number_format((float) ($product->reviews_avg_rating ?? 0), 1) }}</div>
                    <div class="mt-1">
                        @for ($s = 1; $s <= 5; $s++)
                            <i class="fas fa-star"
                                style="font-size:.7rem;color:{{ $s <= round((float) ($product->reviews_avg_rating ?? 0)) ? '#111827' : '#d1d5db' }};"></i>
                        @endfor
                    </div>
                    <div class="lbl">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</div>
                </div>

                <div>
                    @for ($star = 5; $star >= 1; $star--)
                        @php
                            $starCount = (int) ($ratingDistribution[$star] ?? 0);
                            $starPct = $reviewCount > 0 ? round(($starCount / $reviewCount) * 100) : 0;
                        @endphp
                        <div class="pd-bar-row">
                            <span class="lbl">{{ $star }} Star</span>
                            <div class="pd-bar"><span style="width: {{ $starPct }}%;"></span></div>
                            <span class="cnt">{{ $starCount }}</span>
                        </div>
                    @endfor

                    @if ($reviewCount === 0)
                        <div class="pd-no-reviews">No reviews yet.</div>
                    @endif
                </div>
            </div>

            {{-- Review list --}}
            @foreach ($reviews as $review)
                <div class="pd-review-item" wire:key="rev-{{ $review->id }}">
                    <div class="pd-review-av">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                    <div style="flex:1;">
                        <div class="pd-review-top">
                            <span class="pd-review-name">{{ $review->user->name ?? 'User' }}</span>
                            <span class="pd-review-date">{{ $review->created_at?->format('d M, Y') }}</span>
                        </div>
                        <div class="mt-1">
                            @for ($s = 1; $s <= 5; $s++)
                                <i class="fas fa-star"
                                    style="font-size:.62rem;color:{{ $s <= $review->rating ? '#f5a623' : '#d1d5db' }};"></i>
                            @endfor
                        </div>
                        @if ($review->comment)
                            <p class="pd-review-comment">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($reviews->hasPages())
                <div class="mt-3">{{ $reviews->links('pagination::bootstrap-5') }}</div>
            @endif

            {{-- Review form --}}
            <div class="pd-rev-form">
                @auth
                    <div class="pd-opt-label mb-2">Write a Review</div>

                    <div class="pd-star-pick mb-2" role="radiogroup" aria-label="Your rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" class="{{ $rating >= $i ? 'on' : '' }}"
                                wire:click="$set('rating', {{ $i }})"
                                aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                    </div>
                    @error('rating')
                        <div class="text-danger" style="font-size:.76rem;">{{ $message }}</div>
                    @enderror

                    <textarea class="pd-input" rows="3" placeholder="Share your experience with this product..."
                        wire:model="comment"></textarea>
                    @error('comment')
                        <div class="text-danger mt-1" style="font-size:.76rem;">{{ $message }}</div>
                    @enderror

                    <button type="button" class="pd-submit mt-2" wire:click="submitReview">
                        <span wire:loading.remove wire:target="submitReview">Submit Review</span>
                        <span wire:loading wire:target="submitReview">Submitting...</span>
                    </button>
                @else
                    <p class="pd-no-reviews mb-0">
                        Please <a href="{{ route('login') }}" class="fw-semibold">login</a> to write a review.
                    </p>
                @endauth
            </div>
        </div>

        {{-- ===== You may also like ===== --}}
        @if ($relatedProducts->count())
            <div class="pd-box">
                <h2 class="pd-sec-title">You may also like</h2>
                <div class="pd-rel-grid">
                    @foreach ($relatedProducts as $rel)
                        @php
                            $ri = $rel->price_info;
                            $relRoute = route('frontend.product.single.view', $rel->slug);
                        @endphp
                        <div class="pd-rel-card" wire:key="rel-{{ $rel->id }}">
                            <a href="{{ $relRoute }}" class="pd-rel-img">
                                @if (!empty($ri['has_discount']))
                                    <span
                                        class="pd-sale-badge {{ !empty($ri['is_flash']) ? 'pd-sale-badge--flash' : '' }}">
                                        {{ !empty($ri['is_flash']) ? 'FLASH' : 'SALE' }}
                                    </span>
                                @endif
                                <img src="{{ $rel->thumbnail ? asset('storage/' . $rel->thumbnail) : 'https://placehold.co/300x300' }}"
                                    alt="{{ $rel->name }}" loading="lazy">
                            </a>
                            <div class="pd-rel-body">
                                <a href="{{ $relRoute }}" class="pd-rel-title">{{ $rel->name }}</a>

                                <div class="pd-rel-price">
                                    {{ $cur }}{{ $fmt($ri['final'] ?? 0) }}
                                    @if (!empty($ri['has_discount']))
                                        <span class="old">{{ $cur }}{{ $fmt($ri['original'] ?? 0) }}</span>
                                        <span class="pct">-{{ (int) ($ri['percent'] ?? 0) }}%</span>
                                    @endif
                                </div>

                                <x-rating-stars :rating="$rel->reviews_avg_rating ?? 0" :count="$rel->reviews_count ?? 0" />

                                <button type="button" class="pd-rel-cart" wire:click="addToCart({{ $rel->id }})">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- ===== Mobile sticky bar ===== --}}
    <div class="pd-sticky-bar">
        <button type="button" class="pd-fav" wire:click="addToWishlist({{ $product->id }})"
            aria-label="Add to wishlist">
            <i class="far fa-heart"></i>
        </button>
        <button type="button" class="pd-btn pd-btn--cart" wire:click="addToCart({{ $product->id }})"
            @disabled(!$isInStock)>
            <i class="fas fa-shopping-cart"></i> Add to Cart
        </button>
        <button type="button" class="pd-btn pd-btn--buy" wire:click="buyNow({{ $product->id }})" @disabled(!$isInStock)>
            <i class="fas fa-bolt"></i> Buy Now
        </button>
    </div>
    <div class="pd-sticky-spacer"></div>
</div>

@script
    <script>
        $wire.on('show-variant-warning', () => {
            const w = document.getElementById('variant-select-warning');
            if (w) {
                w.style.display = 'flex';
                w.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        $wire.on('variant-resolved', () => {
            const w = document.getElementById('variant-select-warning');
            if (w) w.style.display = 'none';
        });
    </script>
@endscript
