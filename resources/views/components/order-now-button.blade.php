@props([
    'product',
    'label' => 'Buy Now',
    'disabled' => false,
    'variantId' => null,
    'optionId' => null,
    'qty' => 1,
    'class' => 'pd-btn pd-btn--buy',
])

@if (\App\Services\PurchaseActions::popupOrderEnabled())
    <button type="button" class="{{ $class }}"
        x-on:click='$dispatch("order-popup-open", { productId: {{ (int) $product->id }}, variantId: {{ $variantId ? (int) $variantId : "null" }}, optionId: {{ $optionId ? (int) $optionId : "null" }}, qty: (function(){ var el = document.querySelector("[data-pd-qty]"); var v = el ? parseInt(el.value, 10) : 0; return v > 0 ? v : {{ max(1, (int) $qty) }}; })() })'
        @disabled($disabled)>
        <i class="fas fa-bolt"></i>
        <span>{{ $label }}</span>
    </button>
@else
    <button type="button" class="{{ $class }}" wire:click="buyNow({{ $product->id }})" @disabled($disabled)>
        <i class="fas fa-bolt"></i>
        <span wire:loading.remove wire:target="buyNow">{{ $label }}</span>
        <span wire:loading wire:target="buyNow">Please wait...</span>
    </button>
@endif
