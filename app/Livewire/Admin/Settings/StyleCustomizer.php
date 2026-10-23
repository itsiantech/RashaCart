<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Settings;
use App\Services\StyleSettings;
use Livewire\Component;

class StyleCustomizer extends Component
{
    public array $form = [];

    public function mount(): void
    {
        foreach (StyleSettings::FIELDS as $key => $meta) {
            $this->form[$key] = StyleSettings::get($key);
        }
    }

    public function save(): void
    {
        $rules = [];
        foreach (StyleSettings::FIELDS as $key => $meta) {
            if ($meta['type'] === 'color') {
                $rules['form.'.$key] = ! empty($meta['optional'])
                    ? 'nullable|regex:/^#[0-9A-Fa-f]{6}$/'
                    : 'required|regex:/^#[0-9A-Fa-f]{6}$/';
            } else {
                $rules['form.'.$key] = 'nullable|string|max:50';
            }
        }
        $this->validate($rules);

        if (! isset(StyleSettings::FONTS[$this->form['body_font'] ?? ''])) {
            $this->form['body_font'] = 'segoe';
        }
        if (($this->form['flash_font'] ?? '') !== '' && ! isset(StyleSettings::FONTS[$this->form['flash_font']])) {
            $this->form['flash_font'] = '';
        }

        foreach (StyleSettings::FIELDS as $key => $meta) {
            StyleSettings::put($key, $this->form[$key] ?? $meta['default']);
        }
        Settings::clearCache();

        $this->dispatch('success', message: 'Style save হয়েছে — পুরো site-এ লেগে গেছে।');
    }

    public function resetDefaults(): void
    {
        foreach (StyleSettings::FIELDS as $key => $meta) {
            StyleSettings::put($key, $meta['default']);
            $this->form[$key] = $meta['default'];
        }
        Settings::clearCache();

        $this->dispatch('success', message: 'Default design ফিরিয়ে আনা হয়েছে।');
    }

    public function render()
    {
        return view('livewire.admin.settings.style-customizer', [
            'fields' => StyleSettings::FIELDS,
            'fonts' => StyleSettings::FONTS,
        ])->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'Style Settings']);
    }
}
