<!DOCTYPE html>
<html lang="en" data-critters-container=""
    style="--shop-color-primary: #EB0000; --shop-color-secondary: #7A0000; --shop-color-tertiary: #292929;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Standard Meta Tags -->
    <title>{{ $meta['title'] ?? $meta_title ?? setting('app_name') ?? config('app.name') }}</title>

    <!-- Description -->
    <meta name="description" content="{{ $meta['description'] ?? $meta_description ?? 'Default e-commerce description' }}">

    <!-- Keywords -->
    <meta name="keywords" content="{{ $meta['keywords'] ?? $meta_keywords ?? 'shopping, bangladesh, ecommerce' }}">

    <meta name="tags" content="{{ $meta['tags'] ?? $meta_tags ?? '' }}">

    <!-- Open Graph for Social Media -->
    <meta property="og:title" content="{{ $meta['title'] ?? setting('app_name') ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta['meta_description'] ?? '' }}">
    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:title" content="{{ $meta_title ?? setting('app_name') ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta_description ?? 'Shop at Ordinary Bangladesh.' }}">
    <meta property="og:tags" content="{{ $meta_tags ?? 'ecommerce, shopping, Bangladesh' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <!-- Ensure you add an image for social previews -->
    <meta property="og:image" content="{{ asset('path-to-your-logo.png') }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta_title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $meta_description ?? '' }}">



    <meta name="theme-color" content="#EB0000">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ setting('app_name') ?? config('app.name') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $og_image ?? 'https://www.medleybiz.com.bd/og-image.jpg' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $meta_title ?? setting('app_name') ?? config('app.name') }}">
    <meta property="twitter:description" content="{{ $meta_description ?? 'Shop the latest products at Ordinary Bangladesh. Fast delivery, secure payments, and excellent customer service.' }}">
    <meta property="twitter:image" content="{{ $og_image ?? 'https://www.medleybiz.com.bd/og-image.jpg' }}">

    <!-- Favicon (Dynamic from Admin Settings) -->
    @php $favicon = setting('fev_icon') ?: setting('app_logo'); @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/logo/' . rawurlencode($favicon)) }}">
    @endif

    <!-- Custom Meta Tags (Raw HTML) -->
    {!! $meta_tags ?? '' !!}

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="preconnect" href="https://cdn.saleecom.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.saleecom.com">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    @php
    $theme = setting('theme') ?? 'default';
    @endphp
    <link rel="stylesheet" href="{{ asset("assets/frontend/themes/$theme/assets/style.css?v=2.0.0.0") }}">

    {{-- Dynamic style vars (Style Settings page) --}}
    @include('partials.style-vars')

    <!-- Material Components -->
    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    {!! \App\Services\PixelManager::head() !!}

    {{-- Server-Side Tracking Pixel --}}
    @include('partials.tracking-pixel')

    {{-- Google SEO (verification, GTM, schema) --}}
    @include('partials.seo-tags')

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('track-event', ({
                eventName,
                data
            }) => {
                if (window.fbq) fbq('track', eventName, data);
                if (window.ttq) ttq.track(mapTiktokEvent(eventName), data);
                if (window.gtag) gtag('event', mapGaEvent(eventName), data);
            });
        });

        function mapTiktokEvent(e) {
            return {
                ViewContent: 'ViewContent',
                AddToCart: 'AddToCart',
                InitiateCheckout: 'InitiateCheckout',
                Purchase: 'CompletePayment'
            } [e] || e;
        }

        function mapGaEvent(e) {
            return {
                ViewContent: 'view_item',
                AddToCart: 'add_to_cart',
                InitiateCheckout: 'begin_checkout',
                Purchase: 'purchase'
            } [e] || e;
        }
    </script>

    @stack('styles')
    @livewireStyles
</head>

<body>
    {{-- GTM noscript --}}
    @include('partials.seo-body')
    <!-- Top Bar -->
    {{-- @include('livewire.frontend.layouts.partial.topbar.topbar-default') --}}

    <!-- Header -->
    @include('livewire.frontend.layouts.partial.header.header-default')

    {{-- Storefront chrome follows admin Style Settings --}}
    <style>
        body { font-family: var(--body-font, 'Segoe UI', sans-serif); }
        .main-header { background-color: var(--hd-bg, #12225B); }
        .main-header .action-btn,
        .main-header .menu-toggle,
        .main-header .search-toggle-btn { color: var(--hd-icon, #fff); }
        .main-header .badge { background-color: var(--hd-badge, #ffc107); color: #111; }
        .search-container input { background-color: var(--hd-search-bg, #fff); }
        .search-container button { background-color: var(--hd-search-btn, #f5a623); color: var(--hd-search-icon, #fff); }
        .search-container button:hover { background-color: var(--hd-search-btn, #f5a623); }
        .main-header .s input[type="text"],
        .main-header .s input[type="search"] { background: var(--hd-search-bg, #fff); }
        .main-header .s button[type="submit"],
        .main-header .s .search-btn { background: var(--hd-search-btn, #f5a623) !important; color: var(--hd-search-icon, #fff) !important; }
        .footer-top, .footer-bottom { background-color: var(--ft-bg, #12225B); color: var(--ft-text, #fff); }
        .footer-title { color: var(--ft-text, #fff); }
        .footer-title::after { background-color: var(--ft-text, #fff); }
        .footer-links a { color: var(--ft-nav, #f0f0f0); }
        .footer-links a:hover { color: var(--ft-nav, #fff); }
        .footer-bottom-links a { color: var(--ft-nav, #fff); }
        .footer-bottom-links a:hover { color: var(--ft-nav, #fff); }
        @media (max-width: 767.98px) {
            .footer-bottom { padding-bottom: 96px; }
        }
    </style>



    <!-- Main Content -->
    <main class="main-content">

        @isset($slot)
        {{ $slot }}
        @endisset
        @yield('content')
        <!-- Right Side Shopping Cart -->
        @php
        $cartItems = session()->get('cart', []);
        $cartCount = array_sum(array_column($cartItems, 'qty'));
        @endphp
        <div class="cart-sidebar" x-data="{ cartCount: {{ $cartCount }} }" x-on:cart-updated.window="cartCount = $event.detail.count">

            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" x-text="cartCount">{{ $cartCount }}</span>
            </div>
            <div class="cart-dropdown">
                <div class="cart-header">
                    <h4>Your Cart</h4>
                    <span class="close-cart">&times;</span>
                </div>


                <div class="cart-items">
                    <livewire:frontend.components.cart-items :cartItems="$cartItems" />
                </div>
                <div class="cart-footer">
                    <div class="cart-actions">
                        <!-- <a wire:navigate href="{{ route('frontend.cart.view') }}"
                            class="btn btn-secondary btn-view-cart">Go to Cart</a> -->
                        <a wire:navigate href="{{ route('frontend.checkout') }}"
                            class="btn btn-dark btn-checkout">CHECKOUT NOW</a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    @unless(request()->routeIs('frontend.product.single.view'))
    @include('livewire.frontend.layouts.partial.footer.footer-default')
    @endunless


    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="{{ asset('assets/frontend/themes/' . setting('theme') . '/js/tabs.js') }}"></script>
    <script src="{{ asset('assets/frontend/themes/' . setting('theme') . '/js/main.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {

            // Livewire success/error events
            Livewire.on('success', (event) => {
                const message = event.message || event[0]?.message || 'Success!';
                toastr.success(message);
            });

            Livewire.on('error', (event) => {
                const message = event.message || event[0]?.message || 'Error!';
                toastr.error(message);
            });

            // Check for Laravel session flash messages (middleware or controller)
            @if(session() -> has('success'))
            toastr.success("{{ session('success') }}");
            @endif

            @if(session() -> has('error'))
            toastr.error("{{ session('error') }}");
            @endif
        });

        // Toastr options
        toastr.options = {
            "progressBar": true,
            "closeButton": true,
            "positionClass": "toast-top-right",
            "timeOut": 2500,
            "preventDuplicates": true,
            "preventOpenDuplicates": true
        };

        // SweetAlert Delete Confirmation
        window.addEventListener('showDeleteConfirmation', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won’t be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed');
                }
            });
        });
    </script>


    @stack('scripts')
    @include('partials.customer-auth-modal')
    @livewireScripts
</body>

</html>