<?php
    use Addons\PartialPayment\Models\OrderPlan;
    $ppCurrency = setting('currency');
?>

<div class="col-xxl">
    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Partial Payment — Refunds &amp; verification</h5>
            <div class="d-flex gap-3 flex-wrap">
                <small class="text-muted">
                    Collected advance: <strong><?php echo e($ppCurrency); ?><?php echo e(number_format($collected, 2)); ?></strong>
                </small>
                <small class="text-muted">
                    To refund: <strong class="text-danger"><?php echo e($ppCurrency); ?><?php echo e(number_format($toRefund, 2)); ?></strong>
                </small>
            </div>
        </div>

        <div class="card-body">
            <h6 class="text-muted">Refunds to process</h6>

            <div class="table-responsive mb-4">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Advance</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('admin.order.view', $plan->order_id)); ?>" wire:navigate>
                                        <?php echo e($plan->order?->invoice_no ?? '#'.$plan->order_id); ?>

                                    </a>
                                </td>
                                <td>
                                    <?php echo e($plan->order?->name); ?>

                                    <small class="d-block text-muted"><?php echo e($plan->order?->phone); ?></small>
                                </td>
                                <td><?php echo e($ppCurrency); ?><?php echo e(number_format((float) $plan->advance_amount, 2)); ?></td>
                                <td><span class="badge bg-label-danger">Refund due</span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-success"
                                        wire:click="markRefunded(<?php echo e($plan->id); ?>)"
                                        wire:confirm="রিফান্ড সম্পন্ন হয়েছে হিসেবে চিহ্নিত করবেন?">
                                        Mark refunded
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-muted">কোনো রিফান্ড বাকি নেই।</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h6 class="text-muted">Awaiting verification</h6>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Sender</th>
                            <th>TrxID</th>
                            <th class="text-end">Verify</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $waiting; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('admin.order.view', $payment->order_id)); ?>" wire:navigate>
                                        <?php echo e($payment->order?->invoice_no ?? '#'.$payment->order_id); ?>

                                    </a>
                                </td>
                                <td>
                                    <?php echo e($payment->order?->name); ?>

                                    <small class="d-block text-muted"><?php echo e($payment->order?->phone); ?></small>
                                </td>
                                <td><?php echo e(\Addons\PartialPayment\Support\Options::METHODS[$payment->method] ?? $payment->method); ?></td>
                                <td><?php echo e($ppCurrency); ?><?php echo e(number_format((float) $payment->amount, 2)); ?></td>
                                <td><small><?php echo e($payment->sender_number ?: '—'); ?></small></td>
                                <td><code><?php echo e($payment->trx_id); ?></code></td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('admin.order.view', $payment->order_id)); ?>#partial-payment-panel"
                                        class="btn btn-sm btn-primary">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-muted">কোনো পেমেন্ট ভেরিফিকেশনের অপেক্ষায় নেই।</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\livewire\refunds.blade.php ENDPATH**/ ?>