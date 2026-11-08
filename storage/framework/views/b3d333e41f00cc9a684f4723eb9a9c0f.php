<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $productId): ?>
        <p class="text-muted mb-0">
            প্রোডাক্ট সেভ করার পর এখান থেকে এডভান্স সেট করুন।
        </p>
    <?php else: ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">এডভান্স লাগবে</label>
                <select class="form-select" wire:model.live="mode">
                    <option value="none">না — সাধারণ ক্যাশ অন ডেলিভারি</option>
                    <option value="percent">শেয়ারক হিসেবে (%)</option>
                    <option value="fixed">নির্দিষ্ট টাকা</option>
                </select>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode !== 'none'): ?>
                <div class="col-md-4">
                    <label class="form-label"><?php echo e($mode === 'percent' ? 'শেয়ারক (%)' : 'টাকা'); ?></label>
                    <input type="number" step="0.01" min="0.01" class="form-control" wire:model.live="value"
                        placeholder="<?php echo e($mode === 'percent' ? '30' : '500'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger d-block"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary" wire:click="save">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($example): ?>
            <div class="alert alert-info mt-3 mb-0 py-2">
                <small class="d-block text-muted">উদাহরণ — <?php echo e(setting('currency')); ?><?php echo e(number_format($example['price'], 2)); ?> পণ্য
                    + <?php echo e(setting('currency')); ?><?php echo e(number_format($example['shipping'], 2)); ?> ডেলিভারি:</small>
                <strong>এখনই <?php echo e(setting('currency')); ?><?php echo e(number_format($example['advance'], 0)); ?></strong>
                <span class="mx-2">·</span>
                <span>ডেলিভারির সময় <?php echo e(setting('currency')); ?><?php echo e(number_format($example['due'], 0)); ?></span>
            </div>
        <?php elseif($mode !== 'none'): ?>
            <div class="alert alert-warning mt-3 mb-0 py-2">
                <small>
                    এই সেটিংয়ে পুরোটাই এডভান্স হয়ে যাচ্ছে (বাকি টাকা থাকছে না) — শেয়ারক কমান, অথবা পণ্যের দাম বাড়ান।
                </small>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\livewire\product-rule.blade.php ENDPATH**/ ?>