<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Landing Page</a>
            </li>
            <li class="breadcrumb-item active">Index</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="card-title mb-0">Landing Pages</h5>
                <a href="<?php echo e(route('admin.landing-page.create')); ?>" wire:navigate
                    class="btn btn-primary btn-sm d-inline-flex align-items-center mt-2 mt-md-0">
                    <i class="bx bx-plus me-1"></i> Create New
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Main Title</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($landingPages->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="text-center">No landing pages found.</td>
                        </tr>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $landingPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $landingPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($landingPage->trashed() ? 'table-danger' : ''); ?>">
                                <td><?php echo e($landingPage->title); ?></td>
                                <td><?php echo e($landingPage->slug); ?></td>
                                <td><?php echo e($landingPage->main_title ?? 'No data'); ?></td>
                                <td><?php echo e(count($landingPage->product_ids ?? [])); ?> products</td>
                                <td>
                                    <span class="badge <?php echo e($landingPage->status ? 'bg-success' : 'bg-danger'); ?>">
                                        <?php echo e($landingPage->status ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="<?php echo e(route('public.landing', $landingPage->slug)); ?>" target="_blank"
                                            class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$landingPage->trashed()): ?>
                                            <a wire:navigate href="<?php echo e(route('admin.landing-page.edit', $landingPage->id)); ?>"
                                                class="btn btn-sm btn-primary me-1" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button wire:click="toggleStatus(<?php echo e($landingPage->id); ?>)"
                                                class="btn btn-sm btn-warning me-1" title="Toggle Status">
                                                <i class="bx bx-power-off"></i>
                                            </button>
                                            <button wire:click="delete(<?php echo e($landingPage->id); ?>)"
                                                class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button wire:click="restore(<?php echo e($landingPage->id); ?>)"
                                                class="btn btn-sm btn-success me-1" title="Restore">
                                                <i class="bx bx-undo"></i>
                                            </button>
                                            <button wire:click="forceDelete(<?php echo e($landingPage->id); ?>)"
                                                class="btn btn-sm btn-danger" title="Permanently Delete">
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
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/landing-page/landing-page-index.blade.php ENDPATH**/ ?>