<?php
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
?>
<div class="product-card" <?php if($cardSwatches->isNotEmpty()): ?> x-data="{ cardImg: '<?php echo e($cardDefaultImg); ?>' }" <?php endif; ?>>

    <div class="product-image">
        <a wire:navigate href="<?php echo e(route('frontend.product.single.view', $product->slug)); ?>">
            <img <?php if($cardSwatches->isNotEmpty()): ?> :src="cardImg" <?php endif; ?>
                src="<?php echo e($cardDefaultImg); ?>"
                alt="<?php echo e($product->name); ?>" loading="lazy">
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cardSwatches->isNotEmpty()): ?>
        <div class="d-flex gap-1 mt-1 px-1 pb-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cardSwatches->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button"
                <?php if($sw['image']): ?> @click="cardImg = '<?php echo e($sw['image']); ?>'" <?php endif; ?>
                class="d-inline-block rounded-circle border p-0" title="<?php echo e($sw['value']); ?>"
                style="width:16px;height:16px;<?php echo e($sw['hex'] ? 'background:' . $sw['hex'] . ';' : 'background:#e5e7eb;'); ?>"
                aria-label="Show <?php echo e($sw['value']); ?> image"></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Models\FreeShipping::hasProductFreeShipping($product->id, $product->category_id)): ?>
        <div class="free-shipping-badge" style="background-color: orangered; top: 0px; left:0px;">
            <i class="fas fa-truck"></i> FREE
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



    </div>

    <div class="product-info">

        <h3 class="product-title"><?php echo e($product->name); ?></h3>

        <div class="product-price d-flex justify-content-between gap-1">
            <div>
                <span class="current-price">
                    <?php echo e(setting('currency')); ?><?php echo e(number_format($p['final'], 2)); ?>

                </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p['has_discount']): ?>
                <span class="original-price"><?php echo e(number_format($p['original'], 2)); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p['has_discount']): ?>
            <div class="product-badge">-<?php echo e($p['percent']); ?>%</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="product-cta">
            <!-- Buy Now Button -->
            <button class="btn-order-now" wire:click="buyNow(<?php echo e($product->id); ?>)" wire:loading.attr="disabled" wire:target="buyNow">
                <span wire:loading.remove wire:target="buyNow(<?php echo e($product->id); ?>)">
                    Buy Now
                </span>
                <span wire:loading wire:target="buyNow(<?php echo e($product->id); ?>)">
                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Please wait...
                </span>
            </button>

            <button class="cart-button"
                wire:click="addToCart(<?php echo e($product->id); ?>)" wire:loading.attr="disabled"
                wire:target="addToCart(<?php echo e($product->id); ?>)">

                <span wire:loading.remove wire:target="addToCart(<?php echo e($product->id); ?>)">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <span wire:loading wire:target="addToCart(<?php echo e($product->id); ?>)">
                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                </span>
            </button>
        </div>

    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/layouts/partial/product-card.blade.php ENDPATH**/ ?>