<li class="mn-item" data-menu-item data-id="<?php echo e($row['id']); ?>" wire:key="mn-item-<?php echo e($row['id']); ?>">
    <div class="mn-row <?php echo e($row['missing'] ? 'mn-row-missing' : ''); ?>">
        <span class="mn-drag" title="Drag to reorder"><i class="bx bx-grid-vertical"></i></span>
        <i class="bx <?php echo e($row['type'] === 'page' ? 'bx-file' : 'bx-category'); ?> mn-type-icon"></i>
        <div class="mn-label">
            <span class="mn-name"><?php echo e($row['label']); ?></span>
            <span class="mn-url <?php echo e($row['missing'] ? 'is-missing' : ''); ?>">
                <?php echo e($row['missing'] ? 'Missing or inactive — hidden on the storefront' : $row['url']); ?>

            </span>
        </div>
        <span class="badge bg-label-<?php echo e($row['type'] === 'page' ? 'info' : 'warning'); ?>"><?php echo e(ucfirst($row['type'])); ?></span>
        <button type="button" class="mn-remove" wire:click="removeItem('<?php echo e($row['id']); ?>')" wire:confirm="Remove this item from the menu?" title="Remove">
            <i class="bx bx-trash"></i>
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['depth'] < 2): ?>
        <ul class="mn-list mn-children<?php echo e(empty($row['children']) ? ' is-empty' : ''); ?>" data-menu-list data-parent="<?php echo e($row['id']); ?>"
            data-label="<?php echo e($row['depth'] === 0 ? 'sub menu' : 'sub sub menu'); ?>"
            wire:key="mn-list-<?php echo e($row['id']); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $row['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('livewire.admin.settings.partial.menu-item', ['row' => $child], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\settings\partial\menu-item.blade.php ENDPATH**/ ?>