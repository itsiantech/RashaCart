<?php
$categories = $categories ?? collect();
$brands = $brands ?? collect();
?>
<section class="category-list">
    <ul class="categories-list">
        <h3 class="filter-title"><i class="fas fa-microchip"></i> Category</h3>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="<?php echo e($category->allChildren->count() ? 'cat-submenu' : ''); ?>">
            <a wire:navigate href="<?php echo e(route('frontend.category.products.view', $category->slug)); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->image): ?>
                <img src="<?php echo e(asset('storage/'.$category->image)); ?>" alt="<?php echo e($category->name); ?>" width="20" height="20" style="object-fit:cover; margin-right:5px;">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($category->name); ?>


                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->allChildren->count()): ?>
                <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->allChildren->count()): ?>
            <ul class="submenu">
                <?php echo $__env->make('livewire.frontend.layouts.partial.category.subcategory-list', ['categories' => $category->allChildren], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </ul>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->count() > 6): ?>
        <li><a wire:navigate href="<?php echo e(route('frontend.category.view')); ?>"><i class="fas fa-ellipsis-h"></i> View All Categories</a></li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>

    <ul class="categories-list">
        <h3 class="filter-title"><i class="fas fa-microchip"></i> Brand</h3>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $brands->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <a wire:navigate href="<?php echo e(route('frontend.brands.products.view', $brand->slug)); ?>">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand->logo): ?>
                <img
                    src="<?php echo e(asset('storage/'.$brand->logo)); ?>"
                    alt="<?php echo e($brand->name); ?>"
                    width="20"
                    height="20"
                    style="object-fit:cover; margin-right:5px;">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php echo e($brand->name); ?>

            </a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brands->count() > 6): ?>
        <li>
            <a wire:navigate href="<?php echo e(route('frontend.brand.view')); ?>">
                <i class="fas fa-ellipsis-h"></i> View All Brands
            </a>
        </li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
</section><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/layouts/partial/category/category-list.blade.php ENDPATH**/ ?>