<form wire:submit.prevent="search">
    <div class="search-container" style="position: relative; z-index: 100;">
        <input type="text" wire:model.live.debounce.50ms="q" placeholder="Search for products..." class="">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($q)): ?>
        <div class="search-results"
            style=" position:fixed;
        top:100px;s
        left:0;
        right:0;
        background:#fff;
        border:1px solid #ddd;
        z-index:10000;
        max-height:450px;
        overflow-y:auto;
        overflow-x:hidden;
        box-shadow:0 10px 20px rgba(0,0,0,0.08);
        border-radius:10px;">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a wire:navigate href="<?php echo e(route('frontend.product.single.view', $item->slug)); ?>"
                style="display:flex; align-items:center; gap:10px; padding:8px; border-bottom:1px solid #eee; text-decoration:none; color:#333;">

                <!-- Product Image -->
                <img src="<?php echo e($item->thumbnail ? asset('storage/' . $item->thumbnail) : 'https://placehold.co/500x500'); ?>"
                    alt="<?php echo e($item->name); ?>"
                    style="width:40px; height:40px; object-fit:cover; border-radius:5px;">

                <!-- Text Section -->
                <div>
                    <strong><?php echo e($item->name); ?></strong>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($item->price)): ?>
                    <div style="font-size:13px; color:gray;">$<?php echo e($item->price); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p style="padding:10px;">No results found</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('search_error')): ?>
            <p style="padding:10px;color:red;">
                <?php echo e(session('search_error')); ?>

            </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->count() > 0): ?>
            <?php
            $firstCategory = $results->first()->category;
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($firstCategory): ?>
            <a wire:navigate
                href="<?php echo e(route('frontend.category.view', $firstCategory->slug)); ?>"
                style="display:block; padding:10px; text-align:center; font-weight:bold; background:#f8f9fa; text-decoration:none;">
                See all in <?php echo e($firstCategory->name); ?>

            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</form><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/components/search-bar.blade.php ENDPATH**/ ?>