<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Automation</h4>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperAdmin): ?>
        <button type="button"
            wire:click="toggleMaster"
            wire:confirm="Master switch <?php echo e($masterOn ? 'OFF' : 'ON'); ?> করবেন? <?php echo e($masterOn ? 'সব automation থেমে যাবে।' : ''); ?>"
            class="btn btn-sm <?php echo e($masterOn ? 'btn-danger' : 'btn-success'); ?>">
            <i class="bx bx-power-off me-1"></i>Master: <?php echo e($masterOn ? 'ON' : 'OFF'); ?>

        </button>
        <?php else: ?>
        <span class="badge bg-<?php echo e($masterOn ? 'success' : 'danger'); ?>">Master: <?php echo e($masterOn ? 'ON' : 'OFF'); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$masterOn): ?>
    <div class="alert alert-danger">Master switch OFF — সব module থেমে আছে (emergency kill switch)।</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="card mb-4">
        <div class="card-body d-flex gap-4 flex-wrap">
            <div><small class="text-muted">AI খরচ আজ</small>
                <h5 class="mb-0">৳<?php echo e(number_format($budget['today'], 2)); ?> / ৳<?php echo e(number_format($budget['daily_limit'], 2)); ?></h5></div>
            <div><small class="text-muted">এই মাসে</small>
                <h5 class="mb-0">৳<?php echo e(number_format($budget['month'], 2)); ?> / ৳<?php echo e(number_format($budget['monthly_limit'], 2)); ?></h5></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($budget['warn']): ?>
            <div class="alert alert-warning py-1 px-2 mb-0">⚠️ বাজেট ৮০%+ শেষ — শীঘ্রই লিমিটে পৌঁছাবে।</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="row">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0"><i class="<?php echo e($c['icon']); ?> me-1"></i><?php echo e($c['name']); ?></h6>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c['status'] === 'coming_soon'): ?>
                        <span class="badge bg-secondary">Coming soon</span>
                        <?php elseif($c['mode'] === 'live'): ?>
                        <span class="badge bg-success">Live</span>
                        <?php elseif($c['mode'] === 'shadow'): ?>
                        <span class="badge bg-warning text-dark">Shadow</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Off</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-muted small"><?php echo e($c['description']); ?></p>
                    <small class="text-muted d-block">
                        <span title="<?php echo e($c['health_message']); ?>"><?php echo e($c['healthy'] ? '🟢' : '🔴'); ?></span>
                        আজ <?php echo e($c['today']); ?>টি event
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c['last']): ?> • শেষ: <?php echo e(\Carbon\Carbon::parse($c['last'])->diffForHumans()); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </small>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="<?php echo e(route('admin.automation.module', $c['key'])); ?>" class="btn btn-sm btn-outline-primary">Settings</a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperAdmin): ?>
                        <button type="button" wire:click="toggle('<?php echo e($c['key']); ?>')" class="btn btn-sm btn-outline-secondary">
                            <?php echo e($c['mode'] === 'off' ? 'চালু (Shadow)' : 'বন্ধ'); ?>

                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($c['mode'] !== 'off'): ?>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" wire:click="setMode('<?php echo e($c['key']); ?>', 'shadow')"
                                class="btn <?php echo e($c['mode'] === 'shadow' ? 'btn-warning' : 'btn-outline-warning'); ?>">Shadow</button>
                            <button type="button" wire:click="setMode('<?php echo e($c['key']); ?>', 'live')"
                                wire:confirm="Live mode চালু করবেন? Module এখন আসল action নেবে।"
                                class="btn <?php echo e($c['mode'] === 'live' ? 'btn-success' : 'btn-outline-success'); ?>">Live</button>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="card">
        <div class="card-header"><h6 class="mb-0">সাম্প্রতিক Activity (শেষ ২০)</h6></div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>সময়</th><th>Module</th><th>Event</th><th>Status</th><th>Message</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $feed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><small><?php echo e($f->created_at?->diffForHumans()); ?></small></td>
                        <td><small><?php echo e($f->module_key); ?></small></td>
                        <td><small><?php echo e($f->event); ?></small></td>
                        <td><span class="badge bg-<?php echo e($f->status === 'failed' ? 'danger' : ($f->status === 'success' ? 'success' : 'secondary')); ?>"><?php echo e($f->status); ?></span></td>
                        <td><small><?php echo e($f->message); ?></small></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">এখনো কোনো activity নেই।</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/automation/automation-index.blade.php ENDPATH**/ ?>