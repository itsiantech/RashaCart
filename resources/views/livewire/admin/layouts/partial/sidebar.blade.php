@php
$user = Auth::user();
$isAdmin = $user && $user->hasRole('admin');
$currentRoute = request()->route()?->getName() ?? '';
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

        <!-- Inventory & Stock -->
        @if ($isAdmin)
        <li class="menu-item {{ Route::is('admin.inventory.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Inventory & Stock</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('admin.inventory.overview') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.overview') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-box"></i>
                        <div>Stock Overview</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.category-wise') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.category-wise') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-sitemap"></i>
                        <div>Category Wise Stock</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.variant-stock') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.variant-stock') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                        <div>Attribute / Variant Stock</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.supplier-stock') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.supplier-stock') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-truck"></i>
                        <div>Supplier Stock (Purchase)</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.movements') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.movements') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                        <div>Stock Movements</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.damage') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.damage') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-error-circle text-danger"></i>
                        <div>Damage & Returned Stock</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.adjustment') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.adjustment') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-edit-alt"></i>
                        <div>Stock Adjustment</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.transfer') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.transfer') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-repeat"></i>
                        <div>Warehouse Transfer</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.inventory.low-stock') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.inventory.low-stock') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-bell text-warning"></i>
                        <div>Low Stock Alerts</div>
                    </a>
                </li>
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

        <!-- Seller section (visibility: Business Settings → Seller Menu; vendors always keep Payout) -->
        @if (setting('seller_menu', true) || ($user && $user->hasRole('vendor')))
        <li class="menu-item {{ Route::is('admin.vendor.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category-alt"></i>
                <div>Seller Manage</div>
            </a>
            <ul class="menu-sub">
                @if (setting('seller_menu', true) && ($isAdmin || Auth::user()->can('vendor.index')))
                <li class="menu-item {{ Route::is('admin.vendor.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>Seller List</div>
                    </a>
                </li>
                @endif

                @if (setting('seller_menu', true) && ($isAdmin || Auth::user()->can('vendor.applied')))
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

                @if (setting('seller_menu', true) && ($isAdmin || Auth::user()->can('vendor.payout.request')))
                <li class="menu-item {{ Route::is('admin.vendor.payout.request') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.payout.request') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-receipt"></i>
                        <div>Seller Payout Request</div>
                    </a>
                </li>
                @endif

                @if (setting('seller_menu', true) && ($isAdmin || Auth::user()->can('vendor.manage.commission')))
                <li class="menu-item {{ Route::is('admin.vendor.manage.commission') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.vendor.manage.commission') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-money"></i>
                        <div>Manage Commission</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif
		


		<!-- Accounts & Finance -->
        @if ($isAdmin || Auth::user()->can('accounts.view'))
        <li class="menu-item {{ Route::is('admin.accounts.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div>Accounts & Finance</div>
            </a>
            <ul class="menu-sub">
                
                <!-- Accounts Dashboard -->
                <li class="menu-item {{ Route::is('admin.accounts.dashboard') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.dashboard') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-pie-chart-alt-2"></i>
                        <div>Accounts Dashboard</div>
                    </a>
                </li>

                <!-- Sales & Category Costs -->
                <li class="menu-item {{ Route::is('admin.accounts.sales-category') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.sales-category') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart"></i>
                        <div>Sales & Return Cost</div>
                    </a>
                </li>

                <!-- Supplier Ledger -->
                <li class="menu-item {{ Route::is('admin.accounts.supplier-ledger') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.supplier-ledger') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-truck"></i>
                        <div>Supplier Ledger</div>
                    </a>
                </li>

                <!-- Office Operating Cost -->
                <li class="menu-item {{ Route::is('admin.accounts.office-expenses') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.office-expenses') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-wallet"></i>
                        <div>Office Operating Cost</div>
                    </a>
                </li>

                <!-- Tech & Server Maintenance -->
                <li class="menu-item {{ Route::is('admin.accounts.tech-cost') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.tech-cost') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-server"></i>
                        <div>Tech & Server Cost</div>
                    </a>
                </li>

                <!-- Marketing & Ads Expense -->
                <li class="menu-item {{ Route::is('admin.accounts.marketing-ads') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.marketing-ads') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-bullseye"></i>
                        <div>Marketing & Ads Cost</div>
                    </a>
                </li>

                <!-- Payroll & Investment -->
                <li class="menu-item {{ Route::is('admin.accounts.payroll-investment') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.payroll-investment') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user-check"></i>
                        <div>Salary & Investment</div>
                    </a>
                </li>

                <!-- Void & Cancel Logs -->
                <li class="menu-item {{ Route::is('admin.accounts.void-logs') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.accounts.void-logs') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-block text-danger"></i>
                        <div class="text-danger fw-bold">Void & Cancel Logs</div>
                    </a>
                </li>

                <!-- Excel Reports Download -->
                <li class="menu-item {{ Route::is('admin.accounts.export-reports') ? 'active' : '' }}">
                    <a href="{{ route('admin.accounts.export-reports') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file text-success"></i>
                        <div class="text-success fw-bold">Export Excel Report</div>
                    </a>
                </li>

            </ul>
        </li>
        @endif
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

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

        <!-- Landing Page -->
        @if ($isAdmin)
        <li class="menu-item {{ Route::is('admin.landing-page.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div>Landing Page</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('admin.landing-page.create') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.landing-page.create') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-plus-circle"></i>
                        <div>Create Landing Page</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.landing-page.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.landing-page.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>Landing Page List</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Automation (registry-driven; toggles are super-admin only) -->
        @if (setting('automation_menu', true))
        @php
            $autoModules = \App\Services\AutomationRegistry::all();
            $autoHideOff = (bool) setting('automation_hide_disabled', false);
            $autoVisible = collect($autoModules)->filter(fn ($m) => $isAdmin || Auth::user()->can('automation.'.$m->key().'.view'));
        @endphp
        @if ($autoVisible->isNotEmpty())
        <li class="menu-header small text-uppercase"><span class="menu-header-text">AUTOMATION</span></li>
        <li class="menu-item {{ Route::is('admin.automation.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-chip"></i>
                <div>Automation</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('admin.automation.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.automation.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                        <div>Overview</div>
                    </a>
                </li>
                @foreach ($autoVisible as $am)
                @php
                    $amMode = \App\Services\AutomationRegistry::mode($am->key());
                    $amOff = $amMode === 'off';
                @endphp
                @if (!($amOff && $autoHideOff))
                <li class="menu-item {{ Route::is('admin.automation.module') && request()->route('key') === $am->key() ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.automation.module', $am->key()) }}"
                        class="menu-link {{ $amOff ? 'text-muted' : '' }}">
                        <i class="menu-icon tf-icons {{ $am->icon() }}"></i>
                        <div>{{ $am->name() }}</div>
                        @if ($am->status() === 'coming_soon')
                        <span class="badge bg-secondary ms-1">Coming soon</span>
                        @elseif ($amOff)
                        <span class="badge bg-secondary ms-1">Off</span>
                        @endif
                    </a>
                </li>
                @endif
                @endforeach
                @if ($isAdmin)
                <li class="menu-item {{ Route::is('admin.automation.ai-settings') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.automation.ai-settings') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cog"></i>
                        <div>AI Settings</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif
        @endif {{-- automation_menu master toggle (Business Settings) --}}

        <!-- SEO section (visibility: Business Settings toggle) -->
        @if (setting('seo_menu', true) && ($isAdmin || Auth::user()->can('setting.view')))
        <li class="menu-item {{ Route::is('admin.seo.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-globe"></i>
                <div>Google SEO</div>
            </a>
            <ul class="menu-sub">
                @foreach (['console' => ['Search Console', 'bx-search-alt'], 'ga4' => ['Google Analytics 4', 'bx-line-chart'], 'gtm' => ['Google Tag Manager', 'bx-tag'], 'sitemap' => ['XML Sitemap', 'bx-sitemap'], 'robots' => ['Robots.txt', 'bx-file'], 'schema' => ['Schema Markup', 'bx-code-alt']] as $tab => $item)
                <li class="menu-item {{ Route::is('admin.seo.index') && request()->route('tab') === $tab ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.seo.index', ['tab' => $tab]) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx {{ $item[1] }}"></i>
                        <div>{{ $item[0] }}</div>
                    </a>
                </li>
                @endforeach
            </ul>
        </li>
        @endif

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
                <li class="menu-item {{ Route::is('admin.settings.style') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.settings.style') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-palette"></i>
                        <div>Style Settings</div>
                    </a>
                </li>
                @endif

                @if ($isAdmin || Auth::user()->can('pixel.view'))
                <li class="menu-item {{ Route::is('admin.server-side-tracking.index') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.server-side-tracking.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-data"></i>
                        <div>Server Side Tracking</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::is('admin.server-side-tracking.logs') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.server-side-tracking.logs') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-list-ul"></i>
                        <div>Tracking Logs</div>
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

                @if ($isAdmin)
                <li class="menu-item {{ Route::is('admin.settings.homepage') ? 'active' : '' }}">
                    <a wire:navigate href="{{ route('admin.settings.homepage') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-alt"></i>
                        <div>Home Page Settings</div>
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