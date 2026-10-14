<div>
     <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Coupon</a>
            </li>
            <li class="breadcrumb-item active">Index</li>
        </ol>
    </nav>
    <div class="col-xxl">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="col-12 col-md-4 mb-2 mb-md-0 pe-md-2">
                        <input type="text" class="form-control" wire:model.live="search"
                            placeholder="Search attributes...">
                    </div>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('banner.create')): ?>
                        <div class="col-12 col-md-auto ms-md-auto text-md-end">
                            <a href="<?php echo e(route('admin.cupon.create')); ?>" wire:navigate
                                class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                <i class="bx bx-plus me-1"></i> Add New
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
           
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Assigned Products</th>
                            <th>Assigned Categories</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($coupon->code); ?></td>
                                <td>
                                    <?php echo e($coupon->discount_type == 'percent' ? $coupon->discount_value.'%' : $coupon->discount_value); ?>

                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $coupon->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-info"><?php echo e($product->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $coupon->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-warning"><?php echo e($category->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo e($coupon->status ? 'bg-success' : 'bg-secondary'); ?>">
                                        <?php echo e($coupon->status ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td>
                                     <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cupon.update')): ?>
                                        <a wire:navigate href="<?php echo e(route('admin.cupon.create', $coupon->id)); ?>"
                                            class="btn btn-sm btn-primary me-1">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cupon.delete')): ?>
                                        <button onclick="confirmDelete(<?php echo e($coupon->id); ?>)"
                                            class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">No coupons found</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/cupon/cupon-index.blade.php ENDPATH**/ ?>