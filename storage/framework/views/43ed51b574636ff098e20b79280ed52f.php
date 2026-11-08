<div>
    <section class="hero-section">
        <div class="container">

            
            <div class="new-arrival-page-header">
                <div class="new-arrival-page-left">
                    <span class="arrival-icon-lg">✨</span>
                    <div>
                        <h1 class="arrival-page-title">NEW ARRIVALS</h1>
                        <p class="arrival-page-subtitle">Fresh picks just landed — <?php echo e($total); ?> products</p>
                    </div>
                </div>
                <div class="arrival-sort-wrapper">
                    <label class="sort-label">Sort by:</label>
                    <select wire:model.live="sortBy" class="arrival-sort-select">
                        <option value="latest">Latest</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="name_az">Name: A–Z</option>
                    </select>
                </div>
            </div>

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
                </aside>

                
                <div class="products-grid-container">

                    <div class="section-header">
                        <h2>✨ New Arrivals</h2>
                        <span class="results-count"><?php echo e($total); ?> products found</span>
                    </div>

                    <div class="products-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $newArrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arrival): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($arrival->product && $arrival->product->slug): ?>
                        <?php $product = $arrival->product; ?>
                        <?php echo $__env->make('livewire.frontend.layouts.partial.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-products">
                            <div class="cart-empty">
                                <i class="fas fa-box-open"></i>
                                <p>No products found.</p>
                                <p>Try adjusting your filters or check back soon.</p>
                                <a wire:navigate href="<?php echo e(route('home')); ?>" class="btn-continue-shopping">
                                    Back to Home
                                </a>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMore): ?>
                    <div class="text-center mt-4">
                        <button wire:click="nextPage" class="load-more-btn">
                            <span wire:loading.remove wire:target="nextPage">Load More</span>
                            <span wire:loading wire:target="nextPage">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </span>
                        </button>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
            </div>
        </div>
    </section>

    <style>
        /* ── New Arrival Page Header ─────────────────────── */
        .new-arrival-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: linear-gradient(135deg, #FF5500, #ff8c00);
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            margin-bottom: 0.75rem;
            color: #fff;
        }

        .new-arrival-page-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .arrival-icon-lg {
            font-size: 2.5rem;
            line-height: 1;
        }

        .arrival-page-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
        }

        .arrival-page-subtitle {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* ── Sort Dropdown ───────────────────────────────── */
        .arrival-sort-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sort-label {
            font-size: 0.85rem;
            opacity: 0.9;
            white-space: nowrap;
            margin: 0;
        }

        .arrival-sort-select {
            padding: 7px 14px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            cursor: pointer;
            outline: none;
            backdrop-filter: blur(4px);
        }

        .arrival-sort-select option {
            background: #fff;
            color: #333;
        }

        /* ── Category filter buttons ─────────────────────── */
        .category-filter-btn {
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 6px 8px;
            cursor: pointer;
            color: inherit;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }

        .category-filter-btn:hover {
            background: #fff5f0;
            color: #FF5500;
        }

        .category-filter-btn.active {
            color: #FF5500;
            font-weight: 600;
            background: #fff0ea;
        }

        /* ── Clear filters ───────────────────────────────── */
        .clear-filters-btn {
            width: 100%;
            padding: 7px;
            border: 1px dashed #FF5500;
            background: none;
            color: #FF5500;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .clear-filters-btn:hover {
            background: #fff0ea;
        }

        /* ── Section header with count ───────────────────── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 1rem;
        }

        .results-count {
            font-size: 13px;
            color: #888;
        }

        /* ── Products grid ───────────────────────────────── */
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

        /* ── Load More ───────────────────────────────────── */
        .load-more-btn {
            background: var(--cart-btn-bg, #FF5500);
            color: var(--cart-btn-text, #fff);
            border: none;
            padding: 12px 40px;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .load-more-btn:hover {
            filter: brightness(.92);
            transform: translateY(-1px);
        }

        /* ── Empty state ─────────────────────────────────── */
        .empty-products {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem 0;
        }

        .cart-empty {
            text-align: center;
        }

        .cart-empty i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
            display: block;
        }

        /* ── Mobile ──────────────────────────────────────── */
        @media (max-width: 768px) {
            .new-arrival-page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .arrival-sort-wrapper {
                width: 100%;
            }

            .arrival-sort-select {
                flex: 1;
            }
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
</div><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\frontend\new-arrival\new-arrivals.blade.php ENDPATH**/ ?>