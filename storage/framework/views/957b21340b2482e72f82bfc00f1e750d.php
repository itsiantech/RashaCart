<div class="list-icon-picker d-flex flex-wrap gap-2">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\LandingPage::LIST_ICONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label
            class="list-icon-option <?php echo e($value === $icon ? 'is-selected' : ''); ?>"
            title="<?php echo e($icon); ?>"
        >
            <input
                type="radio"
                class="d-none"
                wire:model.live="<?php echo e($model); ?>"
                value="<?php echo e($icon); ?>"
            >
            <i class="bx <?php echo e($icon); ?>"></i>
        </label>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<small class="text-muted d-block mt-2">All items in this list will use the same icon.</small>

<?php if (! $__env->hasRenderedOnce('439688ce-dd77-42ff-a0fd-7debf91554ec')): $__env->markAsRenderedOnce('439688ce-dd77-42ff-a0fd-7debf91554ec'); ?>
<style>
    .list-icon-option {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #d9dee3;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
        margin: 0;
        color: #566a7f;
        transition: all .15s ease;
    }
    .list-icon-option i {
        font-size: 1.35rem;
        line-height: 1;
    }
    .list-icon-option:hover {
        border-color: #71dd37;
        color: #71dd37;
    }
    .list-icon-option.is-selected {
        border-color: #71dd37;
        background: #e8fadf;
        color: #71dd37;
        box-shadow: 0 0 0 2px rgba(113, 221, 55, .25);
    }
</style>
<?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/landing-page/partials/list-icon-picker.blade.php ENDPATH**/ ?>