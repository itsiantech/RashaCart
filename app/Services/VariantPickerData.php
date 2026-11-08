<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

/**
 * Builds the data a variant picker needs (groups, per-value availability,
 * options) so the product page modal and the checkout popup stay identical.
 * Prices/stock are still re-validated by CombinationResolver on submit.
 */
class VariantPickerData
{
    public static function for(Product $product): array
    {
        $variants = ProductVariant::with([
            'variantAttributeValues.attribute',
            'variantAttributeValues.attributeValue',
            'inventories',
        ])
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $names = [];
        foreach ($variants as $variant) {
            foreach ($variant->variantAttributeValues as $vav) {
                $name = $vav->attribute?->name;

                if ($name && ! in_array($name, $names, true)) {
                    $names[] = $name;
                }
            }
        }

        // Same ordering as the product page: color, size, then the rest.
        usort($names, function ($a, $b) {
            $rank = function ($name) {
                $lower = strtolower($name);

                if (str_contains($lower, 'color') || str_contains($lower, 'colour')) {
                    return 0;
                }

                return str_contains($lower, 'size') ? 1 : 2;
            };

            return $rank($a) <=> $rank($b) ?: strcmp($a, $b);
        });

        $required = $product->productAttributes()->get()
            ->where('pivot.is_required', true)
            ->pluck('name')
            ->all();

        $groups = [];
        foreach ($names as $name) {
            $values = [];
            foreach ($variants as $variant) {
                foreach ($variant->variantAttributeValues as $vav) {
                    if ($vav->attribute?->name !== $name) {
                        continue;
                    }

                    $value = $vav->attributeValue?->value;

                    if ($value === null || $value === '' || isset($values[$value])) {
                        continue;
                    }

                    $values[$value] = [
                        'value' => $value,
                        'image' => self::storage($variant->image)
                            ?: self::storage($vav->attributeValue?->swatch_image),
                        'color_hex' => $vav->attributeValue?->color_hex,
                    ];
                }
            }

            $isColor = str_contains(strtolower($name), 'color') || str_contains(strtolower($name), 'colour');

            $groups[] = [
                'name' => $name,
                'type' => $isColor ? 'swatch' : 'chip',
                // Legacy products without pivot rows: every group is required.
                'required' => empty($required) || in_array($name, $required, true),
                'values' => array_values($values),
            ];
        }

        $variantRows = [];
        // Products without any inventory ledger behave like the resolver:
        // stock is not tracked, so nothing is disabled for being "out".
        $hasLedger = \App\Models\Inventory::where('product_id', $product->id)->exists();

        foreach ($variants as $variant) {
            $attrs = [];
            foreach ($variant->variantAttributeValues as $vav) {
                if ($vav->attribute?->name) {
                    $attrs[$vav->attribute->name] = $vav->attributeValue?->value;
                }
            }

            $stock = $hasLedger
                ? (int) $variant->inventories->sum(fn ($row) => in_array($row->type, ['in', 'return', 'adjustment'], true)
                    ? (int) $row->quantity
                    : -(int) $row->quantity)
                : PHP_INT_MAX;

            $variantRows[] = [
                'id' => (int) $variant->id,
                'attrs' => $attrs,
                'image' => self::storage($variant->image),
                'stock' => $stock,
            ];
        }

        $options = [];

        if ($product->has_image_options) {
            foreach ($product->imageOptions()->ordered()->get() as $option) {
                $ownStock = ImageOptionStock::hasOwnStock($option->id);
                $inStock = ! $ownStock || ImageOptionStock::currentStock($option->id) > 0;

                $options[] = [
                    'id' => (int) $option->id,
                    'label' => (string) $option->label,
                    'image' => (string) self::storage($option->image),
                    'available' => $option->is_active && $inStock,
                    'reason' => ! $option->is_active
                        ? 'এই অপশনটি এখন নেই।'
                        : ($inStock ? null : '"'.$option->label.'" sold out।'),
                ];
            }
        }

        return [
            'groups' => $groups,
            'variants' => $variantRows,
            'options' => $options,
            'option_required' => (bool) $product->require_image_option && ! empty($options),
        ];
    }

    public static function groupNames(array $data): array
    {
        return array_column($data['groups'] ?? [], 'name');
    }

    /** Exact match on every group; null when a group is unpicked or nothing matches. */
    public static function match(array $data, array $picks): ?array
    {
        $names = self::groupNames($data);

        if (empty($names)) {
            return null;
        }

        foreach ($data['variants'] as $variant) {
            $matches = true;
            foreach ($names as $name) {
                $picked = $picks[$name] ?? null;

                if ($picked === null || $picked === '' || ($variant['attrs'][$name] ?? null) !== $picked) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * True when choosing $value for $name (keeping the other picks) still
     * leaves an in-stock variant.
     */
    public static function valueAvailable(array $data, array $picks, string $name, string $value): bool
    {
        foreach ($data['variants'] as $variant) {
            if (($variant['attrs'][$name] ?? null) !== $value || $variant['stock'] <= 0) {
                continue;
            }

            $ok = true;
            foreach ($picks as $otherName => $otherValue) {
                if ($otherName === $name || $otherValue === null || $otherValue === '') {
                    continue;
                }

                if (($variant['attrs'][$otherName] ?? null) !== $otherValue) {
                    $ok = false;
                    break;
                }
            }

            if ($ok) {
                return true;
            }
        }

        return false;
    }

    /** [group name => [value => bool]] for the current picks. */
    public static function availability(array $data, array $picks): array
    {
        $map = [];

        foreach ($data['groups'] ?? [] as $group) {
            foreach ($group['values'] as $value) {
                $map[$group['name']][$value['value']] = self::valueAvailable(
                    $data,
                    $picks,
                    $group['name'],
                    $value['value']
                );
            }
        }

        return $map;
    }

    public static function pickedFromVariant(array $data, $variantId): array
    {
        $variantId = (int) $variantId;

        foreach ($data['variants'] ?? [] as $variant) {
            if ($variant['id'] === $variantId) {
                return $variant['attrs'];
            }
        }

        return [];
    }

    protected static function storage(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}
