<header class="main-header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="logo">
                <a wire:navigate href="{{route('home')}}">
                    <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" alt="{{setting('app_name')}}">
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
               
                <div class="menu-toggle" id="menuToggle">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
              
                <livewire:frontend.components.search-bar />

            </div>


            <!-- Sidebar for Mobile view-->
            <div class="mobile-sidebar" id="mobileSidebar">
                <div class="mobile-header">
                    <h3><i class="fas fa-list"></i> Categories</h3>
                    <button class="close-btn" id="closeSidebar"><i class="fas fa-times"></i></button>
                </div>                
                @include('livewire.frontend.layouts.partial.category.category-list', ['categories' => $categories->where('parent_id', null)])                        
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
               <div class="header-cart" x-data="{ cartCount: {{ $cartCount }} }" 
                x-on:cart-updated.window="cartCount = $event.detail.count">
                
                <a wire:navigate href="{{route('frontend.cart.view')}}" class="action-btn cart-toggle" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="action-text">Cart</span>
                    <span class="badge cart-count" x-text="cartCount">{{ $cartCount }}</span>
                </a>
            </div>
                @guest
                    <!-- Guest user -->
                    <a href="{{ route('googleLogin') }}" class="action-btn" title="Login with Google">
                        <i class="far fa-user"></i>
                        <span class="action-text">Login with Google</span>
                    </a>

                    {{-- <a href="{{ route('login') }}" class="action-btn" title="Login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="action-text">Login</span>
                    </a> --}}
                @else
                    <!-- Authenticated user -->
                    @if (!Auth::user()->hasRole('user'))
                        <a wire:navigate href="{{ route('admin.dashboard') }}" class="action-btn" title="Dashboard">
                            <i class="fas fa-user-shield"></i>
                            <span class="action-text">Admin Dashboard</span>
                        </a>
                    @else
                        <a wire:navigate href="{{ route('home') }}" class="action-btn" title="My Account">
                            <i class="fas fa-user"></i>
                            <span class="action-text">My Account</span>
                        </a>
                    @endif

                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="action-btn" title="Logout" style="border:none; background:none; padding:0;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="action-text">Logout</span>
                        </button>
                    </form>
                @endguest

            </div>
        </div>
    </div>
</header>
