<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Brands\Index as BrandsIndex;
use App\Livewire\Frontend\{Welcome, OrderTracking};
use App\Livewire\Frontend\BestOffer\BestOffer;
use App\Livewire\Frontend\Brand\BrandProduct;
use App\Livewire\Frontend\Brand\BrandView;
use App\Livewire\Frontend\Brands\Products;
use App\Livewire\Frontend\Category\CategoryProduct;
use App\Livewire\Frontend\FlashSale\FlashSaleProducts;
use App\Livewire\Frontend\NewArrival\NewArrivals;
use App\Livewire\Frontend\Preorder\PreOrderSection;
use App\Livewire\Frontend\Product\{SingleView, Checkout, CategoryView, CartView, OrderSuccess};
use App\Livewire\Frontend\Product\UserProductsView;
use App\Livewire\Frontend\TrendyProduct\TrendyProduct;
use App\Livewire\UserViews\HomeComponent;
use App\Models\NewArrival;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('home');

Route::get('/seed-database-secret', function () {
    try {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return 'Database wiped, migrated, and seeded successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/linkstorage', function () {
  Artisan::call('storage:link');
  return 'Symlink process successfully completed';
});

Route::get('/clear-cache', function () {
  Artisan::call('config:clear');
  Artisan::call('cache:clear');
  Artisan::call('view:clear');
  return 'Configuration, cache, and views cleared successfully!';
});

Route::get('/view-logs', function () {
  $path = storage_path('logs/laravel.log');
  if (!file_exists($path)) {
    return 'No log file found at ' . $path;
  }
  $content = file_get_contents($path);
  $lines = explode("\n", $content);
  $lastLines = array_slice($lines, -150);
  return '<pre>' . implode("\n", $lastLines) . '</pre>';
});


Route::prefix('frontend')->name('frontend.')->group(function () {

  Route::get('/product-data/{slug}', SingleView::class)->name('product.single.view');
  Route::get('/user/products/{id}', UserProductsView::class)->name('user.products.view');
  Route::get('/category-view/{slug?}', CategoryView::class)->name('category.view');
  Route::get('/category/products/{category}', CategoryProduct::class)->name('category.products.view');
  Route::get('/brands-view/{slug?}', BrandView::class)->name('brand.view');
  Route::get('/flash-sale', FlashSaleProducts::class)->name('flash.sale.products');
  Route::get('/best-offers', BestOffer::class)->name('best.offers');

  //Route::get('/new-arrivals', Welcome::class)->name('new-arrivals.view');

  Route::get('/trendy-products', TrendyProduct::class)->name('trendy-products.view');

  Route::get('/new-arrivals', NewArrivals::class)->name('new-arrivals.view');

  Route::get('/preorder', PreOrderSection::class)->name('preorder.view');

  Route::get('/brands/products/{brand}', BrandProduct::class)->name('brands.products.view');
  Route::get('/cart-view', CartView::class)->name('cart.view');
  Route::get('/checkout', Checkout::class)->name('checkout');
  Route::get('/order/success/{order}', OrderSuccess::class)->name('order.success');
  Route::get('/order/tracking', OrderTracking::class)->name('order.tracking');

  Route::get('/privacy-policy', function () {
    return view('livewire.frontend.pages.privacy-policy');
  })->name('privacy.policy');

  Route::get('/terms-and-conditions', function () {
    return view('livewire.frontend.pages.terms-and-conditions');
  })->name('terms.and.conditions');

  Route::get('/refund-and-returns', function () {
    return view('livewire.frontend.pages.refund-and-returns');
  })->name('refund.and.returns');

  Route::get('/about-us', function () {
    return view('livewire.frontend.pages.about-us');
  })->name('about.us');
});


require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

  Route::get('/profile/index', [ProfileController::class, 'index'])->name('profile.index');
  Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
  Route::patch('/profile/resetPass', [ProfileController::class, 'resetPass'])->name('profile.resetPass');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  require __DIR__ . '/web/admin.php';
  require __DIR__ . '/web/user.php';
  require __DIR__ . '/web/rider.php';
});
