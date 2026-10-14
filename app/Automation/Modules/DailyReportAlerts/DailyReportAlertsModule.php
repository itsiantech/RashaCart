<?php

namespace App\Automation\Modules\DailyReportAlerts;

use App\Automation\AutomationModule;

class DailyReportAlertsModule implements AutomationModule
{
    public function key(): string { return 'daily_report_alerts'; }
    public function name(): string { return 'Daily AI Report & Anomaly Alert'; }
    public function description(): string { return 'প্রতিদিন সকালে sales summary + অস্বাভাবিক alert দেবে।'; }
    public function icon(): string { return 'bx bx-line-chart'; }
    public function status(): string { return 'coming_soon'; }
    public function defaultSettings(): array { return ['time' => '08:00', 'channel' => 'dashboard']; }
    public function settingsSchema(): array
    {
        return [
            ['name' => 'time', 'label' => 'রিপোর্ট সময়', 'type' => 'text', 'default' => '08:00', 'rules' => 'required|date_format:H:i', 'help' => 'প্রতিদিন কখন রিপোর্ট বানাবে (24h)।'],
            ['name' => 'channel', 'label' => 'চ্যানেল', 'type' => 'select', 'default' => 'dashboard', 'rules' => 'required|in:dashboard,email', 'help' => 'কোথায় পাঠাবে।', 'options' => ['dashboard' => 'Dashboard', 'email' => 'Email']],
        ];
    }
    public function routes(): array { return []; }
    public function permissions(): array { return ['view']; }
    public function boot(): void {}
    public function scheduledTasks(): array { return []; }
    public function healthCheck(): array { return ['healthy' => true, 'message' => 'Stub — not built yet']; }
    public function guide(): array
    {
        return [
            'what' => 'গতকালের sales, top product, হঠাৎ ড্রপ/স্পাইক alert।',
            'enable' => 'Shadow/Live একই — শুধু পড়ে, কিছু বদলায় না।',
            'verify' => 'সকালে রিপোর্ট কার্ড দেখুন।',
        ];
    }
    public function plannedFeatures(): array
    {
        return ['Daily sales summary', 'Anomaly alerts', 'WhatsApp/Email delivery'];
    }
}
