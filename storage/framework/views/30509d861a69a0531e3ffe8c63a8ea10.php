
<?php use App\Services\CustomerAuthSettings; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(CustomerAuthSettings::enabled()): ?>
    <button type="button" class="nav-icon" title="Login / Register" data-cust-open="login" aria-haspopup="dialog" style="border:none;background:none;cursor:pointer;">
        <i class="fas fa-user"></i>
    </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php elseif(Auth::user()->role === 'customer' && CustomerAuthSettings::enabled()): ?>
    <div class="cust-dropdown">
        <button type="button" class="nav-icon" id="custMenuBtn" aria-haspopup="menu" aria-expanded="false" title="My Account" style="border:none;background:none;cursor:pointer;">
            <i class="fas fa-user"></i>
        </button>
        <div class="cust-menu" id="custMenu" role="menu" hidden>
            <a href="<?php echo e(route('customer.dashboard')); ?>" role="menuitem"><i class="fas fa-gauge-high me-2"></i>Dashboard</a>
            <a href="<?php echo e(route('customer.dashboard', ['tab' => 'orders'])); ?>" role="menuitem"><i class="fas fa-bag-shopping me-2"></i>Orders</a>
            <form method="POST" action="<?php echo e(route('customer.logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" role="menuitem"><i class="fas fa-right-from-bracket me-2"></i>Logout</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <a href="<?php echo e(route('profile.index')); ?>" class="nav-icon d-none d-md-block">
        <i class="fas fa-user"></i>
    </a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/partials/customer-account-icon.blade.php ENDPATH**/ ?>