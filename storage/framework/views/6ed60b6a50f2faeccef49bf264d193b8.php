<section class="hero-section">
    <div class="container">
        <h2 class="brand-index-title">All Brands</h2>

        <div class="brand-search-card">
            <i class="fas fa-search"></i>
            <input type="search" placeholder="Search your brands" wire:model.live.debounce.400ms="search"
                aria-label="Search brands">
        </div>

        <div class="brand-index-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a wire:navigate href="<?php echo e(route('frontend.brands.products.view', $brand->slug)); ?>"
                class="brand-index-card">
                <span class="brand-index-logo">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand->logo): ?>
                    <img src="<?php echo e(asset('storage/' . $brand->logo)); ?>"
                        alt="<?php echo e($brand->name); ?>" loading="lazy">
                    <?php else: ?>
                    <i class="fas fa-copyright"></i>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span class="brand-index-name"><?php echo e($brand->name); ?></span>
                <small class="brand-index-count"><?php echo e($brand->products_count); ?> products</small>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-products">
                <div class="cart-empty">
                    <i class="fas fa-shopping-basket"></i>
                    <p>No brands found.</p>
                    <p>Try a different search.</p>
                    <a wire:navigate href="<?php echo e(route('home')); ?>" class="btn-continue-shopping">Continue Shopping</a>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalCount > $brands->count()): ?>
        <div class="text-center mt-4 mb-2">
            <button wire:click="loadMore" class="brand-load-more"
                wire:loading.attr="disabled" wire:target="loadMore">
                <span wire:loading.remove wire:target="loadMore">LOAD MORE</span>
                <span wire:loading wire:target="loadMore">Loading...</span>
            </button>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <style>
            .brand-index-title { font-size: 1.15rem; font-weight: 700; margin: 18px 0 12px; }
            .brand-search-card {
                background: #fff; border: 1px solid #eef0f4; border-radius: 10px;
                display: flex; align-items: center; gap: 10px; padding: 10px 14px; margin-bottom: 16px;
            }
            .brand-search-card i { color: #9ca3af; }
            .brand-search-card input { border: none; outline: none; flex: 1; font-size: .9rem; }
            .brand-index-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }
            @media (max-width: 1100px) { .brand-index-grid { grid-template-columns: repeat(4, 1fr); } }
            @media (max-width: 768px) { .brand-index-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; } }
            @media (max-width: 480px) { .brand-index-grid { grid-template-columns: repeat(2, 1fr); } }
            .brand-index-card {
                background: #fff; border: 1px solid #eef0f4; border-radius: 12px;
                padding: 18px 8px 14px; text-decoration: none;
                display: flex; flex-direction: column; align-items: center; gap: 8px;
                transition: box-shadow .15s, transform .15s;
            }
            .brand-index-card:hover { box-shadow: 0 8px 22px rgba(18, 34, 91, .10); transform: translateY(-2px); }
            .brand-index-logo {
                height: 64px; display: inline-flex; align-items: center; justify-content: center;
                color: #12225B; font-size: 1.6rem;
            }
            .brand-index-logo img { max-height: 64px; max-width: 100%; object-fit: contain; }
            .brand-index-name { font-size: .83rem; font-weight: 500; color: #333a45; text-align: center; line-height: 1.3; }
            .brand-index-card:hover .brand-index-name { color: #7A0000; }
            .brand-index-count { color: #9ca3af; font-size: .75rem; }
            .brand-load-more {
                border: 1px solid #12225B; background: #fff; color: #12225B;
                border-radius: 8px; padding: 10px 64px; font-weight: 700; letter-spacing: .04em; cursor: pointer;
            }
            .brand-load-more:hover { background: #12225B; color: #fff; }
        </style>

        </div>
</section>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/brand/brand-view.blade.php ENDPATH**/ ?>