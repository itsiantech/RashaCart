<?php
    use Addons\PartialPayment\Models\OrderPlan;

    $ppPlan = isset($data['order_id']) ? OrderPlan::forOrder((int) $data['order_id']) : null;
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ppPlan): ?>
    <p>
        <?php echo e(__('partial-payment::messages.success_advance', [
            'amount' => setting('currency').' '.number_format((float) $ppPlan->advance_amount, 0),
        ])); ?>

    </p>
    <p>
        <?php echo e(__('partial-payment::messages.success_due', [
            'amount' => setting('currency').' '.number_format((float) $ppPlan->due_amount, 0),
        ])); ?>

    </p>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\popup\success.blade.php ENDPATH**/ ?>