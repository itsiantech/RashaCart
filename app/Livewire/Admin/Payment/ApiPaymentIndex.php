<?php

namespace App\Livewire\Admin\Payment;

use Livewire\Component;

class ApiPaymentIndex extends Component
{
    // SSL Commerz
    public bool $sslcommerz_enabled = false;
    public string $sslcommerz_store_id = '';
    public string $sslcommerz_store_password = '';
    public string $sslcommerz_mode = 'sandbox'; // sandbox | live
    public string $sslcommerz_currency = 'BDT';

    // UI state
    public bool $showPassword = false;
    public ?string $successMessage = null;
    public ?string $testResult = null;
    public bool $testLoading = false;

    protected function rules(): array
    {
        return [
            'sslcommerz_store_id'       => $this->sslcommerz_enabled ? 'required|string|max:100' : 'nullable',
            'sslcommerz_store_password' => $this->sslcommerz_enabled ? 'required|string|max:200' : 'nullable',
            'sslcommerz_mode'           => 'required|in:sandbox,live',
            'sslcommerz_currency'       => 'required|in:BDT,USD',
        ];
    }

    protected array $messages = [
        'sslcommerz_store_id.required'       => 'Store ID is required when SSL Commerz is enabled.',
        'sslcommerz_store_password.required' => 'Store password is required when SSL Commerz is enabled.',
    ];

    public function mount(): void
    {
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        // Example: load from DB / config / settings table
        // $s = ApiPaymentSetting::first();
        // if ($s) { $this->fill($s->toArray()); }
    }

    public function togglePasswordVisibility(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function testConnection(): void
    {
        $this->validate();
        $this->testLoading = true;
        $this->testResult  = null;

        // Example: ping SSL Commerz sandbox/live endpoint
        // $url = $this->sslcommerz_mode === 'sandbox'
        //     ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
        //     : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
        //
        // $response = Http::asForm()->post($url, [
        //     'store_id'   => $this->sslcommerz_store_id,
        //     'store_passwd' => $this->sslcommerz_store_password,
        //     'total_amount' => 10,
        //     'currency'   => $this->sslcommerz_currency,
        //     'tran_id'    => 'TEST_' . time(),
        //     'success_url' => route('payment.success'),
        //     'fail_url'   => route('payment.fail'),
        //     'cancel_url' => route('payment.cancel'),
        // ]);
        //
        // $this->testResult = $response->successful() ? 'success' : 'failed';

        // Simulate for now:
        $this->testResult  = 'success';
        $this->testLoading = false;
    }

    public function save(): void
    {
        $this->validate();

        // Example: persist to DB
        // ApiPaymentSetting::updateOrCreate([], [
        //     'sslcommerz_enabled'         => $this->sslcommerz_enabled,
        //     'sslcommerz_store_id'        => $this->sslcommerz_store_id,
        //     'sslcommerz_store_password'  => encrypt($this->sslcommerz_store_password),
        //     'sslcommerz_mode'            => $this->sslcommerz_mode,
        //     'sslcommerz_currency'        => $this->sslcommerz_currency,
        // ]);

        $this->successMessage = 'API payment settings saved successfully.';
        $this->dispatch('settings-saved');
    }

    public function render()
    {
        return view('livewire.admin.payment.api-payment-index')
            ->layout('livewire.admin.layouts.app');
    }
}
