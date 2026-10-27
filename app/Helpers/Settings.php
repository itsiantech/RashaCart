<?php

use App\Models\Settings;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Settings::getAllSettings()[$key] ?? $default;
    }
}

if (! function_exists('delivery_cities')) {
    /**
     * Admin-configured city-wise delivery charges.
     * Falls back to the legacy inside/outside charges until the admin saves a list.
     *
     * @return array<int, array{city: string, charge: float}>
     */
    function delivery_cities(): array
    {
        $raw = setting('delivery_cities');

        $rows = ($raw === null || $raw === '' || $raw === false)
            ? [
                ['city' => 'Inside City', 'charge' => setting('dc_inside', 60)],
                ['city' => 'Outside City', 'charge' => setting('dc_outside', 120)],
            ]
            : json_decode((string) $raw, true);

        return collect(is_array($rows) ? $rows : [])
            ->filter(fn ($row) => is_array($row))
            ->map(fn ($row) => [
                'city' => trim((string) ($row['city'] ?? '')),
                'charge' => $row['charge'] ?? null,
            ])
            ->filter(fn ($row) => $row['city'] !== '' && is_numeric($row['charge']))
            ->map(fn ($row) => ['city' => $row['city'], 'charge' => (float) $row['charge']])
            ->values()
            ->all();
    }
}
