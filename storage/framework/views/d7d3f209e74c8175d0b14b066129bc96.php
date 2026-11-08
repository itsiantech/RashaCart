<?php
    $purchaseAddEnabled = \App\Services\PurchaseActions::addToCartEnabled();
    $purchaseBuyEnabled = \App\Services\PurchaseActions::buyNowEnabled();
    $purchaseAddLabel = \App\Services\PurchaseActions::addToCartLabel();
    $purchaseDisabledText = \App\Services\PurchaseActions::disabledText();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchaseAddEnabled): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Services\PurchaseActions::requiresVariantSelection($product)): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Services\PurchaseActions::variantPopupEnabled()): ?>
        <button type="button" class="btn-add-cart" title="<?php echo e($purchaseAddLabel); ?>"
            onclick="Livewire.dispatch('quick-add-open', { productId: <?php echo e((int) $product->id); ?> })">
            <i class="fas fa-cart-plus me-1"></i><?php echo e($purchaseAddLabel); ?>

        </button>
        <?php else: ?>
        <a href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>" wire:navigate
            class="btn-add-cart" title="<?php echo e($purchaseAddLabel); ?>">
            <i class="fas fa-cart-plus me-1"></i><?php echo e($purchaseAddLabel); ?>

        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
    <form method="POST" action="<?php echo e(route('frontend.cart.add', $product->id)); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn-add-cart" title="<?php echo e($purchaseAddLabel); ?>">
            <i class="fas fa-cart-plus me-1"></i><?php echo e($purchaseAddLabel); ?>

        </button>
    </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php elseif(! $purchaseBuyEnabled): ?>
<span class="purchase-disabled-text"><?php echo e($purchaseDisabledText); ?></span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views\partials\purchase-add-to-cart.blade.php ENDPATH**/ ?>