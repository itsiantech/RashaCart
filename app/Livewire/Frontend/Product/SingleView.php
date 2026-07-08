<?php

namespace App\Livewire\Frontend\Product;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Traits\HandlesCartActions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SingleView extends Component
{
    use HandlesCartActions {
        HandlesCartActions::addToCart as traitAddToCart;
        HandlesCartActions::buyNow as traitBuyNow;
    }
    use WithPagination;

    public $relatedProducts;

    public $product;

    public $meta_title;

    public $meta_description;

    public $meta_keywords;

    public $meta_tags;

    public bool $enableVariant = true;

    public bool $enableColor = true;

    public bool $enableSize = true;

    public $selectedVariant = null;

    public $selectedColor = null;

    public $selectedSize = null;

    public $colorAttrName = 'Color';

    public $sizeAttrName = 'Size';

    public $quantity = 1;

    public $rating = 0;

    public $comment = '';

    public $displayPrice;

    public $strikePrice;

    public $flashSaleItem = null;

    // ← ADD THESE NEW PROPERTIES
    public $currentStock = 0;

    public $isInStock = false;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:3',
    ];

    public function mount($slug)
    {
        $this->product = Product::with([
            'category',
            'user',
            'coupons',
            'variants' => function ($q) {
                $q->with([
                    'variantAttributeValues' => function ($q2) {
                        $q2->with(['attribute', 'attributeValue']);
                    },
                    'inventories',
                ]);
            },
            'inventories',
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $this->meta_title = $this->product->meta_title ?? $this->product->name;
        $this->meta_description = $this->product->meta_description;
        $this->meta_keywords = $this->product->meta_keywords;
        $this->meta_tags = $this->product->meta_tags;

        $this->relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('status', true)
            ->take(5)
            ->get();

        // Attach total_sold
        $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id')
            ->pluck('total_sold', 'product_id');

        $this->relatedProducts = $this->relatedProducts->map(function ($product) use ($totalSold) {
            $product->total_sold = $totalSold[$product->id] ?? 0;

            return $product;
        });

        // Check if variants exist
        $this->enableVariant = $this->product->variants->count() > 0;

        $this->flashSaleItem = \App\Models\FlashSaleItem::whereHas('flashSale', function ($q) {
            $q->where('is_active', true)
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now());
        })->where('product_id', $this->product->id)
            ->where('is_active', true)
            ->first();

        $basePrice = $this->product->sale_price ?? $this->product->price;
        $originalPrice = $this->product->price;

        if ($this->flashSaleItem) {
            $this->displayPrice = $basePrice * (1 - $this->flashSaleItem->discount_percent / 100);
            $this->strikePrice = $basePrice;
        } else {
            $this->displayPrice = $basePrice;
            $this->strikePrice = $originalPrice;
        }

        // ← ADD THIS: Update stock on mount
        $this->updateStockStatus();
    }

    public function getCurrentStock()
    {
        if ($this->enableVariant && $this->selectedVariant) {
            // Get stock from selected variant
            $variant = \App\Models\ProductVariant::find($this->selectedVariant);

            return $variant?->inventories()
                ->latest('id')
                ->first()?->current_stock ?? 0;
        }

        // Get stock from main product
        return $this->product->inventories()
            ->latest('id')
            ->first()?->current_stock ?? 0;
    }

    public function updateStockStatus()
    {
        $this->currentStock = $this->getCurrentStock();
        $this->isInStock = $this->currentStock > 0;
    }

    private function validateStock()
    {
        if (! $this->isInStock) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'This product is out of stock!',
            ]);

            return false;
        }

        if ($this->quantity > $this->currentStock) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Only {$this->currentStock} items available. Quantity adjusted.",
            ]);
            $this->quantity = $this->currentStock;

            return true;
        }

        return true;
    }

    private function validateVariantSelected(): bool
    {
        if (! $this->enableVariant || $this->product->variants->count() === 0) {
            return true;
        }

        $hasColor = $this->hasColorAttribute();
        $hasSize = $this->hasSizeAttribute();

        // Color only product
        if ($hasColor && ! $hasSize) {
            if (empty($this->selectedColor)) {
                $this->dispatch('error', message: 'Please select a color before continuing.');
                $this->dispatch('show-variant-warning');

                return false;
            }

            return true;
        }

        if (! $hasColor && $hasSize) {
            if (empty($this->selectedSize)) {
                $this->dispatch('error', message: 'Please select a size before continuing.');
                $this->dispatch('show-variant-warning');

                return false;
            }

            return true;
        }

        if ($hasColor && $hasSize) {
            if (empty($this->selectedColor)) {
                $this->dispatch('error', message: 'Please select a color before continuing.');
                $this->dispatch('show-variant-warning');

                return false;
            }
            if (empty($this->selectedSize)) {
                $this->dispatch('error', message: 'Please select a size before continuing.');
                $this->dispatch('show-variant-warning');

                return false;
            }
            if (empty($this->selectedVariant)) {
                $this->dispatch('error', message: 'The selected combination is not available.');

                return false;
            }

            return true;
        }

        return true;
    }

    public function addToCart($productId)
    {

        if (! $this->validateVariantSelected()) {
            return false;
        }

        if (! $this->validateStock()) {
            return false;
        }

        return $this->traitAddToCart($productId);
    }

    public function buyNow($productId)
    {
        if (! $this->validateVariantSelected()) {
            return false;
        }

        if (! $this->validateStock()) {
            return false;
        }

        return $this->traitBuyNow($productId);
    }

    public function selectVariant($variantId)
    {
        $this->selectedVariant = $variantId;
        $this->quantity = 1;
        $this->updateStockStatus();
    }

    public function selectColor($color)
    {
        if ($this->selectedColor === $color) {
            $this->selectedColor = null;
            $this->selectedSize = null;
        } else {
            $this->selectedColor = $color;
            $this->quantity = 1;
        }

        $this->selectedVariant = null;

        $this->tryResolveVariant();
        $this->updateStockStatus();

        $this->dispatch('color-selected', color: $this->selectedColor);
    }

    public function selectSize($sizeValue)
    {
        if ($this->selectedSize === $sizeValue) {
            $this->selectedSize = null;
        } else {
            $this->selectedSize = $sizeValue;
            $this->quantity = 1;
        }

        $this->selectedVariant = null;

        $this->tryResolveVariant();
        $this->updateStockStatus();

        $this->dispatch('size-selected', size: $this->selectedSize);
    }

    private function matchSelectedVariant()
    {
        if (! $this->enableVariant) {
            return;
        }

        foreach ($this->product->variants as $variant) {
            $matchColor = false;
            $matchSize = false;

            $hasColorAttribute = false;
            $hasSizeAttribute = false;

            foreach ($variant->variantAttributeValues as $vav) {
                $attrName = strtolower($vav->attribute?->name ?? '');
                $value = $vav->attributeValue?->value;

                if (in_array($attrName, ['color', 'colour'])) {
                    $hasColorAttribute = true;
                    if ($value == $this->selectedColor) {
                        $matchColor = true;
                    }
                }

                if (in_array($attrName, ['size', 'siize'])) {
                    $hasSizeAttribute = true;
                    if ($value == $this->selectedSize) {
                        $matchSize = true;
                    }
                }
            }

            if ($hasColorAttribute && $hasSizeAttribute) {
                if ($matchColor && $matchSize) {
                    $this->selectedVariant = $variant->id;
                    $this->updateStockStatus();

                    return;
                }
            } elseif ($hasColorAttribute && ! $hasSizeAttribute && $matchColor) {
                $this->selectedVariant = $variant->id;
                $this->updateStockStatus();

                return;
            }
            // যদি শুধু সাইজ থাকে এবং ম্যাচ করে
            elseif (! $hasColorAttribute && $hasSizeAttribute && $matchSize) {
                $this->selectedVariant = $variant->id;
                $this->updateStockStatus();

                return;
            }
        }

        $this->selectedVariant = null;
        $this->updateStockStatus();
    }

    private function getProductAttributeTypes()
    {
        $attributeTypes = collect();
        foreach ($this->product->variants as $variant) {
            foreach ($variant->variantAttributeValues as $vav) {
                $attrName = strtolower($vav->attribute?->name ?? '');
                if (! $attributeTypes->contains($attrName)) {
                    $attributeTypes->push($attrName);
                }
            }
        }

        return $attributeTypes;
    }

    private function hasColorAttribute(): bool
    {
        return $this->getProductAttributeTypes()->contains(
            fn ($a) => str_contains($a, 'color') || str_contains($a, 'colour')
        );
    }

    private function hasSizeAttribute(): bool
    {
        return $this->getProductAttributeTypes()->contains(
            fn ($a) => str_contains($a, 'size')
        );
    }

    private function tryResolveVariant()
    {
        $hasColor = $this->hasColorAttribute();
        $hasSize = $this->hasSizeAttribute();

        // If product only has colors, resolve when color is selected
        if ($hasColor && ! $hasSize) {
            if (! empty($this->selectedColor)) {
                $this->resolveVariant();
            }

            return;
        }

        // If product only has sizes, resolve when size is selected
        if (! $hasColor && $hasSize) {
            if (! empty($this->selectedSize)) {
                $this->resolveVariant();
            }

            return;
        }

        // If product has both, resolve only when BOTH are selected
        if ($hasColor && $hasSize) {
            if (! empty($this->selectedColor) && ! empty($this->selectedSize)) {
                $this->resolveVariant();
            } else {
                $this->selectedVariant = null;
                $this->dispatch('variant-resolved', variantId: null);
            }

            return;
        }

        if (! empty($this->selectedVariant)) {
            return;
        }

        if ($this->product->variants->count() > 0) {
            $this->selectedVariant = $this->product->variants->first()->id;
        }
    }

    protected function resolveVariant()
    {
        $hasColor = $this->hasColorAttribute();
        $hasSize = $this->hasSizeAttribute();

        if ($hasColor && $hasSize) {
            if (empty($this->selectedColor) || empty($this->selectedSize)) {
                $this->selectedVariant = null;
                $this->dispatch('variant-resolved', variantId: null);

                return;
            }
        }

        foreach ($this->product->variants as $variant) {
            $colorMatch = ! $hasColor;
            $sizeMatch = ! $hasSize;

            foreach ($variant->variantAttributeValues as $vav) {
                if (! $vav->attributeValue || ! $vav->attribute) {
                    continue;
                }

                $attrName = strtolower($vav->attribute->name ?? '');
                $val = $vav->attributeValue->value ?? '';

                // ← str_contains instead of in_array
                if (str_contains($attrName, 'color') || str_contains($attrName, 'colour')) {
                    $colorMatch = ($val === $this->selectedColor);
                }

                if (str_contains($attrName, 'size')) {
                    $sizeMatch = ($val === $this->selectedSize);
                }
            }

            if ($colorMatch && $sizeMatch) {
                $this->selectedVariant = $variant->id;
                $this->dispatch('variant-resolved', variantId: $this->selectedVariant);

                return;
            }
        }

        $this->selectedVariant = null;
        $this->dispatch('variant-resolved', variantId: null);
    }

    // ── Quantity Controls ──
    public function increaseQty()
    {
        // Don't allow more than available stock
        if ($this->quantity < $this->currentStock) {
            $this->quantity++;
        }
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    // ── Reviews ──
    public function submitReview()
    {
        $this->validate();

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->reset('rating', 'comment');
        $this->dispatch('success', message: 'Review submitted successfully!');
    }

    // ── Wishlist ──
    public function addToWishlist($productId)
    {
        // Implement your wishlist logic here
        $this->dispatch('wishlist-added', productId: $productId);
    }

    // ── Computed Properties ──
    public function getColorOptionsProperty()
    {
        $colorOptions = collect();

        foreach ($this->product->variants as $variant) {
            $colorVal = null;

            foreach ($variant->variantAttributeValues as $vav) {
                if (! $vav->attributeValue || ! $vav->attribute) {
                    continue;
                }

                $attrName = strtolower($vav->attribute->name ?? '');
                $val = $vav->attributeValue->value ?? '';

                if ($attrName === strtolower($this->colorAttrName)) {
                    $colorVal = $val;
                    break;
                }
            }

            if ($colorVal && ! $colorOptions->contains('value', $colorVal)) {
                $colorOptions->push([
                    'value' => $colorVal,
                    'variant_id' => $variant->id,
                    'image' => $variant->image,
                ]);
            }
        }

        return $colorOptions;
    }

    public function getSizeOptionsProperty()
    {
        $sizeOptions = collect();

        foreach ($this->product->variants as $variant) {
            foreach ($variant->variantAttributeValues as $vav) {
                if (! $vav->attributeValue || ! $vav->attribute) {
                    continue;
                }

                $attrName = strtolower($vav->attribute->name ?? '');
                $val = $vav->attributeValue->value ?? '';

                if ($attrName === strtolower($this->sizeAttrName)) {
                    if (! $sizeOptions->contains('value', $val)) {
                        $sizeOptions->push(['value' => $val]);
                    }
                    break;
                }
            }
        }

        return $sizeOptions;
    }

    public function getAvailableSizesProperty()
    {
        if (! $this->selectedColor) {
            return $this->sizeOptions->map(fn ($s) => [
                'value' => $s['value'],
                'stock' => 1,
            ]);
        }

        $availableSizes = collect();

        foreach ($this->product->variants as $variant) {
            $colorVal = null;
            $sizeVal = null;

            foreach ($variant->variantAttributeValues as $vav) {
                if (! $vav->attributeValue || ! $vav->attribute) {
                    continue;
                }

                $attrName = strtolower($vav->attribute->name ?? '');
                $val = $vav->attributeValue->value ?? '';

                if ($attrName === strtolower($this->colorAttrName)) {
                    $colorVal = $val;
                }
                if ($attrName === strtolower($this->sizeAttrName)) {
                    $sizeVal = $val;
                }
            }

            if ($colorVal === $this->selectedColor && $sizeVal) {
                $stock = $variant->inventories->sortByDesc('id')->first()?->current_stock ?? 0;
                $availableSizes->push([
                    'value' => $sizeVal,
                    'stock' => $stock,
                    'variant_id' => $variant->id,
                ]);
            }
        }

        return $availableSizes;
    }

    public function getActiveVariantProperty()
    {
        if (! $this->selectedVariant) {
            return null;
        }

        $variant = \App\Models\ProductVariant::with([
            'variantAttributeValues.attribute',
            'variantAttributeValues.attributeValue',
            'inventories',
        ])->find($this->selectedVariant);

        if (! $variant) {
            return null;
        }

        // If this variant has no image, find the color variant's image
        if (empty($variant->image) && ! empty($this->selectedColor)) {
            $colorVariant = $this->product->variants->first(function ($v) {
                foreach ($v->variantAttributeValues as $vav) {
                    if (! $vav->attribute || ! $vav->attributeValue) {
                        continue;
                    }
                    $attrName = strtolower($vav->attribute->name ?? '');
                    if (
                        in_array($attrName, ['color', 'colour'])
                        && $vav->attributeValue->value === $this->selectedColor
                    ) {
                        return true;
                    }
                }

                return false;
            });

            if ($colorVariant && ! empty($colorVariant->image)) {
                $variant->image = $colorVariant->image;
            }
        }

        return $variant;
    }

    public function render()
    {
        return view('livewire.frontend.product.single-view', [
            'reviews' => $this->product->reviews()
                ->with('user')
                ->latest()
                ->paginate(3),
            'product' => $this->product,
            'colorOptions' => $this->colorOptions,
            'sizeOptions' => $this->sizeOptions,
            'availableSizes' => $this->availableSizes,
            'activeVariant' => $this->activeVariant,
            'displayPrice' => $this->displayPrice,
            'strikePrice' => $this->strikePrice,
            'flashSaleItem' => $this->flashSaleItem,
            'currentStock' => $this->currentStock,
            'isInStock' => $this->isInStock,
        ])->layout('livewire.frontend.layouts.app')
            ->layoutData([
                'meta' => [
                    'title' => $this->meta_title ?? '',
                    'description' => $this->meta_description ?? '',
                    'keywords' => $this->meta_keywords ?? '',
                    'tags' => $this->meta_tags ?? '',
                ],
            ]);
    }
}
