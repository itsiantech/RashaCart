<?php

namespace Tests\Feature\Addons;

use App\Addons\AddonException;
use App\Addons\AddonManager;
use App\Livewire\Admin\Addons\Index;
use App\Models\Addon;
use App\Models\User;
use App\Support\Hooks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AddonSystemTest extends TestCase
{
    use RefreshDatabase;

    protected AddonManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Hooks::flush();
        $this->manager = $this->useAddonsFrom(base_path('tests/Fixtures/addons'));
    }

    protected function tearDown(): void
    {
        Hooks::flush();

        parent::tearDown();
    }

    protected function useAddonsFrom(string $path): AddonManager
    {
        config(['addons.path' => $path]);
        $this->app->forgetInstance(AddonManager::class);

        $manager = $this->app->make(AddonManager::class);
        $manager->registerAutoloader();

        return $manager;
    }

    protected function adminUser(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    public function test_discovers_addons_from_manifests(): void
    {
        $found = $this->manager->discover();

        $this->assertArrayHasKey('TestDemo', $found);
        $this->assertSame('Test Demo', $found['TestDemo']['name']);
        $this->assertSame('1.2.0', $found['TestDemo']['version']);
        $this->assertSame(['TestDemo'], $found['Dependent']['depends_on']);
        $this->assertSame('Addons\\TestDemo\\TestDemoServiceProvider', $found['TestDemo']['provider']);
    }

    public function test_zero_addons_leaves_the_core_untouched(): void
    {
        $manager = $this->useAddonsFrom(base_path('tests/Fixtures/empty-addons'));

        $this->assertSame([], $manager->discover());
        $this->assertSame([], $manager->activeSlugs());
        $this->assertFalse($manager->isActive('Anything'));

        $manager->boot(); // must be a no-op, not an error

        $this->assertSame([], $manager->bootedProviders());
        $this->assertSame('pending', Hooks::filter('order.initial_status', 'pending'));
        $this->assertSame('', (string) Hooks::renderSlot('popup.summary.extra'));
    }

    public function test_install_runs_migrations_and_the_install_hook(): void
    {
        $record = $this->manager->install('TestDemo');

        $this->assertSame(Addon::STATUS_INSTALLED, $record->status);
        $this->assertSame('1.2.0', $record->version);
        $this->assertTrue(Schema::hasTable('test_demo_items'));
        $this->assertDatabaseHas('test_demo_items', ['title' => 'installed']);
        $this->assertDatabaseHas('addons', ['slug' => 'TestDemo', 'status' => 'installed']);
    }

    public function test_activate_and_deactivate_keep_addon_data(): void
    {
        $this->manager->install('TestDemo');

        $this->manager->activate('TestDemo');
        $this->assertDatabaseHas('addons', ['slug' => 'TestDemo', 'status' => 'active']);
        $this->assertTrue($this->manager->isActive('TestDemo'));

        $this->manager->deactivate('TestDemo');

        $this->assertDatabaseHas('addons', ['slug' => 'TestDemo', 'status' => 'inactive']);
        $this->assertFalse($this->manager->isActive('TestDemo'));
        $this->assertTrue(Schema::hasTable('test_demo_items'));
        $this->assertDatabaseHas('test_demo_items', ['title' => 'installed']);

        $this->manager->activate('TestDemo');
        $this->assertTrue($this->manager->isActive('TestDemo'));
    }

    public function test_uninstall_keeps_data_unless_the_admin_drops_it(): void
    {
        $this->manager->install('TestDemo');
        $this->manager->deactivate('TestDemo');

        $this->manager->uninstall('TestDemo', dropData: false);

        $this->assertDatabaseMissing('addons', ['slug' => 'TestDemo']);
        $this->assertTrue(Schema::hasTable('test_demo_items'));
    }

    public function test_uninstall_can_drop_addon_tables(): void
    {
        $this->manager->install('TestDemo');

        $this->manager->uninstall('TestDemo', dropData: true);

        $this->assertDatabaseMissing('addons', ['slug' => 'TestDemo']);
        $this->assertFalse(Schema::hasTable('test_demo_items'));
    }

    public function test_dependency_rules_are_enforced(): void
    {
        try {
            $this->manager->install('Dependent');
            $this->fail('Installing an add-on whose dependency is missing must fail.');
        } catch (AddonException $e) {
            $this->assertStringContainsString('TestDemo', $e->getMessage());
        }

        $this->manager->install('TestDemo');
        $this->manager->install('Dependent');

        try {
            $this->manager->activate('Dependent');
            $this->fail('Activating with an inactive dependency must fail.');
        } catch (AddonException $e) {
            $this->assertStringContainsString('active', $e->getMessage());
        }

        $this->manager->activate('TestDemo');
        $this->manager->activate('Dependent');
        $this->assertTrue($this->manager->isActive('Dependent'));

        try {
            $this->manager->deactivate('TestDemo');
            $this->fail('Deactivating a required add-on must fail.');
        } catch (AddonException $e) {
            $this->assertStringContainsString('Dependent', $e->getMessage());
        }

        $cards = collect($this->manager->all())->keyBy('slug');
        $this->assertFalse($cards['TestDemo']['can_deactivate']);
    }

    public function test_core_version_constraint_is_enforced(): void
    {
        $this->expectException(AddonException::class);

        $this->manager->install('BadCore');
    }

    public function test_version_constraint_parser(): void
    {
        $this->assertTrue($this->manager->satisfies('*', '1.4.2'));
        $this->assertTrue($this->manager->satisfies('^1.0', '1.9.9'));
        $this->assertFalse($this->manager->satisfies('^1.0', '2.0.0'));
        $this->assertTrue($this->manager->satisfies('^0.2.3', '0.2.9'));
        $this->assertFalse($this->manager->satisfies('^0.2.3', '0.3.0'));
        $this->assertTrue($this->manager->satisfies('~1.2.3', '1.2.9'));
        $this->assertFalse($this->manager->satisfies('~1.2.3', '1.3.0'));
        $this->assertTrue($this->manager->satisfies('>=1.0 <2.0', '1.5.0'));
        $this->assertFalse($this->manager->satisfies('>=1.0 <2.0', '2.1.0'));
        $this->assertTrue($this->manager->satisfies('^2.0 || ^3.0', '3.1.0'));
        $this->assertTrue($this->manager->satisfies('1.2.3', '1.2.3'));
        $this->assertFalse($this->manager->satisfies('1.2.3', '1.2.4'));
    }

    public function test_filters_and_slots_only_apply_while_active(): void
    {
        $this->manager->install('TestDemo');
        $this->manager->activate('TestDemo');

        Hooks::flush();
        $this->manager->boot();

        $this->assertSame(10, Hooks::filter('test.multiply', 5));
        $this->assertSame('tested', Hooks::filter('order.initial_status', 'pending'));
        $this->assertStringContainsString('test-demo-slot', (string) Hooks::renderSlot('test.slot'));

        $this->manager->deactivate('TestDemo');
        Hooks::flush();
        $this->manager->boot();

        $this->assertSame(5, Hooks::filter('test.multiply', 5));
        $this->assertSame('pending', Hooks::filter('order.initial_status', 'pending'));
        $this->assertSame('', (string) Hooks::renderSlot('test.slot'));

        // Reactivating brings the same hooks and data back.
        $this->manager->activate('TestDemo');
        Hooks::flush();
        $this->manager->boot();

        $this->assertSame(10, Hooks::filter('test.multiply', 5));
        $this->assertDatabaseHas('test_demo_items', ['title' => 'installed']);
    }

    public function test_broken_provider_is_auto_deactivated_and_the_shop_keeps_working(): void
    {
        $this->manager->install('Broken');
        $this->manager->activate('Broken');

        $this->manager->boot(); // must not throw

        $record = Addon::where('slug', 'Broken')->first();

        $this->assertSame(Addon::STATUS_INACTIVE, $record->status);
        $this->assertNotNull($record->last_error);
        $this->assertStringContainsString('exploded', $record->last_error);
        $this->assertArrayNotHasKey('Broken', $this->manager->bootedProviders());
        $this->assertFalse($this->manager->isActive('Broken'));
        $this->assertSame('pending', Hooks::filter('order.initial_status', 'pending'));
    }

    public function test_hook_blade_directive_renders_registered_slots(): void
    {
        $empty = Blade::render("@hook('test.slot')");
        $this->assertSame('', trim($empty));

        Hooks::addSlot('test.slot', fn () => '<span class="inline-slot">inline</span>');

        $this->assertStringContainsString('inline-slot', Blade::render("@hook('test.slot')"));
    }

    public function test_addon_ui_icons_exist_in_the_admin_boxicons_build(): void
    {
        $font = file_get_contents(base_path('public/assets/admin/vendor/fonts/boxicons.css'));

        // The bundled Boxicons build has no bx-puzzle, so a wrong name renders
        // as an empty square instead of an icon.
        $icons = ['bx-extension', 'bx-list-check'];

        foreach ([
            base_path('resources/views/livewire/admin/addons/index.blade.php'),
            base_path('addons/HelloWorld/resources/views/admin/menu.blade.php'),
        ] as $view) {
            preg_match_all('/\bbx\s+(bx-[a-z0-9-]+)/', file_get_contents($view), $matches);
            $icons = array_merge($icons, $matches[1]);
        }

        foreach (array_unique($icons) as $icon) {
            $this->assertStringContainsString(
                '.'.$icon.':before',
                $font,
                "Icon {$icon} does not exist in the admin boxicons build."
            );
        }
    }

    public function test_admin_page_lists_addons_and_runs_the_lifecycle(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)->test(Index::class)
            ->assertSee('Test Demo')
            ->assertSee('Not installed')
            ->call('install', 'TestDemo')
            ->assertRedirect(route('admin.addons.index'));

        $this->assertDatabaseHas('addons', ['slug' => 'TestDemo', 'status' => 'installed']);

        Livewire::actingAs($admin)->test(Index::class)
            ->assertSee('Activate')
            ->call('activate', 'TestDemo')
            ->assertRedirect(route('admin.addons.index'));

        $this->assertDatabaseHas('addons', ['slug' => 'TestDemo', 'status' => 'active']);

        Livewire::actingAs($admin)->test(Index::class)
            ->assertSee('Deactivate')
            ->call('deactivate', 'TestDemo')
            ->assertRedirect(route('admin.addons.index'));

        $this->assertDatabaseHas('addons', ['slug' => 'TestDemo', 'status' => 'inactive']);

        Livewire::actingAs($admin)->test(Index::class)
            ->call('confirmUninstall', 'TestDemo')
            ->assertSee('Also delete this add-on')
            ->set('dropData', true)
            ->call('uninstall')
            ->assertRedirect(route('admin.addons.index'));

        $this->assertDatabaseMissing('addons', ['slug' => 'TestDemo']);
        $this->assertFalse(Schema::hasTable('test_demo_items'));
    }

    public function test_admin_page_shows_dependency_problems(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)->test(Index::class)
            ->assertSee('Depends on [TestDemo], which is not installed.');
    }

    public function test_addon_page_returns_404_while_the_addon_is_inactive(): void
    {
        $manager = $this->useAddonsFrom(base_path('addons'));
        $admin = $this->adminUser();

        $this->actingAs($admin)->get('/admin/addons/hello-world')->assertNotFound();

        $manager->install('HelloWorld');
        $manager->activate('HelloWorld');

        Hooks::flush();
        $manager->boot();

        $this->actingAs($admin)->get('/admin/addons/hello-world')->assertOk();

        $manager->deactivate('HelloWorld');
        Hooks::flush();

        $this->actingAs($admin)->get('/admin/addons/hello-world')->assertNotFound();
    }
}
