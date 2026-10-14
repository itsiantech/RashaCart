<?php

namespace App\Automation\Modules\OrderConfirmationBot;

use App\Automation\AutomationModule;

class OrderConfirmationBotModule implements AutomationModule
{
    public function key(): string { return 'order_confirmation_bot'; }
    public function name(): string { return 'Order Confirmation Bot'; }
    public function description(): string { return 'নতুন order-এ customer-কে confirmation message পাঠাবে।'; }
    public function icon(): string { return 'bx bx-bot'; }
    public function status(): string { return 'coming_soon'; }
    public function defaultSettings(): array { return ['channel' => 'sms', 'delay_minutes' => 5]; }
    public function settingsSchema(): array
    {
        return [
            ['name' => 'channel', 'label' => 'চ্যানেল', 'type' => 'select', 'default' => 'sms', 'rules' => 'required|in:sms,whatsapp', 'help' => 'কোন চ্যানেলে পাঠাবে।', 'options' => ['sms' => 'SMS', 'whatsapp' => 'WhatsApp']],
            ['name' => 'delay_minutes', 'label' => 'বিলম্ব (মিনিট)', 'type' => 'number', 'default' => 5, 'rules' => 'required|integer|min:0|max:1440', 'help' => 'Order-এর কত মিনিট পরে পাঠাবে।'],
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
            'what' => 'নতুন COD order-এ customer-কে confirmation চাইবে (ভুয়া order কমবে)।',
            'enable' => 'Shadow-এ শুধু log, Live-এ আসল message।',
            'verify' => 'Test order করে activity log দেখুন।',
        ];
    }
    public function plannedFeatures(): array
    {
        return ['SMS/WhatsApp confirmation', 'No-reply auto-cancel rule', 'COD verify flow'];
    }
}
