<?php

namespace Addons\Dependent;

use Illuminate\Support\ServiceProvider;

class DependentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Nothing to register — this fixture exists for dependency rules.
    }
}
