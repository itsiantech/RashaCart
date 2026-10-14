<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Google SEO data layer. All values come from admin settings (seo_* keys),
 * so IDs/tags change with Save — no code or .env edits.
 */
class SeoService
{
    public static function get(string $key, $default = null)
    {
        return setting('seo_'.$key, $default);
    }

    public static function put(string $key, $value): void
    {
        \App\Models\Settings::updateOrCreate(['key' => 'seo_'.$key], ['value' => $value]);
    }

    // ── Search Console ──
    public static function verificationCode(): ?string
    {
        $v = trim((string) self::get('gsc_code', ''));

        return $v !== '' ? $v : null;
    }

    // ── Tag Manager ──
    public static function gtmId(): ?string
    {
        if (! self::get('gtm_active', false)) {
            return null;
        }
        $v = trim((string) self::get('gtm_id', ''));

        return $v !== '' ? $v : null;
    }

    // ── GA4 (single source: tracking_platforms row — no duplicate tags) ──
    public static function ga4MeasurementId(): ?string
    {
        return \App\Models\TrackingPlatform::where('platform', 'ga4')
            ->where('is_active', true)
            ->value('pixel_id');
    }

    public static function saveGa4MeasurementId(?string $id): void
    {
        $id = trim((string) $id);
        $row = \App\Models\TrackingPlatform::firstOrNew(['platform' => 'ga4']);
        $row->fill([
            'name' => $row->name ?: 'GA4 (SEO)',
            'pixel_id' => $id !== '' ? $id : null,
            'is_active' => $id !== '',
        ]);
        $row->save();
        app(\App\Services\Tracking\TrackingManager::class)->clearCache();
    }

    // ── Robots.txt ──
    public static function defaultRobots(): string
    {
        return "User-agent: *\nAllow: /\n\nSitemap: ".url('/sitemap.xml');
    }

    public static function robots(): string
    {
        $v = trim((string) self::get('robots_content', ''));

        return $v !== '' ? $v : self::defaultRobots();
    }

    // ── Sitemap rows (cached 1h, cleared on product/category save) ──
    public static function sitemapRows(): array
    {
        return Cache::remember('seo.sitemap', 3600, function () {
            $rows = [[
                'loc' => url('/'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ]];

            foreach (['privacy.policy' => '0.3', 'terms.and.conditions' => '0.3', 'refund.and.returns' => '0.3', 'about.us' => '0.3'] as $route => $pr) {
                try {
                    $rows[] = ['loc' => route($route), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => $pr];
                } catch (\Throwable $e) {
                }
            }

            if (self::get('sitemap_categories', true)) {
                foreach (Category::where('status', 1)->select('id', 'slug', 'updated_at')->cursor() as $cat) {
                    $rows[] = [
                        'loc' => route('frontend.category.products.view', $cat->slug ?? $cat->id),
                        'lastmod' => $cat->updated_at?->toDateString() ?? now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            }

            if (self::get('sitemap_products', true)) {
                foreach (Product::where('status', true)->select('id', 'slug', 'is_featured', 'updated_at')->cursor() as $p) {
                    $rows[] = [
                        'loc' => route('frontend.product.single.view', $p->slug),
                        'lastmod' => $p->updated_at?->toDateString() ?? now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority' => $p->is_featured ? '1.0' : '0.8',
                    ];
                }
            }

            return $rows;
        });
    }

    public static function clearSitemapCache(): void
    {
        Cache::forget('seo.sitemap');
    }

    // ── Schema.org JSON-LD ──
    public static function orgSchema(): ?array
    {
        if (! self::get('schema_org', true)) {
            return null;
        }
        $name = setting('app_name', config('app.name'));
        $logo = setting('app_logo') ? asset('storage/logo/'.setting('app_logo')) : null;
        $sameAs = array_values(array_filter([
            setting('facebook'), setting('instagram'), setting('youtube'),
        ]));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $name,
            'url' => url('/'),
            'logo' => $logo,
            'contactPoint' => setting('contact_phone') ? [
                '@type' => 'ContactPoint',
                'telephone' => setting('contact_phone'),
                'contactType' => 'customer service',
            ] : null,
            'sameAs' => $sameAs ?: null,
        ]);
    }

    public static function websiteSchema(): ?array
    {
        if (! self::get('schema_website', true)) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => setting('app_name', config('app.name')),
            'url' => url('/'),
        ];
    }

    public static function productSchema(Product $product): ?array
    {
        if (! self::get('schema_product', true)) {
            return null;
        }

        $product->loadMissing(['brand']);
        $product->loadCount('reviews');
        $avg = (float) ($product->reviews()->avg('rating') ?? 0);

        $offers = [
            '@type' => 'Offer',
            'priceCurrency' => 'BDT',
            'price' => number_format((float) $product->sale_price, 2, '.', ''),
            'availability' => 'https://schema.org/InStock',
            'url' => route('frontend.product.single.view', $product->slug),
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->thumbnail ? asset('storage/'.$product->thumbnail) : null,
            'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($product->short_description ?? $product->long_description)), 500),
            'sku' => $product->sku,
            'brand' => ['@type' => 'Brand', 'name' => $product->brand?->name ?? setting('app_name')],
            'offers' => $offers,
        ];

        if ($product->reviews_count > 0 && $avg > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($avg, 1),
                'reviewCount' => $product->reviews_count,
            ];
        }

        return $schema;
    }

    /** BreadcrumbList for product pages. */
    public static function breadcrumbSchema(array $crumbs): ?array
    {
        if (! self::get('schema_breadcrumb', true) || empty($crumbs)) {
            return null;
        }

        $items = [];
        foreach (array_values($crumbs) as $i => $c) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $c['name'],
                'item' => $c['url'] ?? null,
            ];
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    /** Resolve the current product for schema (product pages only, cached). */
    public static function currentProduct(): ?Product
    {
        $route = request()->route();
        if (! $route || $route->getName() !== 'frontend.product.single.view') {
            return null;
        }
        $slug = $route->parameter('slug');
        if (! $slug) {
            return null;
        }

        return Cache::remember('seo.product.'.$slug, 3600, fn () => Product::where('slug', $slug)->where('status', true)->first());
    }
}
