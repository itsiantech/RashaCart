
<?php $cartCount = $cartCount ?? array_sum(array_column(session()->get('cart', []), 'qty')); ?>
<nav class="navbar-main sticky-top">
    <div class="hp-container">
        <div class="hp-navbar-row">
            <a class="hp-logo" href="<?php echo e(route('home')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('app_logo')): ?>
                    <img src="<?php echo e(asset('storage/logo/' . rawurlencode(setting('app_logo')))); ?>"
                         alt="<?php echo e(setting('app_name') ?: config('app.name')); ?>">
                <?php else: ?>
                    <i class="fas fa-shopping-bag"></i>
                    <span><?php echo e(setting('app_name') ?: config('app.name')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>

            <form class="hp-search" method="GET" action="<?php echo e(route('home')); ?>">
                <input type="search" name="q" class="form-control" placeholder="Search products..." value="<?php echo e(request('q')); ?>">
                <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="hp-nav-icons">
                <?php echo $__env->make('partials.customer-account-icon', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <a href="#" class="nav-icon d-none d-md-block">
                    <i class="fas fa-heart"></i>
                </a>
                <a href="<?php echo e(route('frontend.cart.view')); ?>" class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-warning text-dark"><?php echo e($cartCount); ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>


<div class="subnav d-none d-md-block">
    <div class="hp-container">
        <nav class="d-flex align-items-center flex-wrap gap-1 py-1">
            <a href="<?php echo e(route('frontend.category.view')); ?>" class="nav-link">Categories</a>
            <a href="<?php echo e(route('home')); ?>" class="nav-link">Products</a>
            <a href="<?php echo e(route('register')); ?>" class="btn btn-sell ms-1">Sell on Padmazon</a>
            <a href="#" class="nav-link">Discounts</a>
            <a href="#" class="nav-link">All Shops</a>
            <a href="<?php echo e(route('frontend.brand.view')); ?>" class="nav-link">All Brands</a>
            <a href="<?php echo e(route('frontend.order.tracking')); ?>" class="nav-link">Track Order</a>
            <a href="<?php echo e(route('home')); ?>" class="nav-link">Home</a>
        </nav>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/partials/main-header.blade.php ENDPATH**/ ?>