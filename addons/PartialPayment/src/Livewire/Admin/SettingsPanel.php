<?php

namespace Addons\PartialPayment\Livewire\Admin;

use App\Models\Settings;
use Livewire\Component;

class SettingsPanel extends Component
{
    public string $pp_base = 'total';

    public int $pp_expiry_hours = 24;

    public string $pp_bkash_number = '';

    public string $pp_nagad_number = '';

    public string $pp_rocket_number = '';

    public string $pp_instructions = '';

    public function mount(): void
    {
        $this->pp_base = (string) setting('pp_base', 'total');
        $this->pp_expiry_hours = (int) setting('pp_expiry_hours', 24);
        $this->pp_bkash_number = (string) setting('pp_bkash_number', '');
        $this->pp_nagad_number = (string) setting('pp_nagad_number', '');
        $this->pp_rocket_number = (string) setting('pp_rocket_number', '');
        $this->pp_instructions = (string) setting('pp_instructions', '');
    }

    public function save(): void
    {
        $this->validate([
            'pp_base' => 'required|in:total,subtotal',
            'pp_expiry_hours' => 'required|integer|min:0|max:720',
            'pp_bkash_number' => 'nullable|string|max:20',
            'pp_nagad_number' => 'nullable|string|max:20',
            'pp_rocket_number' => 'nullable|string|max:20',
            'pp_instructions' => 'nullable|string|max:1000',
        ]);

        Settings::set('pp_base', $this->pp_base);
        Settings::set('pp_expiry_hours', (string) $this->pp_expiry_hours);
        Settings::set('pp_bkash_number', trim($this->pp_bkash_number));
        Settings::set('pp_nagad_number', trim($this->pp_nagad_number));
        Settings::set('pp_rocket_number', trim($this->pp_rocket_number));
        Settings::set('pp_instructions', trim($this->pp_instructions));

        $this->dispatch('success', message: 'Partial Payment settings saved!');
    }

    public function render()
    {
        return view('partial-payment::livewire.settings-panel');
    }
}
