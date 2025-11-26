<div class="product-card">
    @if ($product->discount_price)
        <div class="product-badge">
            @php
                $discountPercent =
                    $product->discount_type === 'percent'
                        ? $product->discount_price
                        : round(($product->discount_price / $product->price) * 100);
            @endphp
            {{ $discountPercent }}% OFF
        </div>
    @endif

    <div class="product-image">
        <a wire:navigate href="{{ route('frontend.product.single.view', $product->slug) }}">
            <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/500x500' }}"
                alt="{{ $product->name }}" loading="lazy">
        </a>

        <button class="wishlist-heart" wire:click="addToWishlist({{ $product->id }})" aria-label="Add to wishlist">
            <i class="far fa-heart"></i>
        </button>

    </div>

    <div class="product-info">
        <div class="product-rating">
           @php
                $average = $product->reviews->avg('rating') ?? 0;
                $reviewCount = $product->reviews->count();
            @endphp

            <div class="stars">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="{{ $i <= $average ? 'fas' : 'far' }} fa-star"></i>
                @endfor
                <span class="rating-count">({{ $reviewCount }})</span>
            </div>

        </div>
        <h3 class="product-title">{{ $product->name }}</h3>

        <div class="product-price">
            <span class="current-price">
                {{ setting('currency') }}{{ number_format($product->sale_price ?? $product->price, 2) }}
            </span>
            @if ($product->sale_price)
                <span class="original-price">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>

        <div class="product-cta">
            <!-- Add to Cart Button -->
            <button class="basket-btn" wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart">
                <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                    <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                </span>
                <span wire:loading wire:target="addToCart">
                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                </span>
            </button>

            <!-- Buy Now Button -->
            <button class="btn-order-now" wire:click="buyNow({{ $product->id }})" wire:loading.attr="disabled" wire:target="buyNow">
                <span wire:loading.remove wire:target="buyNow">
                    অর্ডার করুন
                </span>
                <span wire:loading wire:target="buyNow">
                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i> অপেক্ষা করুন...
                </span>
            </button>
        </div>

    </div>
</div>
