{{-- Shared storefront header + subnav (same markup as homepage). --}}
@php $cartCount = $cartCount ?? array_sum(array_column(session()->get('cart', []), 'qty')); @endphp
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
                <input type="search" name="q" class="form-control" placeholder="Search products..." value="{{ request('q') }}">
                <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="hp-nav-icons">
                <a href="{{ route('profile.index') }}" class="nav-icon d-none d-md-block">
                    <i class="fas fa-user"></i>
                </a>
                <a href="#" class="nav-icon d-none d-md-block">
                    <i class="fas fa-heart"></i>
                </a>
                <a href="{{ route('frontend.cart.view') }}" class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-warning text-dark">{{ $cartCount }}</span>
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
            <a href="{{ route('register') }}" class="btn btn-sell ms-1">Sell on Padmazon</a>
            <a href="#" class="nav-link">Discounts</a>
            <a href="#" class="nav-link">All Shops</a>
            <a href="{{ route('frontend.brand.view') }}" class="nav-link">All Brands</a>
            <a href="{{ route('frontend.order.tracking') }}" class="nav-link">Track Order</a>
            <a href="{{ route('home') }}" class="nav-link">Home</a>
        </nav>
    </div>
</div>
