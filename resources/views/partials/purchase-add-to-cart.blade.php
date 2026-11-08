@php
    $purchaseAddEnabled = \App\Services\PurchaseActions::addToCartEnabled();
    $purchaseBuyEnabled = \App\Services\PurchaseActions::buyNowEnabled();
    $purchaseAddLabel = \App\Services\PurchaseActions::addToCartLabel();
    $purchaseDisabledText = \App\Services\PurchaseActions::disabledText();
@endphp

@if ($purchaseAddEnabled)
    @if (\App\Services\PurchaseActions::requiresVariantSelection($product))
        @if (\App\Services\PurchaseActions::variantPopupEnabled())
        <button type="button" class="btn-add-cart" title="{{ $purchaseAddLabel }}"
            onclick="Livewire.dispatch('quick-add-open', { productId: {{ (int) $product->id }} })">
            <i class="fas fa-cart-plus me-1"></i>{{ $purchaseAddLabel }}
        </button>
        @else
        <a href="{{ route('frontend.product.single.view', $product->slug) }}" wire:navigate
            class="btn-add-cart" title="{{ $purchaseAddLabel }}">
            <i class="fas fa-cart-plus me-1"></i>{{ $purchaseAddLabel }}
        </a>
        @endif
    @else
    <form method="POST" action="{{ route('frontend.cart.add', $product->id) }}">
        @csrf
        <button type="submit" class="btn-add-cart" title="{{ $purchaseAddLabel }}">
            <i class="fas fa-cart-plus me-1"></i>{{ $purchaseAddLabel }}
        </button>
    </form>
    @endif
@elseif (! $purchaseBuyEnabled)
<span class="purchase-disabled-text">{{ $purchaseDisabledText }}</span>
@endif
