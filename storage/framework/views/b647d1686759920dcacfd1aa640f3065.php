<div>
    <div class="col-xxl">
        <!-- Brand Form -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showForm): ?>
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><?php echo e($isEdit ? 'Edit Brand' : 'Add New Brand'); ?></h5>
                <button type="button" class="btn-close" wire:click="cancelForm"></button>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveBrand">
                    <!-- Name Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="name">Brand Name *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <input
                                    type="text"
                                    class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="name"
                                    wire:model.blur="name"
                                    placeholder="Enter brand name"
                                    autofocus>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Slug Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="slug">Slug *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-link"></i></span>
                                <input
                                    type="text"
                                    class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="slug"
                                    wire:model="slug"
                                    placeholder="brand-slug">
                            </div>
                            <small class="text-muted">Used in URLs. Auto-generated from name.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="description">Description</label>
                        <div class="col-sm-10">
                            <textarea
                                class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="description"
                                wire:model.blur="description"
                                placeholder="Enter brand description"
                                rows="3"></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Logo Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="logo">Brand Logo</label>
                        <div class="col-sm-10">
                            <input
                                type="file"
                                class="form-control <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="logo"
                                wire:model="logo"
                                accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.pdf,.doc,.docx">
                            <small class="text-muted">
                                Allowed: JPG, PNG, GIF, BMP, WEBP, SVG, PDF, DOC, DOCX (Max: 5MB)
                            </small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <!-- Logo Preview Area -->
                            <div class="mt-3">
                                <!-- Current Logo -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($current_logo_path): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3">
                                        <img
                                            src="<?php echo e(Storage::url($current_logo_path)); ?>"
                                            alt="Current logo"
                                            style="max-height: 80px; max-width: 200px;"
                                            class="rounded border p-1">
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="$set('logo', 'delete')">
                                        <i class="bx bx-trash me-1"></i> Remove Logo
                                    </button>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <!-- New Logo Preview -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo && !is_string($logo) && $logo !== 'delete'): ?>
                                <div class="mt-2 p-2 border rounded">
                                    <p class="mb-1 fw-semibold">New Logo Preview:</p>
                                    <img
                                        src="<?php echo e($logo->temporaryUrl()); ?>"
                                        alt="Preview"
                                        style="max-height: 80px; max-width: 200px;"
                                        class="rounded">
                                    <p class="small text-muted mt-1 mb-0">
                                        File: <?php echo e($logo->getClientOriginalName()); ?>

                                        (<?php echo e(round($logo->getSize() / 1024, 2)); ?> KB)
                                    </p>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <!-- Logo Delete Indicator -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo === 'delete'): ?>
                                <div class="alert alert-warning py-2 mt-2">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Current logo will be removed upon saving.
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sort Order Field -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="sort_order">Sort Order</label>
                        <div class="col-sm-10">
                            <input
                                type="number"
                                class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="sort_order"
                                wire:model.blur="sort_order"
                                placeholder="0"
                                min="0">
                            <small class="text-muted">Lower numbers appear first in listings.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    type="checkbox"
                                    id="is_active"
                                    wire:model="is_active"
                                    role="switch">
                                <label class="form-check-label" for="is_active">
                                    <?php echo e($is_active ? 'Active' : 'Inactive'); ?>

                                </label>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="bx bx-save me-1"></i>
                                        <?php echo e($isEdit ? 'Update Brand' : 'Create Brand'); ?>

                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Saving...
                                    </span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm" wire:loading.attr="disabled">
                                    <i class="bx bx-x me-1"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Brands List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <!-- Title Column -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Brands</h5>
                        <p class="text-muted small mb-0">Manage your product brands</p>
                    </div>

                    <!-- Search and Add Button Column -->
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Search Input -->
                            <div class="flex-grow-1" style="min-width: 200px;">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        wire:model.live.debounce.300ms="search"
                                        placeholder="Search by name, slug, or description...">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                        <i class="bx bx-x"></i>
                                    </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedBrands) > 0): ?>
                            <div class="flex-shrink-0">
                                <button
                                    class="btn btn-danger d-flex align-items-center"
                                    wire:click="confirmDelete"
                                    wire:loading.attr="disabled">
                                    <i class="bx bx-trash me-1"></i>
                                    <span>Delete (<?php echo e(count($selectedBrands)); ?>)</span>
                                </button>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <!-- Add New Button -->
                            <div class="flex-shrink-0">
                                <button
                                    class="btn btn-primary d-flex align-items-center"
                                    wire:click="showAddForm">
                                    <i class="bx bx-plus me-1"></i>
                                    <span>Add Brand</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        wire:model="selectAll"
                                        id="selectAll">
                                </div>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('name')" class="text-dark text-decoration-none">
                                    Name
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'name'): ?>
                                    <i class="bx bx-chevron-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th>Logo</th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('products_count')" class="text-dark text-decoration-none">
                                    Products
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'products_count'): ?>
                                    <i class="bx bx-chevron-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('sort_order')" class="text-dark text-decoration-none">
                                    Sort
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'sort_order'): ?>
                                    <i class="bx bx-chevron-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('is_active')" class="text-dark text-decoration-none">
                                    Status
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'is_active'): ?>
                                    <i class="bx bx-chevron-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        value="<?php echo e($brand->id); ?>"
                                        wire:model="selectedBrands"
                                        id="brand_<?php echo e($brand->id); ?>">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand->logo): ?>
                                    <div class="flex-shrink-0 me-3">
                                        <img
                                            src="<?php echo e(Storage::url($brand->logo)); ?>"
                                            alt="<?php echo e($brand->name); ?>"
                                            style="height: 40px; width: 40px; object-fit: contain;"
                                            class="rounded border">
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">
                                            <i class="bx bx-store me-1 text-primary"></i>
                                            <?php echo e($brand->name); ?>

                                        </div>
                                        <div class="text-muted small">
                                            <code>/<?php echo e($brand->slug); ?></code>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand->website): ?>
                                            <span class="ms-2">
                                                <i class="bx bx-link-external"></i>
                                                <a href="<?php echo e($brand->website); ?>" target="_blank" class="text-decoration-none">Website</a>
                                            </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand->description): ?>
                                        <p class="mb-0 small text-muted mt-1"><?php echo e(Str::limit($brand->description, 60)); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand->logo): ?>
                                <img src="<?php echo e(Storage::url($brand->logo)); ?>"
                                    alt="<?php echo e($brand->name); ?>"
                                    style="height: 40px; width: 40px; object-fit: contain;"
                                    class="rounded border">
                                <?php else: ?>
                                <span class="badge bg-label-secondary">No logo</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-label-primary rounded-pill px-3 py-1">
                                    <?php echo e($brand->products_count); ?> products
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-info rounded-pill px-3 py-1">
                                    <?php echo e($brand->sort_order); ?>

                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch d-inline-block">
                                    <input
                                        class="form-check-input cursor-pointer"
                                        type="checkbox"
                                        wire:change="toggleStatus(<?php echo e($brand->id); ?>)"
                                        <?php if($brand->is_active): echo 'checked'; endif; ?>
                                    id="status_<?php echo e($brand->id); ?>"
                                    data-bs-toggle="tooltip"
                                    title="Toggle Status">
                                    <label class="form-check-label" for="status_<?php echo e($brand->id); ?>">
                                        <span class="badge bg-<?php echo e($brand->is_active ? 'success' : 'secondary'); ?>">
                                            <?php echo e($brand->is_active ? 'Active' : 'Inactive'); ?>

                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-icon btn-outline-primary"
                                        wire:click="showEditForm(<?php echo e($brand->id); ?>)"
                                        data-bs-toggle="tooltip"
                                        title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-outline-danger"
                                        wire:click="deleteBrands(<?php echo e($brand->id); ?>)"
                                        data-bs-toggle="tooltip"
                                        title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bx bx-package bx-lg mb-3 opacity-25"></i>
                                    <h5 class="mb-2">No brands found</h5>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                                    <p class="mb-3">No results found for "<?php echo e($search); ?>"</p>
                                    <button class="btn btn-sm btn-outline-secondary" wire:click="$set('search', '')">
                                        <i class="bx bx-x me-1"></i> Clear search
                                    </button>
                                    <?php else: ?>
                                    <p class="mb-3">Get started by adding your first brand</p>
                                    <button class="btn btn-primary" wire:click="showAddForm">
                                        <i class="bx bx-plus me-1"></i> Add Your First Brand
                                    </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brands->hasPages()): ?>
            <div class="card-footer">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div class="text-muted small">
                        Showing <?php echo e($brands->firstItem()); ?> to <?php echo e($brands->lastItem()); ?> of <?php echo e($brands->total()); ?> brands

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedBrands) > 0): ?>
                        <span class="ms-2">
                            • <strong><?php echo e(count($selectedBrands)); ?></strong> selected
                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 120px;">
                            <label class="input-group-text" for="perPage">Show:</label>
                            <select class="form-select form-select-sm" id="perPage" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div>
                            <?php echo e($brands->links(data: ['scrollTo' => false])); ?>

                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed top-0 end-0 m-4">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <strong>Processing...</strong>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('livewire:init', () => {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Listen for delete confirmation - FIXED SYNTAX
        Livewire.on('show-confirmation', (data) => {
            Swal.fire({
                title: data[0].title || 'Are you sure?',
                text: data[0].message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: data[0].confirmText || 'Delete',
                cancelButtonText: data[0].cancelText || 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (data[0].confirmMethod) {
                        // In Livewire 3, you need to call the method directly
                        Livewire.dispatch(data[0].confirmMethod, {
                            ids: data[0].confirmParams
                        });
                    }
                }
            });
        });

        // Listen for toast notifications - FIXED SYNTAX
        Livewire.on('show-toast', (data) => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: data[0].type,
                title: data[0].message
            });
        });
    });
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/brands/index.blade.php ENDPATH**/ ?>