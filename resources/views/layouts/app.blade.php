<!DOCTYPE html>
<html lang="en" dir="{{ str_replace('_', '-', app()->getLocale()) }}" data-critters-container="" style="--shop-color-primary: #EB0000; --shop-color-secondary: #7A0000; --shop-color-tertiary: #292929;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Meta Tags ডাইনামিকভাবে লোড হবে -->
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}

    <!-- Fallback যদি SEO টুলস না থাকে -->
    @hasSection('title')
    <title>@yield('title') - {{ config('app.name') }}</title>
    @else
    <title>{{ config('app.name') }}</title>
    @endif

    @hasSection('meta_description')
    <meta name="description" content="@yield('meta_description')">
    @endif

    <!-- CSRF Token & Scripts -->
    @csrf
    @livewireStyles


    <meta name="description"
        content="@yield('description', 'Shop the latest products at Ordinary Bangladesh. Fast delivery, secure payments, and excellent customer service.')">
    <meta name="theme-color" content="#EB0000">
    <meta name="robots" content="index, follow">
    <link rel="icon" href="https://zetech.top/demo/img/tara-mart-logo-icon.png">


    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="preconnect" href="https://cdn.saleecom.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.saleecom.com">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    @php
    $theme = setting('theme') ?? 'default';
    @endphp

    <link rel="stylesheet" href="{{ asset("assets/frontend/themes/$theme/assets/style.css") }}">

    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Social Media Meta Tags -->
    <meta property="og:title" content="Tara Mart - Your Trusted Online Shopping Destination">
    <meta property="og:description"
        content="Shop the latest products at Tara Mart. Fast delivery, secure payments, and excellent customer service.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://zetech.top/demo/img/tara-mart-logo-icon.png">
    <meta name="twitter:card" content="summary_large_image">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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

    <!-- Vendor Alert -->
    @if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show text-center mb-0" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

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
                        <a wire:navigate href="{{ route('frontend.cart.view') }}"
                            class="btn btn-secondary btn-view-cart">Go to Cart</a>
                        <a wire:navigate href="{{ route('frontend.checkout') }}"
                            class="btn btn-primary btn-checkout">Checkout</a>
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
                text: "You won't be able to revert this!",
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