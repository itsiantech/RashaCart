<?php

namespace App\Livewire\Admin\Automation;

use App\Models\AutomationAuditLog;
use App\Models\Settings;
use App\Services\AI\AiService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class AutomationAiSettings extends Component
{
    public string $provider = 'claude';

    public string $apiKey = ''; // new key input (never filled from DB)

    public string $apiKeyMasked = '';

    public string $model = '';

    public string $smallModel = '';

    public $dailyBudget = 500;

    public $monthlyBudget = 5000;

    public $timeout = 30;

    public function mount(): void
    {
        $this->provider = setting('automation_provider', 'claude');
        $this->model = setting('automation_model', 'claude-haiku-4-5');
        $this->smallModel = setting('automation_small_model', 'claude-haiku-4-5');
        $this->dailyBudget = setting('automation_daily_budget', 500);
        $this->monthlyBudget = setting('automation_monthly_budget', 5000);
        $this->timeout = setting('automation_request_timeout', 30);
        $this->apiKeyMasked = setting('automation_api_key') ? '••••••••'.substr($this->decryptedKey() ?? '', -4) : '';
    }

    protected function decryptedKey(): ?string
    {
        $enc = setting('automation_api_key');
        if (! $enc) {
            return null;
        }
        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function save(): void
    {
        $this->validate([
            'provider' => 'required|in:claude,openai,other',
            'model' => 'required|string|max:100',
            'smallModel' => 'required|string|max:100',
            'dailyBudget' => 'required|numeric|min:0',
            'monthlyBudget' => 'required|numeric|min:0',
            'timeout' => 'required|integer|min:5|max:120',
            'apiKey' => 'nullable|string|min:8|max:500',
        ]);

        $old = ['provider' => setting('automation_provider')];

        Settings::updateOrCreate(['key' => 'automation_provider'], ['value' => $this->provider]);
        Settings::updateOrCreate(['key' => 'automation_model'], ['value' => $this->model]);
        Settings::updateOrCreate(['key' => 'automation_small_model'], ['value' => $this->smallModel]);
        Settings::updateOrCreate(['key' => 'automation_daily_budget'], ['value' => $this->dailyBudget]);
        Settings::updateOrCreate(['key' => 'automation_monthly_budget'], ['value' => $this->monthlyBudget]);
        Settings::updateOrCreate(['key' => 'automation_request_timeout'], ['value' => $this->timeout]);

        // Key stored encrypted, never logged
        if ($this->apiKey !== '') {
            Settings::updateOrCreate(['key' => 'automation_api_key'], ['value' => Crypt::encryptString($this->apiKey)]);
            $this->apiKey = '';
        }

        Settings::clearCache();
        AutomationAuditLog::record('__ai__', 'settings_changed', $old, ['provider' => $this->provider]);
        $this->mount();

        $this->dispatch('success', message: 'AI Settings save হয়েছে।');
    }

    public function testConnection(): void
    {
        $key = 'ai-test:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->dispatch('error', message: 'বেশি চেষ্টা হয়েছে — ১ মিনিট পরে আবার।');

            return;
        }
        RateLimiter::hit($key, 60);

        $result = AiService::provider()->testConnection();
        $this->dispatch($result['success'] ? 'success' : 'error', message: $result['message']);
    }

    public function render()
    {
        return view('livewire.admin.automation.automation-ai-settings')
            ->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'AI Settings']);
    }
}
