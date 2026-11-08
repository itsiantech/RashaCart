@php
    use Addons\PartialPayment\Models\OrderPlan;
    use Addons\PartialPayment\Models\Payment;

    $ppPlan = OrderPlan::forOrder((int) $order->id);

    if (! $ppPlan) {
        return;
    }

    $ppCurrency = setting('currency');
    $ppWaiting = Payment::pendingAdvance((int) $order->id);

    $ppBadge = match ($ppPlan->payment_status) {
        OrderPlan::PENDING => $ppWaiting ? ['bg-label-warning', 'টাকা পাঠানো হয়েছে — Verify'] : ['bg-label-secondary', 'এডভান্স বাকি'],
        OrderPlan::ADVANCE_PAID => ['bg-label-info', 'এডভান্স পেইড'],
        OrderPlan::PAID => ['bg-label-success', 'সব আদায় হয়েছে'],
        OrderPlan::REFUND_DUE => ['bg-label-danger', 'রিফান্ড বাকি'],
        OrderPlan::REFUNDED => ['bg-label-secondary', 'রিফান্ড সম্পন্ন'],
        default => ['bg-label-secondary', $ppPlan->payment_status],
    };
@endphp

<a href="{{ route('admin.order.view', $order->id) }}" wire:navigate class="text-decoration-none">
    <span class="badge {{ $ppBadge[0] }}">
        এডভান্স {{ $ppCurrency }}{{ number_format((float) $ppPlan->advance_amount, 0) }}
    </span>
</a>
<span class="badge {{ $ppBadge[0] }} d-block mt-1">{{ $ppBadge[1] }}</span>

@if ((float) $ppPlan->due_amount > 0 && $ppPlan->payment_status !== \Addons\PartialPayment\Models\OrderPlan::PAID)
    <small class="d-block text-muted mt-1">ডেলিভারিতে {{ $ppCurrency }}{{ number_format((float) $ppPlan->due_amount, 0) }}</small>
@endif
