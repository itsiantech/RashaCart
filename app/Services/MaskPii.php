<?php

namespace App\Services;

/** Masks PII before anything leaves the app (logs, AI payloads). */
class MaskPii
{
    protected static array $keys = [
        'email', 'phone', 'mobile', 'name', 'first_name', 'last_name',
        'address', 'city', 'em', 'ph', 'fn', 'ln', 'external_id',
    ];

    public static function mask($value)
    {
        if (is_array($value)) {
            return self::maskArray($value);
        }

        return $value;
    }

    public static function maskArray(array $data): array
    {
        foreach ($data as $k => $v) {
            if (in_array(strtolower((string) $k), self::$keys, true)) {
                $data[$k] = '[MASKED]';
            } elseif (is_array($v)) {
                $data[$k] = self::maskArray($v);
            } elseif (is_string($v) && self::looksLikePhoneOrEmail($v)) {
                $data[$k] = '[MASKED]';
            }
        }

        return $data;
    }

    protected static function looksLikePhoneOrEmail(string $v): bool
    {
        if (filter_var($v, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        $digits = preg_replace('/[^0-9]/', '', $v);

        return strlen($digits) >= 10 && strlen($digits) <= 15 && is_numeric($digits);
    }
}
