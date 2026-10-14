<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Settings;
use App\Services\SeoService;
use Livewire\Component;

class SeoSettings extends Component
{
    public const TABS = ['console', 'ga4', 'gtm', 'sitemap', 'robots', 'schema'];

    public string $tab = 'console';

    // Search Console
    public $gsc_code = '';

    public $gsc_property = '';

    // GA4 (writes the shared tracking row — single source, no duplicate tags)
    public $ga4_id = '';

    // GTM
    public $gtm_id = '';

    public $gtm_active = false;

    // Sitemap
    public $sitemap_products = true;

    public $sitemap_categories = true;

    // Robots
    public $robots_content = '';

    // Schema
    public $schema_org = true;

    public $schema_website = true;

    public $schema_product = true;

    public $schema_breadcrumb = true;

    public function mount(?string $tab = null): void
    {
        if ($tab && in_array($tab, self::TABS, true)) {
            $this->tab = $tab;
        }

        $this->gsc_code = SeoService::get('gsc_code', '');
        $this->gsc_property = SeoService::get('gsc_property', '');
        $this->ga4_id = SeoService::ga4MeasurementId() ?? '';
        $this->gtm_id = SeoService::get('gtm_id', '');
        $this->gtm_active = (bool) SeoService::get('gtm_active', false);
        $this->sitemap_products = (bool) SeoService::get('sitemap_products', true);
        $this->sitemap_categories = (bool) SeoService::get('sitemap_categories', true);
        $this->robots_content = SeoService::get('robots_content', '');
        $this->schema_org = (bool) SeoService::get('schema_org', true);
        $this->schema_website = (bool) SeoService::get('schema_website', true);
        $this->schema_product = (bool) SeoService::get('schema_product', true);
        $this->schema_breadcrumb = (bool) SeoService::get('schema_breadcrumb', true);
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, self::TABS, true)) {
            $this->tab = $tab;
        }
    }

    public function saveConsole(): void
    {
        $this->validate([
            'gsc_code' => 'nullable|string|max:255',
            'gsc_property' => 'nullable|url|max:255',
        ]);
        SeoService::put('gsc_code', trim($this->gsc_code));
        SeoService::put('gsc_property', trim($this->gsc_property));
        Settings::clearCache();
        $this->dispatch('success', message: 'Search Console settings save হয়েছে।');
    }

    public function saveGa4(): void
    {
        $this->validate(['ga4_id' => 'nullable|regex:/^G-[A-Z0-9]+$/']);
        SeoService::saveGa4MeasurementId($this->ga4_id ?: null);
        Settings::clearCache();
        $this->dispatch('success', message: 'GA4 ID save হয়েছে (tracking module-ও এটাই ব্যবহার করে)।');
    }

    public function saveGtm(): void
    {
        $this->validate([
            'gtm_id' => 'nullable|regex:/^GTM-[A-Z0-9]+$/',
            'gtm_active' => 'boolean',
        ]);
        SeoService::put('gtm_id', trim($this->gtm_id));
        SeoService::put('gtm_active', $this->gtm_active ? 1 : 0);
        Settings::clearCache();
        $this->dispatch('success', message: 'Tag Manager settings save হয়েছে।');
    }

    public function saveSitemap(): void
    {
        SeoService::put('sitemap_products', $this->sitemap_products ? 1 : 0);
        SeoService::put('sitemap_categories', $this->sitemap_categories ? 1 : 0);
        Settings::clearCache();
        SeoService::clearSitemapCache();
        $this->dispatch('success', message: 'Sitemap settings save হয়েছে।');
    }

    public function saveRobots(): void
    {
        $this->validate(['robots_content' => 'nullable|string|max:5000']);
        SeoService::put('robots_content', trim($this->robots_content));
        Settings::clearCache();
        $this->dispatch('success', message: 'Robots.txt save হয়েছে।');
    }

    public function resetRobots(): void
    {
        $this->robots_content = '';
        SeoService::put('robots_content', '');
        Settings::clearCache();
        $this->dispatch('success', message: 'Default robots.txt ফিরিয়ে আনা হয়েছে।');
    }

    public function saveSchema(): void
    {
        foreach (['schema_org', 'schema_website', 'schema_product', 'schema_breadcrumb'] as $k) {
            SeoService::put($k, $this->$k ? 1 : 0);
        }
        Settings::clearCache();
        $this->dispatch('success', message: 'Schema settings save হয়েছে।');
    }

    public function render()
    {
        return view('livewire.admin.settings.seo-settings')
            ->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'Google SEO']);
    }
}
