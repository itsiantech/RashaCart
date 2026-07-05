<header class="main-header">
    <div class="header-content">
        <!-- Logo -->
        <div class="logo">
            <a wire:navigate href="{{ route('home') }}">
                <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" alt="{{ setting('app_name') }}" style="width: 140px; height: auto;">
            </a>
        </div>

        <!-- Search Bar -->
        <div class="s">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </div>
            <livewire:frontend.components.search-bar />
        </div>

        <!-- Sidebar for Mobile view-->
        <div class="mobile-sidebar" id="mobileSidebar">
            <div class="mobile-header">
                <button class="close-btn" id="closeSidebar"><i class="fas fa-times"></i></button>
            </div>
            @include('livewire.frontend.layouts.partial.category.category-list', ['categories' => \App\Models\Category::where('parent_id', null)->get()])
        </div>

        <!-- Overlay for mobile sidebar -->
        <div class="sidebar-overlay" id="mobileOverlay"></div>

        <!-- Side bar script for mobile view dont't remove -->
        <script>
            function initMobileSidebar() {
                const menuToggle = document.getElementById('menuToggle');
                const sidebar = document.getElementById('mobileSidebar');
                const overlay = document.getElementById('mobileOverlay');
                const closeBtn = document.getElementById('closeSidebar');

                if (!menuToggle || !sidebar || !overlay || !closeBtn) return;

                // Remove existing listeners to avoid duplicates
                menuToggle.onclick = () => {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                };

                overlay.onclick = closeSidebar;
                closeBtn.onclick = closeSidebar;

                function closeSidebar() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }

                // Submenu toggle for mobile
                document.querySelectorAll('.has-submenu > a').forEach(item => {
                    item.onclick = (e) => {
                        e.preventDefault();
                        const parent = item.parentElement;
                        parent.classList.toggle('open');
                    };
                });
            }

            // Run once on first load
            document.addEventListener('DOMContentLoaded', initMobileSidebar);

            // Run again every time Livewire navigates
            document.addEventListener('livewire:navigated', initMobileSidebar);
        </script>

        @php
        $cartItems = session()->get('cart', []);
        $cartCount = array_sum(array_column($cartItems, 'qty'));
        @endphp

        <div class="user-actions">
            <div class="header-preorder d-none">
                <a wire:navigate href="{{ route('frontend.preorder.view') }}">Pre-Order</a>
            </div>
            <div class="header-cart" x-data="{ cartCount: {{ $cartCount }} }"
                x-on:cart-updated.window="cartCount = $event.detail.count">
                <a wire:navigate href="{{ route('frontend.cart.view') }}" class="action-btn cart-toggle" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="action-text"></span>
                    <span class="badge cart-count" x-text="cartCount">{{ $cartCount }}</span>
                </a>
            </div>

            @guest
            <!-- Guest user -->
            <a href="{{ route('googleLogin') }}" class="action-btn" title="Login with Google">
                <i class="far fa-user"></i>
                <span class="action-text"></span>
            </a>

            <a href="{{ route('vendor.login.form') }}" class="action-btn" title="Vendor Login">
                <i class="fas fa-store"></i>
                <span class="action-text"></span>
            </a>
            @else
            <!-- Authenticated user -->
            @if (Auth::user()->hasRole('admin'))
            <a wire:navigate href="{{ route('admin.dashboard') }}" class="action-btn">
                <i class="fas fa-user-shield"></i>
                <span class="action-text">Admin Dashboard</span>
            </a>
            @elseif (Auth::user()->hasRole('vendor'))
            <a wire:navigate href="{{ route('admin.dashboard') }}" class="action-btn">
                <i class="fas fa-store"></i>
                <span class="action-text">Seller Dashboard</span>
            </a>
            @else
            <a wire:navigate href="{{ route('home') }}" class="action-btn">
                <i class="fas fa-user"></i>
                <span class="action-text">My Account</span>
            </a>
            @endif

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="action-btn" style="border:none;background:none;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="action-text">Logout</span>
                </button>
            </form>
            @endguest
        </div>

    </div>
    <style>
        /* =============================================
   MOBILE HEADER - matches the image exactly
   ============================================= */
        @media (max-width: 768px) {

            .main-header {
                background-color: #FF5500;
                padding: 10px 14px 12px;
            }

            .header-content {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0;
            }

            /* ── ROW 1: Logo (left) + User Actions (right) ── */
            .logo {
                order: 1;
                flex: 1;
            }

            .logo img {
                width: 140px !important;
                height: auto !important;
            }

            .user-actions {
                order: 2;
                display: flex;
                align-items: center;
                gap: 0px;
            }

            /* Hide text labels on mobile */
            .user-actions .action-text {
                display: none !important;
            }


            /* ── ROW 2: Search bar full width ── */
            .s {
                order: 3;
                width: 100%;
                margin-top: 10px;
                display: flex !important;
                align-items: center;
                gap: 0;
            }

            /* Search bar pill shape */
            .s form,
            .s .search-form,
            .s>div:not(.menu-toggle) {
                flex: 1;
            }

            /* Force search input styling */
            .s input[type="text"],
            .s input[type="search"] {
                background: #fff;
                border-radius: 30px 0 0 30px;
                border: none;
                padding: 10px 14px;
                font-size: 14px;
                width: 280px;
                outline: none;
                color: #333;
                margin-left: -30px;
            }

            .s button[type="submit"],
            .s .search-btn {
                background: #111 !important;
                color: #fff !important;
                border: none;
                border-radius: 0 30px 30px 0;
                padding: 10px 16px;
                font-size: 16px;
                cursor: pointer;
            }

            /* Wrap the whole search component in pill shape */
            .s .search-wrapper,
            .s form {
                display: flex;
                align-items: center;
                background: #fff;
                border-radius: 30px;
                overflow: hidden;
                width: 100%;
                box-shadow: none;
                margin-left: 8px;
            }
        }

        .header-preorder a {
            color: white;
            font-size: 18px;
            font-weight: 600;
            text-decoration: none;
            padding: 5px 10px;
        }
    </style>
</header>