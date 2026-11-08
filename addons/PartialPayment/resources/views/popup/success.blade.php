@php
    use Addons\PartialPayment\Models\OrderPlan;

    $ppPlan = isset($data['order_id']) ? OrderPlan::forOrder((int) $data['order_id']) : null;
@endphp

@if ($ppPlan)
    <p>
        {{ __('partial-payment::messages.success_advance', [
            'amount' => setting('currency').' '.number_format((float) $ppPlan->advance_amount, 0),
        ]) }}
    </p>
    <p>
        {{ __('partial-payment::messages.success_due', [
            'amount' => setting('currency').' '.number_format((float) $ppPlan->due_amount, 0),
        ]) }}
    </p>
@endif
