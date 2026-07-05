<div class="product-card">

    <div class="product-image">
        <a wire:navigate href="{{ route('frontend.product.single.view', $product->slug) }}">
            <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/500x500' }}"
                alt="{{ $product->name }}" loading="lazy">
        </a>

        @if (\App\Models\FreeShipping::hasProductFreeShipping($product->id, $product->category_id))
        <div class="free-shipping-badge" style="background-color: orangered; top: 0px; left:0px;">
            <i class="fas fa-truck"></i> FREE
        </div>
        @endif



    </div>

    <div class="product-info">

        <h3 class="product-title">{{ $product->name }}</h3>

        <div class="product-price d-flex justify-content-between gap-1">
            <div class="">
                <span class="current-price">
                    {{ setting('currency') }}{{ number_format($product->sale_price ?? $product->price, 2) }}
                </span>
                @if ($product->sale_price)
                <span class="original-price">{{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            @if ($product->sale_price && $product->sale_price < $product->price)
                <div class="product-badge">
                    @php
                    $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
                    @endphp
                    -{{ $discountPercent }}%
                </div>
                @endif
        </div>

        <div class="product-cta">
            <!-- Buy Now Button -->
            <button class="btn-order-now" wire:click="buyNow({{ $product->id }})" wire:loading.attr="disabled" wire:target="buyNow">
                <span wire:loading.remove wire:target="buyNow({{ $product->id }})">
                    Buy Now
                </span>
                <span wire:loading wire:target="buyNow({{ $product->id }})">
                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Please wait...
                </span>
            </button>

            <button class="cart-button"
                wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled"
                wire:target="addToCart({{ $product->id }})">

                <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <span wire:loading wire:target="addToCart({{ $product->id }})">
                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                </span>
            </button>
        </div>

    </div>
</div>