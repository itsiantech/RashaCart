<?php

use App\Models\Category;
use App\Models\Page;
use App\Models\Settings;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Settings::getAllSettings()[$key] ?? $default;
    }
}

if (! function_exists('header_menu')) {
    /**
     * Resolve the admin-built header menu (Settings → Menu Settings) into a
     * nested list of [label, url, children]. Items whose page/category was
     * deleted or deactivated are dropped so the storefront never links to 404s.
     *
     * @return array<int, array{label: string, url: string, type: string, children: array}>
     */
    function header_menu(): array
    {
        $raw = setting('header_menu');
        $tree = is_string($raw) && $raw !== '' ? json_decode($raw, true) : [];

        if (! is_array($tree) || $tree === []) {
            return [];
        }

        $pages = Page::query()->get(['id', 'title', 'slug', 'status'])->keyBy('id');
        $categories = Category::query()->get(['id', 'name', 'slug', 'status'])->keyBy('id');

        $resolve = function (array $items) use (&$resolve, $pages, $categories): array {
            $rows = [];

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $type = $item['type'] ?? '';
                $refId = (int) ($item['ref_id'] ?? 0);

                if ($type === 'page') {
                    $page = $pages->get($refId);
                    if (! $page || ! $page->status) {
                        continue;
                    }
                    $label = $page->title;
                    $url = route('public.page', $page->slug);
                } elseif ($type === 'category') {
                    $category = $categories->get($refId);
                    if (! $category || ! $category->status) {
                        continue;
                    }
                    $label = $category->name;
                    $url = route('frontend.category.products.view', $category->slug);
                } else {
                    continue;
                }

                $rows[] = [
                    'label' => $label,
                    'url' => $url,
                    'type' => $type,
                    'children' => $resolve(is_array($item['children'] ?? null) ? $item['children'] : []),
                ];
            }

            return $rows;
        };

        return $resolve($tree);
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
