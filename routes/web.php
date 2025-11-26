<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UserViews\HomeComponent;
// use App\Livewire\UserViews\OrderTrackingComponent;
use App\Http\Controllers\ProfileController;
use App\Livewire\Frontend\{Welcome, OrderTracking};
use App\Livewire\Frontend\Product\{SingleView, Checkout, CategoryView, CartView, OrderSuccess};

Route::get('/', Welcome::class)->name('home');



  Route::prefix('frontend')->name('frontend.')->group(function () {

    Route::get('/product-data/{slug}', SingleView::class)->name('product.single.view');
    Route::get('/category-view/{slug?}', CategoryView::class)->name('category.view');
    Route::get('/cart-view', CartView::class)->name('cart.view');
    Route::get('/checkout', Checkout::class)->name('checkout');   
    Route::get('/order/success/{order}', OrderSuccess::class)->name('order.success');
    Route::get('/order/tracking', OrderTracking::class)->name('order.tracking');
    
    Route::get('/privacy-policy', function(){
      return view('livewire.frontend.pages.privacy-policy');
    })->name('privacy.policy');

    Route::get('/terms-and-conditions', function(){
      return view('livewire.frontend.pages.terms-and-conditions');
    })->name('terms.and.conditions');

    Route::get('/refund-and-returns', function(){
      return view('livewire.frontend.pages.refund-and-returns');
    })->name('refund.and.returns');

    Route::get('/about-us', function(){
      return view('livewire.frontend.pages.about-us');
    })->name('about.us');



  });

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    
    Route::get('/profile/index', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/resetPass', [ProfileController::class, 'resetPass'])->name('profile.resetPass');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    require __DIR__.'/web/admin.php';
    require __DIR__.'/web/user.php';
    require __DIR__.'/web/rider.php';

});





 
    






