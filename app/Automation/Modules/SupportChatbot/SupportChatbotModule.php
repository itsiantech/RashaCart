<?php

namespace App\Automation\Modules\SupportChatbot;

use App\Automation\AutomationModule;

class SupportChatbotModule implements AutomationModule
{
    public function key(): string { return 'support_chatbot'; }
    public function name(): string { return 'Support Chatbot'; }
    public function description(): string { return 'কমন প্রশ্নের (ডেলিভারি/রিটার্ন) auto-reply দেবে।'; }
    public function icon(): string { return 'bx bx-chat'; }
    public function status(): string { return 'coming_soon'; }
    public function defaultSettings(): array { return ['handover_keywords' => 'manush,agent,complain', 'confidence' => 80]; }
    public function settingsSchema(): array
    {
        return [
            ['name' => 'confidence', 'label' => 'Confidence %', 'type' => 'number', 'default' => 80, 'rules' => 'required|integer|min:0|max:100', 'help' => 'এর নিচে হলে মানুষের কাছে পাঠাবে।'],
            ['name' => 'handover_keywords', 'label' => 'Handover keywords', 'type' => 'text', 'default' => 'manush,agent,complain', 'rules' => 'nullable|string|max:255', 'help' => 'এই শব্দ থাকলে সরাসরি মানুষের কাছে যাবে।'],
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
            'what' => 'ডেলিভারি চার্জ, রিটার্ন, অর্ডার স্ট্যাটাস — কমন প্রশ্নের জবাব দেবে।',
            'enable' => 'Shadow-এ suggested reply log হবে।',
            'verify' => 'Test প্রশ্ন করে log মিলিয়ে দেখুন।',
        ];
    }
    public function plannedFeatures(): array
    {
        return ['বাংলা FAQ bot', 'Human handover', 'Order lookup reply'];
    }
}
