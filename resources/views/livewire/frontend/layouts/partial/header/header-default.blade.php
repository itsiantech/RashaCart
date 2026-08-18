<header class="main-header">
    <div class="header-content">
        <!-- Mobile hamburger (moved out to row 1) -->
        <div class="menu-toggle d-lg-none" id="menuToggle">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </div>

        <!-- Logo -->
        <div class="logo">
            <a wire:navigate href="{{ route('home') }}">
                <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" alt="{{ setting('app_name') }}" style="width: 140px; height: auto;">
            </a>
        </div>

        <!-- Search Bar -->
        <div class="s d-none d-lg-block" id="mobileSearchBar">
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

        <!-- Side bar + mobile search script, don't remove -->
        <script>
            function initMobileSidebar() {
                const menuToggle = document.getElementById('menuToggle');
                const sidebar = document.getElementById('mobileSidebar');
                const overlay = document.getElementById('mobileOverlay');
                const closeBtn = document.getElementById('closeSidebar');

                if (!menuToggle || !sidebar || !overlay || !closeBtn) return;

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

                document.querySelectorAll('.has-submenu > a').forEach(item => {
                    item.onclick = (e) => {
                        e.preventDefault();
                        const parent = item.parentElement;
                        parent.classList.toggle('open');
                    };
                });
            }

            function initMobileSearchToggle() {
                const searchToggle = document.getElementById('mobileSearchToggle');
                const searchBar = document.getElementById('mobileSearchBar');
                if (!searchToggle || !searchBar) return;

                searchToggle.onclick = () => {
                    searchBar.classList.toggle('active');
                };
            }

            document.addEventListener('DOMContentLoaded', () => {
                initMobileSidebar();
                initMobileSearchToggle();
            });
            document.addEventListener('livewire:navigated', () => {
                initMobileSidebar();
                initMobileSearchToggle();
            });
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
                    <span class="action-text d-none d-lg-block">Cart</span>
                    <span class="badge cart-count" x-text="cartCount">{{ $cartCount }}</span>
                </a>
            </div>

            <span class="action-divider d-lg-none"></span>

            <button type="button" class="search-toggle-btn d-lg-none" id="mobileSearchToggle" title="Search">
                <i class="fas fa-search"></i>
            </button>

            @guest
            <!-- Guest user -->
            <a href="{{ route('login') }}" class="action-btn" title="Login with Google">
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
                background: linear-gradient(135deg, #FF7A1A 0%, #FF5500 60%, #FF3D00 100%);
                padding: 10px 10px;
                margin: 5px 5px;
                border-radius: 10px;
                box-shadow: 0 12px 25px rgba(255, 85, 0, 0.28);
            }

            .header-content {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0;
                position: relative;
            }

            /* ── Hamburger box (row 1, left) ── */
            .menu-toggle {
                order: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                position: static;
                width: 32px;
                height: 32px;
                flex-shrink: 0;
                background: transparent;
                border-radius: 7px;
                color: #fff;
                font-size: 17px;
                margin-right: 10px;
                padding: 0;
                box-shadow: rgba(0, 0, 0, 0.07) 0px 1px 2px, rgba(0, 0, 0, 0.07) 0px 2px 4px, rgba(0, 0, 0, 0.07) 0px 4px 8px, rgba(0, 0, 0, 0.07) 0px 8px 16px, rgba(0, 0, 0, 0.07) 0px 16px 32px, rgba(0, 0, 0, 0.07) 0px 32px 64px;
            }

            /* ── Logo ── */
            .logo {
                order: 2;
                flex: 1;
            }

            .logo img {
                width: 120px !important;
                height: auto !important;
            }

            /* ── User actions (row 1, right): cart | search ── */
            .user-actions {
                order: 3;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .user-actions .action-text {
                display: none !important;
            }

            /* Hide login/vendor/dashboard/logout on mobile — reachable via bottom-nav Account */
            .user-actions>a.action-btn:not(.cart-toggle),
            .user-actions form {
                display: none !important;
            }

            .header-preorder {
                display: none !important;
            }

            .header-cart .action-btn {
                color: #fff;
                font-size: 19px;
                position: relative;
                padding: 0;
            }

            .header-cart .badge {
                background: #111;
                color: #fff;
                font-size: 10px;
                width: 14px;
                height: 14px;
                line-height: 0;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                position: absolute;
                top: -13px;
                right: 10px !important;
                left: 3px;
                padding: 0;
            }

            .action-divider {
                width: 1px;
                height: 20px;
                background: rgba(255, 255, 255, 0.55);
            }

            .search-toggle-btn {
                background: none;
                border: none;
                color: #fff;
                font-size: 19px;
                padding: 0;
                cursor: pointer;
                line-height: 1;
            }

            /* ── Search bar (row 2) — hidden by default, slides open ── */
            .s {
                order: 4;
                width: 100% !important;
                display: flex !important;
                max-height: 0;
                opacity: 0;
                overflow: hidden;
                margin-top: 0;
                transition: max-height 0.25s ease, opacity 0.2s ease, margin-top 0.25s ease;
            }

            .s.active {
                max-height: 60px;
                opacity: 1;
                margin-top: 12px;
            }

            .search-container {
                margin-left: 0;
            }

            /* Force search input styling */
            .s input[type="text"],
            .s input[type="search"] {
                background: #fff;
                border-radius: 30px 0 0 30px;
                border: none;
                padding: 10px 14px;
                font-size: 14px;
                width: 100%;
                outline: none;
                color: #333;
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

            .s .search-wrapper,
            .s form {
                display: flex;
                align-items: center;
                background: #fff;
                border-radius: 30px;
                overflow: hidden;
                width: 100%;
                box-shadow: none;
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