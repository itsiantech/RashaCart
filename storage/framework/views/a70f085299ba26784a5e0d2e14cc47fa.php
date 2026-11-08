<?php
    use Addons\PartialPayment\Services\PlanCalculator;

    $ppPlan = PlanCalculator::preview(
        (int) ($product_id ?? 0),
        (float) ($unit_price ?? 0),
        max(1, (int) ($qty ?? 1)),
        (float) ($shipping ?? 0)
    );
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($ppPlan)): ?>
    <div class="op-row pp-row">
        <span><?php echo e(__('partial-payment::messages.pay_now')); ?></span>
        <strong><?php echo e($currency ?? setting('currency')); ?> <?php echo e(number_format($ppPlan['advance'], 0)); ?></strong>
    </div>
    <div class="op-row pp-row pp-row--due">
        <span><?php echo e(__('partial-payment::messages.pay_later')); ?></span>
        <strong><?php echo e($currency ?? setting('currency')); ?> <?php echo e(number_format($ppPlan['due'], 0)); ?></strong>
    </div>

    <p class="pp-note">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ppPlan['advance_type'] === 'percent'): ?>
            <?php echo e(__('partial-payment::messages.note_percent', [
                'pct' => rtrim(rtrim(number_format($ppPlan['advance_value'], 2), '0'), '.'),
                'amount' => number_format($ppPlan['advance'], 0),
            ])); ?>

        <?php else: ?>
            <?php echo e(__('partial-payment::messages.note_fixed', ['amount' => number_format($ppPlan['advance'], 0)])); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo e(__('partial-payment::messages.note_rest', ['amount' => number_format($ppPlan['due'], 0)])); ?>

    </p>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\resources\views\popup\summary-rows.blade.php ENDPATH**/ ?>