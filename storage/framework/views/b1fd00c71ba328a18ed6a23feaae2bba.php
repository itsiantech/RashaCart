<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
            </li>
            <li class="breadcrumb-item active">Pages</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="card-title mb-0">All Pages</h5>
                <a href="<?php echo e(route('admin.pages.create')); ?>" wire:navigate
                    class="btn btn-primary btn-sm d-inline-flex align-items-center mt-2 mt-md-0">
                    <i class="bx bx-plus me-1"></i> Add Page
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Sections</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pages->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bx bx-file-blank bx-lg text-muted d-block mb-2"></i>
                                <span class="text-muted">No pages yet. <a href="<?php echo e(route('admin.pages.create')); ?>" wire:navigate>Create your first page</a>.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($page->trashed() ? 'table-danger' : ''); ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->feature_image): ?>
                                            <img src="<?php echo e(asset('storage/' . $page->feature_image)); ?>" alt=""
                                                style="width:38px;height:38px;object-fit:cover;border-radius:.4rem;">
                                        <?php else: ?>
                                            <span class="d-inline-flex align-items-center justify-content-center text-muted"
                                                style="width:38px;height:38px;border-radius:.4rem;background:#f3f4f9;">
                                                <i class="bx bx-image-alt"></i>
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?php echo e($page->title); ?></div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->meta_title): ?>
                                                <small class="text-muted">Meta: <?php echo e($page->meta_title); ?></small>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>/page/<?php echo e($page->slug); ?></code>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->seo_title): ?>
                                        <div><small class="text-muted"><?php echo e($page->seo_title); ?></small></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e(count($page->blocks ?? [])); ?></td>
                                <td>
                                    <span class="badge <?php echo e($page->status ? 'bg-success' : 'bg-danger'); ?>">
                                        <?php echo e($page->status ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td><small class="text-muted"><?php echo e($page->updated_at?->format('d M Y, h:i A')); ?></small></td>
                                <td>
                                    <div class="d-flex">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($page->trashed())): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->status): ?>
                                                <a href="<?php echo e(route('public.page', $page->slug)); ?>" target="_blank"
                                                    class="btn btn-sm btn-info me-1" title="View page">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <a wire:navigate href="<?php echo e(route('admin.pages.edit', $page->id)); ?>"
                                                class="btn btn-sm btn-primary me-1" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button wire:click="toggleStatus(<?php echo e($page->id); ?>)"
                                                class="btn btn-sm btn-warning me-1" title="Toggle status">
                                                <i class="bx bx-power-off"></i>
                                            </button>
                                            <button wire:click="delete(<?php echo e($page->id); ?>)"
                                                wire:confirm="Move this page to trash?"
                                                class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button wire:click="restore(<?php echo e($page->id); ?>)"
                                                class="btn btn-sm btn-success me-1" title="Restore">
                                                <i class="bx bx-undo"></i>
                                            </button>
                                            <button wire:click="forceDelete(<?php echo e($page->id); ?>)"
                                                wire:confirm="Delete this page permanently?"
                                                class="btn btn-sm btn-danger" title="Delete permanently">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\pages\page-index.blade.php ENDPATH**/ ?>