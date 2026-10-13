<div>
    <h4 class="fw-bold mb-4">Tracking Event Logs</h4>

    
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Events sent today</small>
                <h4 class="mb-0"><?php echo e($stats['sentToday']); ?></h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Success rate (today)</small>
                <h4 class="mb-0"><?php echo e($stats['successRate']); ?>%</h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Failed (today)</small>
                <h4 class="mb-0 text-danger"><?php echo e($stats['failedToday']); ?></h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Last event</small>
                <h6 class="mb-0"><?php echo e($stats['lastEventAt'] ? $stats['lastEventAt']->diffForHumans() : '—'); ?></h6>
            </div></div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['stuckJobs'] > 0): ?>
        <div class="alert alert-warning">
            <i class="bx bx-error me-1"></i>
            <?php echo e($stats['stuckJobs']); ?> tracking job(s) waiting in queue. If this number keeps growing,
            the queue worker may not be running — start it with
            <code>php artisan queue:work --queue=tracking</code> (see README).
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="card mb-4">
        <div class="card-body d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label">Resend Purchase for order</label>
                <input type="number" class="form-control" placeholder="Order ID" wire:model="resendOrderId">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['resendOrderId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button wire:click="resendOrder" class="btn btn-outline-primary">
                <i class="bx bx-send me-1"></i>Resend
            </button>
            <small class="text-muted w-100">Rebuilds fresh event data from the order and queues it. Cancelled/returned orders are never sent.</small>
        </div>
    </div>

    
    <div class="card mb-3">
        <div class="card-body row g-2">
            <div class="col-md-2">
                <select class="form-select" wire:model.live="platform">
                    <option value="">All platforms</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>"><?php echo e($p); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.live="event">
                    <option value="">All events</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e); ?>"><?php echo e($e); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.live="status">
                    <option value="">All statuses</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['pending','success','failed','skipped','duplicate']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>"><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Order ID" wire:model.live.debounce.500ms="orderId">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.live="date">
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th><th>Platform</th><th>Event</th><th>Event ID</th><th>Order</th>
                        <th>Status</th><th>HTTP</th><th>Attempts</th><th>Time</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($log->id); ?></td>
                            <td><?php echo e($log->platform->platform ?? '—'); ?></td>
                            <td><?php echo e($log->event_name); ?></td>
                            <td><small class="text-muted"><?php echo e(\Illuminate\Support\Str::limit($log->event_id, 22)); ?></small></td>
                            <td><?php echo e($log->order_id ?? '—'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning')); ?>">
                                    <?php echo e($log->status); ?>

                                </span>
                            </td>
                            <td><?php echo e($log->http_status ?? '—'); ?></td>
                            <td><?php echo e($log->attempts); ?></td>
                            <td><small><?php echo e($log->created_at->diffForHumans()); ?></small></td>
                            <td class="text-nowrap">
                                <button wire:click="toggleExpand(<?php echo e($log->id); ?>)" class="btn btn-sm btn-outline-secondary">View</button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->status === 'failed' && $log->order_id): ?>
                                    <button wire:click="retry(<?php echo e($log->id); ?>)" class="btn btn-sm btn-outline-primary">Retry</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expandedId === $log->id): ?>
                            <tr>
                                <td colspan="10">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->error_message): ?><div class="alert alert-danger py-1 mb-2"><small><?php echo e($log->error_message); ?></small></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-6"><strong>Payload (PII masked)</strong><pre class="bg-light p-2 small"><?php echo e(json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre></div>
                                        <div class="col-md-6"><strong>Response</strong><pre class="bg-light p-2 small"><?php echo e(json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="10" class="text-center text-muted py-4">No events logged yet.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($logs->links()); ?></div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/tracking/tracking-log-index.blade.php ENDPATH**/ ?>