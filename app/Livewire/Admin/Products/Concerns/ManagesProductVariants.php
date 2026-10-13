<?php

namespace App\Livewire\Admin\Products\Concerns;

use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\VariantGenerator;

/**
 * Shared variant-combination builder for Add/Edit Product.
 * Works on the component's $variants rows without touching the
 * legacy single-variant form. Row shape (superset, BC-safe):
 * ['id'?, 'sku', 'variant_price', 'sale_price'?, 'stock'?, 'low_stock_threshold'?,
 *  'is_active'?, 'is_default'?, 'image'?, 'values' => [['attribute_id','attribute_value_id']]]
 */
trait ManagesProductVariants
{
    public $genAttrs = [];

    public $genValues = [];

    public $quickAttrId = null;

    public $quickValue = '';

    public $quickColor = '';

    public $showQuickAdd = false;

    /** Wholesale slabs keyed by variant row id: [rowId => [['min_qty'=>..,'price'=>..]]] */
    public $variantSlabs = [];

    public $slabOpen = null;

    public function rowKey(array $row, $index): string
    {
        return (string) ($row['id'] ?? 'idx_'.$index);
    }

    /** Base SKU for generated variant SKUs. Components without a $sku prop fall back to 'SKU'. */
    protected function variantBaseSku(): string
    {
        $sku = property_exists($this, 'sku') ? $this->sku : null;

        return $sku ?: 'SKU';
    }

    public function toggleSlabs($index): void
    {
        $key = $this->rowKey($this->variants[$index] ?? [], $index);
        $this->slabOpen = $this->slabOpen === $key ? null : $key;

        if (! isset($this->variantSlabs[$key])) {
            $variantId = $this->variants[$index]['id'] ?? null;
            $existing = is_numeric($variantId)
                ? \App\Models\VariantPriceSlab::where('product_variant_id', $variantId)
                    ->orderBy('min_qty')->get()
                    ->map(fn ($s) => ['min_qty' => $s->min_qty, 'price' => $s->price])->toArray()
                : [];
            $this->variantSlabs[$key] = $existing;
        }
    }

    public function addSlabRow($index): void
    {
        $key = $this->rowKey($this->variants[$index] ?? [], $index);
        $this->variantSlabs[$key][] = ['min_qty' => 10, 'price' => null];
    }

    public function removeSlabRow($index, $slabIndex): void
    {
        $key = $this->rowKey($this->variants[$index] ?? [], $index);
        unset($this->variantSlabs[$key][$slabIndex]);
        $this->variantSlabs[$key] = array_values($this->variantSlabs[$key] ?? []);
    }

    /** Persist validated slabs for a saved variant. */
    public function syncVariantSlabs(\App\Models\ProductVariant $variant, array $slabs): void
    {
        $clean = [];
        foreach ($slabs as $slab) {
            $min = (int) ($slab['min_qty'] ?? 0);
            $price = $slab['price'] ?? null;
            if ($min >= 2 && is_numeric($price) && $price >= 0) {
                $clean[$min] = (float) $price;
            }
        }
        ksort($clean);

        $variant->priceSlabs()->delete();
        foreach ($clean as $min => $price) {
            $variant->priceSlabs()->create(['min_qty' => $min, 'price' => $price]);
        }
    }

    public function openQuickAdd($attrId): void
    {
        $this->quickAttrId = (int) $attrId;
        $this->quickValue = '';
        $this->quickColor = '';
        $this->showQuickAdd = true;
    }

    /**
     * Inline "add value" from Add/Edit Product — no page reload.
     * Refreshes the attribute list and pre-selects the new value.
     */
    public function quickAddValue(): void
    {
        $this->validate([
            'quickAttrId' => 'required|exists:attributes,id',
            'quickValue' => 'required|string|min:1|max:255',
            'quickColor' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'quickValue.required' => 'Value-এর নাম দিন।',
        ]);

        $value = \App\Models\AttributeValue::create([
            'attribute_id' => $this->quickAttrId,
            'value' => trim($this->quickValue),
            'slug' => \Illuminate\Support\Str::slug(trim($this->quickValue)),
            'color_hex' => $this->quickColor ?: null,
        ]);

        $this->productAttributes = \App\Models\Attribute::with('values')->get();
        $this->genAttrs[$this->quickAttrId] = true;
        $this->genValues[$this->quickAttrId] = array_values(
            array_unique(array_merge($this->genValues[$this->quickAttrId] ?? [], [$value->id]))
        );

        $this->showQuickAdd = false;
        $this->quickValue = '';
        $this->quickColor = '';
        $this->dispatch('success', message: "Value '{$value->value}' যোগ হয়েছে।");
    }

    public function toggleGenAttr($attrId): void
    {
        $attrId = (int) $attrId;
        if (isset($this->genAttrs[$attrId])) {
            unset($this->genAttrs[$attrId], $this->genValues[$attrId]);
        } else {
            $this->genAttrs[$attrId] = true;
            $this->genValues[$attrId] = [];
        }
    }

    public function toggleGenValue($attrId, $valueId): void
    {
        $attrId = (int) $attrId;
        $valueId = (int) $valueId;
        $current = $this->genValues[$attrId] ?? [];
        if (in_array($valueId, $current)) {
            $this->genValues[$attrId] = array_values(array_diff($current, [$valueId]));
        } else {
            $this->genValues[$attrId] = array_values(array_unique(array_merge($current, [$valueId])));
        }
    }

    public function variantComboKey(array $row): string
    {
        $combo = [];
        foreach ($row['values'] ?? [] as $v) {
            $combo[(int) $v['attribute_id']] = (int) $v['attribute_value_id'];
        }

        return app(VariantGenerator::class)->comboKey($combo);
    }

    /**
     * Build all combinations from selected values, skipping ones
     * that already exist as rows. Never duplicates.
     */
    public function generateVariants(): void
    {
        $selection = [];
        foreach (array_keys($this->genAttrs) as $attrId) {
            if (! empty($this->genValues[$attrId])) {
                $selection[$attrId] = $this->genValues[$attrId];
            }
        }

        if (empty($selection)) {
            $this->dispatch('error', message: 'অন্তত একটি attribute-এর value সিলেক্ট করুন।');

            return;
        }

        $gen = app(VariantGenerator::class);
        $combos = $gen->combinations($selection);

        if (count($combos) > 200) {
            $this->dispatch('error', message: 'অনেক বেশি combination ('.count($combos).')। Value কমিয়ে আবার চেষ্টা করুন (সর্বোচ্চ ২০০)।');

            return;
        }

        $existing = [];
        foreach ($this->variants as $row) {
            if (! empty($row['_delete'])) {
                continue;
            }
            $existing[$this->variantComboKey($row)] = true;
        }

        // Also skip combinations already saved in DB for this product
        // (Edit page) — otherwise save crashes on the unique SKU index.
        $rowIds = [];
        foreach ($this->variants as $row) {
            if (isset($row['id']) && is_numeric($row['id'])) {
                $rowIds[] = (int) $row['id'];
            }
        }
        $dbSkus = [];
        $productId = property_exists($this, 'productId') ? $this->productId : null;
        if ($productId) {
            $saved = \App\Models\ProductVariant::where('product_id', $productId)
                ->with('values')->get();
            foreach ($saved as $sv) {
                $combo = [];
                foreach ($sv->values as $vv) {
                    $combo[(int) $vv->attribute_id] = (int) $vv->attribute_value_id;
                }
                if ($combo) {
                    $existing[$gen->comboKey($combo)] = true;
                }
                $dbSkus[strtolower($sv->sku)] = $sv->id;
            }
        }

        $hasDefault = collect($this->variants)->contains(fn ($r) => ! empty($r['is_default']));
        $added = 0;
        $skipped = 0;
        foreach ($combos as $combo) {
            if (isset($existing[$gen->comboKey($combo)])) {
                $skipped++;
                continue;
            }

            $values = [];
            foreach ($combo as $attrId => $valueId) {
                $values[] = ['attribute_id' => $attrId, 'attribute_value_id' => $valueId];
            }

            $sku = $gen->skuFor($this->variantBaseSku(), $combo);
            // Keep SKU unique among rows AND saved variants (except own rows)
            $suffix = 1;
            $candidate = $sku;
            $rowSkus = array_column($this->variants, 'sku');
            while (in_array($candidate, $rowSkus)
                || (isset($dbSkus[strtolower($candidate)]) && ! in_array($dbSkus[strtolower($candidate)], $rowIds))) {
                $candidate = $sku.'-'.$suffix++;
            }

            $this->variants[] = [
                'id' => 'new_'.uniqid(),
                'sku' => $candidate,
                // Empty = auto (product price). Admin types only when different.
                'variant_price' => null,
                'sale_price' => null,
                'stock' => 0,
                'low_stock_threshold' => 5,
                'is_active' => true,
                'is_default' => ! $hasDefault && $added === 0,
                'image' => null,
                'values' => $values,
            ];
            $existing[$gen->comboKey($combo)] = true;
            $added++;
        }

        $this->dispatch('success', message: "{$added}টি variant তৈরি হয়েছে।".($skipped > 0 ? " {$skipped}টি আগে থেকেই ছিল।" : ''));
    }

    public function bulkSetVariantPrice($price): void
    {
        foreach ($this->variants as $i => $row) {
            $this->variants[$i]['variant_price'] = $price === '' || $price === null ? null : $price;
            $this->variants[$i]['sale_price'] = $price === '' || $price === null ? null : $price;
        }
    }

    /** Clear all variant prices → every row follows the product price. */
    public function autoVariantPrices(): void
    {
        foreach ($this->variants as $i => $row) {
            $this->variants[$i]['variant_price'] = null;
            $this->variants[$i]['sale_price'] = null;
        }
        $this->dispatch('success', message: 'সব variant এখন product দাম follow করবে (auto)।');
    }

    /** Copy the main product stock number into every variant row. */
    public function copyProductStockToVariants(): void
    {
        $stock = (int) ($this->stock ?? 0);
        foreach ($this->variants as $i => $row) {
            $this->variants[$i]['stock'] = $stock;
        }
        $this->dispatch('success', message: "Product stock ({$stock}) সব variant-এ বসানো হয়েছে।");
    }

    public function bulkSetVariantStock($qty): void
    {
        foreach ($this->variants as $i => $row) {
            $this->variants[$i]['stock'] = (int) $qty;
        }
    }

    public function bulkSetVariantActive($active): void
    {
        foreach ($this->variants as $i => $row) {
            $this->variants[$i]['is_active'] = (bool) $active;
        }
    }

    public function variantOrderedCount(int $variantId): int
    {
        return OrderItem::where('product_variant_id', $variantId)->count();
    }

    /** Normalize '' → null for optional money/stock fields (empty = auto-follow). */
    protected function nullIfEmpty($v)
    {
        return ($v === '' || $v === null) ? null : $v;
    }

    /**
     * Server-side validation for generated rows. Returns [index => [messages]] (Bangla).
     */
    public function validateVariantRows(?int $productId = null): array
    {
        $errors = [];
        $seenSku = [];
        $seenCombo = [];

        foreach ($this->variants as $i => $row) {
            // Rows flagged for deletion are ignored by validation
            if (! empty($row['_delete'])) {
                continue;
            }

            $rowErrors = [];
            $label = 'Row '.($i + 1);

            // SKU
            $sku = trim($row['sku'] ?? '');
            if ($sku === '') {
                $rowErrors[] = "{$label}: SKU দিতে হবে।";
            } else {
                $lower = strtolower($sku);
                if (isset($seenSku[$lower])) {
                    $rowErrors[] = "{$label}: SKU '{$sku}' দুইবার ব্যবহার হয়েছে।";
                }
                $seenSku[$lower] = true;

                // SKU must be globally unique (same product included — only the
                // row's own saved variant is excluded). Catches DB duplicates
                // with a Bangla message instead of a SQL crash.
                $q = ProductVariant::where('sku', $sku);
                if (isset($row['id']) && is_numeric($row['id'])) {
                    $q->where('id', '!=', $row['id']);
                }
                if ($q->exists()) {
                    $rowErrors[] = "{$label}: SKU '{$sku}' ইতিমধ্যে ব্যবহার হয়েছে। SKU বদলান বা duplicate row মুছুন।";
                }
            }

            // Price: empty = auto (product price). Explicit 0 = ৳0.
            $vp = $row['variant_price'] ?? null;
            if ($vp !== null && $vp !== '' && (! is_numeric($vp) || $vp < 0)) {
                $rowErrors[] = "{$label}: দাম খালি রাখুন (auto) অথবা ০ বা বেশি লিখুন।";
            }

            // Stock
            if (! isset($row['stock']) || filter_var($row['stock'], FILTER_VALIDATE_INT) === false || (int) $row['stock'] < 0) {
                $rowErrors[] = "{$label}: স্টক পূর্ণসংখ্যা (০ বা বেশি) হতে হবে।";
            }

            // Values
            if (empty($row['values'])) {
                $rowErrors[] = "{$label}: অন্তত একটি attribute value থাকতে হবে।";
            } else {
                $key = $this->variantComboKey($row);
                if (isset($seenCombo[$key])) {
                    $rowErrors[] = "{$label}: এই combination আগেই আছে (duplicate)।";
                }
                $seenCombo[$key] = true;
            }

            if ($rowErrors) {
                $errors[$i] = $rowErrors;
            }
        }

        return $errors;
    }
}
