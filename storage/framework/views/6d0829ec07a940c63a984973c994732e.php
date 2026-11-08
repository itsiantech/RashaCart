<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Advance payment</h5>
        <small class="text-muted">এই পণ্যের জন্য গ্রাহককে আগে এডভান্স দিতে হবে কি না ঠিক করুন।</small>
    </div>
    <div class="card-body">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('pp-product-rule', ['productId' => $productId]);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2132765832-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\admin\product-section.blade.php ENDPATH**/ ?>