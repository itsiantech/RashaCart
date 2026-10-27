<?php

use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\ProductFeedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Frontend\BestOffer\BestOffer;
use App\Livewire\Frontend\Brand\BrandProduct;
use App\Livewire\Frontend\Brand\BrandView;
use App\Livewire\Frontend\Category\CategoryProduct;
use App\Livewire\Frontend\FlashSale\FlashSaleProducts;
use App\Livewire\Frontend\NewArrival\NewArrivals;
use App\Livewire\Frontend\OrderTracking;
use App\Livewire\Frontend\Preorder\PreOrderSection;
use App\Livewire\Frontend\Product\CartView;
use App\Livewire\Frontend\Product\CategoryView;
use App\Livewire\Frontend\Product\Checkout;
use App\Livewire\Frontend\Product\OrderSuccess;
use App\Livewire\Frontend\Product\SingleView;
use App\Livewire\Frontend\Product\UserProductsView;
use App\Livewire\Frontend\TrendyProduct\TrendyProduct;
use App\Livewire\Frontend\Welcome;
use App\Livewire\Frontend\LandingPage\PublicLandingPage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function (\Illuminate\Http\Request $request) {
    $show = (int) $request->query('show', 12);
    $show = max(12, min($show, 60));

    $liveFlashSale = \App\Models\FlashSale::active()->live()->latest()->first();

    // Flash sale products ONLY from the live campaign (no fallbacks — the
    // section stays hidden unless a campaign is actually live).
    $flashSaleProducts = collect([]);
    $offerPercent = 0;
    $countdownTarget = now();
    if ($liveFlashSale) {
        $flashSaleProducts = \App\Models\Product::whereHas('activeFlashSaleItem', fn ($q) => $q->where('flash_sale_id', $liveFlashSale->id))
            ->withCount('reviews')->with('activeFlashSaleItem')->limit(5)->get();
        $offerPercent = (int) round($liveFlashSale->activeItems()->max('discount_percent') ?? 0);
        if (! $offerPercent) {
            $offerPercent = (int) $flashSaleProducts->max(fn ($p) => $p->price_info['percent'] ?? 0);
        }
        $countdownTarget = $liveFlashSale->end_at;
        // Server-rendered initial values (JS takes over ticking every second)
        $diff = max(0, now()->diffInSeconds($countdownTarget));
        $countdownParts = [
            'days' => intdiv($diff, 86400),
            'hours' => intdiv($diff % 86400, 3600),
            'mins' => intdiv($diff % 3600, 60),
            'secs' => $diff % 60,
        ];
    }

    // Homepage categories with products
    $homeCategories = \App\Models\HomepageCategory::with(['category.products' => function($query) {
        $query->where('status', 1)->latest();
    }])
    ->where('is_active', true)
    ->orderBy('sort_order', 'asc')
    ->get();

    // Navigation categories (root level, up to 3 levels of children)
    $navCategories = \App\Models\Category::select('id', 'name', 'slug', 'parent_id', 'status', 'image', 'sort_order')
        ->where('status', 1)->whereNull('parent_id')
        ->with(['children' => function ($q) {
            $q->select('id', 'name', 'slug', 'parent_id', 'status', 'image', 'sort_order')
                ->where('status', 1)->orderBy('name')
                ->with(['children' => function ($q2) {
                    $q2->select('id', 'name', 'slug', 'parent_id', 'status', 'image', 'sort_order')
                        ->where('status', 1)->orderBy('name');
                }]);
        }])
        ->orderBy('name')->limit(15)->get();

    // "Shop by Category" showcase: root categories with image + product count (sub-categories included)
    $productCounts = \App\Models\Product::selectRaw('category_id, count(*) as total')
        ->where('status', 1)->groupBy('category_id')->pluck('total', 'category_id');

    $categoryGrid = $navCategories->map(function ($cat) use ($productCounts) {
        $ids = [$cat->id];
        $walk = function ($nodes) use (&$walk, &$ids) {
            foreach ($nodes as $node) {
                $ids[] = $node->id;
                if ($node->relationLoaded('children')) {
                    $walk($node->children);
                }
            }
        };
        $walk($cat->children);

        $count = collect($ids)->sum(fn ($id) => (int) ($productCounts[$id] ?? 0));

        return ['category' => $cat, 'products_count' => $count];
    })->values();

    // "Shop by Category" card count comes from admin settings
    $categoryGrid = $categoryGrid->take(max(1, (int) setting('shop_by_category_limit', 8)));

    // Recommended For You settings
    $recommendedTitle = setting('recommended_title') ?? 'Recommended For You';
    $recommendedLimit = setting('recommended_product_limit') ?? 12;

    return view('homepage', [
        'categories'          => $navCategories,
        'categoryGrid'        => $categoryGrid,
        'heroBanners'         => \App\Models\Banner::where('status', 1)->latest()->limit(5)->get(),
        'topShops'            => \App\Models\User::where('role', 'vendor')->with('vendorDetails')->withCount('products')->latest()->limit(6)->get(),
        'topBrands'           => \App\Models\Brand::withCount('products')->orderByDesc('products_count')->orderBy('name')->take(max(1, (int) setting('top_brands_limit', 8)))->get(),
        'flashSaleProducts'   => $flashSaleProducts,
        'liveFlashSale'       => $liveFlashSale,
        'offerPercent'        => $offerPercent,
        'countdownTarget'     => $countdownTarget,
        'countdownParts'      => $countdownParts ?? ['days' => 0, 'hours' => 0, 'mins' => 0, 'secs' => 0],
        'homeCategories'      => $homeCategories,
        'recommendedTitle'    => $recommendedTitle,
        'recommendedLimit'    => $recommendedLimit,
        'recommendedProducts' => \App\Models\Product::with('activeFlashSaleItem')->withCount('reviews')->latest()->limit($recommendedLimit)->get(),
        'totalProducts'       => \App\Models\Product::count(),
        'show'                => $show,
        'cartCount'           => array_sum(array_column(session()->get('cart', []), 'qty')),
    ]);
})->name('home');

Route::get('/seed-database-secret', function () {
    try {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

        return 'Database wiped, migrated, and seeded successfully!';
    } catch (\Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');

    return 'Symlink process successfully completed';
});

// Catalog feeds: one row per variant (item_group_id = product id)
Route::get('/feed/google.xml', [ProductFeedController::class, 'google'])->name('feed.google');
Route::get('/feed/meta.csv', [ProductFeedController::class, 'meta'])->name('feed.meta');

// Interactive UI demos (static previews, no DB needed)
Route::get('/demo-plp', fn () => response()->file(public_path('demo-plp.html'), ['Content-Type' => 'text/html']))->name('demo.plp');

// Dynamic SEO files (managed from admin Google SEO page)
Route::get('/sitemap.xml', [SitemapController::class, 'xml'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('frontend')->name('frontend.')->group(function () {

    Route::get('/product-data/{slug}', SingleView::class)->name('product.single.view');
    Route::get('/user/products/{id}', UserProductsView::class)->name('user.products.view');
    Route::get('/category-view/{slug?}', CategoryView::class)->name('category.view');
    Route::get('/category/products/{category}', CategoryProduct::class)->name('category.products.view');
    Route::get('/brands-view/{slug?}', BrandView::class)->name('brand.view');
    Route::get('/flash-sale', FlashSaleProducts::class)->name('flash.sale.products');
    Route::get('/best-offers', BestOffer::class)->name('best.offers');

    // Route::get('/new-arrivals', Welcome::class)->name('new-arrivals.view');

    Route::get('/trendy-products', TrendyProduct::class)->name('trendy-products.view');

    Route::get('/new-arrivals', NewArrivals::class)->name('new-arrivals.view');

    Route::get('/preorder', PreOrderSection::class)->name('preorder.view');

    Route::get('/brands/products/{brand}', BrandProduct::class)->name('brands.products.view');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
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

// Public Landing Page Route (outside frontend prefix for direct access)
Route::get('/landing/{slug}', PublicLandingPage::class)->name('public.landing');

require __DIR__.'/auth.php';
require __DIR__.'/web/customer.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        return match ($user?->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'vendor' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');

    Route::get('/profile/index', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/resetPass', [ProfileController::class, 'resetPass'])->name('profile.resetPass');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    require __DIR__.'/web/admin.php';
    require __DIR__.'/web/user.php';
    require __DIR__.'/web/rider.php';
});


Route::prefix('admin/accounts')->name('admin.accounts.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AccountsController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales-category', [AccountsController::class, 'salesCategory'])->name('sales-category');
    Route::get('/supplier-ledger', [AccountsController::class, 'supplierLedger'])->name('supplier-ledger');
    Route::post('/suppliers/store', [AccountsController::class, 'storeSupplier'])->name('suppliers.store');
    Route::post('/purchases/store', [AccountsController::class, 'storePurchase'])->name('purchases.store');
    Route::post('/purchases/{id}/void', [AccountsController::class, 'voidPurchase'])->name('purchases.void');
    Route::get('/purchases/{id}/details', [AccountsController::class, 'purchaseDetails'])->name('purchases.details');
    Route::get('/office-expenses', [AccountsController::class, 'officeExpenses'])->name('office-expenses');
    Route::get('/tech-cost', [AccountsController::class, 'techCost'])->name('tech-cost');
    Route::get('/marketing-ads', [AccountsController::class, 'marketingAds'])->name('marketing-ads');
    Route::get('/payroll-investment', [AccountsController::class, 'payrollInvestment'])->name('payroll-investment');
    Route::get('/void-logs', [AccountsController::class, 'voidLogs'])->name('void-logs');
    Route::get('/export-reports', [AccountsController::class, 'exportReports'])->name('export-reports');
});