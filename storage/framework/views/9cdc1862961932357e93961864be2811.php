<div>
    <button type="button" id="sb-theme-fab" wire:click="$toggle('open')" title="Sidebar theme" aria-label="Sidebar theme">
        <i class="bx bx-palette"></i>
    </button>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($open): ?>
    <div id="sb-theme-panel">
        <h6>Sidebar Theme</h6>
        <div class="sb-dots">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" class="sb-dot <?php echo e($theme === $key ? 'sel' : ''); ?>"
                style="background:<?php echo e($t['swatch']); ?>" title="<?php echo e($t['label']); ?>"
                aria-label="<?php echo e($t['label']); ?>"
                wire:click="selectTheme('<?php echo e($key); ?>')"></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <h6>Menu Font Size</h6>
        <div class="sb-row">
            <button type="button" wire:click="stepFont(-1)" <?php if($fontSize <= $min): echo 'disabled'; endif; ?> aria-label="Decrease font size">A-</button>
            <span class="sb-size"><?php echo e($fontSize); ?>px</span>
            <button type="button" wire:click="stepFont(1)" <?php if($fontSize >= $max): echo 'disabled'; endif; ?> aria-label="Increase font size">A+</button>
            <button type="button" wire:click="resetTheme" title="Reset to default" aria-label="Reset">↺</button>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/settings/sidebar-theme-picker.blade.php ENDPATH**/ ?>