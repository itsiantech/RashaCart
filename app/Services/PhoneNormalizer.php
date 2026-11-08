<?php

namespace App\Services;

class PhoneNormalizer
{
    /**
     * Reduce a Bangladeshi mobile number to its 11-digit form.
     * Accepts +8801XXXXXXXXX, 8801XXXXXXXXX, 01XXXXXXXXX and 1XXXXXXXXX,
     * with spaces, dashes or brackets anywhere in between.
     */
    public static function normalize(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw) ?? '';

        if (str_starts_with($digits, '0088')) {
            $digits = substr($digits, 4);
        } elseif (str_starts_with($digits, '88') && strlen($digits) > 11) {
            $digits = substr($digits, 2);
        }

        // 10 digits starting with 1 → operator prefix 0 was dropped
        if (strlen($digits) === 10 && $digits[0] === '1') {
            $digits = '0'.$digits;
        }

        return $digits !== '' ? $digits : null;
    }

    public static function isValid(?string $raw): bool
    {
        return (bool) preg_match('/^01[3-9]\d{8}$/', (string) self::normalize($raw));
    }
}
