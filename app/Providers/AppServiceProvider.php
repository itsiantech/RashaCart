<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
 use Laravel\Socialite\Facades\Socialite;
use Cache;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

       
        if (Schema::hasTable('settings')) {
            config([
                'services.google.client_id' => setting('GOOGLE_CLIENT_ID'),
                'services.google.client_secret' => setting('GOOGLE_CLIENT_SECRET'),
                'services.google.redirect' => url('/auth/google/callback'),
            ]);
        }

        if (Schema::hasTable('categories') && Schema::hasTable('banners') && Schema::hasTable('products')) {
            $categories = Cache::rememberForever('categories', function () {
                return Category::with('allChildren')
                    ->where('status', true)
                    ->get();
            });
            View::share('categories', $categories);

            $ads = Cache::rememberForever('ads', function () {
                return Banner::where('status', true)->where('type', 'ads')->get();
            });
            View::share('ads', $ads);

            $popup = Cache::rememberForever('popup', function () {
                return Banner::where('status', true)->where('type', 'popup')->get();
            });
            View::share('popup', $popup);

            $banners = Cache::rememberForever('banners', function () {
                return Banner::where('status', true)->where('type', 'banner')->get();
            });
            View::share('banners', $banners);

            $products = Cache::rememberForever('products', function () {
                return Product::with([
                        'category',
                        'user',
                        'coupons',
                        'variants.variantAttributeValues.attribute',
                        'variants.variantAttributeValues.attributeValue'
                    ])
                    ->withCount('reviews')
                    ->withAvg('reviews', 'rating')
                    ->where('status', true)
                    ->get();
            });
            View::share('products', $products);
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
