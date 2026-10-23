<?php

namespace App\Services;

/**
 * Style customizer: admin-controlled colors/fonts rendered as :root CSS vars.
 * Stored in the existing Settings store (style.* keys). Everything falls back
 * to the current design, so nothing changes until the admin saves.
 */
class StyleSettings
{
    public const FONTS = [
        'segoe' => ['label' => 'Segoe UI (default)', 'stack' => "'Segoe UI', sans-serif"],
        'inter' => ['label' => 'Inter', 'stack' => "'Inter', 'Segoe UI', sans-serif"],
        'hind' => ['label' => 'Hind Siliguri (বাংলা)', 'stack' => "'Hind Siliguri', 'Segoe UI', sans-serif"],
        'roboto' => ['label' => 'Roboto', 'stack' => "'Roboto', 'Segoe UI', sans-serif"],
        'poppins' => ['label' => 'Poppins', 'stack' => "'Poppins', 'Segoe UI', sans-serif"],
    ];

    public const FONT_LINK = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&family=Roboto:wght@400;500;700&family=Poppins:wght@400;500;600&display=swap';

    /** key => [var, label, type, default] */
    public const FIELDS = [
        'header_bg' => ['var' => '--hd-bg', 'label' => 'Header color', 'type' => 'color', 'default' => '#12225B'],
        'nav_text' => ['var' => '--hd-nav', 'label' => 'Navigation text color', 'type' => 'color', 'default' => '#FFFFFF'],
        'nav_icon' => ['var' => '--hd-icon', 'label' => 'Icon color (full website)', 'type' => 'color', 'default' => '#FFFFFF'],
        'cart_badge' => ['var' => '--hd-badge', 'label' => 'Cart count icon color', 'type' => 'color', 'default' => '#FFC107'],
        'nav_hover_bg' => ['var' => '--hd-nav-hover', 'label' => 'Navigation hover background (dynamic)', 'type' => 'color', 'default' => '#1E2F73'],
        'search_field' => ['var' => '--hd-search-bg', 'label' => 'Search text field color', 'type' => 'color', 'default' => '#FFFFFF'],
        'search_btn' => ['var' => '--hd-search-btn', 'label' => 'Search button color', 'type' => 'color', 'default' => '#F5A623'],
        'search_icon' => ['var' => '--hd-search-icon', 'label' => 'Search icon color', 'type' => 'color', 'default' => '#FFFFFF'],
        'footer_bg' => ['var' => '--ft-bg', 'label' => 'Footer color', 'type' => 'color', 'default' => '#12225B'],
        'footer_text' => ['var' => '--ft-text', 'label' => 'Footer text color', 'type' => 'color', 'default' => '#FFFFFF'],
        'footer_nav' => ['var' => '--ft-nav', 'label' => 'Footer navigation text color', 'type' => 'color', 'default' => '#FFFFFF'],
        'social_icon' => ['var' => '--ft-social', 'label' => 'Social media icon color', 'type' => 'color', 'default' => '#FFFFFF'],
        'body_font' => ['var' => '--body-font', 'label' => 'Full body font family', 'type' => 'font', 'default' => 'segoe'],
        'flash_bg' => ['var' => '--flash-bg', 'label' => 'Flash sales card background', 'type' => 'color', 'default' => '#EA580C'],
        'flash_text' => ['var' => '--flash-text', 'label' => 'Flash sales font color', 'type' => 'color', 'default' => '#FFFFFF'],
        'flash_font' => ['var' => '--flash-font', 'label' => 'Flash sales font family', 'type' => 'font', 'default' => ''],
        'cart_btn_bg' => ['var' => '--cart-btn-bg', 'label' => 'Button background (Add to Cart / Order Confirm / all action buttons)', 'type' => 'color', 'default' => null, 'optional' => true],
        'cart_btn_text' => ['var' => '--cart-btn-text', 'label' => 'Button text color (all action buttons)', 'type' => 'color', 'default' => null, 'optional' => true],
        'cart_btn_icon' => ['var' => '--cart-btn-icon', 'label' => 'Button icon color (empty = Icon color)', 'type' => 'color', 'default' => null, 'optional' => true],
        'buy_btn_bg' => ['var' => '--buy-btn-bg', 'label' => 'Buy Now button background (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'buy_btn_text' => ['var' => '--buy-btn-text', 'label' => 'Buy Now button text color (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'wa_btn_bg' => ['var' => '--wa-btn-bg', 'label' => 'WhatsApp button background (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'wa_btn_text' => ['var' => '--wa-btn-text', 'label' => 'WhatsApp button text color (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'call_btn_bg' => ['var' => '--call-btn-bg', 'label' => 'Call to Order button background (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'call_btn_text' => ['var' => '--call-btn-text', 'label' => 'Call to Order button text color (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'cat_title_bg' => ['var' => '--cat-title-bg', 'label' => 'All Categories title background (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
        'cat_title_text' => ['var' => '--cat-title-text', 'label' => 'All Categories title text color (empty = unchanged)', 'type' => 'color', 'default' => null, 'optional' => true],
    ];

    public static function get(string $key)
    {
        $def = self::FIELDS[$key]['default'] ?? null;

        return setting('style.'.$key, $def);
    }

    public static function put(string $key, $value): void
    {
        \App\Models\Settings::updateOrCreate(['key' => 'style.'.$key], ['value' => $value]);
    }

    public static function fontStack(string $key): string
    {
        return self::FONTS[$key]['stack'] ?? self::FONTS['segoe']['stack'];
    }

    /** :root variable map (resolved values, fonts mapped to stacks). Optional keys stay out when unset. */
    public static function cssVars(): array
    {
        $vars = [];
        foreach (self::FIELDS as $key => $meta) {
            $value = self::get($key);
            if (! empty($meta['optional']) && ($value === null || $value === '')) {
                continue;
            }
            if ($meta['type'] === 'font') {
                $value = $value === '' || $value === null
                    ? self::fontStack((string) self::get('body_font'))
                    : self::fontStack((string) $value);
            }
            $vars[$meta['var']] = $value;
        }

        return $vars;
    }
}
