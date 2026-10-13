<div class="cart-list">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="cart-item">
        <div class="col-product">
            <img src="<?php echo e($item['img'] ?? 'https://placehold.co/500x500'); ?>" alt="<?php echo e($item['title'] ?? 'Product'); ?>">
            <div class="product-details">

                <div class="ci-title"><?php echo e($item['title'] ?? 'Untitled Product'); ?></div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['color'])): ?>
                <div class="ci-variant ci-color small" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-circle me-1" style="color: #d32f2f;"></i>
                    <strong>Color:</strong>
                    <span style="color: #333; font-weight: 600;"><?php echo e($item['color']); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['size'])): ?>
                <div class="ci-variant ci-size small" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-expand me-1" style="color: #0056b3;"></i>
                    <strong>Size:</strong>
                    <span style="color: #333; font-weight: 600;"><?php echo e($item['size']); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($item['color']) && empty($item['size']) && !empty($item['variant_label'])): ?>
                <div class="ci-variant small text-muted" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-palette me-1"></i>
                    <strong>Variant:</strong>
                    <span style="color: #d32f2f; font-weight: 600;"><?php echo e($item['variant_label']); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ci-meta">
                    
                    <div class="quantity-controls">
                        <button wire:click="decreaseQty('<?php echo e($id); ?>')" class="ci-minus"
                            wire:loading.attr="disabled" wire:target="decreaseQty('<?php echo e($id); ?>')">
                            <span wire:loading.remove wire:target="decreaseQty('<?php echo e($id); ?>')">-</span>
                            <i wire:loading wire:target="decreaseQty('<?php echo e($id); ?>')" class="fa-solid fa-spinner fa-spin"></i>
                        </button>

                        <input type="number" value="<?php echo e($item['qty'] ?? 1); ?>" readonly class="ci-input">

                        <button wire:click="increaseQty('<?php echo e($id); ?>')" class="ci-plus"
                            wire:loading.attr="disabled" wire:target="increaseQty('<?php echo e($id); ?>')">
                            <span wire:loading.remove wire:target="increaseQty('<?php echo e($id); ?>')">+</span>
                            <i wire:loading wire:target="increaseQty('<?php echo e($id); ?>')" class="fa-solid fa-spinner fa-spin"></i>
                        </button>
                    </div>

                    <div class="ci-price">Price: <?php echo e(setting('currency')); ?> <?php echo e(number_format($item['price'] ?? 0)); ?></div>
                    <div class="ci-total">Total: <?php echo e(setting('currency')); ?> <?php echo e(number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1))); ?></div>

                    <button wire:click="removeItem('<?php echo e($id); ?>')" class="ci-remove" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                        <span wire:loading wire:target="removeItem('<?php echo e($id); ?>')">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="cart-empty">
        <i class="fas fa-shopping-basket"></i>
        <p>Your Cart List is Empty!</p>
        <p>Sorry! your cart has no item to show. Please add some product to see here.</p>
        <a wire:navigate href="<?php echo e(route('home')); ?>" class="btn-continue-shopping">Continue Shopping</a>
    </div>
    <?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($cartItems) > 0): ?>
    <div class="cart-subtotal d-none">
        <div class="summary-section">Summary</div>
        <div class="subtotal-section">
            <span class="d-flex">
                Subtotal <p class="small">(<?php echo e(count($cartItems)); ?> items)</p>:
            </span>
            <strong>
                <?php echo e(setting('currency')); ?> <?php echo e(number_format(collect($cartItems)->sum(fn($i) => ($i['price'] ?? 0) * ($i['qty'] ?? 1)))); ?>

            </strong>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/components/cart-items.blade.php ENDPATH**/ ?>