@if (\App\Services\PurchaseActions::addToCartEnabled() && \App\Services\PurchaseActions::variantPopupEnabled())
    <livewire:frontend.quick-add-popup />
@endif
