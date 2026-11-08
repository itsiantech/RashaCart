<div class="op-root">
<?php echo $__env->make("livewire.frontend.partials.popup-styles", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($open): ?>
        <div class="op-layer" wire:keydown.escape.window="close">
            <div class="op-backdrop" wire:click="close"></div>

            <div class="op-card" role="dialog" aria-modal="true" aria-labelledby="op-title">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($placed): ?>
                    <div class="op-success">
                        <div class="op-check"><i class="fas fa-check"></i></div>
                        <h3>অর্ডার সফলভাবে জমা হয়েছে!</h3>
                        <p>ইনভয়েস নম্বর: <strong><?php echo e($placedInvoice); ?></strong></p>
                        <p>আমাদের প্রতিনিধি শীঘ্রই কল করে অর্ডারটি কনফার্ম করবেন।</p>
                        <?php echo \App\Support\Hooks::renderSlot('popup.success.extra', ['invoice' => $placedInvoice, 'data' => $successData]); ?>

                        <div class="op-actions">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($waUrl !== ''): ?>
                                <a class="op-btn op-btn--wa" href="<?php echo e($waUrl); ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-whatsapp"></i> WhatsApp-এ চ্যাট করুন
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <button type="button" class="op-btn op-btn--cod" wire:click="close">ঠিক আছে</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="op-head">
                        <div>
                            <h3 id="op-title">
                                <?php echo e($showPicker ? 'আপনার পছন্দ বেছে নিন' : setting('popup_order_title', 'Order Now')); ?>

                            </h3>
                            <p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPicker): ?>
                                    <?php echo e(collect($pickerData['groups'] ?? [])->pluck('name')->implode(' · ')); ?><?php echo e(! empty($pickerData['options']) ? ' · ডিজাইন' : ''); ?> বেছে নিন
                                <?php else: ?>
                                    ক্যাশ অন ডেলিভারিতে অর্ডার করুন
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                        <button type="button" class="op-x" wire:click="close" aria-label="বন্ধ করুন">&times;</button>
                    </div>

                    <div class="op-prod">
                        <div>
                            <strong><?php echo e($productName); ?></strong>
                            <small>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $showPicker && $this->pickedLabel() !== ''): ?>
                                    <?php echo e($this->pickedLabel()); ?> ·
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                পরিমাণ: <?php echo e($qty); ?>

                            </small>
                        </div>
                        <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($unitPrice, 2)); ?></span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPicker): ?>
                        <div class="op-picker">
                            <?php if (isset($component)) { $__componentOriginald3dae08b25edfea620a1ed9881eb12f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald3dae08b25edfea620a1ed9881eb12f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.variant-picker','data' => ['groups' => $pickerData['groups'] ?? [],'picks' => $picks,'availability' => $availability,'options' => $pickerData['options'] ?? [],'optionId' => $optionId,'optionRequired' => $pickerData['option_required'] ?? false,'select' => 'selectPick','selectOption' => 'selectPickerOption']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('variant-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['groups' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pickerData['groups'] ?? []),'picks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($picks),'availability' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availability),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pickerData['options'] ?? []),'option-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($optionId),'option-required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pickerData['option_required'] ?? false),'select' => 'selectPick','select-option' => 'selectPickerOption']); ?>
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
                                    <button type="button" wire:click="decreaseQty" <?php if($qty <= 1): echo 'disabled'; endif; ?>>&minus;</button>
                                    <b><?php echo e($qty); ?></b>
                                    <button type="button" wire:click="increaseQty"
                                        <?php if($pickerStock > 0 && $qty >= $pickerStock): echo 'disabled'; endif; ?>>+</button>
                                </div>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pickerError !== ''): ?>
                                <div class="vp-note"><?php echo e($pickerError); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="op-actions">
                                <button type="button" class="op-btn op-btn--cod" wire:click="continueToForm">
                                    চালিয়ে যান <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                    <form wire:submit="confirmCod" novalidate>
                        <?php
                            $stepSlot = $this->stepSlot();
                            $hookData = [
                                'component' => $this,
                                'product_id' => $productId,
                                'variant_id' => $variantId,
                                'option_id' => $optionId,
                                'qty' => $qty,
                                'zone' => $zone,
                                'unit_price' => $unitPrice,
                                'subtotal' => $unitPrice * $qty,
                                'shipping' => $this->deliveryCharge(),
                                'total' => $this->total(),
                                'currency' => setting('currency'),
                            ];
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pickerRequired): ?>
                            <div class="op-picked">
                                <span><?php echo e($this->pickedLabel()); ?></span>
                                <button type="button" wire:click="editPicker">পরিবর্তন</button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="op-hp" aria-hidden="true">
                            <label>Website</label>
                            <input type="text" tabindex="-1" autocomplete="off" wire:model="website">
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stepSlot === null): ?>
                        <div class="op-field">
                            <label for="op-name">আপনার নাম *</label>
                            <input id="op-name" type="text" wire:model="name" placeholder="আপনার নাম লিখুন"
                                autocomplete="name">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="op-err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="op-field">
                            <label for="op-phone">মোবাইল নাম্বার *</label>
                            <input id="op-phone" type="tel" wire:model="phone" placeholder="01712345678"
                                inputmode="numeric" autocomplete="tel">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="op-err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="op-field">
                            <label for="op-address">সম্পূর্ণ ঠিকানা *</label>
                            <textarea id="op-address" wire:model="address" rows="2"
                                placeholder="বাসা/রোড, এলাকা, থানা, জেলা"></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="op-err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="op-field">
                            <label for="op-email">ইমেইল (ঐচ্ছিক)</label>
                            <input id="op-email" type="email" wire:model="email" placeholder="you@example.com"
                                autocomplete="email">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="op-err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="op-field">
                            <label>ডেলিভারি এরিয়া *</label>
                            <div class="op-zones">
                                <label class="op-zone <?php echo e($zone === 'inside' ? 'is-active' : ''); ?>">
                                    <input type="radio" wire:model.live="zone" value="inside">
                                    <span>ঢাকার ভিতরে</span>
                                    <strong><?php echo e(setting('currency')); ?> <?php echo e(number_format((float) setting('dc_inside', 60), 0)); ?></strong>
                                </label>
                                <label class="op-zone <?php echo e($zone === 'outside' ? 'is-active' : ''); ?>">
                                    <input type="radio" wire:model.live="zone" value="outside">
                                    <span>ঢাকার বাইরে</span>
                                    <strong><?php echo e(setting('currency')); ?> <?php echo e(number_format((float) setting('dc_outside', 120), 0)); ?></strong>
                                </label>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['zone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="op-err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="op-summary">
                            <div class="op-row">
                                <span>সাবটোটাল</span>
                                <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($unitPrice * $qty, 2)); ?></span>
                            </div>
                            <div class="op-row">
                                <span>ডেলিভারি চার্জ</span>
                                <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($this->deliveryCharge(), 2)); ?></span>
                            </div>
                            <div class="op-row op-total">
                                <span>সর্বমোট</span>
                                <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($this->total(), 2)); ?></span>
                            </div>
                            <?php echo \App\Support\Hooks::renderSlot('popup.summary.extra', $hookData); ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stepSlot !== null): ?>
                            <?php echo \App\Support\Hooks::renderSlot($stepSlot, $hookData); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="op-actions">
                            <button type="submit" class="op-btn op-btn--cod" wire:loading.attr="disabled"
                                wire:target="confirmCod,confirmWhatsApp">
                                <span wire:loading.remove wire:target="confirmCod,confirmWhatsApp">
                                    <i class="fas fa-check"></i> <?php echo e($primaryLabel); ?>

                                </span>
                                <span wire:loading wire:target="confirmCod,confirmWhatsApp">অপেক্ষা করুন...</span>
                            </button>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whatsappEnabled): ?>
                                <button type="button" class="op-btn op-btn--wa" wire:click="confirmWhatsApp"
                                    wire:loading.attr="disabled" wire:target="confirmCod,confirmWhatsApp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp-এ অর্ডার
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php echo \App\Support\Hooks::renderSlot('popup.footer', $hookData); ?>
                        </div>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

    <?php
        $__scriptKey = '3843455611-0';
        ob_start();
    ?>
    <script>
        window.addEventListener('pagehide', () => { document.body.style.overflow = ''; });
    </script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\frontend\order-popup.blade.php ENDPATH**/ ?>