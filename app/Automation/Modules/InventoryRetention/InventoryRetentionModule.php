<?php

namespace App\Automation\Modules\InventoryRetention;

use App\Automation\AutomationModule;

class InventoryRetentionModule implements AutomationModule
{
    public function key(): string { return 'inventory_retention'; }
    public function name(): string { return 'Inventory Forecast & Retention'; }
    public function description(): string { return 'স্টক শেষের পূর্বাভাস + পুরনো customer ফেরানোর অফার।'; }
    public function icon(): string { return 'bx bx-package'; }
    public function status(): string { return 'coming_soon'; }
    public function defaultSettings(): array { return ['days_ahead' => 14, 'low_days' => 3]; }
    public function settingsSchema(): array
    {
        return [
            ['name' => 'days_ahead', 'label' => 'পূর্বাভাস (দিন)', 'type' => 'number', 'default' => 14, 'rules' => 'required|integer|min:1|max:90', 'help' => 'কত দিনের stock forecast দেখাবে।'],
            ['name' => 'low_days', 'label' => 'সতর্কতা (দিন)', 'type' => 'number', 'default' => 3, 'rules' => 'required|integer|min:1|max:30', 'help' => 'এত দিনের stock থাকলে alert।'],
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
            'what' => 'বিক্রির গতি দেখে কোন product কবে শেষ হবে + win-back offer।',
            'enable' => 'Shadow-এ forecast log হবে।',
            'verify' => 'Forecast তালিকা মিলিয়ে দেখুন।',
        ];
    }
    public function plannedFeatures(): array
    {
        return ['Stock-out forecast', 'Reorder suggestion', 'Win-back campaigns'];
    }
}
