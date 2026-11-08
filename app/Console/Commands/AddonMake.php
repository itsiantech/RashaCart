<?php

namespace App\Console\Commands;

use App\Addons\AddonManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AddonMake extends Command
{
    protected $signature = 'addon:make {name : Add-on name in StudlyCase, e.g. BlogTools} {--force : Overwrite the folder if it already exists}';

    protected $description = 'Scaffold a new add-on (manifest, provider, route, Livewire page, migration stub)';

    public function handle(AddonManager $manager): int
    {
        $studly = Str::studly((string) $this->argument('name'));

        if ($studly === '' || ! preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $studly)) {
            $this->error('Use letters and digits, starting with a letter (example: BlogTools).');

            return self::FAILURE;
        }

        $base = $manager->path().'/'.$studly;

        if (is_dir($base) && ! $this->option('force')) {
            $this->error("The folder already exists: {$base} (use --force to overwrite).");

            return self::FAILURE;
        }

        $replace = [
            '{{Studly}}' => $studly,
            '{{title}}' => Str::headline($studly),
            '{{kebab}}' => Str::kebab($studly),
            '{{snake}}' => Str::snake($studly),
            '{{core}}' => $manager->coreVersion(),
            '{{date}}' => now()->format('Y_m_d_His'),
        ];

        $files = [
            'addon.json' => $this->manifestStub(),
            'src/{{Studly}}ServiceProvider.php' => $this->providerStub(),
            'src/Livewire/Admin/Dashboard.php' => $this->livewireStub(),
            'routes/admin.php' => $this->routesStub(),
            'resources/views/admin/menu.blade.php' => $this->menuViewStub(),
            'resources/views/admin/page.blade.php' => $this->pageViewStub(),
            'database/migrations/{{date}}_create_{{snake}}_items_table.php' => $this->migrationStub(),
            'config.php' => $this->configStub(),
            'lang/en/messages.php' => $this->langStub(),
            'README.md' => $this->readmeStub(),
        ];

        File::ensureDirectoryExists($base);

        foreach ($files as $path => $contents) {
            $path = strtr($path, $replace);
            $target = $base.'/'.$path;

            File::ensureDirectoryExists(dirname($target));
            File::put($target, strtr($contents, $replace));

            $this->line(' <fg=green>+</> '.$studly.'/'.$path);
        }

        $this->newLine();
        $this->info("Add-on [{$studly}] scaffolded in {$base}");
        $this->line('Next steps:');
        $this->line('  php artisan addon:install '.$studly);
        $this->line('  php artisan addon:activate '.$studly);

        return self::SUCCESS;
    }

    protected function manifestStub(): string
    {
        return <<<'JSON'
{
    "name": "{{title}}",
    "slug": "{{Studly}}",
    "version": "1.0.0",
    "description": "Describe what {{title}} adds to the shop.",
    "author": "Your Name",
    "requires_core": "^{{core}}",
    "provider": "Addons\\{{Studly}}\\{{Studly}}ServiceProvider",
    "depends_on": [],
    "provides": [
        "{{title}} admin page (Add Ons > {{title}})",
        "Sidebar menu entry via the admin.menu slot",
        "order.initial_status filter example"
    ]
}

JSON;
    }

    protected function providerStub(): string
    {
        return <<<'PHP'
<?php

namespace Addons\{{Studly}};

use App\Support\Hooks;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class {{Studly}}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config.php', '{{snake}}');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', '{{kebab}}');
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');

        Livewire::component('{{kebab}}-admin', \Addons\{{Studly}}\Livewire\Admin\Dashboard::class);

        // Filter example: return the status untouched until you need to change it.
        Hooks::addFilter('order.initial_status', function (string $status) {
            return $status;
        });

        // Slot example: the sidebar item shown under "Add Ons".
        Hooks::addSlot('admin.menu', '{{kebab}}::admin.menu');
    }

    /**
     * Runs after the add-on migrations have been applied, on install.
     */
    public function onInstall(): void
    {
        // Seed default settings here, e.g. setting('{{snake}}_enabled', '1');
    }
}

PHP;
    }

    protected function livewireStub(): string
    {
        return <<<'PHP'
<?php

namespace Addons\{{Studly}}\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('{{kebab}}::admin.page')->layout('livewire.admin.layouts.app');
    }
}

PHP;
    }

    protected function routesStub(): string
    {
        return <<<'PHP'
<?php

use Addons\{{Studly}}\Livewire\Admin\Dashboard;
use App\Http\Middleware\EnsureAddonActive;
use Illuminate\Support\Facades\Route;

// EnsureAddonActive is required: the URL must 404 while the add-on is inactive.
Route::middleware(['web', 'auth', 'vendor.approved', 'role:admin', EnsureAddonActive::class.':{{Studly}}'])
    ->prefix('admin/addons')
    ->name('admin.addons.')
    ->group(function () {
        Route::get('{{kebab}}', Dashboard::class)->name('{{kebab}}');
    });

PHP;
    }

    protected function menuViewStub(): string
    {
        return <<<'BLADE'
<li class="menu-item {{ Route::is('admin.addons.{{kebab}}') ? 'active' : '' }}">
    <a wire:navigate href="{{ route('admin.addons.{{kebab}}') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-extension"></i>
        <div>{{title}}</div>
    </a>
</li>

BLADE;
    }

    protected function pageViewStub(): string
    {
        return <<<'BLADE'
<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">{{title}}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-2">{{title}}</h5>
            <p class="text-muted mb-0">
                This page is served by the {{Studly}} add-on. Edit
                <code>addons/{{Studly}}/src/Livewire/Admin/Dashboard.php</code>
                and this view to build your feature.
            </p>
        </div>
    </div>
</div>

BLADE;
    }

    protected function migrationStub(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{{snake}}_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{{snake}}_items');
    }
};

PHP;
    }

    protected function configStub(): string
    {
        return <<<'PHP'
<?php

return [
    // Read with config('{{snake}}.enabled')
    'enabled' => true,
];

PHP;
    }

    protected function langStub(): string
    {
        return <<<'PHP'
<?php

return [
    'title' => '{{title}}',
];

PHP;
    }

    protected function readmeStub(): string
    {
        return <<<'MD'
# {{title}}

An add-on for this shop.

- **Slug:** `{{Studly}}`
- **Provider:** `Addons\{{Studly}}\{{Studly}}ServiceProvider`
- **Admin page:** `/admin/addons/{{kebab}}`

## Install

```bash
php artisan addon:install {{Studly}}
php artisan addon:activate {{Studly}}
```

## Hooks used

| Hook | Type | Purpose |
| --- | --- | --- |
| `order.initial_status` | filter | Example filter, returns the status unchanged |
| `admin.menu` | slot | Sidebar item under "Add Ons" |

See `addons/README.md` for the full hook reference.
MD;
    }
}
