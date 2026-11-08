<?php
    use Addons\PartialPayment\Models\OrderPlan;
    use Addons\PartialPayment\Models\Payment;

    $ppPlan = OrderPlan::forOrder((int) $order->id);

    if (! $ppPlan) {
        return;
    }

    $ppCurrency = setting('currency');
    $ppWaiting = Payment::pendingAdvance((int) $order->id);

    $ppBadge = match ($ppPlan->payment_status) {
        OrderPlan::PENDING => $ppWaiting ? ['bg-label-warning', 'টাকা পাঠানো হয়েছে — Verify'] : ['bg-label-secondary', 'এডভান্স বাকি'],
        OrderPlan::ADVANCE_PAID => ['bg-label-info', 'এডভান্স পেইড'],
        OrderPlan::PAID => ['bg-label-success', 'সব আদায় হয়েছে'],
        OrderPlan::REFUND_DUE => ['bg-label-danger', 'রিফান্ড বাকি'],
        OrderPlan::REFUNDED => ['bg-label-secondary', 'রিফান্ড সম্পন্ন'],
        default => ['bg-label-secondary', $ppPlan->payment_status],
    };
?>

<a href="<?php echo e(route('admin.order.view', $order->id)); ?>" wire:navigate class="text-decoration-none">
    <span class="badge <?php echo e($ppBadge[0]); ?>">
        এডভান্স <?php echo e($ppCurrency); ?><?php echo e(number_format((float) $ppPlan->advance_amount, 0)); ?>

    </span>
</a>
<span class="badge <?php echo e($ppBadge[0]); ?> d-block mt-1"><?php echo e($ppBadge[1]); ?></span>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $ppPlan->due_amount > 0 && $ppPlan->payment_status !== \Addons\PartialPayment\Models\OrderPlan::PAID): ?>
    <small class="d-block text-muted mt-1">ডেলিভারিতে <?php echo e($ppCurrency); ?><?php echo e(number_format((float) $ppPlan->due_amount, 0)); ?></small>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\src/../resources/views/admin/column.blade.php ENDPATH**/ ?>