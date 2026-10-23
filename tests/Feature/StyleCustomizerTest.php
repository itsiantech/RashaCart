<?php

namespace Tests\Feature;

use App\Livewire\Admin\Settings\StyleCustomizer;
use App\Models\User;
use App\Services\StyleSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StyleCustomizerTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'setting.view', 'guard_name' => 'web']);
        $u = User::factory()->create();
        $u->assignRole('admin');

        return $u;
    }

    public function test_defaults_match_current_design(): void
    {
        $vars = StyleSettings::cssVars();
        $this->assertSame('#12225B', $vars['--hd-bg']);
        $this->assertSame('#12225B', $vars['--ft-bg']);
        $this->assertSame('#EA580C', $vars['--flash-bg']);
        $this->assertSame('#FFFFFF', $vars['--flash-text']);
        $this->assertStringContainsString('Segoe UI', $vars['--body-font']);
        // flash font follows body font by default
        $this->assertSame($vars['--body-font'], $vars['--flash-font']);
        $this->assertCount(16, $vars);
    }

    public function test_save_and_reset_flow(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(StyleCustomizer::class)
            ->set('form.header_bg', '#000000')
            ->set('form.body_font', 'hind')
            ->call('save')
            ->assertDispatched('success');

        $this->assertSame('#000000', setting('style.header_bg'));
        $this->assertStringContainsString('Hind Siliguri', StyleSettings::cssVars()['--body-font']);

        // Invalid values rejected
        Livewire::actingAs($admin)->test(StyleCustomizer::class)
            ->set('form.header_bg', 'not-a-color')
            ->call('save')
            ->assertHasErrors(['form.header_bg']);

        // Reset restores defaults
        Livewire::actingAs($admin)->test(StyleCustomizer::class)
            ->call('resetDefaults')
            ->assertDispatched('success');
        $this->assertSame('#12225B', setting('style.header_bg'));
    }

    public function test_homepage_reflects_custom_style(): void
    {
        \App\Models\Settings::updateOrCreate(['key' => 'style.header_bg'], ['value' => '#123456']);
        \App\Models\Settings::updateOrCreate(['key' => 'style.flash_bg'], ['value' => '#654321']);
        \App\Models\Settings::clearCache();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('--hd-bg: #123456', $html);
        $this->assertStringContainsString('--flash-bg: #654321', $html);
    }

    public function test_product_details_page_receives_style_vars(): void
    {
        $p = \App\Models\Product::factory()->create(['status' => true]);

        $html = $this->get(route('frontend.product.single.view', $p->slug))->assertOk()->getContent();
        $this->assertStringContainsString('style-customizer-vars', $html);
        $this->assertStringContainsString('--hd-bg:', $html);
    }

    public function test_route_and_sidebar_entry_guarded(): void
    {
        // Guest first (actingAs persists within a test)
        $this->get(route('admin.settings.style'))->assertRedirect();

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.settings.style'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('Style Settings', false);
    }

    public function test_cart_button_colors_optional_and_applied(): void
    {
        // Unset → vars absent → every context keeps its own look
        $vars = StyleSettings::cssVars();
        $this->assertArrayNotHasKey('--cart-btn-bg', $vars);
        $this->assertArrayNotHasKey('--cart-btn-text', $vars);
        $this->assertArrayNotHasKey('--cart-btn-icon', $vars);
        $this->assertCount(16, $vars);

        $admin = $this->admin();
        Livewire::actingAs($admin)->test(StyleCustomizer::class)
            ->set('form.cart_btn_bg', '#111111')
            ->set('form.cart_btn_text', '#FFFFFF')
            ->set('form.cart_btn_icon', '#FF0000')
            ->call('save')
            ->assertDispatched('success');

        $vars = StyleSettings::cssVars();
        $this->assertSame('#111111', $vars['--cart-btn-bg']);
        $this->assertSame('#FFFFFF', $vars['--cart-btn-text']);
        $this->assertSame('#FF0000', $vars['--cart-btn-icon']);

        // CSS hooks present everywhere (fallbacks = current look)
        $home = $this->get('/')->getContent();
        $this->assertStringContainsString('--cart-btn-bg', $home);
        $this->assertStringContainsString('--cart-btn-bg: #111111', $home);

        // Clear → back to unset
        Livewire::actingAs($admin)->test(StyleCustomizer::class)
            ->call('resetDefaults');
        $this->assertArrayNotHasKey('--cart-btn-bg', StyleSettings::cssVars());
    }

    public function test_action_button_and_title_colors(): void
    {
        $admin = $this->admin();
        Livewire::actingAs($admin)->test(StyleCustomizer::class)
            ->set('form.buy_btn_bg', '#111111')
            ->set('form.buy_btn_text', '#EEEEEE')
            ->set('form.wa_btn_bg', '#222222')
            ->set('form.wa_btn_text', '#DDDDDD')
            ->set('form.call_btn_bg', '#333333')
            ->set('form.call_btn_text', '#CCCCCC')
            ->set('form.cat_title_bg', '#444444')
            ->set('form.cat_title_text', '#BBBBBB')
            ->call('save')
            ->assertDispatched('success');

        $vars = StyleSettings::cssVars();
        foreach (['--buy-btn-bg', '--buy-btn-text', '--wa-btn-bg', '--wa-btn-text',
            '--call-btn-bg', '--call-btn-text', '--cat-title-bg', '--cat-title-text'] as $k) {
            $this->assertArrayHasKey($k, $vars);
        }

        $this->assertCount(24, $vars);
    }
}
