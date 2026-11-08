<select class="form-select" style="max-width: 190px;" wire:model.live="af.payment_status"
    aria-label="Filter by advance payment status">
    <option value="all">Payment: All</option>
    <option value="awaiting_verify">Advance — awaiting verification</option>
    <option value="advance_pending">Advance not paid</option>
    <option value="advance_paid">Advance paid, due open</option>
    <option value="paid">Fully collected</option>
    <option value="refund_due">Refund to process</option>
</select>
<?php /**PATH C:\Users\user\bandrose\addons\PartialPayment\src/../resources/views/admin/filters.blade.php ENDPATH**/ ?>