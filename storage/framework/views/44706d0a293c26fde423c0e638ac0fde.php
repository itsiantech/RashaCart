<?php
    use Addons\PartialPayment\Models\OrderPlan;
    use Addons\PartialPayment\Models\Payment;

    $ppWaiting = Payment::query()->where('type', 'advance')->where('status', 'pending')->count();
    $ppCollected = (float) Payment::query()->where('type', 'advance')->where('status', 'verified')->sum('amount');
    $ppDue = (float) OrderPlan::query()
        ->whereIn('payment_status', [OrderPlan::PENDING, OrderPlan::ADVANCE_PAID])
        ->sum('due_amount');
    $ppCurrency = setting('currency');
?>

<div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="fw-semibold d-block mb-1">Advance — awaiting verification</span>
                    <h3 class="card-title mb-2"><?php echo e($ppWaiting); ?></h3>
                    <a href="<?php echo e(route('admin.addons.partial-payment.refunds')); ?>" wire:navigate
                        class="small fw-semibold text-primary">Review payments</a>
                </div>
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded bg-label-warning">
                        <i class="bx bx-time"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="fw-semibold d-block mb-1">Advance collected</span>
                    <h3 class="card-title mb-2"><?php echo e($ppCurrency); ?><?php echo e(number_format($ppCollected, 2)); ?></h3>
                    <small class="text-success fw-semibold"><i class="bx bx-check"></i> Verified payments</small>
                </div>
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded bg-label-success">
                        <i class="bx bx-dollar-circle"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="fw-semibold d-block mb-1">Due to collect</span>
                    <h3 class="card-title mb-2"><?php echo e($ppCurrency); ?><?php echo e(number_format($ppDue, 2)); ?></h3>
                    <small class="text-muted">Cash on delivery balance</small>
                </div>
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded bg-label-info">
                        <i class="bx bx-purchase-tag"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\admin\dashboard-cards.blade.php ENDPATH**/ ?>