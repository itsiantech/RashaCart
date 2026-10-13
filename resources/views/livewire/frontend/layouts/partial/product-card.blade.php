@php
$p = $product->price_info;
$cardSwatches = collect();
if ($product->relationLoaded('variants')) {
    foreach ($product->variants as $variant) {
        foreach ($variant->variantAttributeValues as $vav) {
            $attrName = strtolower($vav->attribute?->name ?? '');
            if (! in_array($attrName, ['color', 'colour'])) {
                continue;
            }
            $val = $vav->attributeValue?->value;
            if (! $val || $cardSwatches->contains('value', $val)) {
                continue;
            }
            $cardSwatches->push([
                'value' => $val,
                'hex' => $vav->attributeValue?->color_hex,
                'image' => $variant->image ? asset('storage/' . $variant->image) : null,
            ]);
        }
    }
}
$cardDefaultImg = $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/500x500';
@endphp
<div class="product-card" @if($cardSwatches->isNotEmpty()) x-data="{ cardImg: '{{ $cardDefaultImg }}' }" @endif>

    <div class="product-image">
        <a wire:navigate href="{{ route('frontend.product.single.view', $product->slug) }}">
            <img @if($cardSwatches->isNotEmpty()) :src="cardImg" @endif
                src="{{ $cardDefaultImg }}"
                alt="{{ $product->name }}" loading="lazy">
        </a>

        @if ($cardSwatches->isNotEmpty())
        <div class="d-flex gap-1 mt-1 px-1 pb-1">
            @foreach ($cardSwatches->take(6) as $sw)
            <button type="button"
                @if($sw['image']) @click="cardImg = '{{ $sw['image'] }}'" @endif
                class="d-inline-block rounded-circle border p-0" title="{{ $sw['value'] }}"
                style="width:16px;height:16px;{{ $sw['hex'] ? 'background:' . $sw['hex'] . ';' : 'background:#e5e7eb;' }}"
                aria-label="Show {{ $sw['value'] }} image"></button>
            @endforeach
        </div>
        @endif

        @if (\App\Models\FreeShipping::hasProductFreeShipping($product->id, $product->category_id))
        <div class="free-shipping-badge" style="background-color: orangered; top: 0px; left:0px;">
            <i class="fas fa-truck"></i> FREE
        </div>
        @endif



    </div>

    <div class="product-info">

        <h3 class="product-title">{{ $product->name }}</h3>

        <div class="product-price d-flex justify-content-between gap-1">
            <div>
                <span class="current-price">
                    {{ setting('currency') }}{{ number_format($p['final'], 2) }}
                </span>
                @if ($p['has_discount'])
                <span class="original-price">{{ number_format($p['original'], 2) }}</span>
                @endif
            </div>

            @if ($p['has_discount'])
            <div class="product-badge">-{{ $p['percent'] }}%</div>
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