<?php

namespace Tests\Feature;

use App\Livewire\Admin\Settings\SidebarThemePicker;
use App\Models\User;
use App\Services\SidebarTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SidebarThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_for_fresh_user(): void
    {
        $prefs = SidebarTheme::prefs(null);
        $this->assertSame('light', $prefs['theme']);
        $this->assertSame(14, $prefs['font_size']);

        $vars = SidebarTheme::cssVars($prefs);
        $this->assertSame('#ffffff', $vars['--sb-bg']);
        $this->assertSame('14px', $vars['--sb-font-size']);
        $this->assertSame('13px', $vars['--sb-sub-font-size']);
    }

    public function test_invalid_stored_values_fall_back_to_defaults(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['sidebar_theme' => ['theme' => 'nope', 'font_size' => 99]]);

        $prefs = SidebarTheme::prefs($user);
        $this->assertSame('light', $prefs['theme']);
        $this->assertSame(18, $prefs['font_size']); // clamped to max

        $user->forceFill(['sidebar_theme' => ['font_size' => 5]]);
        $this->assertSame(12, SidebarTheme::prefs($user)['font_size']); // clamped to min
    }

    public function test_picker_persists_per_user_and_applies_instantly(): void
    {
        $admin = User::factory()->create();
        $other = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(SidebarThemePicker::class)
            ->call('selectTheme', 'dark')
            ->call('stepFont', 2)
            ->assertDispatched('sidebar-theme-applied');

        $this->assertSame('dark', $admin->fresh()->sidebar_theme['theme']);
        $this->assertSame(16, $admin->fresh()->sidebar_theme['font_size']);
        // Other admin untouched
        $this->assertNull($other->fresh()->sidebar_theme);

        // Invalid theme ignored
        Livewire::actingAs($admin)->test(SidebarThemePicker::class)->call('selectTheme', 'nope');
        $this->assertSame('dark', $admin->fresh()->sidebar_theme['theme']);

        // Reset
        Livewire::actingAs($admin)->test(SidebarThemePicker::class)->call('resetTheme');
        $this->assertSame('light', $admin->fresh()->sidebar_theme['theme']);
        $this->assertSame(14, $admin->fresh()->sidebar_theme['font_size']);
    }

    public function test_all_five_themes_resolve(): void
    {
        $this->assertCount(5, SidebarTheme::THEMES);
        foreach (array_keys(SidebarTheme::THEMES) as $key) {
            $vars = SidebarTheme::cssVars(['theme' => $key, 'font_size' => 14]);
            $this->assertNotEmpty($vars['--sb-bg']);
            $this->assertNotEmpty($vars['--sb-text']);
        }
    }
}
