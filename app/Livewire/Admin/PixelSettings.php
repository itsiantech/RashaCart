<?php

namespace App\Livewire\Admin;

use App\Models\PixelSetting;
use Livewire\Component;

class PixelSettings extends Component
{
    public array $platforms = [
        'facebook' => ['pixel_id' => '', 'access_token' => '', 'test_event_code' => '', 'is_active' => false],
        'tiktok'   => ['pixel_id' => '', 'access_token' => '', 'is_active' => false],
        'google'   => ['pixel_id' => '', 'is_active' => false],
    ];

    public function mount()
    {
        foreach (PixelSetting::all() as $setting) {
            if (isset($this->platforms[$setting->platform])) {
                $this->platforms[$setting->platform] = array_merge(
                    $this->platforms[$setting->platform],
                    $setting->only(array_keys($this->platforms[$setting->platform]))
                );
            }
        }
    }

    public function save()
    {
        foreach ($this->platforms as $platform => $data) {
            PixelSetting::updateOrCreate(['platform' => $platform], $data);
        }

        $this->dispatch('success', message: 'Pixel settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.pixel-settings')
            ->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'Pixel Settings']);
    }
}
