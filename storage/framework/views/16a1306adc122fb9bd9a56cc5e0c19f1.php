<div class="row mb-3">
    <label class="col-sm-2 col-form-label">Page Sections</label>
    <div class="col-sm-10">
        <div class="border rounded p-3">
            <p class="text-muted small mb-3 mb-md-2">Enable a section to show it on the public landing page. Disabled sections stay hidden even if content exists.</p>
            <div class="row">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\LandingPage::SECTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 mb-2">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="<?php echo e($field); ?>" id="<?php echo e($field); ?>" class="form-check-input">
                            <label class="form-check-label" for="<?php echo e($field); ?>"><?php echo e($label); ?></label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/landing-page/partials/section-toggles.blade.php ENDPATH**/ ?>