<?php

namespace App\Livewire\Admin\Products\Concerns;

use App\Models\ImageOption;
use App\Models\ImageOptionStockMovement;
use App\Models\OrderItem;
use App\Services\ImageOptionStock;

/**
 * Image-options layer for Add/Edit Product.
 * Staged rows: ['key','id','label','image','preview','price','sale_price',
 *               'stock','sort_order','is_active','new_upload'?]
 */
trait ManagesImageOptions
{
    public $imageOptions = [];

    public $imageOptionCounter = 0;

    public function loadImageOptions(int $productId): void
    {
        $this->imageOptions = ImageOption::where('product_id', $productId)
            ->ordered()
            ->get()
            ->map(function ($o) {
                $hasLedger = ImageOptionStock::hasOwnStock($o->id);

                return [
                    'key' => 'db_'.$o->id,
                    'id' => $o->id,
                    'label' => $o->label,
                    'image' => $o->image,
                    'preview' => asset('storage/'.$o->image),
                    'price' => $o->price,
                    'sale_price' => $o->sale_price,
                    // Empty = no own stock → variant/product stock applies
                    'stock' => $hasLedger ? ImageOptionStock::currentStock($o->id) : '',
                    'has_ledger' => $hasLedger,
                    'sort_order' => $o->sort_order,
                    'is_active' => (bool) $o->is_active,
                ];
            })
            ->toArray();
    }

    public function addImageOption(): void
    {
        $this->imageOptions[] = [
            'key' => 'new_'.($this->imageOptionCounter++),
            'id' => null,
            'label' => '',
            'image' => null,
            'preview' => null,
            'price' => null,
            'sale_price' => null,
            'stock' => '',
            'has_ledger' => false,
            'sort_order' => count($this->imageOptions),
            'is_active' => true,
        ];
    }

    /** Bulk set option price (sale untouched). */
    public function bulkSetOptionPrice($price): void
    {
        foreach ($this->imageOptions as $i => $row) {
            $this->imageOptions[$i]['price'] = $price;
        }
    }

    /** Bulk set option stock (explicit number → own ledger). */
    public function bulkSetOptionStock($qty): void
    {
        foreach ($this->imageOptions as $i => $row) {
            $this->imageOptions[$i]['stock'] = (int) $qty;
        }
    }

    public function removeImageOption($index): void
    {
        if (! isset($this->imageOptions[$index])) {
            return;
        }

        $row = $this->imageOptions[$index];

        if (! empty($row['id']) && is_numeric($row['id'])) {
            $used = OrderItem::where('image_option_id', $row['id'])->count();
            if ($used > 0) {
                $this->dispatch('error', message: "এই option {$used}টি order-এ আছে। Delete না করে Deactivate করুন।");

                return;
            }
            ImageOption::where('id', $row['id'])->delete();
            $this->dispatch('success', message: 'Option মুছে ফেলা হয়েছে।');
        } elseif (! empty($row['image'])) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($row['image']);
        }

        unset($this->imageOptions[$index]);
        $this->imageOptions = array_values($this->imageOptions);
    }

    /** Validate staged option rows (Bangla). Returns list of messages. */
    public function validateImageOptions(): array
    {
        $errors = [];
        foreach ($this->imageOptions as $i => $row) {
            $label = 'Option '.($i + 1);
            if (trim($row['label'] ?? '') === '') {
                $errors[] = "{$label}: label দিতে হবে।";
            }
            if (empty($row['image']) && empty($row['new_upload'])) {
                $errors[] = "{$label}: ছবি দিতে হবে।";
            }
            foreach (['price', 'sale_price'] as $f) {
                if (isset($row[$f]) && $row[$f] !== '' && $row[$f] !== null
                    && (! is_numeric($row[$f]) || $row[$f] < 0)) {
                    $errors[] = "{$label}: দাম ০ বা বেশি হতে হবে।";
                }
            }
            if (! isset($row['stock']) || $row['stock'] === '' || $row['stock'] === null) {
                // Empty = follow variant/product stock (no own ledger). Allowed.
            } elseif (filter_var($row['stock'], FILTER_VALIDATE_INT) === false
                || (int) $row['stock'] < 0) {
                $errors[] = "{$label}: স্টক খালি রাখুন (auto) অথবা পূর্ণসংখ্যা (০ বা বেশি) লিখুন।";
            }
        }

        return $errors;
    }

    /**
     * Persist staged rows for a product (create/update/delete + stock ledger).
     * Returns non-blocking Bangla warnings (e.g. cleared stock kept due to open orders).
     */
    public function persistImageOptions(int $productId): array
    {
        $warnings = [];
        $keepIds = [];

        foreach (array_values($this->imageOptions) as $sort => $row) {
            $path = $row['image'] ?? null;
            if (! empty($row['new_upload'])) {
                $path = $row['new_upload']->store('products/image-options', 'public');
            }

            if (! empty($row['id']) && is_numeric($row['id'])) {
                $option = ImageOption::where('id', $row['id'])->where('product_id', $productId)->first();
                if (! $option) {
                    continue;
                }
                $option->update([
                    'label' => trim($row['label']),
                    'image' => $path,
                    'price' => $row['price'] === '' ? null : $row['price'],
                    'sale_price' => $row['sale_price'] === '' ? null : $row['sale_price'],
                    'sort_order' => $sort,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]);
                // Stock: number → ledger; cleared → drop own ledger (follow variant),
                // unless open order movements exist (then keep + warn).
                if ($row['stock'] === '' || $row['stock'] === null || ! isset($row['stock'])) {
                    $openOuts = ImageOptionStockMovement::where('image_option_id', $option->id)
                        ->where('type', 'out')
                        ->whereNotExists(function ($q) {
                            $q->selectRaw('1')->from('image_option_stock_movements as r')
                                ->whereColumn('r.order_item_id', 'image_option_stock_movements.order_item_id')
                                ->where('r.type', 'return');
                        })
                        ->exists();
                    if ($openOuts) {
                        $warnings[] = "'{$option->label}'-এর pending order আছে বলে stock আগেরটাই রাখা হয়েছে।";
                    } else {
                        ImageOptionStockMovement::where('image_option_id', $option->id)->delete();
                    }
                } else {
                    ImageOptionStock::adjustTo($option, (int) $row['stock']);
                }
                $keepIds[] = $option->id;
            } else {
                $option = ImageOption::create([
                    'product_id' => $productId,
                    'label' => trim($row['label']),
                    'image' => $path,
                    'price' => $row['price'] === '' ? null : $row['price'],
                    'sale_price' => $row['sale_price'] === '' ? null : $row['sale_price'],
                    'sort_order' => $sort,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]);
                // Ledger only when a stock number was entered (else follow variant)
                if (isset($row['stock']) && $row['stock'] !== '' && $row['stock'] !== null) {
                    ImageOptionStock::setInitial($option, (int) $row['stock']);
                }
                $keepIds[] = $option->id;
            }
        }

        ImageOption::where('product_id', $productId)->whereNotIn('id', $keepIds)->delete();

        return $warnings;
    }

    /**
     * Live preview rows: every variant × option combination with the exact
     * effective price/stock rules the customer will get.
     */
    public function previewCombinations(): array
    {
        $variants = collect($this->variants ?? [])
            ->reject(fn ($r) => ! empty($r['_delete']))
            ->values();
        $options = collect($this->imageOptions ?? [])->values();
        $productPrice = (float) ($this->sale_price ?? $this->price ?? 0);

        $variantRows = $variants->isNotEmpty()
            ? $variants->map(fn ($r) => [
                'sku' => $r['sku'] ?? '—',
                'label' => collect($r['values'] ?? [])->map(function ($v) {
                    $a = \App\Models\Attribute::find($v['attribute_id'] ?? null);
                    $av = \App\Models\AttributeValue::find($v['attribute_value_id'] ?? null);

                    return ($a->name ?? '?').': '.($av->value ?? '?');
                })->implode(', '),
                // Empty = auto (product price)
                'price' => ($r['sale_price'] ?? $r['variant_price'] ?? null) !== null
                    && ($r['sale_price'] ?? $r['variant_price']) !== ''
                    ? (float) ($r['sale_price'] ?? $r['variant_price']) : $productPrice,
                'stock' => (int) ($r['stock'] ?? 0),
            ])->all()
            : [['sku' => ($this->sku ?? 'SKU'), 'label' => '—', 'price' => $productPrice, 'stock' => (int) ($this->stock ?? 0)]];

        $optionRows = $options->isNotEmpty()
            ? $options->map(fn ($o) => [
                'label' => $o['label'] ?: '—',
                'price' => ($o['sale_price'] ?? $o['price']) !== null && ($o['sale_price'] ?? $o['price']) !== ''
                    ? (float) ($o['sale_price'] ?? $o['price']) : null,
                'stock' => isset($o['stock']) && $o['stock'] !== '' ? (int) $o['stock'] : null,
            ])->all()
            : [['label' => '—', 'price' => null, 'stock' => null]];

        $rows = [];
        foreach ($variantRows as $v) {
            foreach ($optionRows as $o) {
                $rows[] = [
                    'sku' => $v['sku'].($o['label'] !== '—' ? ' / '.$o['label'] : ''),
                    'variant' => $v['label'],
                    'option' => $o['label'],
                    'price' => $o['price'] ?? $v['price'],
                    'stock' => $o['stock'] !== null ? min($v['stock'], $o['stock']) : $v['stock'],
                ];
            }
        }

        return $rows;
    }
}
