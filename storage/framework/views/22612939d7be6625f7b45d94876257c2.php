<div>
    <h4 class="fw-bold mb-1">Style Settings <small class="text-muted">(Style Customizer)</small></h4>
    <p class="text-muted">Header, navigation, search, footer, font আর flash sales card — সব এখান থেকে dynamic বদলান। Save করলেই পুরো website-এ লাগবে।</p>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <label class="form-label"><?php echo e($meta['label']); ?></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($meta['type'] === 'color'): ?>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" wire:model="form.<?php echo e($key); ?>"
                            class="form-control form-control-color" value="<?php echo e($meta['default'] ?? '#000000'); ?>">
                        <code><?php echo e($form[$key] ?? 'unset'); ?></code>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($meta['optional'])): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            wire:click="$set('form.<?php echo e($key); ?>', null)">Clear</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php else: ?>
                    <select class="form-select" wire:model="form.<?php echo e($key); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key === 'flash_font'): ?>
                        <option value="">Body font-ই ব্যবহার হবে</option>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fonts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fkey => $font): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($fkey); ?>"><?php echo e($font['label']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.' . $key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger d-block"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="button" wire:click="save" class="btn btn-primary">Save Style</button>
                <button type="button" wire:click="resetDefaults" class="btn btn-outline-secondary">Reset to default</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/settings/style-customizer.blade.php ENDPATH**/ ?>