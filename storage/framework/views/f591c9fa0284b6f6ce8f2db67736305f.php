<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'groups' => [],
    'picks' => [],
    'availability' => [],
    'options' => [],
    'optionId' => null,
    'optionRequired' => false,
    'select' => 'selectPickerValue',
    'selectOption' => 'selectPickerOption',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'groups' => [],
    'picks' => [],
    'availability' => [],
    'options' => [],
    'optionId' => null,
    'optionRequired' => false,
    'select' => 'selectPickerValue',
    'selectOption' => 'selectPickerOption',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $picked = $picks[$group['name']] ?? null;
    ?>
    <div class="vp-group">
        <div class="vp-label">
            <span><?php echo e($group['name']); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($group['required'])): ?>
                <span class="vp-req">*</span>
            <?php else: ?>
                <span class="vp-opt">(optional)</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($picked !== null && $picked !== ''): ?>
                <span class="vp-sel"><?php echo e($picked); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="vp-opts">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $group['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isOut = !($availability[$group['name']][$value['value']] ?? true);
                    $isOn = $picked === $value['value'];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['type'] === 'swatch'): ?>
                    <button type="button" class="vp-swatch <?php echo e($isOn ? 'is-on' : ''); ?>"
                        wire:click="<?php echo e($select); ?>(<?php echo \Illuminate\Support\Js::from($group['name'])->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($value['value'])->toHtml() ?>)"
                        <?php if($isOut): echo 'disabled'; endif; ?>
                        title="<?php echo e($value['value']); ?><?php echo e($isOut ? ' — স্টকে নেই' : ''); ?>"
                        aria-label="Select <?php echo e($group['name']); ?> <?php echo e($value['value']); ?>"
                        aria-pressed="<?php echo e($isOn ? 'true' : 'false'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($value['image'])): ?>
                            <img src="<?php echo e($value['image']); ?>" alt="<?php echo e($value['value']); ?>" loading="lazy">
                        <?php else: ?>
                            <span style="background: <?php echo e($value['color_hex'] ?: '#e5e7eb'); ?>;"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                <?php else: ?>
                    <button type="button" class="vp-chip <?php echo e($isOn ? 'is-on' : ''); ?>"
                        wire:click="<?php echo e($select); ?>(<?php echo \Illuminate\Support\Js::from($group['name'])->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($value['value'])->toHtml() ?>)"
                        <?php if($isOut): echo 'disabled'; endif; ?>
                        aria-pressed="<?php echo e($isOn ? 'true' : 'false'); ?>"><?php echo e($value['value']); ?></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($options)): ?>
    <div class="vp-group">
        <div class="vp-label">
            <span>Design / View</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($optionRequired)): ?>
                <span class="vp-req">*</span>
            <?php else: ?>
                <span class="vp-opt">(optional)</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="vp-options">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" class="vp-option <?php echo e((int) $optionId === (int) $option['id'] ? 'is-on' : ''); ?>"
                    wire:click="<?php echo e($selectOption); ?>(<?php echo e((int) $option['id']); ?>)"
                    <?php if(!$option['available']): echo 'disabled'; endif; ?>
                    title="<?php echo e($option['label']); ?><?php echo e($option['reason'] ? ' — ' . $option['reason'] : ''); ?>">
                    <img src="<?php echo e($option['image']); ?>" alt="<?php echo e($option['label']); ?>" loading="lazy">
                    <em><?php echo e($option['label']); ?></em>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views\components\variant-picker.blade.php ENDPATH**/ ?>