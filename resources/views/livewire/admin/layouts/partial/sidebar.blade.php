@php
$user = Auth::user();
$isAdmin = $user && $user->hasRole('admin');
$currentRoute = request()->route()->getName();
@endphp

<aside wire:ignore id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"
    style="overflow-y: auto; overflow-x: hidden; scrollbar-width: none;">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" class="img-fluid"
                    style="max-width: 200px; max-height: 50px;">
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard -->
        @if ($isAdmin || Auth::user()->can('dashboard.view'))
        <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>
        @endif

        {{-- POS --}}
        @if ($isAdmin || Auth::user()->can('pos.access'))
        <li class="menu-item {{ Route::is('admin.pos.index') ? 'active' : '' }}">
            <a href="{{ route('admin.pos.index') }}" wire:navigate class="menu-link">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>POS Terminal</div>
            </a>
        </li>
        @endif

        <!-- Product Manage -->
        @if ($isAdmin || Auth::user()->can('product.view'))
        <li class="menu-item {{ Route::is('admin.category.*','admin.attribute.*','admin.product.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category-alt"></i>
                <div>Product Manage</div>
            </a>
            <ul class="menu-sub">
                @if ($isAdmin || Auth::user()->can('category.view'))
                <li class="menu-item {{ Route::is('admin.category.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.category.index') }}" wire:navigate class="menu-link">
                        <i class="menu-icon tf-icons bx bx-category-alt"></i>
                        <div>Category</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('attribute.view'))
                <li class="menu-item {{ Route::is('admin.attribute.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.attribute.index') }}" wire:navigate class="menu-link">
                        <i class='menu-icon tf-icons bx bx-book-content'></i>
                        <div>Attributes</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('product.view'))
                <li class="menu-item {{ Route::is('admin.product.index','admin.product.create','admin.product.edit','admin.product.view','admin.product.stock.manage') ? 'active' : '' }}">
                    <a href="{{ route('admin.product.index') }}" wire:navigate class="menu-link">
                        <i class='menu-icon tf-icons bx bxs-duplicate'></i>
                        <div>Product</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <li class="menu-item {{ Route::is('admin.best-offers.index') ? 'active' : '' }} d-none">
            <a wire:navigate href="{{ route('admin.best-offers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bolt-circle"></i>
                <div>Best Offers</div>
            </a>
        </li>

        @if ($isAdmin || Auth::user()->can('flash.sales.view'))
        <li class="menu-item {{ Route::is('admin.flash.sales.index', 'admin.flash.sales.create', 'admin.flash.sales.edit', 'admin.flash.sales.view', 'admin.flash.sales.stock.manage') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-bolt-circle"></i>
                <div>Flash Sales</div>
            </a>
            <ul class="menu-sub">

                @if($isAdmin || Auth::user()->can('flash.sales.view'))
                <li class="menu-item {{ Route::is('admin.flash.sales.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.flash.sales.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>All Campaigns</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('flash.sales.create'))
                <li class="menu-item {{ Route::is('admin.flash.sales.create') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.flash.sales.create') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-plus-circle"></i>
                        <div>Create Campaign</div>
                    </a>
                </li>
                @endif

            </ul>
        </li>
        @endif

        @if ($isAdmin || Auth::user()->can('new.arrival.view'))
        <li class="menu-item {{ Route::is('admin.new.arrival.create') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-list-ul"></i>
                <div>New Arrival</div>
            </a>
            <ul class="menu-sub">
                @if($isAdmin || Auth::user()->can('new.arrival.create'))
                <li class="menu-item {{ Route::is('admin.new.arrival.create') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.new.arrival.create') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-plus-circle"></i>
                        <div>Create New Arrival</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif


        <!-- Brand Manage -->
        @if ($isAdmin || Auth::user()->can('brand.index'))
        <li class="menu-item {{ Route::is('admin.brand.*') ? 'active' : '' }}">
            <a wire:navigate href="{{ route('admin.brand.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-book-bookmark"></i>
                <div>Brand Manage</div>
            </a>
        </li>
        @endif

        <!-- Order Manage -->
        <li class="menu-item {{ Route::is('admin.order.*') ? 'active open' : '' }}">
            @if ($isAdmin || Auth::user()->can('order.view.own'))
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category-alt"></i>
                <div>Order Manage</div>
            </a>
            @endif
            <ul class="menu-sub">
                <!-- All Orders -->
                @if ($isAdmin || Auth::user()->can('order.index'))
                <li class="menu-item {{ Route::is('admin.order.index','admin.order.view') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.order.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>All Orders</div>
                    </a>
                </li>
                @endif

                <!-- Inhouse Orders -->
                @if ($isAdmin || Auth::user()->can('order.inhouse'))
                <li class="menu-item {{ Route::is('admin.order.inhouse','admin.order.inhouse.view') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.order.inhouse') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home"></i>
                        <div>Inhouse Orders</div>
                    </a>
                </li>
                @endif

                <!-- Vendor Orders -->
                @if ($isAdmin || Auth::user()->can('order.view.own'))
                <li class="menu-item {{ Route::is('admin.order.vendor','admin.order.vendor.view') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.order.vendor') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
                        <div>Sellers Order</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('order.bulk.invoice.print'))
                <li class="menu-item {{ Route::is('admin.orders.bulk-print') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.orders.bulk-print') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-printer"></i>
                        <div>Bulk Invoice Print</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        <li class="menu-item {{ Route::is('admin.free-shipping.index' , 'admin.free-shipping.create', 'admin.free-shipping.update', 'admin.free-shipping.delete' ) ? 'active' : '' }}">
            <a href="{{ route('admin.free-shipping.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-truck"></i>
                <div data-i18n="Analytics">Free Shipping</div>
            </a>
        </li>

        <!-- Payment Method -->
        <li class="menu-item {{ Route::is('admin.manual-payment.*','admin.api-payment.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-credit-card"></i>
                <div>Payment Method</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('admin.manual-payment.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.manual-payment.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>Manual Payment</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.api-payment.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.api-payment.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>API Payment</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Cupon Manage -->
        @if ($isAdmin || Auth::user()->can('cupon.view'))
        <li class="menu-item {{ Route::is('admin.cupon.*') ? 'active' : '' }}">
            <a href="{{ route('admin.cupon.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-tag"></i>
                <div>Cupon Manage</div>
            </a>
        </li>
        @endif

        <!-- Customers -->
        @if ($isAdmin || Auth::user()->can('user.view'))
        <li class="menu-item {{ Route::is('admin.customer.index') ? 'active' : '' }}">
            <a wire:navigate href="{{ route('admin.customer.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div>Customers</div>
            </a>
        </li>
        @endif

        <!-- Seller Manage -->
        <li class="menu-item {{ Route::is('admin.vendor.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category-alt"></i>
                <div>Seller Manage</div>
            </a>
            <ul class="menu-sub">
                @if ($isAdmin || Auth::user()->can('vendor.index'))
                <li class="menu-item {{ Route::is('admin.vendor.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>Seller List</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('vendor.applied'))
                <li class="menu-item {{ Route::is('admin.vendor.payment-history') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.payment-history') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user-plus"></i>
                        <div>Payment History</div>
                    </a>
                </li>
                @endif

                <!-- Payout -->
                @if ($isAdmin || $user->hasRole('vendor'))
                <li class="menu-item {{ Route::is('admin.vendor.payout') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.payout') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-wallet"></i>
                        <div>Seller Payout</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('vendor.payout.request'))
                <li class="menu-item {{ Route::is('admin.vendor.payout.request') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.payout.request') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-receipt"></i>
                        <div>Seller Payout Request</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('vendor.manage.commission'))
                <li class="menu-item {{ Route::is('admin.vendor.manage.commission') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.manage.commission') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-money"></i>
                        <div>Manage Commission</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        <!-- Our Staffs -->
        @if ($isAdmin || Auth::user()->can('user.view'))
        <li class="menu-item {{ Route::is('admin.user.index') ? 'active' : '' }}">
            <a wire:navigate href="{{ route('admin.user.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Our Staffs</div>
            </a>
        </li>
        @endif

        <!-- Banner and Ads -->
        <li class="menu-item {{ Route::is('admin.banner.index') ? 'active' : '' }}">
            <a wire:navigate href="{{ route('admin.banner.index') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bxs-duplicate'></i>
                <div>Banner and Ads</div>
            </a>
        </li>

        <!-- Settings -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">APP MANAGE</span></li>
        <li class="menu-item {{ Route::is('admin.settings.*','admin.rolepermission.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Settings</div>
            </a>
            <ul class="menu-sub">
                @if ($isAdmin || Auth::user()->can('setting.view'))
                <li class="menu-item {{ Route::is('admin.settings.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.settings.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cog"></i>
                        <div>All Settings</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('pixel.view'))
                <li class="menu-item {{ Route::is('admin.pixel-settings.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.pixel-settings.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-bullseye"></i>
                        <div>Pixel Tracking</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('r&p.view'))
                <li class="menu-item {{ Route::is('admin.rolepermission.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.rolepermission.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-key"></i>
                        <div>User Permission</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        <!-- Logout -->
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
            <a href="#" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-power-off me-2"></i>
                <div>Log Out</div>
            </a>
        </li>
    </ul>
</aside>

<!-- / Menu -->

<!-- ====== Sidebar Toggle JS (Livewire-safe) ====== -->
<script>
    function initSidebarMenu() {
        document.querySelectorAll('.menu-toggle').forEach(toggle => {
            toggle.replaceWith(toggle.cloneNode(true));
        });

        document.querySelectorAll('.menu-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parentItem = this.closest('.menu-item');
                if (!parentItem) return;

                const isOpen = parentItem.classList.contains('open');
                document.querySelectorAll('.menu-item.open').forEach(item => {
                    item.classList.remove('open');
                });
                if (!isOpen) parentItem.classList.add('open');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initSidebarMenu);
    document.addEventListener('livewire:navigated', function() {
        initSidebarMenu();
        if (typeof window.Helpers !== 'undefined') {
            window.Helpers.init();
        }
    });
</script>