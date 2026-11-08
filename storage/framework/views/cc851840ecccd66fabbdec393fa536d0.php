<?php
    use Addons\PartialPayment\Models\OrderPlan;
    $ppCurrency = setting('currency');
?>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0">Advance payment</h5>
            <small class="text-muted">বিকাশ / নগদ / রকেট ম্যানুয়াল পেমেন্ট ভেরিফিকেশন</small>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
            <span class="badge bg-label-<?php echo e(['advance_pending' => 'warning', 'advance_paid' => 'info', 'paid' => 'success', 'refund_due' => 'danger', 'refunded' => 'secondary'][$plan->payment_status] ?? 'secondary'); ?>">
                <?php echo e(str_replace('_', ' ', ucfirst($plan->payment_status))); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="card-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $plan): ?>
            <p class="text-muted mb-0">
                এই অর্ডারে কোনো এডভান্স প্রয়োজন হয়নি — সাধারণ ক্যাশ অন ডেলিভারি।
            </p>
        <?php else: ?>
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Order total</small>
                    <strong><?php echo e($ppCurrency); ?><?php echo e(number_format((float) $plan->order->total_amount, 2)); ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Advance</small>
                    <strong><?php echo e($ppCurrency); ?><?php echo e(number_format((float) $plan->advance_amount, 2)); ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">COD due</small>
                    <strong><?php echo e($ppCurrency); ?><?php echo e(number_format((float) $plan->due_amount, 2)); ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Advance deadline</small>
                    <strong><?php echo e($plan->expires_at ? $plan->expires_at->format('d M, h:i A') : '—'); ?></strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-2">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Sender</th>
                            <th>TrxID</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><small><?php echo e(ucfirst($payment->type)); ?></small></td>
                                <td><?php echo e(\Addons\PartialPayment\Support\Options::METHODS[$payment->method] ?? ucfirst($payment->method)); ?></td>
                                <td><?php echo e($ppCurrency); ?><?php echo e(number_format((float) $payment->amount, 2)); ?></td>
                                <td><small><?php echo e($payment->sender_number ?: '—'); ?></small></td>
                                <td><code><?php echo e($payment->trx_id ?: '—'); ?></code></td>
                                <td>
                                    <span class="badge bg-label-<?php echo e(['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'][$payment->status]); ?>">
                                        <?php echo e(ucfirst($payment->status)); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->status === 'verified' && $payment->verified_at): ?>
                                        <small class="d-block text-muted">
                                            <?php echo e($payment->verifier?->name); ?> · <?php echo e($payment->verified_at->format('d M h:i A')); ?>

                                        </small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->note): ?>
                                        <small class="d-block text-muted"><?php echo e($payment->note); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->status === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-success mb-1"
                                            wire:click="verify(<?php echo e($payment->id); ?>)">
                                            Verify
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger mb-1"
                                            wire:click="startReject(<?php echo e($payment->id); ?>)">
                                            Reject
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rejecting === $payment->id): ?>
                                        <div class="mt-1 text-start">
                                            <input type="text" class="form-control form-control-sm"
                                                wire:model="rejectNote" placeholder="কেন বাতিল? (required)">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rejectNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="text-danger d-block"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <div class="d-flex gap-1 mt-1">
                                                <button type="button" class="btn btn-sm btn-danger" wire:click="confirmReject">
                                                    Confirm reject
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancelReject">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-muted">
                                    গ্রাহক এখনো কোনো পেমেন্ট জমা দেয়নি।
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $plan->due_amount > 0 && $plan->payment_status !== OrderPlan::PAID): ?>
                    <button type="button" class="btn btn-primary" wire:click="collectDue"
                        wire:confirm="ডেলিভারির সময় বাকি <?php echo e($ppCurrency); ?><?php echo e(number_format((float) $plan->due_amount, 0)); ?> আদায় হয়েছে হিসেবে রেকর্ড করবেন?">
                        <i class="bx bx-money me-1"></i>
                        Collect due <?php echo e($ppCurrency); ?><?php echo e(number_format((float) $plan->due_amount, 0)); ?>

                    </button>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $showRelease): ?>
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showRelease', true)">
                            Release the due
                        </button>
                    <?php else: ?>
                        <div class="d-flex gap-1 flex-wrap">
                            <input type="text" class="form-control form-control-sm" style="max-width: 240px;"
                                wire:model="releaseNote" placeholder="কেন মওকুফ? (required)">
                            <button type="button" class="btn btn-sm btn-warning" wire:click="releaseDue">Confirm</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$set('showRelease', false)">
                                Close
                            </button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['releaseNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->due_override): ?>
                    <span class="badge bg-label-secondary">Due released by admin</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->payment_status === OrderPlan::PAID): ?>
                    <span class="badge bg-label-success">
                        <i class="bx bx-check me-1"></i> সম্পূর্ণ আদায় হয়েছে
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activities->isNotEmpty()): ?>
                <details class="mt-3">
                    <summary class="text-muted small">Activity log (<?php echo e($activities->count()); ?>)</summary>
                    <ul class="small text-muted mb-0 mt-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <?php echo e($activity->created_at?->format('d M Y, h:i A')); ?> —
                                <strong><?php echo e(str_replace('_', ' ', $activity->action)); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->admin): ?> by <?php echo e($activity->admin->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->note): ?> · <?php echo e($activity->note); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </details>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\src/../resources/views/livewire/payments-panel.blade.php ENDPATH**/ ?>