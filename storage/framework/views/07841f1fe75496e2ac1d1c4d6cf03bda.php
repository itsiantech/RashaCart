<?php
    $ppWaiting = \Addons\PartialPayment\Models\Payment::query()
        ->where('type', 'advance')->where('status', 'pending')->count();
?>

<li class="menu-item <?php echo e(request()->is('admin/addons/partial-payment*') ? 'active' : ''); ?>">
    <a wire:navigate href="<?php echo e(route('admin.addons.partial-payment.refunds')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-wallet"></i>
        <div>Partial Payment</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ppWaiting > 0): ?>
            <div class="badge rounded bg-label-warning"><?php echo e($ppWaiting); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>
</li>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\admin\menu.blade.php ENDPATH**/ ?>