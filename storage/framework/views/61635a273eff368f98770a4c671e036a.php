<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($open): ?>
        <?php echo $__env->make('livewire.frontend.partials.popup-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="op-layer" role="dialog" aria-modal="true" aria-labelledby="quick-add-title"
            wire:keydown.escape.window="close">
            <div class="op-backdrop" wire:click="close"></div>
            <div class="op-card">
                <div class="op-head">
                    <div>
                        <h3 id="quick-add-title">আপনার পছন্দ বেছে নিন</h3>
                        <p>
                            <?php echo e(collect($pickerData['groups'] ?? [])->pluck('name')->implode(' · ')); ?><?php echo e(! empty($pickerData['options']) ? ' · ডিজাইন' : ''); ?>

                            বেছে নিয়ে কার্টে যোগ করুন
                        </p>
                    </div>
                    <button type="button" class="op-x" wire:click="close" aria-label="বন্ধ করুন">&times;</button>
                </div>

                <div class="op-prod">
                    <div>
                        <strong><?php echo e($productName); ?></strong>
                        <small><?php echo e($productSku !== '' ? $productSku : 'পরিমাণ: ' . $quantity); ?></small>
                    </div>
                    <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($unitPrice, 2)); ?></span>
                </div>

                <?php if (isset($component)) { $__componentOriginald3dae08b25edfea620a1ed9881eb12f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald3dae08b25edfea620a1ed9881eb12f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.variant-picker','data' => ['groups' => $pickerData['groups'] ?? [],'picks' => $picks,'availability' => $availability,'options' => $pickerData['options'] ?? [],'optionId' => $selectedOption,'optionRequired' => $pickerData['option_required'] ?? false,'select' => 'selectPick','selectOption' => 'selectPickerOption']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('variant-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['groups' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pickerData['groups'] ?? []),'picks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($picks),'availability' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availability),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pickerData['options'] ?? []),'option-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedOption),'option-required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pickerData['option_required'] ?? false),'select' => 'selectPick','select-option' => 'selectPickerOption']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald3dae08b25edfea620a1ed9881eb12f1)): ?>
<?php $attributes = $__attributesOriginald3dae08b25edfea620a1ed9881eb12f1; ?>
<?php unset($__attributesOriginald3dae08b25edfea620a1ed9881eb12f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald3dae08b25edfea620a1ed9881eb12f1)): ?>
<?php $component = $__componentOriginald3dae08b25edfea620a1ed9881eb12f1; ?>
<?php unset($__componentOriginald3dae08b25edfea620a1ed9881eb12f1); ?>
<?php endif; ?>

                <div class="vp-qty">
                    <span>পরিমাণ</span>
                    <div class="vp-stepper">
                        <button type="button" wire:click="dropQty" <?php if((int) $quantity <= 1): echo 'disabled'; endif; ?>>&minus;</button>
                        <b><?php echo e($quantity); ?></b>
                        <button type="button" wire:click="bumpQty"
                            <?php if((int) $quantity >= max((int) $pickerStock, 1)): echo 'disabled'; endif; ?>>+</button>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pickerError !== ''): ?>
                    <div class="vp-note"><?php echo e($pickerError); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="op-summary">
                    <div class="op-row op-total">
                        <span>সর্বমোট</span>
                        <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($unitPrice * (int) $quantity, 2)); ?></span>
                    </div>
                </div>

                <div class="op-actions">
                    <button type="button" class="op-btn op-btn--cod" wire:click="confirmQuickAdd"
                        wire:loading.attr="disabled" wire:target="confirmQuickAdd">
                        <span wire:loading.remove wire:target="confirmQuickAdd">
                            <i class="fas fa-shopping-cart"></i> <?php echo e(\App\Services\PurchaseActions::addToCartLabel()); ?>

                        </span>
                        <span wire:loading wire:target="confirmQuickAdd">যোগ হচ্ছে...</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/quick-add-popup.blade.php ENDPATH**/ ?>