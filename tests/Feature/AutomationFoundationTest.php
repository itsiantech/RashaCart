<?php

namespace Tests\Feature;

use App\Livewire\Admin\Automation\AutomationIndex;
use App\Models\AutomationModule;
use App\Models\Settings;
use App\Models\User;
use App\Services\AutomationRegistry;
use App\Services\MaskPii;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Tests\TestCase;

class AutomationFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function seedModules(): void
    {
        foreach (AutomationRegistry::all() as $key => $module) {
            AutomationModule::updateOrCreate(['key' => $key], [
                'is_enabled' => false, 'mode' => 'off', 'settings' => $module->defaultSettings(),
            ]);
        }
        Settings::updateOrCreate(['key' => 'automation_master_switch'], ['value' => 1]);
        Settings::clearCache();
        AutomationRegistry::clearCache();
    }

    protected function admin(): User
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $u = User::factory()->create();
        $u->assignRole('admin');

        return $u;
    }

    public function test_registry_lists_six_modules_all_off_by_default(): void
    {
        $this->seedModules();

        $this->assertCount(6, AutomationRegistry::all());
        foreach (['risk_score', 'product_content', 'order_confirmation_bot', 'support_chatbot', 'daily_report_alerts', 'inventory_retention'] as $key) {
            $this->assertFalse(AutomationRegistry::isEnabled($key), $key);
            $this->assertSame('off', AutomationRegistry::mode($key));
        }
    }

    public function test_master_switch_overrides_modules(): void
    {
        $this->seedModules();
        AutomationModule::where('key', 'risk_score')->update(['is_enabled' => true, 'mode' => 'live']);
        AutomationRegistry::clearCache();
        $this->assertTrue(AutomationRegistry::isLive('risk_score'));

        Settings::updateOrCreate(['key' => 'automation_master_switch'], ['value' => 0]);
        Settings::clearCache();
        AutomationRegistry::clearCache();

        $this->assertFalse(AutomationRegistry::isEnabled('risk_score'));
        $this->assertFalse(AutomationRegistry::isLive('risk_score'));
    }

    public function test_cache_invalidates_on_save(): void
    {
        $this->seedModules();
        $this->assertFalse(AutomationRegistry::isEnabled('risk_score'));

        // Model observer clears cache on save — no manual clear needed
        AutomationModule::where('key', 'risk_score')->first()
            ->update(['is_enabled' => true, 'mode' => 'shadow']);

        $this->assertTrue(AutomationRegistry::isEnabled('risk_score'));
        $this->assertTrue(AutomationRegistry::isShadow('risk_score'));
        $this->assertFalse(AutomationRegistry::isLive('risk_score'));
    }

    public function test_middleware_blocks_disabled_module_routes(): void
    {
        $this->seedModules();
        $mw = new \App\Http\Middleware\EnsureModuleEnabled();

        $req = Request::create('/x', 'GET');
        $resp = $mw->handle($req, fn () => response('ok'), 'risk_score');
        $this->assertSame(302, $resp->getStatusCode()); // friendly redirect for web

        $json = Request::create('/x', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        try {
            $mw->handle($json, fn () => response('ok'), 'risk_score');
            $this->fail('JSON request was not blocked.');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            $this->assertTrue(true);
        }

        // Enabled → passes through
        AutomationModule::where('key', 'risk_score')->first()->update(['is_enabled' => true, 'mode' => 'live']);
        $resp = $mw->handle(Request::create('/x', 'GET'), fn () => response('ok'), 'risk_score');
        $this->assertSame('ok', $resp->getContent());
    }

    public function test_disabled_trait_helpers_are_noops(): void
    {
        $this->seedModules();
        $helper = new class {
            use \App\Automation\SkipsWhenDisabled;
            public function go(string $k): bool { return $this->skipUnlessEnabled($k); }
            public function live(string $k): bool { return $this->skipUnlessLive($k); }
        };

        $this->assertFalse($helper->go('risk_score'));
        $this->assertFalse($helper->live('risk_score'));
    }

    public function test_unauthorized_users_cannot_toggle(): void
    {
        $this->seedModules();
        $plain = User::factory()->create(); // no admin role

        Livewire::actingAs($plain)->test(AutomationIndex::class)->call('toggle', 'risk_score')
            ->assertForbidden();

        $this->assertFalse(AutomationRegistry::isEnabled('risk_score'));
    }

    public function test_toggle_writes_audit_log_without_secrets(): void
    {
        $this->seedModules();
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(AutomationIndex::class)->call('toggle', 'risk_score');
        $this->assertTrue(AutomationRegistry::isShadow('risk_score'));

        $log = \App\Models\AutomationAuditLog::where('module_key', 'risk_score')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertSame('enabled', $log->action);
        $this->assertSame($admin->id, $log->user_id);
    }

    public function test_mask_pii_and_budget_guard(): void
    {
        $masked = MaskPii::maskArray([
            'email' => 'a@b.com', 'phone' => '01712345678', 'note' => 'hello',
            'nested' => ['fn' => 'Rahim'],
        ]);
        $this->assertSame('[MASKED]', $masked['email']);
        $this->assertSame('[MASKED]', $masked['phone']);
        $this->assertSame('hello', $masked['note']);
        $this->assertSame('[MASKED]', $masked['nested']['fn']);

        // Budget exhausted → blocked + logged, no exception leak
        Settings::updateOrCreate(['key' => 'automation_daily_budget'], ['value' => 0.001]);
        Settings::updateOrCreate(['key' => 'automation_monthly_budget'], ['value' => 0.001]);
        Settings::clearCache();
        \App\Models\AiUsageLog::create([
            'module_key' => 'risk_score', 'tokens_in' => 100000, 'tokens_out' => 0,
            'cost' => 99, 'status' => 'success', 'created_at' => now(),
        ]);

        try {
            \App\Services\AI\AiService::complete('risk_score', 'hi');
            $this->fail('Budget guard did not block.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('বাজেট', $e->getMessage());
        }
        $this->assertSame('blocked', \App\Models\AiUsageLog::latest('id')->first()->status);
    }

    public function test_overview_and_module_pages_render(): void
    {        $this->seedModules();

        // Guest blocked (check first, before any actingAs)
        $this->get(route('admin.automation.index'))->assertRedirect();

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.automation.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.automation.module', 'risk_score'))->assertOk();
        $this->actingAs($admin)->get(route('admin.automation.ai-settings'))->assertOk();
        $this->actingAs($admin)->get(route('admin.automation.module', 'nope'))->assertNotFound();
    }

    public function test_menu_hidden_when_business_setting_off(): void
    {
        $this->seedModules();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Default ON → header visible
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('AUTOMATION', false);

        // OFF from Business Settings → whole section gone
        Settings::updateOrCreate(['key' => 'automation_menu'], ['value' => 0]);
        Settings::clearCache();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertDontSee('AUTOMATION', false);
    }

    public function test_seller_menu_hidden_when_business_setting_off(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $vRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'vendor.payout', 'guard_name' => 'web']);
        $vRole->givePermissionTo('vendor.payout');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $vendor = User::factory()->create(['is_vendor_approved' => true]);
        $vendor->assignRole('vendor');

        // Default ON → visible
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('<div>Seller Manage</div>', false);

        // OFF → hidden for admin, but vendor keeps Payout access
        Settings::updateOrCreate(['key' => 'seller_menu'], ['value' => 0]);
        Settings::clearCache();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertDontSee('<div>Seller Manage</div>', false);
        // Vendor keeps Payout in the same sidebar (payout page uses admin layout)
        $this->actingAs($vendor)->get(route('admin.vendor.payout'))->assertSee('Seller Payout', false);
    }
}
