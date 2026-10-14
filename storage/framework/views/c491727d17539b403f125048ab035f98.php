<section class="hero-section">
    <div class="container">
        <div class="hero-container">

            <!-- Categories Sidebar -->
            <aside class="categories-category-sidebar d-lg-block"
                x-data="{ open: window.innerWidth >= 992 }">
                <div class="categories-header" @click="open = !open" style="cursor:pointer;">
                    <h3>
                        Filters
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeFilterCount): ?>
                        <span class="badge bg-danger ms-1"><?php echo e($this->activeFilterCount); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <i class="fas fa-chevron-down ms-2"
                            :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </h3>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeFilterCount): ?>
                <div class="px-1 mb-2 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <button wire:click="clearAllFilters" class="flt-clear">✕ Clear all filters</button>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="filter-card py-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <ul class="check-list">
                        <?php echo $__env->make('livewire.frontend.layouts.partial.category.category-list', [
                        'categories' => $categories->where('parent_id', null),
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </ul>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category && $this->subcategories->isNotEmpty()): ?>
                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <h3 class="filter-title">More in <?php echo e($category->name); ?></h3>
                    <div class="flt-pills">
                        <a wire:navigate href="<?php echo e(route('frontend.category.products.view', $category->slug)); ?>"
                            class="flt-pill on">All</a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a wire:navigate href="<?php echo e(route('frontend.category.products.view', $sub->slug)); ?>"
                            class="flt-pill"><?php echo e($sub->name); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'">
                    <h3 class="filter-title">Price range</h3>
                    <div class="flt-minmax">
                        <input type="number" min="0" placeholder="Min" wire:model.defer="minPrice">
                        <span class="text-muted">—</span>
                        <input type="number" min="0" placeholder="Max" wire:model.defer="maxPrice">
                        <button wire:click="applyCustomPrice">Go</button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== '')): ?>
                        <button wire:click="clearPrice" class="flt-x" title="Clear price">✕</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <input type="range" min="0" max="100000" step="500" class="flt-range"
                        wire:model.live.debounce.500ms="maxPrice" title="Max price">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">৳0</small>
                        <small class="text-muted"><?php echo e(($maxPrice !== null && $maxPrice !== '') ? '৳'.number_format($maxPrice) : '৳100000+'); ?></small>
                    </div>
                    <div class="size-options mt-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['500-1000', '1000-2000', '2000-3000', '3000-4000']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button
                            wire:click.prevent="togglePrice('<?php echo e($price); ?>')"
                            class="size-option <?php echo e($selectedPrice === $price ? 'active' : ''); ?>">
                            <?php echo e($price); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'" x-data="{ q: '', more: false }">
                    <h3 class="filter-title">Brands</h3>
                    <input class="flt-search" placeholder="Search brands" x-model="q">
                    <div class="flt-pills">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->filterBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $b = $item['brand']; ?>
                        <span x-show="(!q || '<?php echo e(str_replace("'", '', mb_strtolower($b->name))); ?>'.includes(q.toLowerCase())) && (more || <?php echo e($loop->index); ?> < 8)">
                            <button wire:click.prevent="toggleBrand(<?php echo e($b->id); ?>)"
                                class="flt-pill <?php echo e(in_array($b->id, $selectedBrands ?? []) ? 'on' : ''); ?>">
                                <?php echo e($b->name); ?> <small>(<?php echo e($item['count']); ?>)</small>
                            </button>
                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->filterBrands->count() > 8): ?>
                    <button class="flt-more" @click="more = !more" x-text="more ? 'Show less' : 'Show all (<?php echo e($this->filterBrands->count()); ?>)'"></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->filterAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filterAttr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="filter-card pb-3 sidebar-body" :class="open ? 'd-block' : 'collapsed'" x-data="{ q: '', more: false }">
                    <h3 class="filter-title"><?php echo e($filterAttr->name); ?></h3>
                    <input class="flt-search" placeholder="Search <?php echo e(strtolower($filterAttr->name)); ?>" x-model="q">
                    <div class="flt-pills">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filterAttr->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filterVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isOn = in_array($filterVal->id, $selectedAttrs[$filterAttr->id] ?? []); ?>
                        <span x-show="(!q || '<?php echo e(str_replace("'", '', mb_strtolower($filterVal->value))); ?>'.includes(q.toLowerCase())) && (more || <?php echo e($loop->index); ?> < 8)">
                            <button
                                wire:click.prevent="toggleAttrValue(<?php echo e($filterAttr->id); ?>, <?php echo e($filterVal->id); ?>)"
                                class="flt-pill <?php echo e($isOn ? 'on' : ''); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterVal->color_hex): ?>
                                <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:<?php echo e($filterVal->color_hex); ?>;vertical-align:-1px;"></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php echo e($filterVal->value); ?>

                            </button>
                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterAttr->values->count() > 8): ?>
                    <button class="flt-more" @click="more = !more" x-text="more ? 'Show less' : 'Show all (<?php echo e($filterAttr->values->count()); ?>)'"></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </aside>

            <!-- Products Grid -->
            <div class="products-grid-container">
                <div class="section-header">
                    <h2>
                        <?php echo e($category ? 'Category - ' . $category->name : 'All Categories'); ?>

                    </h2>
                    <div class="flt-sort">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['popular' => 'Popular', 'price_asc' => 'Price ↑', 'price_desc' => 'Price ↓', 'newest' => 'Newest']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button wire:click="setSort('<?php echo e($key); ?>')" class="<?php echo e($sort === $key ? 'on' : ''); ?>"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
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

                    /* Desktop: show sidebar body unless collapsed */
                    @media (min-width: 992px) {
                        .sidebar-body:not(.collapsed) {
                            display: block !important;
                        }
                    }

                    /* Collapsed (all screens, beats the rules above) */
                    .sidebar-body.collapsed {
                        display: none !important;
                    }

                    /* Daraz-style filter groups */
                    .flt-search {
                        width: 100%; border: 1px solid #e0e0e0; border-radius: 8px;
                        padding: 7px 10px; font-size: .82rem; margin-bottom: 8px; outline: none;
                    }
                    .flt-search:focus { border-color: #7A0000; }
                    .flt-pills { display: flex; flex-wrap: wrap; gap: 6px; }
                    .flt-pill {
                        border: 1px solid #e0e0e0; background: #fff; border-radius: 14px;
                        padding: 4px 12px; font-size: .78rem; cursor: pointer; color: #374151;
                    }
                    .flt-pill small { color: #9ca3af; }
                    .flt-pill.on { background: #111827; color: #fff; border-color: #111827; }
                    .flt-pill.on small { color: #d1d5db; }
                    .flt-more {
                        background: none; border: none; color: #7A0000; font-size: .8rem;
                        font-weight: 600; cursor: pointer; padding: 6px 0 0;
                    }
                    .flt-minmax { display: flex; gap: 6px; margin-top: 8px; }
                    .flt-minmax input {
                        width: 100%; border: 1px solid #e0e0e0; border-radius: 6px;
                        padding: 6px 8px; font-size: .8rem;
                    }
                    .flt-minmax button {
                        border: 1px solid #e0e0e0; background: #fff; border-radius: 6px;
                        padding: 6px 12px; font-size: .8rem; font-weight: 600; cursor: pointer;
                    }
                    .flt-x { color: #b00 !important; }
                    .flt-range { width: 100%; accent-color: #12225B; margin: 8px 0 2px; }
                    .flt-clear {
                        background: none; border: none; color: #b00; font-size: .82rem;
                        font-weight: 600; cursor: pointer;
                    }
                    .flt-sort { display: flex; gap: 6px; flex-wrap: wrap; }
                    .flt-sort button {
                        border: 1px solid #e0e0e0; background: #fff; border-radius: 14px;
                        padding: 4px 14px; font-size: .78rem; cursor: pointer; color: #374151;
                    }
                    .flt-sort button.on { background: #12225B; color: #fff; border-color: #12225B; }
                </style>
            </div>
        </div>
    </div>
</section>

<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/category/category-product.blade.php ENDPATH**/ ?>