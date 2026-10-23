
<link href="<?php echo e(\App\Services\StyleSettings::FONT_LINK); ?>" rel="stylesheet" />
<style id="style-customizer-vars">
    :root {
        <?php $__currentLoopData = \App\Services\StyleSettings::cssVars(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($k); ?>: <?php echo e($v); ?>;
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    }
</style>
<?php /**PATH C:\Users\user\bandrose\resources\views/partials/style-vars.blade.php ENDPATH**/ ?>