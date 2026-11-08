@php
    use Addons\PartialPayment\Models\OrderPlan;
    $ppCurrency = setting('currency');
@endphp

<div class="col-xxl">
    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Partial Payment — Refunds &amp; verification</h5>
            <div class="d-flex gap-3 flex-wrap">
                <small class="text-muted">
                    Collected advance: <strong>{{ $ppCurrency }}{{ number_format($collected, 2) }}</strong>
                </small>
                <small class="text-muted">
                    To refund: <strong class="text-danger">{{ $ppCurrency }}{{ number_format($toRefund, 2) }}</strong>
                </small>
            </div>
        </div>

        <div class="card-body">
            <h6 class="text-muted">Refunds to process</h6>

            <div class="table-responsive mb-4">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Advance</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($refunds as $plan)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.order.view', $plan->order_id) }}" wire:navigate>
                                        {{ $plan->order?->invoice_no ?? '#'.$plan->order_id }}
                                    </a>
                                </td>
                                <td>
                                    {{ $plan->order?->name }}
                                    <small class="d-block text-muted">{{ $plan->order?->phone }}</small>
                                </td>
                                <td>{{ $ppCurrency }}{{ number_format((float) $plan->advance_amount, 2) }}</td>
                                <td><span class="badge bg-label-danger">Refund due</span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-success"
                                        wire:click="markRefunded({{ $plan->id }})"
                                        wire:confirm="রিফান্ড সম্পন্ন হয়েছে হিসেবে চিহ্নিত করবেন?">
                                        Mark refunded
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">কোনো রিফান্ড বাকি নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h6 class="text-muted">Awaiting verification</h6>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Sender</th>
                            <th>TrxID</th>
                            <th class="text-end">Verify</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waiting as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.order.view', $payment->order_id) }}" wire:navigate>
                                        {{ $payment->order?->invoice_no ?? '#'.$payment->order_id }}
                                    </a>
                                </td>
                                <td>
                                    {{ $payment->order?->name }}
                                    <small class="d-block text-muted">{{ $payment->order?->phone }}</small>
                                </td>
                                <td>{{ \Addons\PartialPayment\Support\Options::METHODS[$payment->method] ?? $payment->method }}</td>
                                <td>{{ $ppCurrency }}{{ number_format((float) $payment->amount, 2) }}</td>
                                <td><small>{{ $payment->sender_number ?: '—' }}</small></td>
                                <td><code>{{ $payment->trx_id }}</code></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.order.view', $payment->order_id) }}#partial-payment-panel"
                                        class="btn btn-sm btn-primary">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">কোনো পেমেন্ট ভেরিফিকেশনের অপেক্ষায় নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
