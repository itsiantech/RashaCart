<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-category-sidebar">
                <div class="categories-header">
                    <h3><i class="fas fa-sliders-h "></i> Filters</h3>
                </div>

                <div class="filter-card py-3">
                    <ul class="check-list">
                        <?php echo $__env->make('livewire.frontend.layouts.partial.category.category-list', [
                        'categories' => collect($allCategories)->where('parent_id', null),
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </ul>
                </div>

                <div class="filter-card  pb-3">
                    <h3 class="filter-title"><i class="fas fa-mobile-alt"></i> Price</h3>
                    <div class="size-options">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['500-1000', '1000-2000', '2000-3000', '3000-4000']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button
                            wire:click.prevent="togglePrice('<?php echo e($price); ?>')"
                            class="size-option <?php echo e($selectedPrice === $price ? 'active' : ''); ?>">
                            <?php echo e($price); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="products-grid-container">
                <div class="section-header">
                    <h2>
                        <?php echo e($category ? 'Category - ' . $category->name : 'All Categories'); ?>

                    </h2>
                </div>

                <div class="products-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a wire:navigate href="<?php echo e(route('frontend.category.products.view', $category->slug)); ?>"
                        class="category-card">
                        <div class="category-image">
                            <img src="<?php echo e($category->image ? asset('storage/' . $category->image) : 'https://placehold.co/500x500'); ?>"
                                alt="<?php echo e($category->name); ?>" loading="lazy">
                        </div>
                        <h3 class="category-name"><?php echo e($category->name); ?></h3>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-products">
                        <div class="cart-empty">
                            <i class="fas fa-shopping-basket"></i>
                            <p>No categories found.</p>
                            <p>We couldn’t find any categories matching your criteria. Please adjust your filters or try a different search.</p>
                            <a wire:navigate href="<?php echo e(route('home')); ?>" class="btn-continue-shopping">Continue Shopping</a>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <style>
                    .products-grid {
                        grid-template-columns: repeat(5, 1fr) !important;
                        gap: 1.25rem;
                    }

                    @media (max-width: 1400px) {
                        .products-grid {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }
                    }

                    @media (max-width: 1200px) {
                        .products-grid {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }

                    @media (max-width: 900px) {
                        .products-grid {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }

                    .empty-products {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 90%;
                        max-width: 300px;
                    }

                    .empty-icon i {
                        font-size: 64px;
                        color: #ccc;
                    }

                    .categories-category-sidebar {
                        width: 100%;
                        background: white;
                        border-radius: 8px;
                        box-shadow: var(--box-shadow);
                        overflow: hidden;
                        margin-top: 20px;
                        margin-bottom: 20px;
                    }

                    @media screen and (max-width: 768px) {
                        .categories-category-sidebar {
                            overflow: hidden;
                            display: none;
                        }
                    }
                </style>

                <div class="pagination-container">
                    <?php echo e($categories->links()); ?>

                </div>

            </div>
        </div>
    </div>
</section><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/product/category-view.blade.php ENDPATH**/ ?>