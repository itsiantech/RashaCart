<!DOCTYPE html>
<html lang="en" data-critters-container=""
    style="--shop-color-primary: #EB0000; --shop-color-secondary: #7A0000; --shop-color-tertiary: #292929;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- Favicon -->
    <link rel="icon" href="https://zetech.top/demo/img/tara-mart-logo-icon.png">

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
    <link rel="stylesheet" href="{{ asset("assets/frontend/themes/$theme/assets/style.css?v=2.0.0.1") }}">

    <!-- Material Components -->
    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    @stack('styles')
    @livewireStyles
</head>

<body>
    <!-- Top Bar -->
    {{-- @include('livewire.frontend.layouts.partial.topbar.topbar-default') --}}

    <!-- Header -->
    @include('livewire.frontend.layouts.partial.header.header-default')



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

    @include('livewire.frontend.layouts.partial.footer.footer-default')


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
    @livewireScripts
</body>

</html>