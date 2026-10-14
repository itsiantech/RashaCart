<?php

namespace Database\Seeders;

use App\Models\AutomationModule;
use App\Services\AutomationRegistry;
use Illuminate\Database\Seeder;

class AutomationSeeder extends Seeder
{
    public function run(): void
    {
        // All modules OFF by default. Risky ones (risk_score,
        // order_confirmation_bot) open in Shadow first — enforced in the UI.
        foreach (AutomationRegistry::all() as $key => $module) {
            AutomationModule::updateOrCreate(
                ['key' => $key],
                [
                    'is_enabled' => false,
                    'mode' => 'off',
                    'settings' => $module->defaultSettings(),
                ]
            );
        }

        // Master switch ON (individual modules stay OFF until enabled).
        // firstOrCreate: never override an admin's choice on re-seed.
        \App\Models\Settings::firstOrCreate(['key' => 'automation_master_switch'], ['value' => 1]);
        \App\Models\Settings::firstOrCreate(['key' => 'automation_menu'], ['value' => 1]);
        \App\Models\Settings::firstOrCreate(['key' => 'automation_provider'], ['value' => 'claude']);
        \App\Models\Settings::firstOrCreate(['key' => 'automation_daily_budget'], ['value' => 500]);
        \App\Models\Settings::firstOrCreate(['key' => 'automation_monthly_budget'], ['value' => 5000]);
        \App\Models\Settings::firstOrCreate(['key' => 'automation_request_timeout'], ['value' => 30]);
        \App\Models\Settings::clearCache();
        AutomationRegistry::clearCache();
    }
}
