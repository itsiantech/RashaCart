<?php

namespace App\Automation\Modules\ProductContent;

use App\Automation\AutomationModule;

class ProductContentModule implements AutomationModule
{
    public function key(): string { return 'product_content'; }
    public function name(): string { return 'AI Product Content & Attribute Detect'; }
    public function description(): string { return 'ছবি/নাম থেকে বাংলা description ও attribute বের করবে।'; }
    public function icon(): string { return 'bx bx-bulb'; }
    public function status(): string { return 'coming_soon'; }
    public function defaultSettings(): array { return ['language' => 'bn', 'auto_fill' => false]; }
    public function settingsSchema(): array
    {
        return [
            ['name' => 'language', 'label' => 'ভাষা', 'type' => 'select', 'default' => 'bn', 'rules' => 'required|in:bn,en', 'help' => 'Content কোন ভাষায় হবে।', 'options' => ['bn' => 'বাংলা', 'en' => 'English']],
            ['name' => 'auto_fill', 'label' => 'Auto-fill', 'type' => 'toggle', 'default' => false, 'rules' => 'boolean', 'help' => 'Live-এ draft হিসেবে বসাবে, publish করবে না।'],
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
            'what' => 'Product নাম/ছবি থেকে বাংলা title, description ও color/size detect করবে।',
            'enable' => 'Shadow-এ draft দেখুন, ঠিক থাকলে Live।',
            'verify' => 'Add Product-এ suggestion দেখা যাবে।',
        ];
    }
    public function plannedFeatures(): array
    {
        return ['বাংলা description লেখা', 'ছবি থেকে color detect', 'SEO title suggestion'];
    }
}
