<?php

namespace Addons\TestDemo;

use App\Support\Hooks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class TestDemoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'test-demo');

        Hooks::addFilter('test.multiply', fn (int $value) => $value * 2);
        Hooks::addFilter('order.initial_status', fn (string $status) => 'tested');
        Hooks::addAction('test.record', function (string $value) {
            $GLOBALS['test_demo_action_value'] = $value;
        });
        Hooks::addSlot('test.slot', 'test-demo::slot');
    }

    public function onInstall(): void
    {
        DB::table('test_demo_items')->insert(['title' => 'installed']);
    }
}
