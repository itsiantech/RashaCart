<?php

namespace App\Livewire\Admin\Settings;

use App\Http\Requests\SettingsRequest;
use App\Models\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppSetting extends Component
{
    use WithFileUploads;

    public $app_name;

    public $app_description;

    public $footer_copyright;

    public $items_per_page;

    public $app_logo;

    public $fev_icon;

    public $current_app_logo;

    public $current_fev_icon;

    public $currency;

    public $google_map_location;

    public $theme;

    public $contact_phone;

    public $contact_email;

    public $contact_address;

    public $business_hours;

    public $support_email;

    public $facebook;

    public $twitter;

    public $linkedin;

    public $instagram;

    public $youtube;

    public $tiktok;

    public $attribute_system;

    public $automation_menu;

    public $seller_menu;

    public $seo_menu;

    public $dc_inside;

    public $dc_outside;

    // ── City-wise delivery charges (dynamic rows: [['city' => ..., 'charge' => ...]]) ──
    public $delivery_cities = [];

    public $sms_notification;

    public $sms_api_key;

    public $sms_api_url;

    public $sms_sender_id;

    public $email_notification;

    public $otp_sms_notification;

    public $otp_email_notification;

    public $steedfast_api_base_url;

    public $steedfast_api_key;

    public $steedfast_api_secret;

    public $selected_courier;

    public $pathao_client_id;

    public $pathao_client_secret;

    public $pathao_username;

    public $pathao_password;

    public $pathao_store_id;

    public $GOOGLE_CLIENT_ID;

    public $GOOGLE_CLIENT_SECRET;

    // ── Customer Login/Register (new section, additive only) ──
    public $customer_login_enabled = true;

    public $customer_login_identifier = 'both';

    public $customer_google_enabled = true;

    // ── Purchase actions (Add to Cart / Buy Now) ──
    public $add_to_cart_enabled = true;

    public $buy_now_enabled = true;

    public $cart_drawer_enabled = true;

    // ── Home page category menu ──
    public $home_category_sidebar = true;

    public $add_to_cart_label;

    public $buy_now_label;

    public $purchase_disabled_text;

    public $balance;

    public $activeTab = 'basic';

    // ─── Called once by Livewire on page load only ───────────────────────────
    public function mount(): void
    {
        $this->loadSettings();

        if ($this->balance === null) {
            $this->fetchApiBalance();
        }
    }

    // ─── Shared settings loader (does NOT touch $balance or $activeTab) ──────
    private function loadSettings(): void
    {
        $this->app_name = setting('app_name');
        $this->app_description = setting('app_description');
        $this->footer_copyright = setting('footer_copyright')
            ?: '© ' . date('Y') . ' ' . (setting('app_name') ?: 'Padmazon') . '. All rights reserved.';
        $this->items_per_page = setting('items_per_page');
        $this->currency = setting('currency');
        $this->google_map_location = setting('google_map_location');
        $this->theme = setting('theme');

        // ✅ Fresh, direct DB read for logos — bypasses any setting()/cache staleness
        $this->current_app_logo = Settings::where('key', 'app_logo')->value('value');
        $this->current_fev_icon = Settings::where('key', 'fev_icon')->value('value');

        $this->dc_inside = setting('dc_inside');
        $this->dc_outside = setting('dc_outside');
        $this->delivery_cities = $this->storedDeliveryCities();

        $this->contact_phone = setting('contact_phone');
        $this->contact_email = setting('contact_email');
        $this->contact_address = setting('contact_address');
        $this->business_hours = setting('business_hours');
        $this->support_email = setting('support_email');

        $this->facebook = setting('facebook');
        $this->twitter = setting('twitter');
        $this->linkedin = setting('linkedin');
        $this->instagram = setting('instagram');
        $this->youtube = setting('youtube');
        $this->tiktok = setting('tiktok');

        $this->attribute_system = (bool) setting('attribute_system');
        $this->automation_menu = (bool) setting('automation_menu', true);
        $this->seller_menu = (bool) setting('seller_menu', true);
        $this->seo_menu = (bool) setting('seo_menu', true);
        $this->sms_notification = (bool) setting('sms_notification');
        $this->sms_api_key = setting('sms_api_key');
        $this->sms_api_url = setting('sms_api_url');
        $this->sms_sender_id = setting('sms_sender_id');
        $this->email_notification = (bool) setting('email_notification');
        $this->otp_sms_notification = (bool) setting('otp_sms_notification');
        $this->otp_email_notification = (bool) setting('otp_email_notification');

        $this->steedfast_api_base_url = setting('steedfast_api_base_url');
        $this->steedfast_api_key = setting('steedfast_api_key');
        $this->steedfast_api_secret = setting('steedfast_api_secret');
        $this->selected_courier = setting('selected_courier');

        $this->pathao_client_id = setting('pathao_client_id');
        $this->pathao_client_secret = setting('pathao_client_secret');
        $this->pathao_username = setting('pathao_username');
        $this->pathao_password = setting('pathao_password');
        $this->pathao_store_id = setting('pathao_store_id');

        $this->GOOGLE_CLIENT_ID = setting('GOOGLE_CLIENT_ID');
        $this->GOOGLE_CLIENT_SECRET = setting('GOOGLE_CLIENT_SECRET');

        // Customer Login/Register (defaults: enabled / both / google on)
        $this->customer_login_enabled = (string) setting('customer_login_enabled', '1') === '1';
        $this->customer_login_identifier = setting('customer_login_identifier', 'both');
        $this->customer_google_enabled = (string) setting('customer_google_enabled', '1') === '1';

        // Purchase actions (defaults: both enabled)
        $this->add_to_cart_enabled = (string) setting('add_to_cart_enabled', '1') === '1';
        $this->buy_now_enabled = (string) setting('buy_now_enabled', '1') === '1';
        $this->cart_drawer_enabled = (string) setting('cart_drawer_enabled', '1') === '1';
        $this->home_category_sidebar = (string) setting('home_category_sidebar', '1') === '1';
        $this->add_to_cart_label = setting('add_to_cart_label') ?: \App\Services\PurchaseActions::DEFAULT_ADD_LABEL;
        $this->buy_now_label = setting('buy_now_label') ?: \App\Services\PurchaseActions::DEFAULT_BUY_LABEL;
        $this->purchase_disabled_text = setting('purchase_disabled_text') ?: \App\Services\PurchaseActions::DEFAULT_DISABLED_TEXT;
    }

    // ─── City-wise delivery charges ──────────────────────────────────────────
    private function storedDeliveryCities(): array
    {
        $raw = setting('delivery_cities');

        if ($raw === null || $raw === '' || $raw === false) {
            $stored = [
                ['city' => 'Inside City', 'charge' => setting('dc_inside', 60)],
                ['city' => 'Outside City', 'charge' => setting('dc_outside', 120)],
            ];
        } else {
            $stored = json_decode((string) $raw, true);
            $stored = is_array($stored) ? $stored : [];
        }

        return collect($stored)
            ->filter(fn ($row) => is_array($row))
            ->map(fn ($row) => [
                'city' => (string) ($row['city'] ?? ''),
                'charge' => (string) ($row['charge'] ?? ''),
            ])
            ->values()
            ->all();
    }

    public function addDeliveryCity(): void
    {
        $this->delivery_cities[] = ['city' => '', 'charge' => ''];
    }

    public function removeDeliveryCity(int $index): void
    {
        unset($this->delivery_cities[$index]);
        $this->delivery_cities = array_values($this->delivery_cities);
    }

    private function normalizedDeliveryCities(): array
    {
        return collect($this->delivery_cities)
            ->map(fn ($row) => [
                'city' => trim((string) ($row['city'] ?? '')),
                'charge' => $row['charge'] ?? '',
            ])
            ->filter(fn ($row) => $row['city'] !== '' && is_numeric($row['charge']))
            ->map(fn ($row) => ['city' => $row['city'], 'charge' => round((float) $row['charge'], 2)])
            ->values()
            ->all();
    }

    // ─── Fetch BulkSMSBD balance ─────────────────────────────────────────────
    public function fetchApiBalance(): void
    {
        $apiKey = setting('sms_api_key');
        $url = "http://bulksmsbd.net/api/getBalanceApi?api_key={$apiKey}";

        try {
            $response = Http::get($url);
            $this->balance = $response->successful()
                ? ($response->json()['balance'] ?? 'N/A')
                : 'Failed to fetch balance.';
        } catch (\Exception $e) {
            $this->balance = 'API Error: ' . $e->getMessage();
        }
    }

    public function updatedSelectedCourier($value): void
    {
        Settings::updateOrCreate(
            ['key' => 'selected_courier'],
            ['value' => $value]
        );
        Settings::clearCache();
    }

    public function updatedAttributeSystem($value): void
    {
        Settings::updateOrCreate(
            ['key' => 'attribute_system'],
            ['value' => $value ? 1 : 0]
        );
        Settings::clearCache();
        $this->dispatch('success', message: $value ? 'Attribute system turned ON.' : 'Attribute system turned OFF.');
    }

    public function updatedAutomationMenu($value): void
    {
        Settings::updateOrCreate(
            ['key' => 'automation_menu'],
            ['value' => $value ? 1 : 0]
        );
        Settings::clearCache();
        $this->dispatch('success', message: $value ? 'Automation menu চালু হয়েছে।' : 'Automation menu বন্ধ হয়েছে।');
    }

    public function updatedSellerMenu($value): void
    {
        Settings::updateOrCreate(
            ['key' => 'seller_menu'],
            ['value' => $value ? 1 : 0]
        );
        Settings::clearCache();
        $this->dispatch('success', message: $value ? 'Seller Manage menu চালু হয়েছে।' : 'Seller Manage menu বন্ধ হয়েছে।');
    }

    public function updatedSeoMenu($value): void
    {
        Settings::updateOrCreate(
            ['key' => 'seo_menu'],
            ['value' => $value ? 1 : 0]
        );
        Settings::clearCache();
        $this->dispatch('success', message: $value ? 'Google SEO menu চালু হয়েছে।' : 'Google SEO menu বন্ধ হয়েছে।');
    }

    // ── Customer Login/Register instant toggles (saved immediately) ──
    public function updatedCustomerLoginEnabled($value): void
    {
        Settings::updateOrCreate(['key' => 'customer_login_enabled'], ['value' => $value ? 1 : 0]);
        Settings::clearCache();
        $this->dispatch('success', message: $value ? 'Customer login/register enabled.' : 'Customer login/register disabled — header icon hidden, guest checkout only.');
    }

    public function updatedCustomerLoginIdentifier($value): void
    {
        if (! in_array($value, ['email', 'phone', 'both'], true)) {
            return;
        }
        Settings::updateOrCreate(['key' => 'customer_login_identifier'], ['value' => $value]);
        Settings::clearCache();
        $this->dispatch('success', message: 'Login identifier updated.');
    }

    public function updatedCustomerGoogleEnabled($value): void
    {
        Settings::updateOrCreate(['key' => 'customer_google_enabled'], ['value' => $value ? 1 : 0]);
        Settings::clearCache();
        $this->dispatch('success', message: $value ? 'Google sign-in enabled.' : 'Google sign-in disabled.');
    }

    public function update(): void
    {
        $validatedData = $this->validate((new SettingsRequest)->rules());

        // Persist explicit 1/0 so unchecked toggles reliably disable the action.
        $validatedData['add_to_cart_enabled'] = $this->add_to_cart_enabled ? 1 : 0;
        $validatedData['buy_now_enabled'] = $this->buy_now_enabled ? 1 : 0;
        $validatedData['cart_drawer_enabled'] = $this->cart_drawer_enabled ? 1 : 0;
        $validatedData['home_category_sidebar'] = $this->home_category_sidebar ? 1 : 0;

        // City-wise delivery charges — first two rows also feed the checkout's
        // legacy inside/outside charges so existing order flow keeps working.
        $cities = $this->normalizedDeliveryCities();

        $validatedData['delivery_cities'] = json_encode($cities);

        if (isset($cities[0])) {
            $validatedData['dc_inside'] = $cities[0]['charge'];
        }

        if (isset($cities[1])) {
            $validatedData['dc_outside'] = $cities[1]['charge'];
        }

        if ($this->app_logo) {
            $oldLogo = setting('app_logo');
            if ($oldLogo && Storage::disk('public')->exists("logo/{$oldLogo}")) {
                Storage::disk('public')->delete("logo/{$oldLogo}");
            }
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]+/', '-', $this->app_logo->getClientOriginalName());
            $this->app_logo->storeAs('logo', $filename, 'public');
            $validatedData['app_logo'] = $filename;

            $this->current_app_logo = $filename;
        } else {
            unset($validatedData['app_logo']);
        }

        if ($this->fev_icon) {
            $oldFev = setting('fev_icon');
            if ($oldFev && Storage::disk('public')->exists("logo/{$oldFev}")) {
                Storage::disk('public')->delete("logo/{$oldFev}");
            }
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]+/', '-', $this->fev_icon->getClientOriginalName());
            $this->fev_icon->storeAs('logo', $filename, 'public');
            $validatedData['fev_icon'] = $filename;

            $this->current_fev_icon = $filename;
        } else {
            unset($validatedData['fev_icon']);
        }

        foreach ($validatedData as $key => $value) {
            Settings::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Settings::clearCache();

        $this->loadSettings();

        $this->app_logo = null;
        $this->fev_icon = null;
        $this->dispatch('success', message: 'Settings Updated!');
    }

    public function resetForm(): void
    {
        $this->loadSettings();
        $this->app_logo = null;
        $this->resetErrorBag();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.settings.app-setting')
            ->layout('livewire.admin.layouts.app');
    }
}
