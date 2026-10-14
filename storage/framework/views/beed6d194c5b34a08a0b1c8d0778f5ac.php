<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['method' => 'Payment Method', 'color' => '#6c757d']));

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

foreach (array_filter((['method' => 'Payment Method', 'color' => '#6c757d']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="text-center py-5">
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
         style="width:64px;height:64px;background:<?php echo e($color); ?>1a;">
        <i class="bi bi-toggle-off fs-2" style="color:<?php echo e($color); ?>;"></i>
    </div>
    <p class="fw-medium mb-1"><?php echo e($method); ?> is currently disabled</p>
    <p class="text-muted small mb-0">Toggle the switch above to configure your <?php echo e($method); ?> details.</p>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/components/payment-disabled-notice.blade.php ENDPATH**/ ?>