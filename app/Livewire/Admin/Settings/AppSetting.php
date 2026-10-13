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

    public $dc_inside;

    public $dc_outside;

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
        $this->items_per_page = setting('items_per_page');
        $this->currency = setting('currency');
        $this->google_map_location = setting('google_map_location');
        $this->theme = setting('theme');

        // ✅ Fresh, direct DB read for logos — bypasses any setting()/cache staleness
        $this->current_app_logo = Settings::where('key', 'app_logo')->value('value');
        $this->current_fev_icon = Settings::where('key', 'fev_icon')->value('value');

        $this->dc_inside = setting('dc_inside');
        $this->dc_outside = setting('dc_outside');

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

    public function update(): void
    {
        $validatedData = $this->validate((new SettingsRequest)->rules());

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
