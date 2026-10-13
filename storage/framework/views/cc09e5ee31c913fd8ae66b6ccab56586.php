<div>
    <div class="col-xxl">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showForm): ?>
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><?php echo e($isEdit ? 'Edit Attribute' : 'Add New Attribute'); ?></h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveAttribute">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Attribute Name</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="name" class="form-control" placeholder="e.g. Size">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Slug</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="slug" class="form-control" readonly>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Type</label>
                        <div class="col-sm-10">
                            <select wire:model="type" class="form-select">
                                <option value="button">Button (Size ইত্যাদি)</option>
                                <option value="color_swatch">Color Swatch (রঙের গোল বাটন)</option>
                                <option value="image_swatch">Image Swatch (ছবির বাটন)</option>
                                <option value="dropdown">Dropdown</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Sort Order</label>
                        <div class="col-sm-10">
                            <input type="number" min="0" wire:model="sort_order" class="form-control" style="max-width:150px;">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2 d-flex gap-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="is_filterable" class="form-check-input" id="attrFilter">
                                <label class="form-check-label" for="attrFilter">Filterable (listing-এ ফিল্টার)</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" wire:model="is_active" class="form-check-input" id="attrActive">
                                <label class="form-check-label" for="attrActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary me-2"><?php echo e($isEdit ? 'Update' : 'Save'); ?></button>
                            <button type="button" class="btn btn-outline-secondary" wire:click="$set('showForm', false)">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Attribute & Attribute Value List</h5>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <div class="flex-grow-1" style="min-width: 150px;">
                                <input type="text" class="form-control" wire:model.live="search" placeholder="Search attributes...">
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-primary d-flex align-items-center" wire:click="showAddForm" style="white-space: nowrap;">
                                    <i class="bx bx-plus me-1"></i> Add New
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
                            <th>SL</th>
                            <th>Attribute</th>
                            <th>Type / Usage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attributeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td> <!-- SL Column -->
                            <td><strong><?php echo e($attribute->name); ?></strong><br><small class="text-muted"><?php echo e($attribute->slug); ?></small></td>
                            <td>
                                <span class="badge bg-info"><?php echo e($attribute->type); ?></span>
                                <small class="d-block text-muted"><?php echo e($attribute->usageCount()); ?> variants ব্যবহার করে</small>
                            </td>
                            <td>
                                <button class="btn btn-sm <?php echo e($attribute->is_active ? 'btn-success' : 'btn-secondary'); ?>"
                                    wire:click="toggleAttributeActive(<?php echo e($attribute->id); ?>)"
                                    title="Active/Deactive">
                                    <?php echo e($attribute->is_active ? 'ON' : 'OFF'); ?>

                                </button>
                            </td>
                            <td class="d-flex align-items-center gap-1">
                                <button class="btn btn-sm btn-primary" wire:click="showEditForm(<?php echo e($attribute->id); ?>)">
                                    <i class="bx bx-edit"></i>
                                </button>

                                <button class="btn btn-sm btn-danger" wire:click="confirmDelete(<?php echo e($attribute->id); ?>)">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <button class="btn btn-sm btn-success" wire:click="toggleValueForm(<?php echo e($attribute->id); ?>)">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </td>
                        </tr>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attribute->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-5"></td> <!-- Blank SL for child row -->
                            <td class="ps-5">
                                <i class="bx bx-subdirectory-right me-2"></i><?php echo e($value->value); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value->color_hex): ?>
                                <span class="d-inline-block rounded-circle border ms-1" style="width:14px;height:14px;background:<?php echo e($value->color_hex); ?>;vertical-align:-2px;"></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value->swatch_image): ?>
                                <img src="<?php echo e(asset('storage/' . $value->swatch_image)); ?>" width="22" height="22" class="rounded ms-1">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <small class="text-muted">(<?php echo e($value->usageCount()); ?> used)</small>
                            </td>
                            <td colspan="2"></td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-outline-primary me-1" wire:click="editValue(<?php echo e($value->id); ?>)">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        wire:confirm="Are you sure you want to delete this value?"
                                        wire:click="deleteValue(<?php echo e($value->id); ?>)">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showValueFormForAttributeId === $attribute->id): ?>
                        <tr>
                            <td class="ps-5">—</td>
                            <td class="ps-5" colspan="3">
                                <div class="d-flex flex-wrap gap-2 align-items-end">
                                    <div>
                                        <input type="text" wire:model.defer="attribute_value_name" class="form-control form-control-sm"
                                            placeholder="<?php echo e($isValueEdit ? 'Edit value...' : 'New value name'); ?>" style="min-width:160px;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['attribute_value_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger d-block"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Color</label>
                                        <input type="color" wire:model.defer="value_color_hex" class="form-control form-control-color form-control-sm" value="#000000">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['value_color_hex'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger d-block"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Swatch image</label>
                                        <input type="file" wire:model.defer="value_swatch_image" class="form-control form-control-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existing_swatch_image): ?>
                                        <img src="<?php echo e(asset('storage/' . $existing_swatch_image)); ?>" width="28" height="28" class="rounded mt-1">
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Sort</label>
                                        <input type="number" min="0" wire:model.defer="value_sort_order" class="form-control form-control-sm" style="width:80px;">
                                    </div>
                                    <div>
                                        <button wire:click="saveAttributeValue(<?php echo e($attribute->id); ?>)" class="btn btn-sm btn-success">
                                            <?php echo e($isValueEdit ? 'Update' : 'Add'); ?>

                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center">No attributes found.</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/attribute/attribute-component.blade.php ENDPATH**/ ?>