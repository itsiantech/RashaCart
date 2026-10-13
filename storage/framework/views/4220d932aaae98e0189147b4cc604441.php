<div>
     <div class="container">
        <h1 class="cart-title">আমার কার্ট</h1>

        <div class="cart-grid">
            <!-- Left: items list -->
            <section class="cart-left">
                <div class="cart-table">                  

                    <div class="ct-body P-3">
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('frontend.components.cart-items', ['cartItems' => $cartItems]);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2653615170-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    </div>


                </div>
            </section>

            <!-- Right: summary -->
            <aside class="cart-right">
                <div class="cart-summary">
                    <div class="cs-title">অর্ডার সারাংশ</div>
                    <div class="cs-lines">
                        <div class="line"><span>সাব টোটাল</span><strong><?php echo e(setting('currency')); ?>  <?php echo e(number_format($subtotal)); ?></strong>
                        </div>
                        <div class="line"><span> ডিসকাউন্ট</span><strong>-<?php echo e(setting('currency')); ?>  <?php echo e(number_format($discount)); ?></strong>
                        </div>
                        <div class="line total"><span>সর্বমোট</span><strong><?php echo e(setting('currency')); ?>  <?php echo e(number_format($total)); ?></strong>
                        </div>
                    </div>
                    <a wire:navigate href="<?php echo e(route('frontend.checkout')); ?>" class="cs-checkout">চেকআউট</a>
                </div>
            </aside>
        </div>

        <!-- You May Like -->
        <section class="cart-reco">
            <h2 class="reco-title">You May Like</h2>
            <div class="products-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mayLikeProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('livewire.frontend.layouts.partial.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>

</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/product/cart-view.blade.php ENDPATH**/ ?>