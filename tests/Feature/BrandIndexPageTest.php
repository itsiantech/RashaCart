<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrandIndexPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_brands_page_matches_mock(): void
    {
        \App\Models\Brand::create(['name' => 'Alpha', 'slug' => 'alpha', 'is_active' => 1]);
        \App\Models\Brand::create(['name' => 'Beta', 'slug' => 'beta', 'is_active' => 0]);
        \App\Models\Brand::create(['name' => 'Gamma', 'slug' => 'gamma', 'is_active' => 1]);

        $html = $this->get(route('frontend.brand.view'))->assertOk()->getContent();

        // Unified homepage chrome
        $this->assertStringContainsString('navbar-main', $html);
        $this->assertStringContainsString('hp-footer', $html);
        // Mock elements
        $this->assertStringContainsString('All Brands', $html);
        $this->assertStringContainsString('Search your brands', $html);
        $this->assertStringContainsString('brand-index-card', $html);
        // Active only + counts
        $this->assertStringContainsString('Alpha', $html);
        $this->assertStringContainsString('Gamma', $html);
        $this->assertStringNotContainsString('>Beta<', $html);
        $this->assertStringContainsString('products', $html);
    }

    public function test_brand_search_and_load_more(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            \App\Models\Brand::create(['name' => "Brand $i", 'slug' => "brand-$i", 'is_active' => 1]);
        }

        $t = Livewire::test(\App\Livewire\Frontend\Brand\BrandView::class);
        $this->assertCount(18, $t->get('brands'));
        $this->assertEquals(20, $t->get('totalCount'));

        $t->call('loadMore');
        $this->assertCount(20, $t->get('brands'));

        $t->set('search', 'Brand 1');
        $names = $t->get('brands')->pluck('name')->all();
        $this->assertNotEmpty($names);
        foreach ($names as $n) {
            $this->assertStringContainsString('Brand 1', $n);
        }
    }
}
