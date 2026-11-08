<div class="tab-pane fade" id="partial-payment" role="tabpanel" aria-labelledby="partial-payment-tab">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('pp-settings-panel');

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-477920297-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\admin\settings-pane.blade.php ENDPATH**/ ?>