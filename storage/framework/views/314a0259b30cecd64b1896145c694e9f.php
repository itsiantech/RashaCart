<div>
    <a href="<?php echo e(route('admin.automation.index')); ?>" class="btn btn-sm btn-outline-secondary mb-3">← Automation</a>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0"><i class="<?php echo e($module->icon()); ?> me-1"></i><?php echo e($module->name()); ?></h4>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($module->status() === 'coming_soon'): ?>
        <span class="badge bg-secondary">Coming soon</span>
        <?php elseif($mode === 'live'): ?>
        <span class="badge bg-success">Live</span>
        <?php elseif($mode === 'shadow'): ?>
        <span class="badge bg-warning text-dark">Shadow</span>
        <?php else: ?>
        <span class="badge bg-secondary">Off</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <p class="text-muted"><?php echo e($module->description()); ?></p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($module->status() === 'coming_soon'): ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <h6>যা যা আসছে</h6>
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $module->plannedFeatures(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($f); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
            <small class="text-muted">এই module এখনো তৈরি হয়নি — চালু করলেও কোনো action হবে না।</small>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">Settings</h6></div>
        <div class="card-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $module->settingsSchema(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-3">
                <label class="form-label"><?php echo e($field['label'] ?? $field['name']); ?></label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($field['type'] ?? 'text') === 'toggle'): ?>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" role="switch"
                        wire:model="form.<?php echo e($field['name']); ?>" <?php if(!$isSuperAdmin): echo 'disabled'; endif; ?>>
                </div>
                <?php elseif(($field['type'] ?? '') === 'select'): ?>
                <select class="form-select" wire:model="form.<?php echo e($field['name']); ?>" <?php if(!$isSuperAdmin): echo 'disabled'; endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <?php elseif(($field['type'] ?? '') === 'textarea'): ?>
                <textarea class="form-control" rows="3" wire:model="form.<?php echo e($field['name']); ?>" <?php if(!$isSuperAdmin): echo 'disabled'; endif; ?>></textarea>
                <?php else: ?>
                <input type="<?php echo e(($field['type'] ?? 'text') === 'number' ? 'number' : 'text'); ?>"
                    class="form-control" wire:model="form.<?php echo e($field['name']); ?>" <?php if(!$isSuperAdmin): echo 'disabled'; endif; ?>>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($field['help'])): ?>
                <small class="text-muted"><?php echo e($field['help']); ?></small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.' . $field['name']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger d-block"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperAdmin): ?>
            <button type="button" wire:click="save" class="btn btn-primary">Save Settings</button>
            <?php else: ?>
            <small class="text-muted">দেখার অনুমতি আছে, বদলানোর নয়।</small>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <button class="btn btn-link p-0 text-decoration-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#autoGuide">
                📖 গাইড (বাংলা)
            </button>
        </div>
        <div id="autoGuide" class="collapse">
            <div class="card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $module->guide(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="mb-2"><strong><?php echo e(ucfirst($title)); ?>:</strong> <?php echo e($text); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/automation/automation-module.blade.php ENDPATH**/ ?>