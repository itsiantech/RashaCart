<!-- Sidebar Blade (full copy-ready) -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ Route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" class="img-fluid"
                    style="max-width: 200px; max-height: 50px;">
            </span>

            {{-- <span class="app-brand-text demo menu-text fw-bolder ms-2" style="text-transform: uppercase!important;">
                {{ setting('app_name') ?: config('app.name') }}
            </span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $currentRoute = request()->route()->getName();
    @endphp

    <ul class="menu-inner py-1">

        <!-- Dashboard -->
        <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <a  href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Product Manage -->
        <li class="menu-item {{ Route::is('admin.category.index', 'admin.attribute.index', 'admin.product.index', 'admin.product.create', 'admin.product.edit', 'admin.product.view') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category-alt"></i>
                <div data-i18n="Account Settings">Product Manage</div>
            </a>
            <ul class="menu-sub">
                @if (Auth::user()->can('category.view'))
                    <li class="menu-item {{ Route::is('admin.category.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.category.index') }}" wire:navigate class="menu-link">
                            <i class="menu-icon tf-icons bx bx-category-alt"></i>
                            <div data-i18n="Analytics">Category</div>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->can('product.view'))
                    <li class="menu-item {{ Route::is('admin.attribute.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.attribute.index') }}" wire:navigate class="menu-link">
                            <i class='menu-icon tf-icons bx bx-book-content'></i>
                            <div data-i18n="Analytics">Attributes</div>
                        </a>
                    </li>                
                    <li class="menu-item {{ Route::is('admin.product.index','admin.product.create', 'admin.product.edit','admin.product.view') ? 'active' : '' }}">
                        <a href="{{ route('admin.product.index') }}" wire:navigate class="menu-link">
                            <i class='menu-icon tf-icons bx bxs-duplicate'></i>
                            <div data-i18n="Analytics">Product</div>
                        </a>
                    </li>
                @endif

                <hr>
            </ul>
        </li>

        @if (Auth::user()->can('order.view'))
        <li class="menu-item {{ Route::is('admin.order.index' , 'admin.order.view' ) ? 'active' : '' }}">
            <a  href="{{ route('admin.order.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-cart-download"></i>
                <div data-i18n="Analytics">Order Manage</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('order.view'))
        <li class="menu-item {{ Route::is('admin.cupon.index' , 'admin.cupon.create', 'admin.cupon.edit' ) ? 'active' : '' }}">
            <a  href="{{ route('admin.cupon.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-credit-card"></i>
                <div data-i18n="Analytics">Cupon Manage</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('user.view'))
            <li class="menu-item {{ Route::is('admin.customer.index' , 'admin.customer.index' ) ? 'active' : '' }}">
                <a wire:navigate href="{{ route('admin.customer.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-user-account"></i>
                    <div data-i18n="Analytics">Customers</div>
                </a>
            </li>
        @endif

         @if (Auth::user()->can('user.view'))
            <li class="menu-item {{ Route::is('admin.user.index') ? 'active' : '' }}">
                <a wire:navigate href="{{ route('admin.user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Analytics">Our Staffs</div>
                </a>
            </li>
        @endif

        <li class="menu-item {{ Route::is('admin.banner.index') ? 'active' : '' }}">
            <a wire:navigate href="{{ route('admin.banner.index') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-duplicate'></i>
                <div data-i18n="Analytics">Banner and Ads</div>
            </a>
        </li>

        <!-- APP MANAGE header -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">APP MANAGE</span>
        </li>

        <li class="menu-item {{ Route::is('admin.settings.index', 'admin.rolepermission.index') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Account Settings">Settings</div>
            </a>
            <ul class="menu-sub">
                @if (Auth::user()->can('setting.view'))
                    
                    <li class="menu-item {{ Route::is('admin.settings.index') ? 'active' : '' }}">
                        <a wire:navigate href="{{ route('admin.settings.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cog"></i>
                            <div data-i18n="Analytics">All Settings</div>
                        </a>
                    </li>                   
                @endif
               

                @if (Auth::user()->can('r&p.view'))
                    <li class="menu-item {{ Route::is('admin.rolepermission.index') ? 'active' : '' }}">
                        <a wire:navigate href="{{ route('admin.rolepermission.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-key"></i>
                            <div data-i18n="Analytics">User Permission</div>
                        </a>
                    </li>
                @endif

                <hr>
            </ul>
        </li>

        <!-- Logout -->
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
            </form>
            <a href="#" class="menu-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-power-off me-2"></i>
                <div data-i18n="Analytics">Log Out</div>
            </a>
        </li>

    </ul>

</aside>
<!-- / Menu -->

<!-- ====== Sidebar Toggle JS (Livewire-safe) ====== -->
<script>
   
    function initLivewireMenu() {        
        document.querySelectorAll('.menu-toggle').forEach(toggle => {
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
        });

        
        document.querySelectorAll('.menu-toggle').forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const parentItem = this.closest('.menu-item');

                if (!parentItem) return;                
                document.querySelectorAll('.menu-item.open').forEach(item => {
                    if (item !== parentItem) item.classList.remove('open');
                });                
                parentItem.classList.toggle('open');
            });
        });
      
        const layoutToggle = document.querySelector('.layout-menu-toggle');
        if (layoutToggle) {          
            const newLt = layoutToggle.cloneNode(true);
            layoutToggle.parentNode.replaceChild(newLt, layoutToggle);
            newLt.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('layout-menu')?.classList.toggle('d-none');
            });
        }
    }
  
    document.addEventListener('DOMContentLoaded', function () {
        initLivewireMenu();
    });
   
    document.addEventListener('livewire:load', () => initLivewireMenu());
    document.addEventListener('livewire:update', () => initLivewireMenu());
    document.addEventListener('livewire:navigated', () => initLivewireMenu());

    document.addEventListener('livewire:navigated', () => {
        if (typeof window.Helpers !== 'undefined') {
            try {
                if (typeof window.Helpers.initSidebarToggle === 'function') {
                    window.Helpers.initSidebarToggle();
                } else if (typeof window.Helpers.initMenu === 'function') {
                    window.Helpers.initMenu();
                }
            } catch (err) {
                // ignore errors from template helpers
                console.warn('Helpers init error', err);
            }
        }
    });
</script>
