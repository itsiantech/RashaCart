<?php

namespace App\Services;

use App\Models\AttributeValue;
use Illuminate\Support\Str;

/**
 * Builds variant combinations (cartesian product) from selected
 * attribute values, with deterministic SKU patterns and dedup keys.
 */
class VariantGenerator
{
    /**
     * @param array $selection [attributeId => [valueId, ...]]
     * @return array list of combos, each [attributeId => valueId] sorted by attributeId
     */
    public function combinations(array $selection): array
    {
        $selection = array_filter($selection, fn ($v) => ! empty($v));

        if (empty($selection)) {
            return [];
        }

        ksort($selection);

        $combos = [[]];
        foreach ($selection as $attrId => $valueIds) {
            $next = [];
            foreach ($combos as $combo) {
                foreach (array_values(array_unique($valueIds)) as $valueId) {
                    $next[] = $combo + [$attrId => $valueId];
                }
            }
            $combos = $next;
        }

        return $combos;
    }

    /** Stable key for a combo, used to skip already-generated variants. */
    public function comboKey(array $combo): string
    {
        ksort($combo);

        return collect($combo)->map(fn ($v, $k) => "{$k}:{$v}")->implode('|');
    }

    /**
     * SKU like BASE-RED-M. Codes come from value slugs (first 3-8 chars).
     */
    public function skuFor(string $baseSku, array $combo): string
    {
        ksort($combo);
        $codes = [];

        foreach ($combo as $valueId) {
            $value = AttributeValue::find($valueId);
            $slug = $value?->slug ?: Str::slug($value?->value ?? (string) $valueId);
            $codes[] = strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $slug), 0, 6) ?: 'X');
        }

        $base = strtoupper(preg_replace('/[^A-Za-z0-9-]/', '', $baseSku) ?: 'SKU');

        return substr($base.'-'.implode('-', $codes), 0, 60);
    }
}
