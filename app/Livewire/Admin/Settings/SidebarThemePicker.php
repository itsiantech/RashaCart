<?php

namespace App\Livewire\Admin\Settings;

use App\Services\SidebarTheme;
use Livewire\Component;

class SidebarThemePicker extends Component
{
    public string $theme = SidebarTheme::DEFAULT_THEME;

    public int $fontSize = SidebarTheme::DEFAULT_FONT_SIZE;

    public bool $open = false;

    public function mount(): void
    {
        $prefs = SidebarTheme::prefs(auth()->user());
        $this->theme = $prefs['theme'];
        $this->fontSize = $prefs['font_size'];
    }

    public function selectTheme(string $theme): void
    {
        if (! isset(SidebarTheme::THEMES[$theme])) {
            return;
        }

        $this->theme = $theme;
        $this->persist();
    }

    public function stepFont(int $delta): void
    {
        $this->fontSize = max(
            SidebarTheme::MIN_FONT_SIZE,
            min(SidebarTheme::MAX_FONT_SIZE, $this->fontSize + $delta)
        );
        $this->persist();
    }

    public function resetTheme(): void
    {
        $this->theme = SidebarTheme::DEFAULT_THEME;
        $this->fontSize = SidebarTheme::DEFAULT_FONT_SIZE;
        $this->persist();
    }

    protected function persist(): void
    {
        auth()->user()->update([
            'sidebar_theme' => ['theme' => $this->theme, 'font_size' => $this->fontSize],
        ]);

        // Instant apply (no reload): layout listens for this event.
        $this->dispatch('sidebar-theme-applied', vars: SidebarTheme::cssVars([
            'theme' => $this->theme,
            'font_size' => $this->fontSize,
        ]));
    }

    public function render()
    {
        return view('livewire.admin.settings.sidebar-theme-picker', [
            'themes' => SidebarTheme::THEMES,
            'min' => SidebarTheme::MIN_FONT_SIZE,
            'max' => SidebarTheme::MAX_FONT_SIZE,
        ]);
    }
}
