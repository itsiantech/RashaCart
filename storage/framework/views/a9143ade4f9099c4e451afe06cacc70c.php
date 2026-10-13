
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-1">Swatch Gallery <small class="text-muted">(রঙ অনুযায়ী ছবি)</small></h5>
        <small class="text-muted d-block mb-3">ছবি আপলোড করে কোন color/value-এর সাথে লিঙ্ক করুন। Product পেজে ওই swatch সিলেক্ট করলে এই ছবিগুলো আগে দেখাবে। খালি রাখলে সব ছবির জন্য common।</small>

        <div class="mb-3">
            <input type="file" wire:model="imageUploads" multiple accept="image/*" class="form-control">
            <div wire:loading wire:target="imageUploads" class="small text-muted mt-1">Uploading...</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['imageUploads.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($imageLinks) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="12%">ছবি</th>
                        <th width="20%">Sort</th>
                        <th>কোন value-এর ছবি</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $imageLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><img src="<?php echo e($link['preview']); ?>" class="img-thumbnail" width="60" height="60"></td>
                        <td>
                            <input type="number" min="0" wire:model="imageLinks.<?php echo e($i); ?>.sort_order"
                                class="form-control form-control-sm" style="max-width:90px;">
                        </td>
                        <td>
                            <select wire:model="imageLinks.<?php echo e($i); ?>.attribute_value_id" class="form-select form-select-sm">
                                <option value="">Common (সবগুলোর জন্য)</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $productAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <optgroup label="<?php echo e($attribute->name); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attribute->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $av): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($av->id); ?>"><?php echo e($av->value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </optgroup>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </td>
                        <td>
                            <button type="button" wire:click="removeImageLink(<?php echo e($i); ?>)"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('ছবিটি মুছে ফেলবেন?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-light border mb-0">এখনো swatch ছবি যোগ হয়নি।</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/products/partials/swatch-image-manager.blade.php ENDPATH**/ ?>