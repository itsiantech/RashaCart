<?php

namespace App\Services;

/**
 * Customer Login/Register settings accessor.
 *
 * Keys (stored in `settings` table, cached via Settings::getAllSettings()):
 *  - customer_login_enabled    : '1' | '0'   (default '1')
 *  - customer_login_identifier : 'email' | 'phone' | 'both' (default 'both')
 *  - customer_google_enabled   : '1' | '0'   (default '1')
 */
class CustomerAuthSettings
{
    public static function enabled(): bool
    {
        return (string) setting('customer_login_enabled', '1') === '1';
    }

    /** @return 'email'|'phone'|'both' */
    public static function identifier(): string
    {
        $v = (string) setting('customer_login_identifier', 'both');
        return in_array($v, ['email', 'phone', 'both'], true) ? $v : 'both';
    }

    public static function googleEnabled(): bool
    {
        return (string) setting('customer_google_enabled', '1') === '1';
    }

    public static function wantsEmail(): bool
    {
        return in_array(self::identifier(), ['email', 'both'], true);
    }

    public static function wantsPhone(): bool
    {
        return in_array(self::identifier(), ['phone', 'both'], true);
    }

    /** Validate that a return URL is a safe internal path (no open-redirect). */
    public static function safeReturnUrl(?string $url, string $fallback = '/'): string
    {
        if (! $url) {
            return $fallback;
        }
        // Allow only relative paths starting with single "/" (no "//", no scheme).
        if (! str_starts_with($url, '/') || str_starts_with($url, '//')) {
            return $fallback;
        }
        // Block protocol tricks and CRLF.
        if (preg_match('#[:\\\\\r\n]#', $url)) {
            return $fallback;
        }
        // Never send customers to admin/vendor/auth internals.
        foreach (['/admin', '/vendor', '/login', '/register', '/auth/'] as $blocked) {
            if (str_starts_with($url, $blocked)) {
                return $fallback;
            }
        }
        return $url;
    }
}
