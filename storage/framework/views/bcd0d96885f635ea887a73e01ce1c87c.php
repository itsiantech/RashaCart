<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Role Permission</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Index</li>
            </ol>
        </nav>

        <div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddRoleForm || $showEditRoleForm): ?>
                <a wire:navigate href="<?php echo e(route('admin.rolepermission.index')); ?>" class="btn btn-danger btn-sm">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
            <?php else: ?>
                <button wire:click="addRoleForm" class="btn btn-danger btn-sm">
                    <i class="bx bx-plus"></i> Add Role
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddRoleForm): ?>
        <div class="bg-white p-4 rounded shadow-sm mb-3">
            <h2 class="h5 mb-4 text-dark">Add New Role</h2>
            <form wire:submit.prevent="roleStore" class="d-flex flex-column flex-md-row align-items-center gap-3">
                <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="Role Name" wire:model="RoleName" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['RoleName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger small mt-2"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Save
                </button>
            </form>
        </div>
    <?php elseif($showEditRoleForm): ?>
        <div class="bg-white p-4 rounded shadow-sm mb-3">
            <h2 class="h5 mb-4 text-dark">Edit Role</h2>
            <form wire:submit.prevent="updateRole" class="d-flex flex-column flex-md-row align-items-center gap-3">
                <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="Role Name"
                        wire:model.debounce.500ms="RoleName" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['RoleName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger small mt-2"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    <?php else: ?>
        <div class="rounded-3 border bg-white pt-3 mt-2" style="border-color: #dee2e6;">
            <div class="d-flex flex-column flex-sm-row justify-content-between col-3 px-4 mb-3">
                <form class="w-100 w-sm-auto position-relative">
                    <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ps-3 text-muted"
                        style="pointer-events:none; font-size: 1.2rem;"></i>
                    <input type="text" placeholder="Search..." wire:model.live.debounce.300ms="search"
                        class="form-control ps-5" style="min-width: 250px;" />
                </form>
            </div>

            <!-- Roles Table -->
            <div class="table-responsive px-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-nowrap">Role Name</th>
                            <th scope="col" class="text-center text-nowrap">Permissions</th>
                            <th scope="col" class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-dark"><?php echo e($role->name); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role->permissions->isNotEmpty()): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-dark"><?php echo e($permission->name); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted fst-italic">No permissions</small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role->name !== 'admin'): ?>
                                            <button wire:click="editRole(<?php echo e($role->id); ?>)"
                                                class="btn btn-sm btn-primary" aria-label="Edit Role" title="Edit Role">
                                                <i class="bx bx-pencil"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger"
                                                x-on:click.prevent="
                              if (confirm('Are you sure you want to delete this role?')) {
                                $wire.deleteRole(<?php echo e($role->id); ?>)
                              }"
                                                aria-label="Delete Role" title="Delete Role">
                                                <i class="bx bx-trash"></i>
                                            </button>

                                            <a wire:navigate
                                                href="<?php echo e(route('admin.rolepermission.assign', ['id' => $role->id])); ?>"
                                                class="btn btn-sm btn-secondary" aria-label="Edit Permissions"
                                                title="Edit Permissions">
                                                <i class="bx bx-key"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted fst-italic py-3">No roles found.</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                <div class="mt-3 mb-3">
                    <?php echo e($roles->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



</div>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\settings\role-permission\role-permission-index.blade.php ENDPATH**/ ?>