    <div class="bg-white">
        <section class="product-view">
            <div class="container">
                <div class="pv-grid">
                    <div class="pv-main">
                        <div class="pv-card">
                            <div class="pv-flex">
                                <!-- ====== GALLERY WITH ALPINE.JS ====== -->
                                @php
                                // Build the full slide list: gallery images, with the active variant/thumbnail image forced first
                                $galleryUrls = (!empty($product->gallery_image) && is_array($product->gallery_image))
                                ? collect($product->gallery_image)->map(fn($img) => asset('storage/' . $img))->values()->all()
                                : [];

                                $initialImage = ($activeVariant && $activeVariant->image)
                                ? asset('storage/' . $activeVariant->image)
                                : ($product->thumbnail ? asset('storage/' . $product->thumbnail) : null);

                                if ($initialImage) {
                                $galleryUrls = collect($galleryUrls)->reject(fn($u) => $u === $initialImage)->values()->all();
                                array_unshift($galleryUrls, $initialImage);
                                }

                                if (empty($galleryUrls)) {
                                $galleryUrls = ['https://placehold.co/500x500'];
                                }
                                @endphp

                                <div class="pv-gallery"
                                    x-data="{
        images: @js($galleryUrls),
        activeIndex: 0,
        scrollTimer: null,

        scrollToIndex(i) {
            const track = this.$refs.track;
            if (!track) return;
            this.activeIndex = i;
            track.scrollTo({ left: i * track.clientWidth, behavior: 'smooth' });
        },

        onScroll() {
            clearTimeout(this.scrollTimer);
            this.scrollTimer = setTimeout(() => {
                const track = this.$refs.track;
                if (!track || track.clientWidth === 0) return;
                this.activeIndex = Math.round(track.scrollLeft / track.clientWidth);
            }, 80);
        },

        addOrJumpTo(src) {
            if (!src) return;
            let idx = this.images.indexOf(src);
            if (idx === -1) {
                this.images.unshift(src);
                idx = 0;
            }
            this.$nextTick(() => {
                const track = this.$refs.track;
                if (!track) return;
                this.activeIndex = idx;
                track.scrollTo({ left: idx * track.clientWidth, behavior: 'auto' });
            });
        }
    }"
                                    @set-main-image.window="if ($event.detail.src) addOrJumpTo($event.detail.src)">

                                    @if ($product->discount_type)
                                    <div class="pv-badge">
                                        @if ($product->discount_type == 'percent')
                                        -{{ $product->discount_price }}% OFF
                                        @else
                                        -{{ setting('currency') }}{{ $product->discount_price }} OFF
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Main Swipeable Image -->
                                    <div class="pv-image-wrapper">
                                        <div class="pv-image-track" x-ref="track" @scroll="onScroll">
                                            <template x-for="(img, index) in images" :key="index">
                                                <div class="pv-slide">
                                                    <img :src="img" alt="{{ $product->name }}" loading="lazy" class="pv-main-image">
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Counter badge, e.g. "1 / 4" -->
                                        <div class="pv-image-counter" x-show="images.length > 1" x-cloak
                                            x-text="(activeIndex + 1) + ' / ' + images.length"></div>

                                        <!-- Dot indicators -->
                                        <div class="pv-dots" x-show="images.length > 1" x-cloak>
                                            <template x-for="(img, index) in images" :key="index">
                                                <button
                                                    type="button"
                                                    class="pv-dot"
                                                    :class="{ 'pv-dot--active': activeIndex === index }"
                                                    @click="scrollToIndex(index)"
                                                    :aria-label="'Go to image ' + (index + 1)"></button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Thumbnails Gallery -->
                                    <div class="pv-thumbs-wrapper d-none" x-show="images.length > 1" x-cloak>
                                        <div class="pv-thumbs">
                                            <template x-for="(img, index) in images" :key="index">
                                                <button
                                                    class="pv-thumb"
                                                    :class="{ 'active': activeIndex === index }"
                                                    @click="scrollToIndex(index)"
                                                    :aria-label="'View image ' + (index + 1)"
                                                    type="button">
                                                    <img :src="img" :alt="'Product thumbnail ' + (index + 1)" loading="lazy">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- ====== PRODUCT INFO SECTION ====== -->
                                <div class="pv-info">
                                    <h1 class="pv-title">{{ $product->name }}</h1>
                                    <p class="pv-description">{{ $product->short_description }}</p>

                                    <div class="product-rating">
                                        <x-rating-stars :rating="$product->reviews_avg_rating" :count="$product->reviews_count" />
                                    </div>

                                    <div class="pv-pricing">
                                        <div class="gap-1">
                                            <span class="pv-price">
                                                {{ setting('currency') }}{{ number_format($displayPrice, 2) }}
                                            </span>

                                            @if ($flashSaleItem)
                                            <span class="pv-price-strike">
                                                {{ setting('currency') }}{{ number_format($strikePrice, 2) }}
                                            </span>
                                            <span class="badge bg-danger ms-1 d-none">
                                                Flash Sale {{ $flashSaleItem->discount_percent }}% OFF
                                            </span>

                                            @elseif ($product->discount_type)
                                            <span class="pv-price-strike">
                                                {{ setting('currency') }}{{ number_format($strikePrice, 2) }}
                                            </span>
                                            @endif
                                        </div>
                                        <!-- Add stock status -->
                                        <div class="pv-stock-status {{ $isInStock ? 'pv-stock-status--in' : 'pv-stock-status--out' }}">
                                            @if ($isInStock)
                                            <i class="fas fa-check-circle"></i> In Stock
                                            @else
                                            <i class="fas fa-times-circle"></i> Out of Stock
                                            @endif
                                        </div>
                                    </div>

                                    @include('livewire/frontend/components/trust-badge')

                                    <!-- ====== VARIANT SELECTION ====== -->
                                    <div class="pv-variant">
                                        @if ($enableVariant && $product->variants->count())
                                        @php
                                        $selectedVariantData = $product->variants->firstWhere('id', $selectedVariant) ?? $product->variants->first();
                                        $selectedPrice = $selectedVariantData->variant_price ?? $product->sale_price ?? 0;
                                        $originalPrice = $selectedVariantData->product->price ?? 0;
                                        $discountPercent = ($originalPrice > 0 && $selectedPrice < $originalPrice)
                                            ? round((($originalPrice - $selectedPrice) / $originalPrice) * 100)
                                            : 0;
                                            @endphp

                                            <div class="variant-wrapper">
                                            <h6 class="variant-section-title">
                                                <i class="fas fa-palette me-2"></i>
                                                Choose Variant
                                            </h6>

                                            <!-- Variant Selection - Smart Display -->
                                            @php
                                            // Get all unique attribute types across all variants
                                            $allAttributeNames = collect();
                                            foreach ($product->variants as $variant) {
                                            foreach ($variant->variantAttributeValues as $vav) {
                                            $attrName = $vav->attribute?->name ?? 'Variant';
                                            if (!$allAttributeNames->contains($attrName)) {
                                            $allAttributeNames->push($attrName);
                                            }
                                            }
                                            }


                                            $allAttributeNames = $allAttributeNames->sortBy(function ($name) {
                                            $lower = strtolower($name);
                                            if (str_contains($lower, 'color') || str_contains($lower, 'colour')) return 0;
                                            if (str_contains($lower, 'size')) return 1;
                                            return 2;
                                            })->values();
                                            @endphp

                                            @forelse ($allAttributeNames as $attributeName)
                                            @php
                                            // Get all unique values for this specific attribute type
                                            $attributeValues = collect();
                                            $variantsWithAttr = collect();

                                            foreach ($product->variants as $variant) {
                                            foreach ($variant->variantAttributeValues as $vav) {
                                            if ($vav->attribute?->name === $attributeName) {
                                            $value = $vav->attributeValue?->value ?? 'Variant';
                                            if (!$attributeValues->contains('value', $value)) {
                                            $attributeValues->push([
                                            'value' => $value,
                                            'variant_id' => $variant->id,
                                            'image' => $variant->image,
                                            ]);
                                            }
                                            if (!$variantsWithAttr->contains($variant->id)) {
                                            $variantsWithAttr->push($variant->id);
                                            }
                                            break;
                                            }
                                            }
                                            }

                                            $attrNameLower = strtolower($attributeName);
                                            $isColorAttribute = str_contains($attrNameLower, 'color') || str_contains($attrNameLower, 'colour');
                                            $isSizeAttribute = str_contains($attrNameLower, 'size');
                                            $isImageVariant = $isColorAttribute || ($attributeValues->first()['image'] ?? null) !== null;
                                            @endphp

                                            <div class="variant-attribute-group">
                                                <label class="variant-attribute-name">
                                                    {{ $attributeName }}
                                                    @if($isColorAttribute && !empty($selectedColor))
                                                    <span class="selected-indicator">(Selected: {{ $selectedColor }})</span>
                                                    @elseif($isSizeAttribute && !empty($selectedSize))
                                                    <span class="selected-indicator">(Selected: {{ $selectedSize }})</span>
                                                    @endif
                                                </label>

                                                @if ($isImageVariant)
                                                <!-- IMAGE VARIANTS (Colors) -->
                                                <div class="variant-images-container">
                                                    @foreach ($attributeValues as $attrOption)
                                                    @php
                                                    // Find first variant with this attribute value for stock check
                                                    $matchingVariant = null;
                                                    foreach ($product->variants as $v) {
                                                    foreach ($v->variantAttributeValues as $vav) {
                                                    if ($vav->attribute?->name === $attributeName &&
                                                    $vav->attributeValue?->value === $attrOption['value']) {
                                                    $matchingVariant = $v;
                                                    break 2;
                                                    }
                                                    }
                                                    }

                                                    $variantStock = $matchingVariant?->inventories->sortByDesc('id')->first()?->current_stock ?? 0;
                                                    $variantLabel = $attrOption['value'];
                                                    $isSelected = $isColorAttribute ? ($selectedColor == $variantLabel) : ($selectedVariant == $attrOption['variant_id']);
                                                    $hasImage = !empty($attrOption['image']);
                                                    @endphp

                                                    <div class="variant-item {{ $isSelected ? 'variant-item--selected' : '' }}"
                                                        @if($isColorAttribute)
                                                        wire:click="selectColor('{{ $variantLabel }}')"
                                                        @click="$dispatch('set-main-image', { src: '{{ $hasImage ? asset('storage/' . $attrOption['image']) : '' }}' })"
                                                        @else
                                                        wire:click="selectVariant({{ $attrOption['variant_id'] }})"
                                                        @endif
                                                        role="button"
                                                        tabindex="0"
                                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                                        aria-label="Select variant: {{ $variantLabel }}"
                                                        @keydown.enter="@if($isColorAttribute)selectColor('{{ $variantLabel }}')@else selectVariant({{ $attrOption['variant_id'] }})@endif"
                                                        @keydown.space.prevent="@if($isColorAttribute)selectColor('{{ $variantLabel }}')@else selectVariant({{ $attrOption['variant_id'] }})@endif">
                                                        <div class="variant-image-box {{ $isSelected ? 'variant-image-box--selected' : '' }}">
                                                            @if ($hasImage)
                                                            <img src="{{ asset('storage/' . $attrOption['image']) }}"
                                                                alt="Variant: {{ $variantLabel }}"
                                                                class="variant-image"
                                                                loading="lazy">
                                                            @else
                                                            <div class="variant-image-placeholder">
                                                                <i class="fas fa-image"></i>
                                                            </div>
                                                            @endif

                                                            @if ($isSelected)
                                                            <div class="variant-checkmark">
                                                                <i class="fas fa-check"></i>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="variant-info">
                                                            <div class="variant-label">{{ $variantLabel }}</div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>

                                                @else
                                                <!-- TEXT VARIANTS (Sizes) - Show as Buttons Only -->
                                                <div class="variant-buttons-container">
                                                    @foreach ($attributeValues as $attrOption)
                                                    @php
                                                    // Find first variant with this attribute value for stock check
                                                    $matchingVariant = null;
                                                    foreach ($product->variants as $v) {
                                                    foreach ($v->variantAttributeValues as $vav) {
                                                    if ($vav->attribute?->name === $attributeName &&
                                                    $vav->attributeValue?->value === $attrOption['value']) {
                                                    $matchingVariant = $v;
                                                    break 2;
                                                    }
                                                    }
                                                    }

                                                    $variantStock = $matchingVariant?->inventories->sortByDesc('id')->first()?->current_stock ?? 0;
                                                    $variantLabel = $attrOption['value'];
                                                    $isSelected = $isSizeAttribute ? ($selectedSize == $variantLabel) : ($selectedVariant == $attrOption['variant_id']);
                                                    @endphp

                                                    <button type="button"
                                                        class="variant-button {{ $isSelected ? 'variant-button--selected' : '' }} {{ $variantStock == 0 ? 'variant-button--disabled' : '' }}"
                                                        @if($isSizeAttribute)
                                                        wire:click="selectSize('{{ $variantLabel }}')"
                                                        @else
                                                        wire:click="selectVariant({{ $attrOption['variant_id'] }})"
                                                        @endif
                                                        {{ $variantStock == 0 ? 'disabled' : '' }}
                                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                                        aria-label="Select {{ strtolower($attributeName) }}: {{ $variantLabel }}">
                                                        {{ $variantLabel }}
                                                        @if ($variantStock == 0)
                                                        <span class="stock-badge">Out of Stock</span>
                                                        @endif
                                                    </button>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            @empty
                                            <p class="text-muted text-center">No variants available</p>
                                            @endforelse

                                            @if ($selectedVariantData)
                                            <div class="variant-price-box">
                                                <div class="variant-price-display">
                                                    <span class="variant-current-price">
                                                        {{ setting('currency') }}{{ number_format($selectedPrice, 2) }}
                                                    </span>
                                                </div>

                                                @php
                                                $attributes = $selectedVariantData->variantAttributeValues
                                                ->mapWithKeys(fn($vav) => [
                                                $vav->attribute?->name ?? 'Attribute' => $vav->attributeValue?->value ?? 'N/A'
                                                ]);
                                                @endphp

                                                @if ($attributes->count())
                                                <div class="variant-attributes">
                                                    @foreach ($attributes as $attributeName => $attributeValue)
                                                    <span class="variant-attribute-item">
                                                        <strong>{{ $attributeName }}:</strong> {{ $attributeValue }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            @else
                                            @php
                                            $hasColor = $variantsByAttribute->keys()->some(fn($k) => in_array(strtolower($k), ['color', 'colour']));
                                            $hasSize = $variantsByAttribute->keys()->some(fn($k) => in_array(strtolower($k), ['size', 'siize']));
                                            $needsBothAttributes = $hasColor && $hasSize;
                                            @endphp
                                            @if($needsBothAttributes && (empty($selectedColor) || empty($selectedSize)))
                                            <div class="variant-price-placeholder">
                                                <p class="text-muted text-center">
                                                    <i class="fas fa-info-circle"></i>
                                                    Select both color and size to see the final price
                                                </p>
                                            </div>
                                            @endif
                                            @endif
                                    </div>
                                    @endif
                                    <div id="variant-select-warning" class="variant-select-warning" style="display: none;">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Please select all variant options before adding to cart.</span>
                                        <button type="button" class="warning-close" onclick="document.getElementById('variant-select-warning').style.display='none';">×</button>
                                    </div>
                                </div>

                                <!-- Add stock status -->
                                <div class="pv-stock-status d-none">
                                    @if ($isInStock)
                                    In Stock ({{ $currentStock }} available)
                                    @else
                                    Out of Stock
                                    @endif
                                </div>
                                <!-- Qty + Actions -->
                                <div class="pv-qty-actions pv-inline-actions">
                                    <div class="pv-qty">
                                        <button
                                            wire:loading.attr="disabled"
                                            wire:target="decreaseQty"
                                            wire:click="decreaseQty"
                                            class="pv-qty-btn"
                                            @disabled($quantity <=1)
                                            type="button">
                                            <span wire:loading.remove wire:target="decreaseQty">−</span>
                                            <i wire:loading wire:target="decreaseQty" class="fa-solid fa-spinner fa-spin"></i>
                                        </button>
                                        <input
                                            type="number"
                                            wire:model="quantity"
                                            min="1"
                                            class="pv-qty-input">
                                        <button
                                            wire:loading.attr="disabled"
                                            wire:target="increaseQty"
                                            wire:click="increaseQty"
                                            class="pv-qty-btn"
                                            type="button">
                                            <span wire:loading.remove wire:target="increaseQty">+</span>
                                            <i wire:loading wire:target="increaseQty" class="fa-solid fa-spinner fa-spin"></i>
                                        </button>
                                    </div>


                                    <div class="pv-actions">
                                        <button
                                            class="addToCart"
                                            wire:click="addToCart({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="addToCart"
                                            type="button">
                                            <span wire:loading.remove wire:target="addToCart">
                                                <i class="fa fa-shopping-basket"></i>
                                                Add to Cart
                                            </span>
                                            <span wire:loading wire:target="addToCart">
                                                <i class="fa fa-spinner fa-spin"></i>
                                                Adding to Cart...
                                            </span>
                                        </button>

                                        <button
                                            class="btn-order-now"
                                            wire:click="buyNow({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="buyNow"
                                            type="button">
                                            <span wire:loading.remove wire:target="buyNow">
                                                <i class="fa fa-bolt"></i>
                                                Buy Now
                                            </span>
                                            <span wire:loading wire:target="buyNow">
                                                <i class="fa fa-spinner fa-spin"></i> Wait...
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Meta Information -->
                                <div class="pv-meta">
                                    <div class="meta-content">
                                        <div class="meta-item d-none">
                                            <span class="meta-icon">
                                                <i class="fa-solid fa-person-dress"></i>
                                            </span>
                                            <div class="d-flex flex-column">
                                                <span class="meta-label">Category:</span>
                                                <a
                                                    wire:navigate
                                                    href="{{ $product->category ? route('frontend.category.products.view', $product->category->slug) : '#' }}"
                                                    class="meta-link">
                                                    {{ $product->category?->name ?? 'N/A' }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="meta-item d-none">
                                            <span class="meta-label">Shop:</span>
                                            <a
                                                wire:navigate
                                                href="{{ route('frontend.user.products.view', $product->user?->id) }}"
                                                class="meta-link">
                                                @if($product->user && $product->user->hasRole('vendor'))
                                                {{ $product->user->name }}
                                                @else
                                                System
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ====== TABS ====== -->
                </div> <!-- closes pv-flex -->

                <!-- ====== TABS ====== -->
                <div class="pv-tabs" x-data="{ activeTab: 'desc' }">
                    <div class="pv-tab-head">
                        <button
                            class="pv-tab-btn active"
                            :class="{ 'active': activeTab === 'desc' }"
                            @click="activeTab = 'desc'"
                            type="button">
                            Description
                        </button>
                        <button
                            class="pv-tab-btn"
                            :class="{ 'active': activeTab === 'rev' }"
                            @click="activeTab = 'rev'"
                            type="button">
                            Reviews ({{ $product->reviews_count }})
                        </button>
                    </div>

                    <div class="pv-tab-body">
                        <!-- Description Tab -->
                        <div class="pv-tab-content" x-show="activeTab === 'desc'" x-transition>
                            {!! $product->long_description !!}
                        </div>

                        <!-- Reviews Tab -->
                        <div x-show="activeTab === 'rev'" x-transition>
                            @php
                            $totalReviews = $reviews->total();
                            $avgRating = $totalReviews > 0 ? $reviews->getCollection()->avg('rating') : 0;
                            $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                            foreach ($reviews->getCollection() as $r) {
                            $starCounts[(int)$r->rating] = ($starCounts[(int)$r->rating] ?? 0) + 1;
                            }
                            @endphp

                            <div class="review-summary-box">
                                <div class="review-summary-left">
                                    <div class="review-avg-score">
                                        {{ number_format($avgRating, 1) }}
                                        <span>/5</span>
                                    </div>
                                    <div class="review-stars-display">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                    </div>
                                    <div class="review-count-label">({{ $totalReviews }} Reviews)</div>
                                </div>
                                <div class="review-summary-right">
                                    @foreach ([5, 4, 3, 2, 1] as $star)
                                    @php
                                    $count = $starCounts[$star] ?? 0;
                                    $pct = $totalReviews > 0 ? ($count / $totalReviews * 100) : 0;
                                    @endphp
                                    <div class="review-bar-row">
                                        <div class="review-bar-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $star ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                        </div>
                                        <div class="review-bar-track">
                                            <div class="review-bar-fill" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="review-bar-count">({{ $count }})</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Individual Reviews List -->
                            <div class="reviews-list-container">
                                @if ($reviews->count() > 0)
                                @foreach ($reviews as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-user-info">
                                            <div class="review-user-avatar">
                                                @if ($review->user && $review->user->profile_image)
                                                <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name }}">
                                                @else
                                                <div class="avatar-placeholder">{{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}</div>
                                                @endif
                                            </div>
                                            <div class="review-user-details">
                                                <h5 class="review-user-name">{{ $review->user?->name ?? 'Anonymous' }}</h5>
                                                <p class="review-user-date">{{ $review->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                            <span class="rating-value">({{ $review->rating }}.0/5.0)</span>
                                    </div>
                                    <div class="review-content">
                                        <p>{{ $review->comment }}</p>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Pagination -->
                                <div class="reviews-pagination mt-3">
                                    {{ $reviews->links() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Related Products -->
                <div class="pv-sidebar mt-4">
                    <div class="pv-side-card">
                        <h3 class="pv-side-title">Related Products</h3>
                        <div class="products-grid">
                            @if (!isset($relatedProducts) || $relatedProducts->isEmpty())
                            <p class="text-center text-muted">Loading related products...</p>
                            @else
                            @foreach ($relatedProducts as $product)
                            @include('livewire.frontend.layouts.partial.product-card', ['product' => $product])
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
        </section>

        <!-- Sticky Bottom Bar -->
        <div class="pv-sticky-bar" id="pvStickyBar">
            <div class="pv-sticky-inner">
                <button
                    class="favorite-heart"
                    wire:click="addToWishlist({{ $product->id }})"
                    aria-label="Add to wishlist"
                    type="button">
                    <i class="far fa-heart"></i>
                </button>

                <button
                    class="pv-sticky-cart"
                    wire:click="addToCart({{ $product->id }})"
                    data-requires-variant="{{ ($enableVariant && $product->variants->count()) ? 'true' : 'false' }}"
                    wire:loading.attr="disabled"
                    wire:target="addToCart"
                    type="button">
                    <span wire:loading.remove wire:target="addToCart">
                        <i class="fa fa-shopping-basket"></i>Add To Cart
                    </span>
                    <span wire:loading wire:target="addToCart">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                </button>

                <button
                    class="pv-sticky-buynow"
                    wire:click="buyNow({{ $product->id }})"
                    data-requires-variant="{{ ($enableVariant && $product->variants->count()) ? 'true' : 'false' }}"
                    wire:loading.attr="disabled"
                    wire:target="buyNow"
                    type="button">
                    <span wire:loading.remove wire:target="buyNow">
                        <i class="fa fa-bolt"></i> Buy Now
                    </span>
                    <span wire:loading wire:target="buyNow">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>

        <div class="pv-sticky-spacer"></div>
    </div>

    <!-- ====== STYLES ====== -->
    <style>
        .pv-image-track {
            display: flex;
            width: 100%;
            height: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .pv-image-track::-webkit-scrollbar {
            display: none;
        }

        .pv-slide {
            flex: 0 0 100%;
            width: 100%;
            height: 100%;
            scroll-snap-align: center;
            scroll-snap-stop: always;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pv-image-counter {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.55);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            z-index: 10;
            letter-spacing: 0.3px;
        }

        .pv-dots {
            position: absolute;
            bottom: 0.85rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            z-index: 10;
        }

        .pv-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.55);
            border: none;
            padding: 0;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .pv-dot--active {
            width: 18px;
            border-radius: 4px;
            background: orangered;
        }

        .variant-buttons-container {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .variant-button {
            padding: 10px 16px;
            border: 2px solid #ddd;
            background: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            color: #333;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .variant-button:hover:not(:disabled) {
            border-color: #333;
        }

        .variant-button--selected {
            border-color: #333;
            background: #333;
            color: #fff;
        }

        .variant-button--disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .stock-badge {
            display: block;
            font-size: 10px;
            margin-top: 4px;
            color: #d32f2f;
            font-weight: 600;
        }

        .variant-attribute-name {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 14px;
            color: #333;
            text-transform: capitalize;
        }

        .selected-indicator {
            font-weight: 400;
            font-size: 12px;
            color: #d32f2f;
            margin-left: 0.5rem;
            font-style: italic;
        }

        /* Variant Selections Summary */
        .variant-selections-summary {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .selections-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .selections-header i {
            color: #0056b3;
            font-size: 1.1rem;
        }

        .selections-content {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .selection-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .selection-label {
            font-weight: 600;
            color: #555;
            min-width: 80px;
        }

        .selection-value {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #333;
        }

        .selection-value strong {
            color: #d32f2f;
            font-weight: 700;
        }

        .selection-clear {
            background: none;
            border: none;
            color: #999;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0 0.25rem;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .selection-clear:hover {
            color: #d32f2f;
            font-weight: bold;
        }

        .selection-status {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f0f0f0;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .selection-status i {
            font-size: 1.1rem;
        }

        .text-success {
            color: #28a745;
        }

        .text-warning {
            color: #ffc107;
        }

        /* Image Wrapper */
        .pv-image-wrapper {
            width: 100%;
            aspect-ratio: 1;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        .pv-main-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        /* Gallery */
        .pv-gallery {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .pv-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: #d32f2f;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.875rem;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .pv-thumbs-wrapper {
            padding: 0.5rem 0;
        }

        .pv-thumbs {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            padding: 0.25rem;
        }

        .pv-thumb {
            flex: 0 0 auto;
            width: 80px;
            height: 80px;
            border: 2px solid #ddd;
            border-radius: 6px;
            padding: 4px;
            cursor: pointer;
            background: white;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .pv-thumb:hover {
            border-color: orangered;
            box-shadow: 0 0 8px rgba(255, 165, 0, 0.2);
        }

        .pv-thumb.active {
            border-color: orangered;
            box-shadow: 0 0 12px rgba(255, 165, 0, 0.3);
        }

        .pv-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 3px;
        }

        /* Product Info */
        .pv-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .pv-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin: 0;
            line-height: 1.3;
        }

        .pv-description {
            font-size: 0.95rem;
            color: #666;
            margin: 0;
            line-height: 1.5;
        }

        .pv-pricing {
            display: flex;
            justify-content: space-between;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .pv-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #d32f2f;
        }

        .pv-price-strike {
            font-size: 0.9rem;
            color: #999;
            text-decoration: line-through;
        }

        /* Variant Styles */
        .pv-variant {
            width: 100%;
        }

        .variant-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .variant-section-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .variant-images-container {
            display: grid;
            grid-template-columns: repeat(6, 105px);
            gap: 0.7rem;
        }

        @media (max-width: 460px) {
            .variant-images-container {
                grid-template-columns: repeat(4, 95px);
            }
        }

        @media (max-width: 430px) {
            .variant-images-container {
                grid-template-columns: repeat(4, 90px);
            }
        }

        @media (max-width: 412px) {
            .variant-images-container {
                grid-template-columns: repeat(3, 90px);
            }
        }

        .variant-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            cursor: pointer;
            transition: transform 0.2s ease;
            position: relative;
        }

        .variant-item:hover {
            transform: translateY(-2px);
        }

        .variant-item:focus-visible {
            outline: 2px solid orangered;
            outline-offset: 4px;
            border-radius: 8px;
        }

        .variant-image-box {
            position: relative;
            width: 105px;
            height: 105px;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 8px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .variant-image-box--selected {
            border-color: orangered;
            background: #f1f8f5;
            box-shadow: 0 0 12px rgba(255, 165, 0, 0.35);
        }

        .variant-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .variant-image-placeholder {
            width: 100%;
            height: 100%;
            background: #f5f5f5;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bbb;
            font-size: 1.5rem;
        }

        .variant-checkmark {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 26px;
            height: 26px;
            background: orangered;
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            box-shadow: 0 2px 8px rgba(255, 165, 0, 0.4);
        }

        .variant-info {
            text-align: center;
        }

        .variant-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .variant-price-box {
            padding: 0.5rem;
            border: 1px solid #efefef;
            border-radius: 8px;
        }

        .variant-price-placeholder {
            padding: 1rem;
            border: 1px dashed #ccc;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .variant-price-placeholder p {
            margin: 0;
            font-size: 0.9rem;
        }

        .variant-price-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }

        .variant-current-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: #d32f2f;
            line-height: 1;
        }

        .variant-attributes {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .variant-attribute-item {
            font-size: 0.9rem;
            color: #555;
        }

        .variant-attribute-item strong {
            color: #333;
        }

        /* Quantity */
        .pv-qty {
            display: flex;
            align-items: stretch;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            background: white;
        }

        .pv-qty-btn {
            width: 40px;
            border: none;
            background: white;
            color: #333;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .pv-qty-btn:hover:not(:disabled) {
            background: #f5f5f5;
        }

        .pv-qty-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pv-qty-input {
            border: none;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            width: 60px;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            padding: 0;
        }

        .pv-qty-input:focus {
            outline: none;
            background: #f5f5f5;
        }

        /* Actions */
        .pv-qty-actions {
            display: flex;
            align-items: stretch;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .pv-actions {
            display: flex;
            gap: 0.75rem;
            flex: 1;
            min-width: 250px;
        }

        .pv-meta {
            border-top: 1px solid #efefef;
        }

        .meta-content {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .meta-item {
            display: flex;
            padding: 0.1rem 1rem;
            background: white;
            border: 1px solid whitesmoke;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
            gap: 0.7rem;
        }

        .meta-label {
            font-size: 1rem;
            font-weight: 700;
            color: black;
        }

        .meta-icon {
            margin-top: 5px;
            width: 40px;
            height: 40px;
            padding: 25px;
            border-radius: 50%;
            background: #fff3ea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .meta-card-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .meta-card-link:hover {
            border-color: orangered;
            background: #fafafa;
        }

        .meta-card-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 8px;
            background: #fff3ea;
            color: orangered;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .meta-card-text {
            display: flex;
            flex-direction: column;
            line-height: 1.35;
            flex: 1;
            min-width: 0;
        }

        .meta-card-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #222;
        }

        .meta-card-sub {
            font-size: 0.8rem;
            color: #888;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .meta-card-arrow {
            color: #ccc;
            font-size: 0.85rem;
        }

        .meta-link {
            color: gray;
        }

        /* Tabs */
        .pv-tabs {
            width: 100%;
            margin-top: 1rem;
        }

        .pv-tab-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #e0e0e0;
            gap: 2rem;
            background: white;
        }

        .pv-tab-btn {
            padding: 1rem 0;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            font-size: 0.95rem;
            position: relative;
        }

        .pv-tab-btn:hover {
            color: #333;
        }

        .pv-tab-btn.active {
            color: orangered;
            border-bottom-color: orangered;
            background: transparent;
        }

        .pv-tab-body {
            padding: 2rem 0;
            width: 100%;
        }

        .pv-tab-content {
            padding: 10px;
            border: 1px solid #333;
        }

        .pv-tab-panel {
            width: 100%;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Review Section */
        .review-summary-box {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .review-summary-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            min-width: 120px;
            text-align: center;
        }

        .review-avg-score {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            line-height: 1;
        }

        .review-avg-score span {
            font-size: 1.2rem;
            color: #999;
            font-weight: 400;
        }

        .review-stars-display {
            color: #ffa500;
            font-size: 0.95rem;
            display: flex;
            gap: 0.25rem;
        }

        .review-count-label {
            font-size: 0.85rem;
            color: #999;
        }

        .review-summary-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .review-bar-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .review-bar-stars {
            color: #ffa500;
            font-size: 0.8rem;
            min-width: 80px;
            display: flex;
            gap: 0.15rem;
        }

        .review-bar-track {
            flex: 1;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .review-bar-fill {
            height: 100%;
            background: #ffa500;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .review-bar-count {
            font-size: 0.8rem;
            color: #999;
            min-width: 40px;
            text-align: right;
        }

        /* Individual Reviews */
        .reviews-list-container {
            margin-top: 1rem;
        }

        .review-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 1.5rem 0;
        }

        .review-item:first-child {
            padding-top: 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .review-user-info {
            display: flex;
            gap: 0.75rem;
        }

        .review-user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .review-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            font-weight: 700;
            color: #999;
            font-size: 1.1rem;
            background: #f0f0f0;
        }

        .review-user-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .review-user-name {
            font-weight: 700;
            color: #333;
            margin: 0;
            font-size: 0.95rem;
        }

        .review-user-date {
            font-size: 0.8rem;
            color: #999;
            margin: 0;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .review-rating i {
            color: #ffa500;
            font-size: 0.85rem;
        }

        .rating-value {
            font-size: 0.85rem;
            color: #999;
            font-weight: 600;
        }

        .review-content {
            color: #555;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .review-content p {
            margin: 0;
        }

        .no-reviews {
            text-align: center;
            padding: 0.5rem 0;
        }

        .reviews-pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.8rem;
        }

        /* Sticky Bar */
        .pv-sticky-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #ddd;
            z-index: 9999;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
        }

        .pv-sticky-inner {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 8px;
            max-height: 70px;
            overflow: hidden;
        }

        .favorite-heart {
            width: 48px;
            min-width: 48px;
            height: 48px;
            padding: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #d32f2f;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .favorite-heart:active {
            background: #f5f5f5;
            transform: scale(0.95);
        }

        .pv-sticky-cart {
            flex: 1;
            height: 48px;
            padding: 0 12px;
            background: orangered;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.18s ease;
        }

        .pv-sticky-cart:active:not(:disabled) {
            background: #b71c1c;
        }

        .pv-sticky-cart:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .pv-sticky-buynow {
            flex: 1;
            height: 48px;
            padding: 0 12px;
            background: orange;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.18s ease;
        }

        .pv-sticky-buynow:active:not(:disabled) {
            background: #f57c00;
        }

        .pv-sticky-buynow:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .pv-sticky-spacer {
            display: none;
            height: 60px;
        }

        .variant-select-warning {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            color: #856404;
            font-weight: 500;
            margin-top: 1rem;
        }

        .variant-select-warning i {
            color: #ffc107;
            font-size: 1.1rem;
        }

        .warning-close {
            background: none;
            border: none;
            color: #856404;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
            line-height: 1;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .warning-close:hover {
            opacity: 1;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .pv-flex {
                flex-direction: column;
            }

            .pv-title {
                font-size: 1.3rem;
            }

            .pv-price {
                font-size: 1.5rem;
            }

            .pv-actions {
                min-width: unset;
            }

            .pv-tab-body {
                padding: 1.5rem 0;
            }

            .review-summary-box {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1rem;
            }

            .review-summary-left {
                width: 100%;
                flex-direction: row;
                min-width: unset;
                text-align: left;
                gap: 1rem;
            }

            .review-summary-right {
                width: 100%;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.6rem;
            }

            .pv-inline-actions {
                display: none !important;
            }

            .pv-sticky-bar {
                display: block;
            }

            .pv-sticky-spacer {
                display: block;
            }

            .meta-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .pv-title {
                font-size: 1.1rem;
            }

            .pv-description {
                font-size: 0.85rem;
            }

            .pv-price {
                font-size: 1.3rem;
            }

            .variant-current-price {
                font-size: 1.3rem;
            }

            .variant-image-box {
                width: 95px;
                height: 95px;
                padding: 6px;
            }

            .variant-checkmark {
                width: 20px;
                height: 20px;
                top: -6px;
                right: -6px;
            }

            .variant-label {
                font-size: 0.65rem;
            }

            .pv-tab-head {
                gap: 0.5rem;
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
            }

            .pv-tab-btn {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }

            .pv-tab-body {
                padding: 1rem 0;
            }

            .review-avg-score {
                font-size: 1.8rem;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            .pv-sticky-inner {
                gap: 6px;
                padding: 35px 6px;
                max-height: 56px;
            }

            .favorite-heart {
                width: 44px;
                min-width: 44px;
                height: 44px;
                font-size: 0.9rem;
            }

            .pv-sticky-cart,
            .pv-sticky-buynow {
                height: 44px;
                font-size: 0.85rem;
                padding: 0 10px;
            }

            .pv-sticky-spacer {
                height: 54px;
            }
        }

        @media (max-width: 380px) {
            .pv-sticky-inner {
                gap: 5px;
                padding: 30px 5px;
            }

            .favorite-heart {
                width: 40px;
                min-width: 40px;
                height: 40px;
                font-size: 0.85rem;
            }

            .pv-sticky-cart,
            .pv-sticky-buynow {
                height: 40px;
                font-size: 0.8rem;
                padding: 0 8px;
                border-radius: 16px;
            }

            .pv-sticky-spacer {
                height: 50px;
            }
        }

        .pv-stock-status {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            width: fit-content;
            padding: 0.35rem 0.9rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .pv-stock-status--in {
            background: #e6f4ea;
            color: #1e7e34;
        }

        .pv-stock-status--in i {
            color: #28a745;
        }

        .pv-stock-status--out {
            background: #fdecea;
            color: #c0392b;
        }

        .pv-stock-status--out i {
            color: #d32f2f;
        }
    </style>

    @script
    <script>
        $wire.on('show-variant-warning', () => {
            const w = document.getElementById('variant-select-warning');
            if (w) w.style.display = 'flex';
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('variant-resolved', (data) => {
                if (data.variantId) {
                    const w = document.getElementById('variant-select-warning');
                    if (w) w.style.display = 'none';
                }
            });
        });
    </script>
    @endscript