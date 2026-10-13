<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Banner</a>
            </li>
            <li class="breadcrumb-item active">Index</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="col-12 col-md-4 mb-2 mb-md-0 pe-md-2">
                    <input type="text" class="form-control" wire:model.live="search"
                        placeholder="Search attributes...">
                </div>

                    <div class="col-12 col-md-auto ms-md-auto text-md-end">
                        <a href="<?php echo e(route('admin.banner.create')); ?>" wire:navigate
                            class="btn btn-primary btn-sm d-inline-flex align-items-center">
                            <i class="bx bx-plus me-1"></i> Add New
                        </a>
                    </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banners->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="text-center">No banners found.</td>
                        </tr>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banner->image): ?>
                                        <img src="<?php echo e(asset('storage/' . $banner->image)); ?>" alt="Banner Image"
                                            style="max-height: 50px;">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td> <?php echo e($banner->title ?? 'No data'); ?></td>
                                <td><?php echo e($banner->description ?? 'No data'); ?></td>
                                <td> <?php echo e($banner->type); ?></td>
                                <td>
                                    <span class="badge <?php echo e($banner->status ? 'bg-success' : 'bg-danger'); ?>">
                                        <?php echo e($banner->status ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                            <a wire:navigate href="<?php echo e(route('admin.banner.edit', $banner->id)); ?>"
                                                class="btn btn-sm btn-primary me-1">
                                                <i class="bx bx-edit"></i> Edit
                                            </a>
                                            <button wire:click="delete(<?php echo e($banner->id); ?>)"
                                                class="btn btn-sm btn-danger">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
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
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/banner/banner-index.blade.php ENDPATH**/ ?>