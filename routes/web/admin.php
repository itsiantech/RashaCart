<?php

use App\Http\Controllers\Admin\BulkInvoiceController;
use App\Http\Controllers\PrintController;
use App\Livewire\Admin\AdminProfile;
use App\Livewire\Admin\Attribute\AttributeComponent;
use App\Livewire\Admin\Banner\BannerCreate;
use App\Livewire\Admin\Banner\BannerEdit;
use App\Livewire\Admin\Banner\BannerIndex;
use App\Livewire\Admin\LandingPage\LandingPageCreate;
use App\Livewire\Admin\LandingPage\LandingPageEdit;
use App\Livewire\Admin\LandingPage\LandingPageIndex;
use App\Livewire\Admin\BestOffer\BestOfferIndex;
use App\Livewire\Admin\Brands\Create;
use App\Livewire\Admin\Brands\Edit;
use App\Livewire\Admin\Brands\Index;
use App\Livewire\Admin\Category\CategoryComponent;
use App\Livewire\Admin\Cupon\CuponCreate;
use App\Livewire\Admin\Cupon\CuponEdit;
use App\Livewire\Admin\Cupon\CuponIndex;
use App\Livewire\Admin\Customer;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ForYou\ForYouIndex;
use App\Livewire\Admin\FreeShippig\FreeShipping;
use App\Livewire\Admin\Inventory\{StockOverview, CategoryWiseStock, VariantStock, SupplierStock, StockMovements, DamageStock, StockAdjustmentComponent, LowStockAlerts};
use App\Livewire\Admin\NewArrival\NewArrivalCreate;
use App\Livewire\Admin\Offers\FlashSalesCreate;
use App\Livewire\Admin\Offers\FlashSalesEdit;
use App\Livewire\Admin\Offers\FlashSalesIndex;
use App\Livewire\Admin\Offers\FlashSalesStockManage;
use App\Livewire\Admin\Offers\FlashSalesView;
use App\Livewire\Admin\Order\BulkInvoicePrint;
use App\Livewire\Admin\Order\OrderIndex;
use App\Livewire\Admin\Order\OrderInhouse;
use App\Livewire\Admin\Order\OrderVendor;
use App\Livewire\Admin\Order\OrderView;
use App\Livewire\Admin\Payment\ApiPaymentIndex;
use App\Livewire\Admin\Payment\ManualPaymentIndex;
use App\Livewire\Admin\PixelSettings;
use App\Livewire\Admin\ServerSideTracking;
use App\Livewire\Admin\Pos\PosTerminal;
use App\Livewire\Admin\Products\{ProductIndex, ProductCreate, ProductEdit, ProductView, ProductStockManage};
use App\Livewire\Admin\Settings\AppSetting;
use App\Livewire\Admin\Settings\HomePageSettings;
use App\Livewire\Admin\Settings\RolePermission\RolePermissionAssign;
use App\Livewire\Admin\Settings\RolePermission\RolePermissionIndex;
use App\Livewire\Admin\Settings\User\UserCreate;
use App\Livewire\Admin\Settings\User\UserEdit;
use App\Livewire\Admin\Settings\User\UserIndex;
use App\Livewire\Admin\Tracking\TrackingLogIndex;
use App\Livewire\Admin\Vendor\ManageCommission;
use App\Livewire\Admin\Vendor\VendorApplied;
use App\Livewire\Admin\Vendor\VendorIndex;
use App\Livewire\Admin\Vendor\VendorPayout;
use App\Livewire\Admin\Vendor\VendorPayoutRequest;
use App\Livewire\Admin\Vendor\VendorProfile;
use App\Livewire\Admin\Vendor\VendorView;
use Illuminate\Support\Facades\Route;

Route::get('orders/print/{order}', [PrintController::class, 'print'])->name('admin.orders.print');

Route::prefix('admin')->middleware(['auth', 'vendor.approved'])->name('admin.')->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('can:dashboard.view');
    Route::get('/customer/index', Customer::class)->name('customer.index');

    Route::get('pos', PosTerminal::class)->name('pos.index');

    Route::get('/settings', AppSetting::class)->name('settings.index')->middleware('can:setting.view');
    Route::get('/settings/homepage', HomePageSettings::class)->name('settings.homepage')->middleware('role:admin');

    Route::get('server-side-tracking', ServerSideTracking::class)->name('server-side-tracking.index')->middleware('role:admin');
    Route::get('server-side-tracking/logs', TrackingLogIndex::class)->name('server-side-tracking.logs')->middleware('role:admin');

    Route::get('/user/index', UserIndex::class)->name('user.index')->middleware('role:admin');
    Route::get('/user/create', UserCreate::class)->name('user.create')->middleware('role:admin');
    Route::get('/user/{id}/edit', UserEdit::class)->name('user.edit')->middleware('role:admin');

    Route::get('/profile/index', AdminProfile::class)->name('profile')->middleware('can:admin.profile.view');

    // Vendors Route
    Route::get('/vendor/profile/{vendorId}', VendorProfile::class)->name('vendor.profile')->middleware('can:vendor.profile');
    Route::get('/vendor/index', VendorIndex::class)->name('vendor.index')->middleware('role:admin');
    Route::get('/vendor/payout', VendorPayout::class)->name('vendor.payout')->middleware('can:vendor.payout');
    Route::get('/vendor/payout/request', VendorPayoutRequest::class)->name('vendor.payout.request')->middleware('role:admin');
    Route::get('/vendor/payment-history', VendorApplied::class)->name('vendor.payment-history')->middleware('role:admin');
    Route::get('/vendor/manage-commision', ManageCommission::class)->name('vendor.manage.commission')->middleware('role:admin');
    Route::get('/vendor/view/{vendorId}', VendorView::class)->name('vendors.view')->middleware('role:admin');

    Route::get('/role-permission/index', RolePermissionIndex::class)->name('rolepermission.index');
    Route::get('/role-permission/{id}/assign', RolePermissionAssign::class)->name('rolepermission.assign');

    Route::get('/banner', BannerIndex::class)->name('banner.index')->middleware('role:admin');
    Route::get('/banner/create', BannerCreate::class)->name('banner.create')->middleware('role:admin');
    Route::get('/banner/edit/{id}', BannerEdit::class)->name('banner.edit')->middleware('role:admin');

    // Landing Pages
    Route::get('/landing-page', LandingPageIndex::class)->name('landing-page.index')->middleware('role:admin');
    Route::get('/landing-page/create', LandingPageCreate::class)->name('landing-page.create')->middleware('role:admin');
    Route::get('/landing-page/edit/{id}', LandingPageEdit::class)->name('landing-page.edit')->middleware('role:admin');

    Route::get('/category', CategoryComponent::class)->name('category.index')->middleware('role:admin');
    Route::get('/attribute', AttributeComponent::class)->name('attribute.index')->middleware('role:admin');

    Route::get('/product', ProductIndex::class)->name('product.index')->middleware('can:product.view');
    Route::get('/product/create', ProductCreate::class)->name('product.create')->middleware('can:product.create');
    Route::get('/product/edit/{id}', ProductEdit::class)->name('product.edit')->middleware('can:product.update');
    Route::get('/product/view/{id}', ProductView::class)->name('product.view')->middleware('can:product.view');
    Route::get('/product/stock/manage/{id}', ProductStockManage::class)->name('product.stock.manage')->middleware('can:product.stock.manage');

    Route::get('/brand', Index::class)->name('brand.index')->middleware('can:brand.view');
    Route::get('/brand/create', Create::class)->name('brand.create')->middleware('can:brand.create');
    Route::get('/brand/edit/{id}', Edit::class)->name('brand.edit')->middleware('can:brand.update');

    // Flash Sales
    Route::get('/flash-sales', FlashSalesIndex::class)->name('flash.sales.index')->middleware('can:flash.sales.view');
    Route::get('/flash-sales/create', FlashSalesCreate::class)->name('flash.sales.create')->middleware('can:flash.sales.create');
    Route::get('/flash-sales/edit/{id}', FlashSalesEdit::class)->name('flash.sales.edit')->middleware('can:flash.sales.update');
    Route::get('/flash-sales/view/{id}', FlashSalesView::class)->name('flash.sales.view')->middleware('can:flash.sales.view');
    Route::get('/flash-sales/stock/manage/{id}', FlashSalesStockManage::class)->name('flash.sales.stock.manage')->middleware('can:flash.sales.stock.manage');

    Route::get('/admin/best-offers', BestOfferIndex::class)->name('best-offers.index');

    // New Arrival
    Route::get('/new-arrival/create', NewArrivalCreate::class)->name('new.arrival.create')->middleware('can:new.arrival.create');

    // For You
    Route::get('/for-you', ForYouIndex::class)->name('for.you.index')->middleware('can:for.you.view');

    Route::get('/orders', OrderIndex::class)->name('order.index')->middleware('can:order.manage');
    Route::get('/order/view/{orderId}', OrderView::class)->name('order.view')->middleware('can:order.manage');
    Route::get('/order/inhouse', OrderInhouse::class)->name('order.inhouse')->middleware('role:admin');
    Route::get('/order/vendor', OrderVendor::class)->name('order.vendor')->middleware('can:order.view.own');
    Route::get('/orders/bulk-print', BulkInvoicePrint::class)->name('orders.bulk-print')->middleware('can:order.bulk.invoice.print');
    Route::post('/invoices/bulk-print-store', [BulkInvoiceController::class, 'store'])->name('invoices.bulk-print-store');
    Route::get('/invoices/bulk-print', [BulkInvoiceController::class, 'show'])->name('invoices.bulk-print-view');

    Route::get('/free-shipping', FreeShipping::class)->name('free-shipping.index')->middleware('can:free.shipping.view');

    // Payment Method
    Route::get('/manual-payment', ManualPaymentIndex::class)->name('manual-payment.index');
    Route::get('/api-payment', ApiPaymentIndex::class)->name('api-payment.index');

    Route::get('/cupon', CuponIndex::class)->name('cupon.index')->middleware('can:cupon.view');
    Route::get('/cupon/create/{coupon_id?}', CuponCreate::class)->name('cupon.create')->middleware('can:cupon.create');

    Route::get('/cupon/edit/{id}', CuponEdit::class)->name('cupon.edit')->middleware('can:cupon.update');

    // Inventory & Stock
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/overview', \App\Livewire\Admin\Inventory\StockOverview::class)->name('overview');
        Route::get('/category-wise', \App\Livewire\Admin\Inventory\CategoryWiseStock::class)->name('category-wise');
        Route::get('/variant-stock', \App\Livewire\Admin\Inventory\VariantStock::class)->name('variant-stock');
        Route::get('/supplier-stock', \App\Livewire\Admin\Inventory\SupplierStock::class)->name('supplier-stock');
        Route::get('/movements', \App\Livewire\Admin\Inventory\StockMovements::class)->name('movements');
        Route::get('/damage-return', \App\Livewire\Admin\Inventory\DamageStock::class)->name('damage');
        Route::get('/adjustment', \App\Livewire\Admin\Inventory\StockAdjustmentComponent::class)->name('adjustment');
        Route::get('/transfer', \App\Livewire\Admin\Inventory\WarehouseTransferComponent::class)->name('transfer');
        Route::get('/low-stock', \App\Livewire\Admin\Inventory\LowStockAlerts::class)->name('low-stock');
    });
});

// Route::controller(SettingsController::class)->prefix('settings')->name('settings.')->group(function () {
//     Route::get('/basic', 'index')->name('basic')->middleware('can:setting.view');
//     Route::post('/update', 'update')->name('update')->middleware('can:setting.update');
// });
