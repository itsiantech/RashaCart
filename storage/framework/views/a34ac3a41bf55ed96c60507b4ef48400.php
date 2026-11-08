<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product',
    'label' => 'Buy Now',
    'disabled' => false,
    'variantId' => null,
    'optionId' => null,
    'qty' => 1,
    'class' => 'pd-btn pd-btn--buy',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'product',
    'label' => 'Buy Now',
    'disabled' => false,
    'variantId' => null,
    'optionId' => null,
    'qty' => 1,
    'class' => 'pd-btn pd-btn--buy',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Services\PurchaseActions::popupOrderEnabled()): ?>
    <button type="button" class="<?php echo e($class); ?>"
        x-on:click='$dispatch("order-popup-open", { productId: <?php echo e((int) $product->id); ?>, variantId: <?php echo e($variantId ? (int) $variantId : "null"); ?>, optionId: <?php echo e($optionId ? (int) $optionId : "null"); ?>, qty: (function(){ var el = document.querySelector("[data-pd-qty]"); var v = el ? parseInt(el.value, 10) : 0; return v > 0 ? v : <?php echo e(max(1, (int) $qty)); ?>; })() })'
        <?php if($disabled): echo 'disabled'; endif; ?>>
        <i class="fas fa-bolt"></i>
        <span><?php echo e($label); ?></span>
    </button>
<?php else: ?>
    <button type="button" class="<?php echo e($class); ?>" wire:click="buyNow(<?php echo e($product->id); ?>)" <?php if($disabled): echo 'disabled'; endif; ?>>
        <i class="fas fa-bolt"></i>
        <span wire:loading.remove wire:target="buyNow"><?php echo e($label); ?></span>
        <span wire:loading wire:target="buyNow">Please wait...</span>
    </button>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views\components\order-now-button.blade.php ENDPATH**/ ?>