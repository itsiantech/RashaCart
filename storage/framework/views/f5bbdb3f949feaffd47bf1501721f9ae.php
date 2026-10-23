<?php if (! $__env->hasRenderedOnce('af76a66e-fd85-41c6-8bd5-91a089110ebb')): $__env->markAsRenderedOnce('af76a66e-fd85-41c6-8bd5-91a089110ebb'); ?>
<style>
    /* Flyout for legacy recursive category lists (desktop hover, keyboard focus, mobile tap-to-open) */
    .cat-submenu { position: relative; }
    .cat-submenu > .submenu {
        display: none; position: absolute; left: 100%; top: 0; z-index: 60;
        min-width: 210px; background: #fff; border: 1px solid #e5e7eb; border-radius: 0 10px 10px 0;
        box-shadow: 8px 8px 24px rgba(0, 0, 0, 0.10); padding: 6px; list-style: none; margin: 0;
    }
    .cat-submenu:hover > .submenu,
    .cat-submenu:focus-within > .submenu,
    .cat-submenu.open > .submenu { display: block; }
    .cat-submenu > .submenu a { display: flex; align-items: center; gap: 6px; padding: .45rem .6rem; font-size: .82rem; color: #4b5563; text-decoration: none; border-radius: 6px; }
    .cat-submenu > .submenu a:hover { background: #f7f8fb; color: #7A0000; }
    .cat-submenu > a .arrow { cursor: pointer; padding: 4px 6px; margin: -4px -6px; }
</style>
<?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<li class="<?php echo e($category->allChildren->count() ? 'cat-submenu' : ''); ?>">

    <a wire:navigate href="<?php echo e(route('frontend.category.products.view', $category->slug)); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->image): ?>
        <img src="<?php echo e(asset('storage/'.$category->image)); ?>" width="20">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo e($category->name); ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->allChildren->count()): ?>
        <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->allChildren->count()): ?>
    <ul class="submenu">
        <?php echo $__env->make('livewire.frontend.layouts.partial.category.subcategory-list', [
            'categories' => $category->allChildren
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/layouts/partial/category/subcategory-list.blade.php ENDPATH**/ ?>