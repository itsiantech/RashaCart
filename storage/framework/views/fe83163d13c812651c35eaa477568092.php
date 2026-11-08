<div class="container">
     <section class="categories-section">
            <div class="section-header">
                <h2>Shop by Brands</h2>
                <a wire:navigate href="<?php echo e(route('frontend.brand.view')); ?>" class="view-all">
                    View All Brands <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="categories-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $featuredBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a wire:navigate href="<?php echo e(route('frontend.brand.view', $brand->slug)); ?>"
                        class="category-card">
                        <div class="category-image">
                            <img src="<?php echo e($brand->logo ? asset('storage/' . $brand->logo) : 'https://placehold.co/500x500'); ?>"
                                alt="<?php echo e($brand->name); ?>" loading="lazy">
                        </div>
                        <h3 class="category-name"><?php echo e($brand->name); ?></h3>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
</div><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\frontend\brand\index.blade.php ENDPATH**/ ?>