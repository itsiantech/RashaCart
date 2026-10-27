<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('app_name') ?: config('app.name') }}</title>

    @php $favicon = setting('fev_icon') ?: setting('app_logo'); @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/logo/' . rawurlencode($favicon)) }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('partials.style-vars')
    @stack('styles')
    @if (\App\Services\PurchaseActions::cartDrawerEnabled())
    @livewireStyles
    @endif
    <style>
        :root {
            --primary: #7A0000;
            --primary-dark: #5a0000;
            --navy: #12225B;
            --accent: #f5a623;
            --red: #dc2626;
            --orange: #f97316;
            --line: #e5e7eb;
        }

        body { font-family: var(--body-font, 'Segoe UI', sans-serif); background: #F3F4F6; }

        /* ── Layout container (max-w-7xl mx-auto px-4) ── */
        .hp-container { max-width: 1280px; margin: 0 auto; padding: 0 16px; width: 100%; }

        /* ── Navbar ── */
        .navbar-main { background: var(--hd-bg, #12225B); }
        .hp-navbar-row { display: flex; align-items: center; gap: 18px; height: 62px; }
        .hp-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
        .hp-logo img { height: 40px; width: auto; object-fit: contain; }
        .hp-logo i { color: #fff; font-size: 1.4rem; }
        .hp-logo span { color: #fff; font-weight: 700; font-size: 1.3rem; }
        .hp-search { flex: 1; display: flex; }
        .hp-search input.form-control { border: none; border-radius: 4px 0 0 4px; font-size: .9rem; background: var(--hd-search-bg, #fff); }
        .hp-search .btn { background: var(--hd-search-btn, #f5a623); border: none; color: var(--hd-search-icon, #fff); border-radius: 0 4px 4px 0; padding: 0 18px; }
        .hp-nav-icons { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
        .nav-icon { color: var(--hd-icon, rgba(255,255,255,.85)); font-size: 1.1rem; position: relative; }
        .nav-icon:hover { color: var(--hd-icon, #fff); }
        .nav-icon .badge { font-size: .6rem; position: absolute; top: -6px; right: -8px; }
        .nav-icon .badge.bg-warning { background-color: var(--hd-badge, #ffc107) !important; }

        /* Desktop logo size; mobile keeps the compact default above. */
        @media (min-width: 768px) {
            .hp-navbar-row { height: 120px; }
            .hp-logo img { height: 120px; width: 230px; }
        }

        /* ── Sub Nav ── */
        .subnav { background: var(--hd-bg, #12225B); border-top: 1px solid rgba(255,255,255,.12); }
        .subnav .nav-link { color: var(--hd-nav, rgba(255,255,255,.85)) !important; font-size: .85rem; padding: .4rem .75rem; border-radius: 6px; }
        .subnav .nav-link:hover { color: #fff !important; background: var(--hd-nav-hover, transparent); }
        .subnav .btn-sell { background: var(--hd-search-btn, #f5a623); color: #fff; font-size: .8rem; border-radius: 20px; padding: .25rem .85rem; }

        /* ── Hero: categories + banner ── */
        .hp-hero { display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px; }
        @media(min-width: 992px) { .hp-hero { grid-template-columns: 240px 1fr; } }

        .hp-cat { display: none; }
        @media(min-width: 992px) { .hp-cat { display: block; } }
        .hp-cat-card { background: #fff; border-radius: 12px; border: 1px solid var(--line); height: 380px; overflow-y: auto; }
        .hp-cat-item { display: flex; justify-content: space-between; align-items: center; padding: .55rem 1rem; font-size: .85rem; color: #4b5563; text-decoration: none; border-bottom: 1px solid #f3f4f6; transition: background .15s, color .15s, padding-left .15s; }
        .hp-cat-item:last-child { border-bottom: none; }
        .hp-cat-item:hover { background: #f7f8fb; color: var(--primary); padding-left: 1.2rem; }
        .hp-cat-item i { font-size: .65rem; color: #9ca3af; }

        .hero-carousel { border-radius: 12px; overflow: hidden; }
        .hero-carousel .carousel-item img { height: 380px; width: 100%; object-fit: cover; display: block; }
        @media(max-width: 991px) { .hero-carousel .carousel-item img { height: 220px; } }
        .hp-slide-fallback .inner { height: 380px; display: flex; flex-direction: column; justify-content: center; padding: 0 8%; background: linear-gradient(120deg, var(--navy), var(--primary)); }
        @media(max-width: 991px) { .hp-slide-fallback .inner { height: 220px; } }
        .hp-slide-fallback .tag { color: #fde68a; font-weight: 600; letter-spacing: 1px; font-size: .8rem; }
        .hp-slide-fallback h2 { color: #fff; font-weight: 800; font-size: 2rem; margin: 4px 0; }
        .hp-slide-fallback .off { color: #fde68a; font-weight: 700; font-size: 1.4rem; }
        .carousel-caption { background: linear-gradient(90deg, rgba(18,34,91,.85), rgba(18,34,91,0) 70%); border-radius: 8px; text-align: left; left: 5%; right: auto; bottom: 20%; padding: 1rem 1.4rem; }

        /* ── Mobile category pills ── */
        .hp-pills { display: flex; gap: 8px; overflow: auto; padding-bottom: 8px; scrollbar-width: none; }
        .hp-pill { background: var(--primary); color: #fff; font-size: .78rem; padding: .4rem .8rem; border-radius: 20px; flex-shrink: 0; text-decoration: none; }
        .hp-pill:hover { color: #fff; }

        /* ── Section shell ── */
        .hp-sec { margin-top: 24px; }
        .hp-box { background: #fff; border-radius: 12px; border: 1px solid var(--line); padding: 20px; }
        .hp-sec-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .hp-sec-title { font-size: 1.1rem; font-weight: 700; color: #111827; border-left: 4px solid var(--red); padding-left: 10px; margin: 0; }
        .hp-see-more { font-size: .85rem; color: var(--primary); text-decoration: none; }
        .hp-see-more:hover { text-decoration: underline; }

        /* ── Deal / Flash sale ── */
        .hp-deal-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
        @media(min-width: 992px) { .hp-deal-grid { grid-template-columns: 250px 1fr; } }
        @media(min-width: 1200px) { .hp-deal-grid { grid-template-columns: 280px 1fr; } }
        .hp-deal-left { background: var(--flash-bg, #EA580C); background: linear-gradient(160deg, color-mix(in srgb, var(--flash-bg, #EA580C) 62%, #ffffff), var(--flash-bg, #EA580C) 55%, color-mix(in srgb, var(--flash-bg, #EA580C) 86%, #000000)); border-radius: 10px; color: var(--flash-text, #fff); padding: 22px 18px; display: flex; flex-direction: column; font-family: var(--flash-font, var(--body-font, 'Segoe UI', sans-serif)); }
        .hp-deal-title { font-size: 1.05rem; font-weight: 700; line-height: 1.35; }
        .hp-deal-title .pct { color: #fde68a; }
        .hp-countdown { display: flex; gap: 8px; margin: 16px 0; }
        .cd-box { background: rgba(255,255,255,.18); border-radius: 8px; text-align: center; padding: 6px 8px; min-width: 52px; }
        .cd-box .num { font-size: 1.15rem; font-weight: 700; display: block; }
        .cd-box .lbl { font-size: .62rem; opacity: .85; }
        .btn-deal-cta { margin-top: auto; background: #fff; color: var(--orange); border: none; border-radius: 3px; font-weight: 600; font-size: .85rem; padding: .5rem 1rem; display: inline-block; text-align: center; text-decoration: none; }
        .btn-deal-cta:hover { background: #fff7ed; color: #ea580c; }
        .hp-deal-products { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        @media(min-width: 576px) { .hp-deal-products { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 992px) { .hp-deal-products { grid-template-columns: repeat(5, 1fr); } }

        /* ── Product card ── */
        .hp-pcard { background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 10px; display: flex; flex-direction: column; transition: box-shadow .2s, transform .2s; height: 100%; }
        .hp-pcard:hover { box-shadow: 0 8px 20px rgba(17,24,39,.1); transform: translateY(-2px); }
        .hp-pimg { position: relative; display: block; aspect-ratio: 1 / 1; background: #fafafa; border-radius: 6px; padding: 8px; overflow: hidden; }
        .hp-pimg img { width: 100%; height: 100%; object-fit: contain; }
        .hp-pbadge { position: absolute; top: 8px; left: 8px; background: var(--red); color: #fff; font-size: .68rem; font-weight: 600; border-radius: 4px; padding: 2px 7px; }
        .hp-pbody { display: flex; flex-direction: column; flex: 1; padding-top: 8px; }
        .hp-ptitle { font-size: .82rem; font-weight: 600; color: #1f2937; line-height: 1.35; margin: 0 0 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.2em; }
        .hp-stars { margin-bottom: 4px; }
        .hp-stars i { font-size: .62rem; color: #000; }
        .hp-stars small { color: #6b7280; font-size: .7rem; }
        .hp-price { font-weight: 700; color: #111827; font-size: .95rem; }
        .hp-price .old { text-decoration: line-through; color: #9ca3af; font-weight: 400; font-size: .78rem; margin-left: 4px; }
        .btn-add-cart { width: 100%; background: var(--btn-all-bg, #fff); color: var(--btn-all-text, var(--navy)); border: 1px solid var(--btn-all-bg, var(--navy)); border-radius: 3px; font-size: .8rem; padding: .42rem; font-weight: 600; margin-top: auto; transition: background .2s ease, color .2s ease; }
        a.btn-add-cart { display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn-add-cart:hover { filter: brightness(.92); }
        .btn-add-cart i { color: var(--cart-btn-icon, var(--icon-color)); }

        /* ── Top shops ── */
        .hp-shops { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        @media(min-width: 768px) { .hp-shops { grid-template-columns: repeat(4, 1fr); } }
        @media(min-width: 992px) { .hp-shops { grid-template-columns: repeat(6, 1fr); } }
        .hp-shop-card { background: #fff; border: 1px solid var(--line); border-radius: 10px; text-align: center; padding: 16px 8px; text-decoration: none; transition: box-shadow .2s, transform .2s; }
        .hp-shop-card:hover { box-shadow: 0 8px 20px rgba(17,24,39,.1); transform: translateY(-2px); }
        .hp-shop-card img { width: 64px; height: 64px; object-fit: cover; border-radius: 50%; border: 2px solid #f3f4f6; margin-bottom: 8px; }
        .hp-shop-logo-fallback { width: 64px; height: 64px; border-radius: 50%; background: #eef1f7; color: var(--navy); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 8px; }
        .hp-shop-name { font-size: .78rem; font-weight: 600; color: #1f2937; margin: 0; }
        .hp-shop-count { font-size: .7rem; color: #6b7280; }

        /* ── Top brands ── */
        .hp-brands { display: grid; grid-template-columns: repeat(var(--brand-cols-sm, 3), minmax(0, 1fr)); gap: 12px; }
        @media(min-width: 576px) { .hp-brands { grid-template-columns: repeat(var(--brand-cols-md, 4), minmax(0, 1fr)); } }
        @media(min-width: 992px) { .hp-brands { grid-template-columns: repeat(var(--brand-cols, 8), minmax(0, 1fr)); } }
        .hp-brand-card { background: #fff; border: 1px solid var(--line); border-radius: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; padding: 14px 10px; transition: box-shadow .2s, transform .2s, border-color .2s; }
        .hp-brand-card:hover { border-color: var(--primary); box-shadow: 0 10px 24px rgba(17,24,39,.1); transform: translateY(-3px); }
        .hp-brand-logo { width: 100%; aspect-ratio: 16 / 10; display: grid; place-items: center; background: #f7f8fb; border-radius: 8px; padding: 8px; overflow: hidden; }
        .hp-brand-logo img { max-width: 100%; max-height: 100%; object-fit: contain; display: block; }
        .hp-brand-mark { width: 44px; height: 44px; border-radius: 50%; display: grid; place-items: center; background: linear-gradient(135deg, var(--navy), var(--primary)); color: #fff; font-weight: 700; font-size: 1.05rem; letter-spacing: .5px; }
        .hp-brand-name { font-size: .78rem; font-weight: 600; color: #1f2937; text-align: center; line-height: 1.25; }
        .hp-brand-count { font-size: .68rem; color: #9ca3af; }

        /* ── Shop by Category (desktop showcase with images) ── */
        .hp-catshow { display: grid; grid-template-columns: repeat(var(--cat-cols, 8), minmax(0, 1fr)); gap: 14px; }
        .hp-catshow-card { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 12px 8px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; transition: box-shadow .2s, transform .2s, border-color .2s; }
        .hp-catshow-card:hover { border-color: var(--primary); box-shadow: 0 10px 24px rgba(17,24,39,.1); transform: translateY(-3px); }
        .hp-catshow-img { width: 100%; max-width: 130px; aspect-ratio: 1 / 1; border-radius: 50%; background: #f6f7f9; overflow: hidden; display: grid; place-items: center; border: 1px solid #f1f2f5; }
        .hp-catshow-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s ease; }
        .hp-catshow-card:hover .hp-catshow-img img { transform: scale(1.07); }
        .hp-catshow-fallback { color: var(--primary); opacity: .45; font-size: 1.5rem; }
        .hp-catshow-name { font-size: .78rem; font-weight: 600; color: #1f2937; text-align: center; line-height: 1.25; }
        .hp-catshow-count { font-size: .68rem; color: #9ca3af; }

        /* ── Homepage Categories grid (adapts to each category's product limit) ── */
        .hp-cat-grid { display: grid; grid-template-columns: repeat(var(--cat-cols, 2), minmax(0, 1fr)); gap: 14px; }
        .hp-cat-cols-1 { --cat-cols: 1; max-width: 280px; margin-inline: auto; }
        .hp-cat-cols-2 { --cat-cols: 2; }
        .hp-cat-cols-3 { --cat-cols: 3; }
        .hp-cat-cols-4 { --cat-cols: 4; }
        .hp-cat-cols-5 { --cat-cols: 5; }
        .hp-cat-cols-6 { --cat-cols: 6; }
        @media (max-width: 575px) {
            .hp-cat-cols-3, .hp-cat-cols-4, .hp-cat-cols-5, .hp-cat-cols-6 { --cat-cols: 2; }
        }
        @media (min-width: 576px) and (max-width: 767px) {
            .hp-cat-cols-4, .hp-cat-cols-5, .hp-cat-cols-6 { --cat-cols: 3; }
        }
        @media (min-width: 768px) and (max-width: 991px) {
            .hp-cat-cols-5, .hp-cat-cols-6 { --cat-cols: 4; }
        }
        @media (min-width: 992px) {
            .hp-cat-cols-1 { max-width: 300px; margin-inline: auto; }
            .hp-cat-cols-2 { max-width: 610px; margin-inline: auto; }
            .hp-cat-cols-3 { max-width: 900px; margin-inline: auto; }
        }

        /* ── Recommended grid ── */
        .rp-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        @media(min-width: 576px) { .rp-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 992px) { .rp-grid { grid-template-columns: repeat(6, 1fr); } }

        /* ── Load more ── */
        .hp-load-wrap { text-align: center; margin-top: 22px; }
        .hp-load-more { display: inline-block; border: 1px solid var(--navy); color: var(--navy); background: #fff; border-radius: 20px; padding: .55rem 2rem; font-size: .85rem; font-weight: 600; text-decoration: none; transition: background .2s, color .2s; }
        .hp-load-more:hover { background: var(--navy); color: #fff; }

        /* ── Footer ── */
        .hp-footer { background: var(--ft-bg, #12225B); color: var(--ft-text, #fff); margin-top: 32px; padding: 40px 0 18px; }
        .f-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        @media(min-width: 768px) { .f-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width: 992px) { .f-grid { grid-template-columns: 1.35fr 1fr 1fr 1fr .95fr; } }
        .hp-footer h6 { color: var(--ft-text, #fff); font-weight: 600; font-size: .92rem; margin-bottom: .8rem; }
        .hp-footer a { color: var(--ft-nav, rgba(255,255,255,.75)); text-decoration: none; font-size: .85rem; }
        .hp-footer a:hover { color: var(--ft-nav, #fff); }
        .hp-footer ul { list-style: none; padding: 0; margin: 0; }
        .hp-footer ul li { margin-bottom: .4rem; }
        .f-contact p { font-size: .82rem; color: var(--ft-text, rgba(255,255,255,.75)); margin: 0; line-height: 1.9; }
        .f-social { display: flex; gap: 10px; }
        .f-social a { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.12); display: inline-flex; align-items: center; justify-content: center; color: var(--ft-social, #fff); }
        .f-social a:hover { background: var(--accent); }
        .f-app-btn { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); border-radius: 8px; padding: 7px 12px; margin-bottom: 8px; max-width: 190px; }
        .f-app-btn i { font-size: 1.3rem; color: #fff; }
        .f-app-btn small { display: block; font-size: .6rem; color: rgba(255,255,255,.65); line-height: 1.1; }
        .f-app-btn strong { font-size: .78rem; color: #fff; line-height: 1.1; }
        .f-bottom { display: flex; flex-direction: column; align-items: center; gap: 8px; border-top: 1px solid rgba(255,255,255,.15); margin-top: 26px; padding-top: 16px; font-size: .82rem; color: rgba(255,255,255,.6); }
        @media(min-width: 768px) { .f-bottom { flex-direction: row; justify-content: space-between; } }

        /* ── Mobile bottom nav ── */
        .mobile-bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: 0 -2px 10px rgba(0,0,0,.1); z-index: 1000; padding: .5rem 0; }
        @media(max-width:768px) { .mobile-bottom-nav { display: flex; justify-content: space-around; } }
        .mobile-bottom-nav a { color: #555; font-size: .7rem; text-align: center; flex: 1; text-decoration: none; }
        .mobile-bottom-nav a i { font-size: 1.2rem; display: block; }
        .mobile-bottom-nav a.active { color: var(--primary); }
        @media(max-width:768px) { body { padding-bottom: 60px; } }
    </style>
    {{-- Google SEO (verification, GTM, schema) --}}
    @include('partials.seo-tags')
</head>
<body>
    {{-- GTM noscript --}}
    @include('partials.seo-body')

{{-- ══════════════ NAVBAR ══════════════ --}}
<nav class="navbar-main sticky-top">
    <div class="hp-container">
        <div class="hp-navbar-row">
            <a class="hp-logo" href="{{ route('home') }}">
                @if(setting('app_logo'))
                    <img src="{{ asset('storage/logo/' . rawurlencode(setting('app_logo'))) }}"
                         alt="{{ setting('app_name') ?: config('app.name') }}">
                @else
                    <i class="fas fa-shopping-bag"></i>
                    <span>{{ setting('app_name') ?: config('app.name') }}</span>
                @endif
            </a>

            <form class="hp-search" method="GET" action="{{ route('home') }}">
                <input type="search" name="q" class="form-control" placeholder="Search products...">
                <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="hp-nav-icons">
                @include('partials.customer-account-icon')
                <a href="#" class="nav-icon d-none d-md-block">
                    <i class="fas fa-heart"></i>
                </a>
                <a href="{{ route('frontend.cart.view') }}" class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-warning text-dark" data-cd-header-count>{{ $cartCount ?? 0 }}</span>
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- ══════════════ SUB NAV ══════════════ --}}
<div class="subnav d-none d-md-block">
    <div class="hp-container">
        <nav class="d-flex align-items-center flex-wrap gap-1 py-1">
            <a href="{{ route('frontend.category.view') }}" class="nav-link">Categories</a>
            <a href="{{ route('home') }}" class="nav-link">Products</a>
            <a href="#" class="nav-link">Discounts</a>
            <a href="{{ route('frontend.brand.view') }}" class="nav-link">All Brands</a>
            <a href="{{ route('frontend.order.tracking') }}" class="nav-link">Track Order</a>
            <a href="{{ route('home') }}" class="nav-link">Home</a>
        </nav>
    </div>
</div>

{{-- ══════════════ HERO: CATEGORIES + BANNER ══════════════ --}}
<section class="hp-container">
    <div class="hp-hero">

        {{-- Categories menu (3-level flyout) --}}
        <aside class="hp-cat">
            @include('livewire.frontend.layouts.partial.category.category-flyout', ['categories' => $categories])
        </aside>

        {{-- Banner slider --}}
        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                @forelse($heroBanners as $index => $banner)
                <div class="carousel-item @if($index === 0) active @endif">
                    <img src="{{ asset('storage/'.$banner->image) }}" class="d-block w-100" alt="{{ $banner->title }}">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="mb-1 fw-bold">{{ $banner->title }}</h5>
                        @if($banner->description)
                        <p class="mb-2 small">{{ $banner->description }}</p>
                        @endif
                        @if($banner->link)
                        <a href="{{ $banner->link }}" class="btn btn-sm btn-warning fw-semibold">Shop Now</a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="carousel-item active hp-slide-fallback">
                    <div class="inner">
                        <span class="tag">LIMITED TIME</span>
                        <h2>SUPER SALE</h2>
                        <span class="off">UP TO 40% OFF</span>
                        <div class="mt-3">
                            <a href="{{ route('frontend.flash.sale.products') }}" class="btn btn-sm btn-warning fw-semibold">Shop Now</a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            @if($heroBanners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            <div class="carousel-indicators">
                @foreach($heroBanners as $i => $b)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" @if($i===0) class="active" @endif></button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Mobile category pills --}}
    <div class="hp-pills d-lg-none mt-3">
        @foreach($categories as $cat)
        <a href="{{ route('frontend.category.view', $cat->slug) }}" class="hp-pill">{{ $cat->name }}</a>
        @endforeach
    </div>
</section>

{{-- ══════════════ SHOP BY CATEGORY (desktop: images below hero) ══════════════ --}}
@if(setting('shop_by_category_enabled', 1) && !empty($categoryGrid) && $categoryGrid->count())
<section class="hp-sec d-none d-lg-block">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">{{ setting('shop_by_category_title', 'Shop by Category') }}</h5>
                <a href="{{ route('frontend.category.view') }}" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-catshow" style="--cat-cols: {{ $categoryGrid->count() > 8 ? (int) ceil($categoryGrid->count() / 2) : $categoryGrid->count() }}">
                @foreach($categoryGrid as $row)
                @php $cat = $row['category']; @endphp
                <a href="{{ route('frontend.category.view', $cat->slug) }}" class="hp-catshow-card">
                    <span class="hp-catshow-img">
                        @if($cat->image)
                        <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" loading="lazy">
                        @else
                        <i class="fas fa-layer-group hp-catshow-fallback"></i>
                        @endif
                    </span>
                    <span class="hp-catshow-name">{{ $cat->name }}</span>
                    @if($row['products_count'] > 0)
                    <span class="hp-catshow-count">{{ $row['products_count'] }} {{ \Illuminate\Support\Str::plural('item', $row['products_count']) }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════ TOP BRANDS ══════════════ --}}
@if(setting('top_brands_enabled', 1) && $topBrands->count())
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">{{ setting('top_brands_title', 'Top Brands') }}</h5>
                <a href="{{ route('frontend.brand.view') }}" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            @php
            $brandShowCount = $topBrands->count();
            $brandCols = $brandShowCount > 8 ? (int) ceil($brandShowCount / 2) : $brandShowCount;
            @endphp
            <div class="hp-brands" style="--brand-cols: {{ $brandCols }}; --brand-cols-md: {{ min($brandCols, 4) }}; --brand-cols-sm: {{ min($brandCols, 3) }}">
                @foreach($topBrands as $brand)
                <a href="{{ route('frontend.brands.products.view', $brand->slug ?? $brand->id) }}" class="hp-brand-card">
                    <span class="hp-brand-logo">
                        @if($brand->logo)
                        <img src="{{ asset('storage/'.$brand->logo) }}" alt="{{ $brand->name }}" loading="lazy"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'hp-brand-mark\'>{{ strtoupper(mb_substr(trim($brand->name), 0, 1)) }}</span>';">
                        @else
                        <span class="hp-brand-mark">{{ strtoupper(mb_substr(trim($brand->name), 0, 1)) }}</span>
                        @endif
                    </span>
                    <span class="hp-brand-name">{{ $brand->name }}</span>
                    @if(($brand->products_count ?? 0) > 0)
                    <span class="hp-brand-count">{{ $brand->products_count }} {{ \Illuminate\Support\Str::plural('product', $brand->products_count) }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════ DEAL / FLASH SALE (live campaign only) ══════════════ --}}
@if(setting('flash_sale_enabled', 1) && !empty($liveFlashSale) && $flashSaleProducts->count())
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">{{ setting('flash_sale_title', 'Flash Sale') }}</h5>
                <a href="{{ route('frontend.flash.sale.products') }}" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-deal-grid">

                <div class="hp-deal-left">
                    <div class="hp-deal-title">
                        Limited Time @if($offerPercent > 0)<span class="pct">{{ $offerPercent }}%</span>@endif Discount Offer Products
                    </div>
                    <div class="hp-countdown">
                        <div class="cd-box"><span class="num" id="cdDays">{{ str_pad($countdownParts['days'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span><span class="lbl">Days</span></div>
                        <div class="cd-box"><span class="num" id="cdHours">{{ str_pad($countdownParts['hours'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span><span class="lbl">Hours</span></div>
                        <div class="cd-box"><span class="num" id="cdMins">{{ str_pad($countdownParts['mins'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span><span class="lbl">Min</span></div>
                        <div class="cd-box"><span class="num" id="cdSecs">{{ str_pad($countdownParts['secs'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span><span class="lbl">Sec</span></div>
                    </div>
                    <a href="{{ route('frontend.flash.sale.products') }}" class="btn-deal-cta">View Products</a>
                </div>

                <div class="hp-deal-products">
                    @foreach($flashSaleProducts as $product)
                    @php $pi = $product->price_info; @endphp
                    <div class="hp-pcard">
                        <a href="{{ route('frontend.product.single.view', $product->slug) }}" class="hp-pimg">
                            <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}"
                                 onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                            @if($pi['percent'] > 0)
                            <span class="hp-pbadge">-{{ $pi['percent'] }}%</span>
                            @endif
                        </a>
                        <div class="hp-pbody">
                            <p class="hp-ptitle">{{ $product->name }}</p>
                            <div class="hp-stars">
                                @for($s=1;$s<=5;$s++)
                                <i class="fas fa-star"></i>
                                @endfor
                                <small>({{ $product->reviews_count }})</small>
                            </div>
                            <div class="hp-price mb-2">
                                ৳{{ number_format($pi['final'], 0) }}
                                @if($pi['has_discount'])
                                <span class="old">৳{{ number_format($pi['original'], 0) }}</span>
                                @endif
                            </div>
                            @include('partials.purchase-add-to-cart', ['product' => $product])
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════ HOMEPAGE CATEGORIES ══════════════ --}}
{{-- DEBUG: {{ $homeCategories->count() }} categories found --}}
@if($homeCategories->count())
@foreach($homeCategories as $homeCat)
@php
$categoryProducts = $homeCat->category->products->take($homeCat->product_limit);
@endphp
{{-- DEBUG: {{ $homeCat->category->name }} - {{ $categoryProducts->count() }} products --}}
@if($categoryProducts->count())
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">{{ $homeCat->category->name }}</h5>
                <a href="{{ route('frontend.category.products.view', $homeCat->category->slug) }}" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-cat-grid hp-cat-cols-{{ min($categoryProducts->count(), 6) }}">
                @foreach($categoryProducts as $product)
                @php $pi = $product->price_info; @endphp
                <div class="hp-pcard">
                    <a href="{{ route('frontend.product.single.view', $product->slug) }}" class="hp-pimg">
                        <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        @if($pi['percent'] > 0)
                        <span class="hp-pbadge">-{{ $pi['percent'] }}%</span>
                        @endif
                    </a>
                    <div class="hp-pbody">
                        <p class="hp-ptitle">{{ $product->name }}</p>
                        <div class="hp-stars">
                            @for($s=1;$s<=5;$s++)
                            <i class="fas fa-star"></i>
                            @endfor
                            <small>({{ $product->reviews_count }})</small>
                        </div>
                        <div class="hp-price mb-2">
                            ৳{{ number_format($pi['final'], 0) }}
                            @if($pi['has_discount'])
                            <span class="old">৳{{ number_format($pi['original'], 0) }}</span>
                            @endif
                        </div>
                        @include('partials.purchase-add-to-cart', ['product' => $product])
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endforeach
@endif

{{-- ══════════════ TOP SHOPS ══════════════ --}}
@if($topShops->count())
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">Top Shops</h5>
                <a href="#" class="hp-see-more">See more <i class="fas fa-chevron-right" style="font-size:.7rem"></i></a>
            </div>
            <div class="hp-shops">
                @foreach($topShops as $shop)
                <a href="{{ route('frontend.user.products.view', $shop->id) }}" class="hp-shop-card">
                    @if($shop->vendorDetails?->profile_image)
                    <img src="{{ asset('storage/'.$shop->vendorDetails->profile_image) }}" alt="{{ $shop->vendorDetails->shop_name ?? $shop->name }}">
                    @else
                    <span class="hp-shop-logo-fallback"><i class="fas fa-store"></i></span>
                    @endif
                    <p class="hp-shop-name">{{ $shop->vendorDetails->shop_name ?? $shop->name }}</p>
                    <span class="hp-shop-count">{{ $shop->products_count }} items</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════ RECOMMENDED FOR YOU ══════════════ --}}
<section class="hp-sec">
    <div class="hp-container">
        <div class="hp-box">
            <div class="hp-sec-head">
                <h5 class="hp-sec-title">{{ $recommendedTitle }}</h5>
            </div>
            <div class="rp-grid">
                @forelse($recommendedProducts as $product)
                @php $pi = $product->price_info; @endphp
                <div class="hp-pcard">
                    <a href="{{ route('frontend.product.single.view', $product->slug) }}" class="hp-pimg">
                        <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        @if($pi['percent'] > 0)
                        <span class="hp-pbadge">-{{ $pi['percent'] }}%</span>
                        @endif
                    </a>
                    <div class="hp-pbody">
                        <p class="hp-ptitle">{{ $product->name }}</p>
                        <div class="hp-stars">
                            @for($s=1;$s<=5;$s++)
                            <i class="fas fa-star"></i>
                            @endfor
                            <small>({{ $product->reviews_count }})</small>
                        </div>
                        <div class="hp-price mb-2">
                            ৳{{ number_format($pi['final'], 0) }}
                            @if($pi['has_discount'])
                            <span class="old">৳{{ number_format($pi['original'], 0) }}</span>
                            @endif
                        </div>
                        @include('partials.purchase-add-to-cart', ['product' => $product])
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted" style="grid-column:1/-1">
                    <i class="fas fa-box-open fa-2x mb-2"></i><p class="mb-0">No products found</p>
                </div>
                @endforelse
            </div>

            @if($show < $totalProducts)
            <div class="hp-load-wrap">
                <a href="{{ route('home', ['show' => min($show + 12, $totalProducts)]) }}" class="hp-load-more">
                    LOAD MORE PRODUCTS
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ══════════════ FOOTER ══════════════ --}}
<footer class="hp-footer">
    <div class="hp-container">
        <div class="f-grid">

            <div class="f-contact">
                <h6>Contact Us</h6>
                <p>
                    @if(setting('contact_address'))
                    <i class="fas fa-map-marker-alt me-2"></i>{{ setting('contact_address') }}<br>
                    @else
                    <i class="fas fa-map-marker-alt me-2"></i>Dhaka, Bangladesh<br>
                    @endif
                    @if(setting('contact_phone'))
                    <i class="fas fa-phone me-2"></i>{{ setting('contact_phone') }}<br>
                    @endif
                    @if(setting('contact_email'))
                    <i class="fas fa-envelope me-2"></i>{{ setting('contact_email') }}
                    @endif
                </p>
            </div>

            <div>
                <h6>Company</h6>
                <ul>
                    <li><a href="{{ route('frontend.about.us') }}">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <div>
                <h6>About Us</h6>
                <ul>
                    <li><a href="{{ route('frontend.terms.and.conditions') }}">Terms and Conditions</a></li>
                    <li><a href="{{ route('frontend.privacy.policy') }}">Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h6>Account</h6>
                <ul>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                    <li><a href="{{ route('frontend.cart.view') }}">My Cart</a></li>
                    <li><a href="{{ route('frontend.order.tracking') }}">Track Order</a></li>
                </ul>
            </div>

            <div>
                <h6>Social</h6>
                <div class="f-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

        </div>

        <div class="f-bottom">
            <span>{{ setting('footer_copyright') ?: '© ' . date('Y') . ' ' . (setting('app_name') ?: 'Padmazon') . '. All rights reserved.' }}</span>
            <div class="d-flex gap-3">
                <a href="{{ route('frontend.privacy.policy') }}">Privacy Policy</a>
                <a href="{{ route('frontend.terms.and.conditions') }}">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

{{-- ══════════════ MOBILE BOTTOM NAV ══════════════ --}}
<div class="mobile-bottom-nav d-md-none">
    <a href="{{ route('home') }}" class="active">
        <i class="fas fa-home"></i>Home
    </a>
    <a href="{{ route('frontend.category.view') }}">
        <i class="fas fa-th-large"></i>Categories
    </a>
    <a href="{{ route('frontend.cart.view') }}">
        <i class="fas fa-shopping-cart"></i>Cart
    </a>
    @if(\App\Services\CustomerAuthSettings::enabled())
        @guest
        <button type="button" data-cust-open="login" style="border:none;background:none;cursor:pointer;color:inherit;font:inherit;">
            <i class="fas fa-user"></i>Account
        </button>
        @else
        <a href="{{ route('customer.dashboard') }}">
            <i class="fas fa-user"></i>Account
        </a>
        @endif
    @else
        <a href="{{ route('profile.index') }}">
            <i class="fas fa-user"></i>Account
        </a>
    @endif
</div>

{{-- Site-wide slide-out cart drawer (admin toggle: Business Settings) --}}
@include('partials.cart-drawer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@include('partials.customer-account-scripts')
@include('partials.customer-auth-modal')

@if (\App\Services\PurchaseActions::cartDrawerEnabled())
@livewireScripts
@endif
@stack('scripts')

@if(session('success') || session('error'))
<div id="cartToast" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:{{ session('error') ? '#dc3545' : '#12225B' }};color:#fff;padding:10px 22px;border-radius:4px;z-index:2000;box-shadow:0 4px 14px rgba(0,0,0,.25);font-size:.9rem;">
    {{ session('success') ?? session('error') }}
</div>
<script>setTimeout(function(){ document.getElementById('cartToast') && document.getElementById('cartToast').remove(); }, 2500);</script>
@endif

@if(!empty($liveFlashSale) && $flashSaleProducts->count())
<script>
(function () {
    var target = {{ $countdownTarget->timestamp * 1000 }};
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var diff = Math.max(0, Math.floor((target - Date.now()) / 1000));
        var d = Math.floor(diff / 86400); diff -= d * 86400;
        var h = Math.floor(diff / 3600);  diff -= h * 3600;
        var m = Math.floor(diff / 60);
        var s = diff - m * 60;
        function set(id, v) { var el = document.getElementById(id); if (el) el.textContent = pad(v); }
        set('cdDays', d); set('cdHours', h); set('cdMins', m); set('cdSecs', s);
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
@endif
</body>
</html>
