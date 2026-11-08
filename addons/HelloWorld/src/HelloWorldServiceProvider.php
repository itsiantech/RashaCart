<?php

namespace Addons\HelloWorld;

use App\Support\Hooks;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class HelloWorldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hello-world');
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');

        Livewire::component('hello-world-admin', \Addons\HelloWorld\Livewire\Admin\Dashboard::class);

        // Demo filter. It returns the value untouched so the storefront behaves
        // exactly like the core until you change the return line.
        Hooks::addFilter('order.initial_status', function (string $status) {
            return $status;
        });

        // Demo slot: one sidebar item under "Add Ons".
        Hooks::addSlot('admin.menu', 'hello-world::admin.menu');
    }
}
