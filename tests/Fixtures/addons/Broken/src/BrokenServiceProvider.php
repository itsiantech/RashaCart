<?php

namespace Addons\Broken;

use Illuminate\Support\ServiceProvider;
use RuntimeException;

class BrokenServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        throw new RuntimeException('Broken add-on exploded on purpose.');
    }
}
