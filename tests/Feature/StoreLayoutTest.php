<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StoreLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_plp_pages_use_unified_homepage_chrome(): void
    {
        $cat = \App\Models\Category::create(['name' => 'Fashion', 'slug' => 'fashion', 'status' => 1]);

        foreach ([
            route('frontend.category.products.view', 'fashion'),
            route('frontend.category.view', 'fashion'),
        ] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            // New unified header/nav/footer
            $this->assertStringContainsString('navbar-main', $html);
            $this->assertStringContainsString('Sell on Padmazon', $html);
            $this->assertStringContainsString('hp-footer', $html);
            $this->assertStringContainsString('mobile-bottom-nav', $html);
            // Old layout header gone
            $this->assertStringNotContainsString('mobile-sidebar', $html);
            $this->assertStringNotContainsString('menuToggle', $html);
        }
    }

    public function test_homepage_untouched(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('navbar-main', $html);
        $this->assertStringContainsString('hp-footer', $html);
    }

    public function test_category_products_render_with_plp_styles(): void
    {
        $cat = \App\Models\Category::create(['name' => 'Fashion', 'slug' => 'fashion', 'status' => 1]);
        \App\Models\Product::factory()->create(['name' => 'Test Tee', 'status' => true, 'category_id' => $cat->id]);

        $html = $this->get(route('frontend.category.products.view', 'fashion'))->assertOk()->getContent();
        $this->assertStringContainsString('Test Tee', $html);
        $this->assertStringContainsString('plp.css', $html);
        $this->assertStringContainsString('products-grid', $html);
        // Centered container rule must reach the browser (header/footer middle)
        $this->assertStringContainsString('max-width: 1280px', $html);
    }

    public function test_brand_sort_and_custom_price_filters(): void
    {
        $cat = \App\Models\Category::create(['name' => 'Fashion', 'slug' => 'fashion2', 'status' => 1]);
        $b1 = \App\Models\Brand::create(['name' => 'Alpha', 'slug' => 'alpha', 'is_active' => 1]);
        $b2 = \App\Models\Brand::create(['name' => 'Beta', 'slug' => 'beta', 'is_active' => 1]);
        \App\Models\Product::factory()->create([
            'name' => 'Cheap Alpha', 'status' => true, 'category_id' => $cat->id,
            'brand_id' => $b1->id, 'price' => 500, 'sale_price' => 400,
        ]);
        \App\Models\Product::factory()->create([
            'name' => 'Pricy Beta', 'status' => true, 'category_id' => $cat->id,
            'brand_id' => $b2->id, 'price' => 5000, 'sale_price' => 4500,
        ]);

        // Brand filter
        $t = Livewire::test(\App\Livewire\Frontend\Category\CategoryProduct::class, ['category' => 'fashion2']);
        $names = $t->get('products')->pluck('name')->all();
        $this->assertContains('Cheap Alpha', $names);

        $t->call('toggleBrand', $b2->id);
        $names = $t->get('products')->pluck('name')->all();
        $this->assertNotContains('Cheap Alpha', $names);
        $this->assertContains('Pricy Beta', $names);

        // Brand list shows counts
        $brands = $t->get('filterBrands');
        $this->assertEquals(1, $brands->firstWhere('brand.id', $b1->id)['count']);

        // Sort high→low
        $t->call('toggleBrand', $b2->id); // off again
        $t->call('setSort', 'price_desc');
        $names = $t->get('products')->pluck('name')->all();
        $this->assertSame(['Pricy Beta', 'Cheap Alpha'], $names);

        // Custom price range
        $t->set('minPrice', 4000)->set('maxPrice', 5000)->call('applyCustomPrice');
        $names = $t->get('products')->pluck('name')->all();
        $this->assertSame(['Pricy Beta'], $names);

        // Clear all
        $t->call('clearAllFilters');
        $this->assertCount(2, $t->get('products'));
    }

    public function test_subcategory_pills_and_price_slider_render(): void
    {
        $cat = \App\Models\Category::create(['name' => 'Fashion', 'slug' => 'fashion3', 'status' => 1]);
        \App\Models\Category::create(['name' => 'Gents', 'slug' => 'gents', 'status' => 1, 'parent_id' => $cat->id]);

        $html = $this->get(route('frontend.category.products.view', 'fashion3'))->assertOk()->getContent();

        $this->assertStringContainsString('More in Fashion', $html);
        $this->assertStringContainsString('Gents', $html);
        $this->assertStringContainsString('flt-range', $html);
        $this->assertStringContainsString('Price range', $html);
    }

    public function test_category_index_uses_title_strip_and_icon_cards(): void
    {
        \App\Models\Category::create(['name' => 'Fashion', 'slug' => 'fashion4', 'status' => 1]);

        $html = $this->get(route('frontend.category.view'))->assertOk()->getContent();

        $this->assertStringContainsString('catview-titlebar', $html);
        $this->assertStringContainsString('All Categories', $html);
        $this->assertStringContainsString('catview-card', $html);
        $this->assertStringContainsString('catview-ic', $html);
        // No sidebar filter panel markup on this page
        $this->assertStringNotContainsString('<aside', $html);
    }
}
