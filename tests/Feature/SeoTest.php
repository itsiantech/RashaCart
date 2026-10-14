<?php

namespace Tests\Feature;

use App\Livewire\Admin\Settings\SeoSettings;
use App\Models\Settings;
use App\Models\User;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'setting.view', 'guard_name' => 'web']);
        $u = User::factory()->create();
        $u->assignRole('admin');

        return $u;
    }

    public function test_menu_hidden_when_business_setting_off(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('Google SEO', false);

        Settings::updateOrCreate(['key' => 'seo_menu'], ['value' => 0]);
        Settings::clearCache();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertDontSee('Google SEO', false);
    }

    public function test_seo_settings_save_and_tabs_render(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.seo.index', ['tab' => 'gtm']))->assertOk();

        Livewire::actingAs($admin)->test(SeoSettings::class)
            ->set('gsc_code', 'abc123')
            ->call('saveConsole')
            ->assertDispatched('success');

        $this->assertSame('abc123', setting('seo_gsc_code'));

        Livewire::actingAs($admin)->test(SeoSettings::class)
            ->set('gtm_id', 'GTM-ABC123')
            ->set('gtm_active', true)
            ->call('saveGtm')
            ->assertDispatched('success');

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('GTM-ABC123', $html);
        $this->assertStringContainsString('googletagmanager.com/ns.html', $html);
    }

    public function test_ga4_id_syncs_to_shared_tracking_row(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(SeoSettings::class)
            ->set('ga4_id', 'G-TEST12345')
            ->call('saveGa4')
            ->assertDispatched('success');

        // Single source: tracking module reads the same row (no duplicate tags)
        $this->assertSame('G-TEST12345', SeoService::ga4MeasurementId());
        $this->assertSame(
            'G-TEST12345',
            \App\Models\TrackingPlatform::where('platform', 'ga4')->value('pixel_id')
        );

        // Clearing disables
        Livewire::actingAs($admin)->test(SeoSettings::class)->set('ga4_id', '')->call('saveGa4');
        $this->assertNull(SeoService::ga4MeasurementId());
    }

    public function test_sitemap_and_robots_are_dynamic(): void
    {
        $p = \App\Models\Product::factory()->create(['status' => true, 'is_featured' => true]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<urlset', $xml);
        $this->assertStringContainsString($p->slug, $xml);

        $this->assertStringContainsString('Sitemap:', $this->get('/robots.txt')->assertOk()->getContent());

        Settings::updateOrCreate(['key' => 'seo_robots_content'], ['value' => "User-agent: *\nDisallow: /admin"]);
        Settings::clearCache();
        $this->assertStringContainsString('Disallow: /admin', $this->get('/robots.txt')->getContent());
    }

    public function test_schema_markup_builders(): void
    {
        $org = SeoService::orgSchema();
        $this->assertSame('Organization', $org['@type']);

        $p = \App\Models\Product::factory()->create(['status' => true, 'sale_price' => 100, 'price' => 120]);
        $schema = SeoService::productSchema($p);
        $this->assertSame('Product', $schema['@type']);
        $this->assertSame('100.00', $schema['offers']['price']);
        $this->assertSame('BDT', $schema['offers']['priceCurrency']);

        $crumbs = SeoService::breadcrumbSchema([['name' => 'Home', 'url' => url('/')]]);
        $this->assertSame('BreadcrumbList', $crumbs['@type']);
    }
}
