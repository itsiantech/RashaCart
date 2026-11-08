<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Settings;
use Livewire\Component;

class OrderSettings extends Component
{
    public bool $popup_order_enabled = false;

    public bool $popup_order_whatsapp = true;

    public bool $variant_popup_enabled = true;

    public string $popup_order_title = 'Order Now';

    public string $popup_order_default_zone = 'inside';

    public function mount(): void
    {
        $this->popup_order_enabled = (bool) setting('popup_order_enabled', 0);
        $this->popup_order_whatsapp = (bool) setting('popup_order_whatsapp', 1);
        $this->variant_popup_enabled = (bool) setting('variant_popup_enabled', 1);
        $this->popup_order_title = (string) setting('popup_order_title', 'Order Now');
        $this->popup_order_default_zone = (string) setting('popup_order_default_zone', 'inside');
    }

    public function save(): void
    {
        $this->validate([
            'popup_order_title' => 'required|string|max:60',
            'popup_order_default_zone' => 'required|in:inside,outside',
        ]);

        Settings::set('popup_order_enabled', $this->popup_order_enabled ? '1' : '0');
        Settings::set('popup_order_whatsapp', $this->popup_order_whatsapp ? '1' : '0');
        Settings::set('variant_popup_enabled', $this->variant_popup_enabled ? '1' : '0');
        Settings::set('popup_order_title', trim($this->popup_order_title));
        Settings::set('popup_order_default_zone', $this->popup_order_default_zone);

        $this->dispatch('success', message: 'Order settings updated!');
    }

    public function render()
    {
        return view('livewire.admin.settings.order-settings', [
            'contactPhone' => (string) setting('contact_phone'),
            'dcInside' => (float) setting('dc_inside', 60),
            'dcOutside' => (float) setting('dc_outside', 120),
        ])->layout('livewire.admin.layouts.app');
    }
}
