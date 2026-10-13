<?php

namespace App\Services;

/**
 * Sidebar theme engine (per-admin).
 * Themes render through CSS variables consumed by sidebar-theme.css,
 * so switching is instant and needs no page reload.
 */
class SidebarTheme
{
    public const DEFAULT_THEME = 'light';
    public const DEFAULT_FONT_SIZE = 14;
    public const MIN_FONT_SIZE = 12;
    public const MAX_FONT_SIZE = 18;

    public const THEMES = [
        'light' => [
            'label' => 'Light',
            'swatch' => '#ffffff',
            'bg' => '#ffffff',
            'text' => '#384551',
            'muted' => '#a1acb8',
            'hover_bg' => '#f5f5f9',
            'active_bg' => 'rgba(105, 108, 255, 0.12)',
            'active_text' => '#696cff',
            'border' => '#e5e7eb',
            'header_text' => '#a1acb8',
        ],
        'dark' => [
            'label' => 'Dark',
            'swatch' => '#232333',
            'bg' => '#232333',
            'text' => '#cbcbe0',
            'muted' => '#7c7db6',
            'hover_bg' => '#2b2c40',
            'active_bg' => 'rgba(105, 108, 255, 0.18)',
            'active_text' => '#ffffff',
            'border' => 'transparent',
            'header_text' => '#7c7db6',
        ],
        'periwinkle' => [
            'label' => 'Periwinkle',
            'swatch' => '#D9DEF2',
            'bg' => '#D9DEF2',
            'text' => '#2b3a55',
            'muted' => '#6b7a99',
            'hover_bg' => '#cfd6ec',
            'active_bg' => '#c2cbe4',
            'active_text' => '#1f2a44',
            'border' => '#c9d1e6',
            'header_text' => '#6b7a99',
        ],
        'mint' => [
            'label' => 'Mint',
            'swatch' => '#E7F0EB',
            'bg' => '#E7F0EB',
            'text' => '#2f4438',
            'muted' => '#6f8879',
            'hover_bg' => '#d9e7de',
            'active_bg' => '#cbddd2',
            'active_text' => '#1e3329',
            'border' => '#cfdccf',
            'header_text' => '#6f8879',
        ],
        'lavender' => [
            'label' => 'Lavender',
            'swatch' => '#E3E0E7',
            'bg' => '#E3E0E7',
            'text' => '#453e54',
            'muted' => '#837a99',
            'hover_bg' => '#d6d1de',
            'active_bg' => '#c9c2d4',
            'active_text' => '#332d42',
            'border' => '#cdc7d6',
            'header_text' => '#837a99',
        ],
    ];

    /** Merged prefs for a user (stored values win, defaults fill the rest). */
    public static function prefs(?object $user): array
    {
        $stored = [];
        if ($user && isset($user->sidebar_theme)) {
            $stored = is_array($user->sidebar_theme)
                ? $user->sidebar_theme
                : (json_decode((string) $user->sidebar_theme, true) ?: []);
        }

        $theme = $stored['theme'] ?? self::DEFAULT_THEME;
        if (! isset(self::THEMES[$theme])) {
            $theme = self::DEFAULT_THEME;
        }

        $size = (int) ($stored['font_size'] ?? self::DEFAULT_FONT_SIZE);
        $size = max(self::MIN_FONT_SIZE, min(self::MAX_FONT_SIZE, $size));

        return ['theme' => $theme, 'font_size' => $size];
    }

    /** CSS variable map for :root. */
    public static function cssVars(array $prefs): array
    {
        $t = self::THEMES[$prefs['theme']];

        return [
            '--sb-bg' => $t['bg'],
            '--sb-text' => $t['text'],
            '--sb-muted' => $t['muted'],
            '--sb-hover-bg' => $t['hover_bg'],
            '--sb-active-bg' => $t['active_bg'],
            '--sb-active-text' => $t['active_text'],
            '--sb-border' => $t['border'],
            '--sb-header-text' => $t['header_text'],
            '--sb-font-size' => $prefs['font_size'].'px',
            '--sb-sub-font-size' => ($prefs['font_size'] - 1).'px',
        ];
    }

    public static function cssVarsForUser(?object $user): array
    {
        return self::cssVars(self::prefs($user));
    }
}
