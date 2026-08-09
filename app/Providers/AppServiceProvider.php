<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        try {
            /* ---------------- Settings ---------------- */
            if (Schema::hasTable('settings')) {
                config([
                    'services.google.client_id' => setting('GOOGLE_CLIENT_ID'),
                    'services.google.client_secret' => setting('GOOGLE_CLIENT_SECRET'),
                    'services.google.redirect' => url('/auth/google/callback'),
                ]);
            }

            /* ---------------- Global Cache ---------------- */
            if (
                Schema::hasTable('categories') &&
                Schema::hasTable('banners') &&
                Schema::hasTable('brands') &&
                Schema::hasTable('products')
            ) {

                // Rebuild ONLY if cache is missing
                if (! Cache::has('global_cache_ready')) {

                    Cache::lock('rebuild-cache-lock', 30)->block(10, function () {

                        Cache::remember('categories', now()->addDays(7), function () {
                            return Category::with('allChildren')
                                ->where('status', true)
                                ->get();
                        });

                        Cache::remember('banners', now()->addDays(7), function () {
                            return Banner::where('status', true)
                                ->where('type', 'banner')
                                ->get();
                        });

                        Cache::remember('ads', now()->addDays(7), function () {
                            return Banner::where('status', true)
                                ->where('type', 'ads')
                                ->get();
                        });

                        Cache::remember('popup', now()->addDays(7), function () {
                            return Banner::where('status', true)
                                ->where('type', 'popup')
                                ->get();
                        });

                        Cache::remember('products', now()->addDays(7), function () {
                            return Product::with([
                                'category',
                                'user',
                                'coupons',
                                'variants.variantAttributeValues.attribute',
                                'variants.variantAttributeValues.attributeValue',
                            ])
                                ->withCount('reviews')
                                ->withAvg('reviews', 'rating')
                                ->where('status', true)
                                ->get();
                        });

                        Cache::remember('brands', now()->addDays(7), function () {
                            return Brand::where('is_active', true)->get();
                        });

                        // Sentinel flag (VERY IMPORTANT)
                        Cache::put('global_cache_ready', true, now()->addDays(7));
                    });
                }

                // Share cached data with all views
                View::share([
                    'categories' => Cache::get('categories'),
                    'banners' => Cache::get('banners'),
                    'ads' => Cache::get('ads'),
                    'popup' => Cache::get('popup'),
                    'products' => Cache::get('products'),
                    'brands' => Cache::get('brands'),
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore connection/missing table exceptions during early bootstrap phase (like artisan commands or migrations)
        }

        /* ---------------- Gates ---------------- */
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
