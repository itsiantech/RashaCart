<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrintController;;

use App\Livewire\Admin\Settings\RolePermission\{RolePermissionIndex, RolePermissionAssign};
use App\Livewire\Admin\Settings\User\{UserCreate, UserEdit, UserIndex};
use App\Livewire\Admin\Settings\User\Profile\ProfileIndex;
use App\Livewire\Admin\Settings\AppSetting;
use App\Livewire\Admin\Banner\{BannerIndex, BannerCreate, BannerEdit};
use App\Livewire\Admin\{Dashboard, Customer};
use App\Livewire\Admin\Category\CategoryComponent;   
use App\Livewire\Admin\Attribute\AttributeComponent;
use App\Livewire\Admin\Products\{ProductIndex, ProductCreate, ProductEdit, ProductView};
use App\Livewire\Admin\Order\{OrderIndex, OrderView, OrderEdit};
use App\Livewire\Admin\Cupon\{CuponIndex, CuponCreate, CuponEdit};
use App\Http\Middleware\AdminAccess;

    route::get('orders/print/{order}', [PrintController::class, 'print'])->name('admin.orders.print');

    Route::prefix('admin')->middleware(['auth', 'admin.access'])->name('admin.')->group(function () {
       
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/customer/index', Customer::class)->name('customer.index');

        Route::get('/settings', AppSetting::class)->name('settings.index');
        
        Route::get('/user/index', UserIndex::class)->name('user.index');
        Route::get('/user/create', UserCreate::class)->name('user.create');
        Route::get('/user/{id}/edit', UserEdit::class)->name('user.edit');
        
        Route::get('/role-permission/index', RolePermissionIndex::class)->name('rolepermission.index');
        Route::get('/role-permission/{id}/assign', RolePermissionAssign::class)->name('rolepermission.assign');

        Route::get('/banner', BannerIndex::class)->name('banner.index')->middleware('can:banner.view');
        Route::get('/banner/create', BannerCreate::class)->name('banner.create')->middleware('can:banner.create');
        Route::get('/banner/edit/{id}', BannerEdit::class)->name('banner.edit')->middleware('can:banner.update');
                
        Route::get('/category', CategoryComponent::class)->name('category.index')->middleware('can:category.view');
        Route::get('/attribute', AttributeComponent::class)->name('attribute.index');
       
        Route::get('/product', ProductIndex::class)->name('product.index')->middleware('can:product.view');
        Route::get('/product/create', ProductCreate::class)->name('product.create')->middleware('can:product.create');
        Route::get('/product/edit/{id}', ProductEdit::class)->name('product.edit')->middleware('can:product.update');
        Route::get('/product/view/{id}', ProductView::class)->name('product.view')->middleware('can:product.view');


        Route::get('/orders', OrderIndex::class)->name('order.index')->middleware('can:order.view');
        Route::get('/order/view/{orderId}', OrderView::class)->name('order.view')->middleware('can:order.view');
        Route::get('/order/edit/{id}', OrderEdit::class)->name('order.edit')->middleware('can:order.edit');


        Route::get('/cupon', CuponIndex::class)->name('cupon.index')->middleware('can:cupon.view');
        Route::get('/cupon/create/{coupon_id?}', CuponCreate::class)->name('cupon.create')->middleware('can:cupon.create');

        Route::get('/cupon/edit/{id}', CuponEdit::class)->name('cupon.edit')->middleware('can:cupon.update');

    });

    
    // Route::controller(SettingsController::class)->prefix('settings')->name('settings.')->group(function () {
    //     Route::get('/basic', 'index')->name('basic')->middleware('can:setting.view');
    //     Route::post('/update', 'update')->name('update')->middleware('can:setting.update');
    // });
        
      
    