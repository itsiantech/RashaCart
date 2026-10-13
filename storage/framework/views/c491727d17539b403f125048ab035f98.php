<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-category-sidebar d-lg-block"
                x-data="{ open: false }">
                <div class="categories-header" @click="open = !open" style="cursor:pointer;">
                    <h3>
                        <i class="fas fa-sliders-h"></i> Filters
                        <i class="fas fa-chevron-down ms-2 d-lg-none"
                            :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </h3>
                </div>

                <div class="filter-card py-3 sidebar-body" :class="open ? 'd-block' : ''">
                    <ul class="check-list">
                        <?php echo $__env->make('livewire.frontend.layouts.partial.category.category-list', [
                        'categories' => $categories->where('parent_id', null),
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </ul>
                </div>

                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : ''">
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

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->filterAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filterAttr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : ''">
                    <h3 class="filter-title"><i class="fas fa-filter"></i> <?php echo e($filterAttr->name); ?></h3>
                    <div class="size-options">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filterAttr->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filterVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isOn = in_array($filterVal->id, $selectedAttrs[$filterAttr->id] ?? []); ?>
                        <button
                            wire:click.prevent="toggleAttrValue(<?php echo e($filterAttr->id); ?>, <?php echo e($filterVal->id); ?>)"
                            class="size-option <?php echo e($isOn ? 'active' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterVal->color_hex): ?>
                            <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:<?php echo e($filterVal->color_hex); ?>;vertical-align:-1px;"></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php echo e($filterVal->value); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </aside>

            <!-- Products Grid -->
            <div class="products-grid-container">
                <div class="section-header">
                    <h2>
                        <?php echo e($category ? 'Category - ' . $category->name : 'All Categories'); ?>

                    </h2>
                </div>

                <div class="products-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('livewire.frontend.layouts.partial.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-products">
                        <div class="cart-empty">
                            <i class="fas fa-shopping-basket"></i>
                            <p>No products found.</p>
                            <p>We couldn’t find any products matching your criteria. Please adjust your filters or try a different search.</p>
                            <a wire:navigate href="<?php echo e(route('home')); ?>" class="btn-continue-shopping">Continue Shopping</a>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="pagination-container">
                    <?php echo e($products->links()); ?>

                </div>
                <style>
                    .products-grid {
                        grid-template-columns: repeat(5, 1fr) !important;
                        gap: 10px;
                    }

                    @media (max-width: 1400px) {
                        .products-grid {
                            grid-template-columns: repeat(4, 1fr) !important;
                        }
                    }

                    @media (max-width: 1200px) {
                        .products-grid {
                            grid-template-columns: repeat(3, 1fr) !important;
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

                    /* Mobile: hide sidebar body by default */
                    @media (max-width: 991px) {
                        .categories-category-sidebar {
                            display: block !important;
                            /* override d-lg-block so header stays visible */
                        }

                        .sidebar-body {
                            display: none;
                            /* hidden by default on mobile */
                        }

                        .categories-header {
                            border-bottom: 1px solid #eee;
                            padding-bottom: 0.5rem;
                            margin-bottom: 0;
                            user-select: none;
                        }
                    }

                    /* Desktop: always show sidebar body */
                    @media (min-width: 992px) {
                        .sidebar-body {
                            display: block !important;
                        }

                        .categories-header {
                            cursor: default !important;
                            pointer-events: none;
                        }
                    }
                </style>
            </div>
        </div>
    </div>
</section><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/category/category-product.blade.php ENDPATH**/ ?>