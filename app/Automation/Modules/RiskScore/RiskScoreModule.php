<?php

namespace App\Automation\Modules\RiskScore;

use App\Automation\AutomationModule;

class RiskScoreModule implements AutomationModule
{
    public function key(): string { return 'risk_score'; }
    public function name(): string { return 'Fake Order Risk Score'; }
    public function description(): string { return 'ভুয়া/COD রিস্ক order ধরবে (shadow-এ শুধু স্কোর করে)।'; }
    public function icon(): string { return 'bx bx-shield-quarter'; }
    public function status(): string { return 'coming_soon'; }
    public function defaultSettings(): array { return ['threshold' => 70, 'auto_hold' => false]; }
    public function settingsSchema(): array
    {
        return [
            ['name' => 'threshold', 'label' => 'রিস্ক threshold (০-১০০)', 'type' => 'number', 'default' => 70, 'rules' => 'required|integer|min:0|max:100', 'help' => 'এই স্কোরের উপরে হলে সন্দেহজনক।'],
            ['name' => 'auto_hold', 'label' => 'Auto-hold', 'type' => 'toggle', 'default' => false, 'rules' => 'boolean', 'help' => 'Live-এ বেশি রিস্কের order auto-hold হবে।'],
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
            'what' => 'COD ভুয়া order-এর রিস্ক স্কোর দেবে (ফোন, ঠিকানা, history দেখে)।',
            'enable' => 'আগে Shadow mode-এ চালান — শুধু log হবে, কোনো action হবে না।',
            'verify' => 'Activity log-এ score entries দেখুন।',
        ];
    }
    public function plannedFeatures(): array
    {
        return ['ফোন/ঠিকানা ভিত্তিক স্কোরিং', 'Blacklist check', 'Auto-hold / review queue'];
    }
}
