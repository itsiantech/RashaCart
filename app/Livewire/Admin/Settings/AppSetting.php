<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;
use App\Http\Requests\SettingsRequest;
use Illuminate\Support\Facades\Http;

class AppSetting extends Component
{
    use WithFileUploads;

    public $app_name, $app_description, $items_per_page, $app_logo, $fev_icon, $currency, $google_map_location, $theme;
    public $contact_phone, $contact_email, $contact_address, $business_hours, $support_email;
    public $facebook, $twitter, $linkedin, $instagram, $youtube, $tiktok;
    public $attribute_system, $dc_inside, $dc_outside;
    public $sms_notification, $sms_api_key, $sms_api_url, $sms_sender_id, $email_notification, $otp_sms_notification, $otp_email_notification;
    public $steedfast_api_base_url, $steedfast_api_key, $steedfast_api_secret, $selected_courier;

    public $GOOGLE_CLIENT_ID, $GOOGLE_CLIENT_SECRET;
    
    public $balance; //bulksmsbd balance

    public function mount()
    {
        $this->app_name = setting('app_name');
        $this->app_description = setting('app_description');
        $this->items_per_page = setting('items_per_page');
        $this->currency = setting('currency'); 
        $this->google_map_location = setting('google_map_location');
        $this->theme = setting('theme');

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
        $this->sms_api_key =  setting('sms_api_key');
        $this->sms_api_url =  setting('sms_api_url');
        $this->sms_sender_id = setting('sms_sender_id');

        $this->email_notification = (bool) setting('email_notification');
        $this->otp_sms_notification = (bool) setting('otp_sms_notification');
        $this->otp_email_notification = (bool) setting('otp_email_notification');


        $this->steedfast_api_base_url = setting('steedfast_api_base_url');
        $this->steedfast_api_key = setting('steedfast_api_key');
        $this->steedfast_api_secret = setting('steedfast_api_secret');
        $this->selected_courier = setting('selected_courier');
    
        $this->GOOGLE_CLIENT_ID = setting('GOOGLE_CLIENT_ID');
        $this->GOOGLE_CLIENT_SECRET = setting('GOOGLE_CLIENT_SECRET');

        $this->fetchApiBalance();

    }

    // bulk sms bd balence
    public function fetchApiBalance()
    {
        $apiKey = setting('sms_api_key');
        $url = "http://bulksmsbd.net/api/getBalanceApi?api_key={$apiKey}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $this->balance = $data['balance'] ?? 'N/A';
            } else {
               $this->balance = 'Failed to fetch balance.';
            }
        } catch (\Exception $e) {
                $this->balance  = 'API Error: ' . $e->getMessage();
        }
    }

    public function update()
    {
        $validatedData = $this->validate((new SettingsRequest())->rules());

       
        if ($this->app_logo) {
            $oldLogo = setting('app_logo');
            if ($oldLogo && Storage::disk('public')->exists("logo/{$oldLogo}")) {
                Storage::disk('public')->delete("logo/{$oldLogo}");
            }

            $filename = time() . '_' . $this->app_logo->getClientOriginalName();
            $this->app_logo->storeAs('logo', $filename, 'public');
            $validatedData['app_logo'] = $filename;
        } else {
            
            unset($validatedData['app_logo']);
        }


        if ($this->fev_icon) {
            $oldFev = setting('fev_icon');
            if ($oldFev && Storage::disk('public')->exists("logo/{$oldFev}")) {
                Storage::disk('public')->delete("logo/{$oldFev}");
            }

            $filename = time() . '_' . $this->fev_icon->getClientOriginalName();
            $this->fev_icon->storeAs('logo', $filename, 'public');
            $validatedData['fev_icon'] = $filename;
        } else {
            
            unset($validatedData['fev_icon']);
        }

       
        foreach ($validatedData as $key => $value) {
            Settings::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Settings::clearCache();

       
        $this->mount();        
        $this->app_logo = null;    
        $this->fev_icon = null; 
        $this->dispatch('success', message: 'Settings Updated!');
       
        
    }

    public function resetForm()
    {
        $this->mount();
        $this->app_logo = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.settings.app-setting')
            ->layout('livewire.admin.layouts.app');
    }
}
