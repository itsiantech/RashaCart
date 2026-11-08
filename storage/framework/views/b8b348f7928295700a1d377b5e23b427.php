<?php $hasChildren = count($row['children']) > 0; ?>

<div class="cat-node cat-depth-<?php echo e($row['depth']); ?>" wire:key="cat-<?php echo e($row['id']); ?>"
    data-id="<?php echo e($row['id']); ?>" data-parent="<?php echo e($row['parent_id'] ?? ''); ?>">
    <div class="cat-item">
        <div class="cat-cell">
            <div class="d-flex align-items-center gap-2" style="padding-left: <?php echo e($row['depth'] * 26); ?>px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortable): ?>
                <span class="cat-drag" title="Drag to reorder"><i class='bx bx-menu'></i></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="cat-sign"><?php echo e($row['depth'] ? str_repeat('- ', $row['depth']) : ''); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['image']): ?>
                <img src="<?php echo e(asset('storage/'.$row['image'])); ?>" alt="<?php echo e($row['name']); ?>" class="cat-thumb">
                <?php else: ?>
                <span class="cat-thumb-placeholder"><i class='bx bx-image'></i></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="cat-name"><?php echo e($row['name']); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($row['parent_name'])): ?>
                <small class="text-muted">(<?php echo e($row['parent_name']); ?>)</small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_featured']): ?>
                <i class='bx bxs-star cat-featured' title="Featured on homepage"></i>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="cat-cell"><span class="text-muted small"><?php echo e($row['slug']); ?></span></div>
        <div class="cat-cell text-center"><span class="cat-products-badge"><?php echo e($row['products']); ?></span></div>
        <div class="cat-cell">
            <button type="button" class="cat-status-pill <?php echo e($row['status'] ? 'cat-active' : 'cat-inactive'); ?>"
                wire:click="toggleStatus(<?php echo e($row['id']); ?>)"
                title="Click to <?php echo e($row['status'] ? 'deactivate' : 'activate'); ?>">
                <span class="dot"></span> <?php echo e($row['status'] ? 'Active' : 'Inactive'); ?>

            </button>
        </div>
        <div class="cat-cell text-end">
            <button class="btn btn-sm btn-icon btn-label-primary me-1"
                wire:click="showEditForm(<?php echo e($row['id']); ?>)" title="Edit">
                <i class="bx bx-edit-alt"></i>
            </button>
            <button class="btn btn-sm btn-icon btn-label-danger"
                wire:click="confirmDelete(<?php echo e($row['id']); ?>)" title="Delete">
                <i class="bx bx-trash"></i>
            </button>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
    <div class="cat-children" wire:key="kids-<?php echo e($row['id']); ?>" data-parent="<?php echo e($row['id']); ?>"
        <?php if($sortable): ?> data-sortable="1" <?php endif; ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $row['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('livewire.admin.category.partial.tree-node', ['row' => $child, 'sortable' => $sortable], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\category\partial\tree-node.blade.php ENDPATH**/ ?>