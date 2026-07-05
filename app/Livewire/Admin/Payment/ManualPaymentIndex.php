<?php

namespace App\Livewire\Admin\Payment;

use App\Models\ManualPaymentSetting;
use Livewire\Component;

class ManualPaymentIndex extends Component
{
    // bKash
    public bool $bkash_enabled = false;
    public string $bkash_number = '';
    public string $bkash_account_type = 'personal';

    // Nagad
    public bool $nagad_enabled = false;
    public string $nagad_number = '';
    public string $nagad_account_type = 'personal';

    // Rocket
    public bool $rocket_enabled = false;
    public string $rocket_number = '';

    // Bank Account
    public bool $bank_enabled = false;
    public string $bank_name = '';
    public string $bank_account_name = '';
    public string $bank_account_number = '';
    public string $bank_routing_number = '';
    public string $bank_branch = '';

    // UI
    public string $activeTab = 'bkash';
    public ?string $successMessage = null;

    protected function rules(): array
    {
        return [
            'bkash_number'        => $this->bkash_enabled ? 'required|regex:/^01[3-9]\d{8}$/' : 'nullable',
            'bkash_account_type'  => 'required|in:personal,merchant',

            'nagad_number'        => $this->nagad_enabled ? 'required|regex:/^01[3-9]\d{8}$/' : 'nullable',
            'nagad_account_type'  => 'required|in:personal,merchant',

            'rocket_number'       => $this->rocket_enabled ? 'required|regex:/^01[3-9]\d{8}$/' : 'nullable',

            'bank_name'           => $this->bank_enabled ? 'required|string|max:100' : 'nullable',
            'bank_account_name'   => $this->bank_enabled ? 'required|string|max:100' : 'nullable',
            'bank_account_number' => $this->bank_enabled ? 'required|string|max:30'  : 'nullable',
            'bank_routing_number' => 'nullable|string|max:20',
            'bank_branch'         => 'nullable|string|max:100',
        ];
    }

    protected array $messages = [
        'bkash_number.regex'           => 'Enter a valid Bangladeshi mobile number.',
        'nagad_number.regex'           => 'Enter a valid Bangladeshi mobile number.',
        'rocket_number.regex'          => 'Enter a valid Bangladeshi mobile number.',
        'bank_name.required'           => 'Bank name is required.',
        'bank_account_name.required'   => 'Account holder name is required.',
        'bank_account_number.required' => 'Account number is required.',
    ];

    public function mount(): void
    {
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $s = ManualPaymentSetting::instance();

        $this->bkash_enabled      = (bool) ($s->bkash_enabled ?? false);
        $this->bkash_number       = $s->bkash_number ?? '';
        $this->bkash_account_type = $s->bkash_account_type ?? 'personal';

        $this->nagad_enabled      = (bool) ($s->nagad_enabled ?? false);
        $this->nagad_number       = $s->nagad_number ?? '';
        $this->nagad_account_type = $s->nagad_account_type ?? 'personal';

        $this->rocket_enabled = (bool) ($s->rocket_enabled ?? false);
        $this->rocket_number  = $s->rocket_number ?? '';

        $this->bank_enabled        = (bool) ($s->bank_enabled ?? false);
        $this->bank_name           = $s->bank_name ?? '';
        $this->bank_account_name   = $s->bank_account_name ?? '';
        $this->bank_account_number = $s->bank_account_number ?? '';
        $this->bank_routing_number = $s->bank_routing_number ?? '';
        $this->bank_branch         = $s->bank_branch ?? '';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        ManualPaymentSetting::instance()->update([
            'bkash_enabled'      => $this->bkash_enabled,
            'bkash_number'       => $this->bkash_number,
            'bkash_account_type' => $this->bkash_account_type,

            'nagad_enabled'      => $this->nagad_enabled,
            'nagad_number'       => $this->nagad_number,
            'nagad_account_type' => $this->nagad_account_type,

            'rocket_enabled' => $this->rocket_enabled,
            'rocket_number'  => $this->rocket_number,

            'bank_enabled'         => $this->bank_enabled,
            'bank_name'            => $this->bank_name,
            'bank_account_name'    => $this->bank_account_name,
            'bank_account_number'  => $this->bank_account_number,
            'bank_routing_number'  => $this->bank_routing_number,
            'bank_branch'          => $this->bank_branch,
        ]);

        $this->successMessage = 'Manual payment settings saved successfully.';
        $this->dispatch('settings-saved');
    }

    public function render()
    {
        return view('livewire.admin.payment.manual-payment-index')
            ->layout('livewire.admin.layouts.app');
    }
}
