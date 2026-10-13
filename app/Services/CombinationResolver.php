<?php

namespace App\Services;

use App\Models\ImageOption;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;

class CombinationException extends \Exception {}

class ResolvedCombination
{
    public function __construct(
        public readonly Product $product,
        public readonly ?ProductVariant $variant,
        public readonly ?ImageOption $option,
        public readonly float $unitPrice,
        public readonly int $available,
        public readonly string $contentId,
        public readonly ?string $variantLabel,
        public readonly ?string $optionLabel,
    ) {}
}

/**
 * Validates + resolves a (variant, image-option) combination server-side.
 * Used by add-to-cart, cart update and checkout — the browser is never trusted.
 * All failures throw CombinationException with Bangla messages.
 */
class CombinationResolver
{
    /**
     * @param bool $lock SELECT ... FOR UPDATE locked rows (checkout only), in
     *                   product → variant → option order to avoid deadlocks.
     */
    public static function resolve(
        Product $product,
        $variantId = null,
        $optionId = null,
        int $qty = 1,
        bool $lock = false
    ): ResolvedCombination {
        $qty = max(1, (int) $qty);

        if ($lock) {
            $product = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();
        }

        $attrsOn = function_exists('setting') ? (bool) setting('attribute_system', true) : true;
        $useVariants = $attrsOn && $product->has_attributes;

        // ── Variant ──
        $variant = null;
        $requiredAttrIds = [];
        $requiredAttrNames = [];

        if ($useVariants) {
            $pivot = $product->productAttributes()->get();
            if ($pivot->isNotEmpty()) {
                $required = $pivot->where('pivot.is_required', true);
                $requiredAttrIds = $required->pluck('id')->all();
                $requiredAttrNames = $required->pluck('name')->all();
            } elseif ($product->variants()->exists()) {
                // Legacy products (no pivot rows yet): variant required as before
                $requiredAttrIds = ['__any__'];
            }

            if ($variantId) {
                $q = ProductVariant::with([
                    'variantAttributeValues.attribute',
                    'variantAttributeValues.attributeValue',
                    'priceSlabs',
                ]);
                $variant = $lock
                    ? $q->where('id', $variantId)->lockForUpdate()->first()
                    : $q->find($variantId);

                if (! $variant || (int) $variant->product_id !== (int) $product->id) {
                    throw new CombinationException('ভুল variant সিলেক্ট হয়েছে। আবার চেষ্টা করুন।');
                }
                if (! $variant->is_active) {
                    throw new CombinationException('দুঃখিত, এই variant এখন আর নেই।');
                }

                // Required attribute groups must all be covered by the variant
                if (! empty($requiredAttrIds) && $requiredAttrIds !== ['__any__']) {
                    $covered = $variant->variantAttributeValues->pluck('attribute_id')->map(fn ($v) => (int) $v)->all();
                    $missing = [];
                    foreach ($requiredAttrIds as $attrId) {
                        if (! in_array((int) $attrId, $covered)) {
                            $missing[] = $attrId;
                        }
                    }
                    if ($missing) {
                        $names = $product->productAttributes()->whereIn('attributes.id', $missing)->pluck('name')->all();
                        throw new CombinationException('সিলেক্ট করুন: '.implode(' + ', $names ?: ['variant']));
                    }
                }
            } elseif (! empty($requiredAttrIds)) {
                if ($requiredAttrIds === ['__any__']) {
                    throw new CombinationException('অনুগ্রহ করে একটি variant সিলেক্ট করুন।');
                }
                $names = $product->productAttributes()->whereIn('attributes.id', $requiredAttrIds)->pluck('name')->all();
                throw new CombinationException('সিলেক্ট করুন: '.implode(' + ', $names));
            }
        } elseif ($variantId) {
            throw new CombinationException('এই product-এ variant প্রযোজ্য নয়।');
        }

        // ── Image option ──
        $option = null;
        $optionsOn = (bool) $product->has_image_options;

        if ($optionsOn) {
            if ($optionId) {
                $oq = ImageOption::query();
                $option = $lock
                    ? $oq->where('id', $optionId)->lockForUpdate()->first()
                    : $oq->find($optionId);

                if (! $option || (int) $option->product_id !== (int) $product->id) {
                    throw new CombinationException('ভুল image option সিলেক্ট হয়েছে। আবার চেষ্টা করুন।');
                }
                if (! $option->is_active) {
                    throw new CombinationException('দুঃখিত, এই image option এখন আর নেই।');
                }
            } elseif ($product->require_image_option && ImageOption::where('product_id', $product->id)->active()->exists()) {
                throw new CombinationException('একটি design/image option সিলেক্ট করুন।');
            }
        } elseif ($optionId) {
            throw new CombinationException('এই product-এ image option প্রযোজ্য নয়।');
        }

        // ── Price ──
        $unitPrice = PriceResolver::resolve($product, $variant, $qty, $option);

        // ── Stock: min(variant-or-product, option if it has own stock) ──
        $hasLedger = Inventory::where('product_id', $product->id)->exists();
        $avail = PHP_INT_MAX;

        if ($hasLedger) {
            $avail = min($avail, InventoryService::currentStock($product->id, $variant?->id));
        }
        if ($option && ImageOptionStock::hasOwnStock($option->id)) {
            $avail = min($avail, ImageOptionStock::currentStock($option->id));
        }

        if ($avail !== PHP_INT_MAX && $avail < $qty) {
            $who = $option && ImageOptionStock::hasOwnStock($option->id)
                && ImageOptionStock::currentStock($option->id) < $qty
                ? '"'.$option->label.'" (image option)'
                : ($variant ? $variant->label : $product->name);
            throw new CombinationException("দুঃখিত, {$who}-এর স্টক শেষ। আছে {$avail}টি।");
        }

        // ── Content id (tracking/feed identity) ──
        // Legacy ids stay untouched (product SKU / variant SKU). Only option
        // combinations get the extended productSKU-variantSKU-optionId form.
        if ($option) {
            $parts = [$product->sku];
            if ($variant) {
                $parts[] = $variant->sku;
            }
            $parts[] = 'opt'.$option->id;
            $contentId = implode('-', $parts);
        } else {
            $contentId = $variant ? (string) $variant->sku : (string) $product->sku;
        }

        return new ResolvedCombination(
            product: $product,
            variant: $variant,
            option: $option,
            unitPrice: $unitPrice,
            available: $avail === PHP_INT_MAX ? 999999 : $avail,
            contentId: $contentId,
            variantLabel: $variant?->label,
            optionLabel: $option?->label,
        );
    }
}
