@php
    use Addons\PartialPayment\Models\OrderPlan;
    $ppCurrency = setting('currency');
@endphp

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0">Advance payment</h5>
            <small class="text-muted">বিকাশ / নগদ / রকেট ম্যানুয়াল পেমেন্ট ভেরিফিকেশন</small>
        </div>

        @if ($plan)
            <span class="badge bg-label-{{ ['advance_pending' => 'warning', 'advance_paid' => 'info', 'paid' => 'success', 'refund_due' => 'danger', 'refunded' => 'secondary'][$plan->payment_status] ?? 'secondary' }}">
                {{ str_replace('_', ' ', ucfirst($plan->payment_status)) }}
            </span>
        @endif
    </div>

    <div class="card-body">
        @if (! $plan)
            <p class="text-muted mb-0">
                এই অর্ডারে কোনো এডভান্স প্রয়োজন হয়নি — সাধারণ ক্যাশ অন ডেলিভারি।
            </p>
        @else
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Order total</small>
                    <strong>{{ $ppCurrency }}{{ number_format((float) $plan->order->total_amount, 2) }}</strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Advance</small>
                    <strong>{{ $ppCurrency }}{{ number_format((float) $plan->advance_amount, 2) }}</strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">COD due</small>
                    <strong>{{ $ppCurrency }}{{ number_format((float) $plan->due_amount, 2) }}</strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Advance deadline</small>
                    <strong>{{ $plan->expires_at ? $plan->expires_at->format('d M, h:i A') : '—' }}</strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-2">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Sender</th>
                            <th>TrxID</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td><small>{{ ucfirst($payment->type) }}</small></td>
                                <td>{{ \Addons\PartialPayment\Support\Options::METHODS[$payment->method] ?? ucfirst($payment->method) }}</td>
                                <td>{{ $ppCurrency }}{{ number_format((float) $payment->amount, 2) }}</td>
                                <td><small>{{ $payment->sender_number ?: '—' }}</small></td>
                                <td><code>{{ $payment->trx_id ?: '—' }}</code></td>
                                <td>
                                    <span class="badge bg-label-{{ ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'][$payment->status] }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                    @if ($payment->status === 'verified' && $payment->verified_at)
                                        <small class="d-block text-muted">
                                            {{ $payment->verifier?->name }} · {{ $payment->verified_at->format('d M h:i A') }}
                                        </small>
                                    @endif
                                    @if ($payment->note)
                                        <small class="d-block text-muted">{{ $payment->note }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($payment->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success mb-1"
                                            wire:click="verify({{ $payment->id }})">
                                            Verify
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger mb-1"
                                            wire:click="startReject({{ $payment->id }})">
                                            Reject
                                        </button>
                                    @endif

                                    @if ($rejecting === $payment->id)
                                        <div class="mt-1 text-start">
                                            <input type="text" class="form-control form-control-sm"
                                                wire:model="rejectNote" placeholder="কেন বাতিল? (required)">
                                            @error('rejectNote')
                                                <small class="text-danger d-block">{{ $message }}</small>
                                            @enderror
                                            <div class="d-flex gap-1 mt-1">
                                                <button type="button" class="btn btn-sm btn-danger" wire:click="confirmReject">
                                                    Confirm reject
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancelReject">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">
                                    গ্রাহক এখনো কোনো পেমেন্ট জমা দেয়নি।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                @if ((float) $plan->due_amount > 0 && $plan->payment_status !== OrderPlan::PAID)
                    <button type="button" class="btn btn-primary" wire:click="collectDue"
                        wire:confirm="ডেলিভারির সময় বাকি {{ $ppCurrency }}{{ number_format((float) $plan->due_amount, 0) }} আদায় হয়েছে হিসেবে রেকর্ড করবেন?">
                        <i class="bx bx-money me-1"></i>
                        Collect due {{ $ppCurrency }}{{ number_format((float) $plan->due_amount, 0) }}
                    </button>

                    @if (! $showRelease)
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showRelease', true)">
                            Release the due
                        </button>
                    @else
                        <div class="d-flex gap-1 flex-wrap">
                            <input type="text" class="form-control form-control-sm" style="max-width: 240px;"
                                wire:model="releaseNote" placeholder="কেন মওকুফ? (required)">
                            <button type="button" class="btn btn-sm btn-warning" wire:click="releaseDue">Confirm</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$set('showRelease', false)">
                                Close
                            </button>
                        </div>
                        @error('releaseNote') <small class="text-danger">{{ $message }}</small> @enderror
                    @endif
                @endif

                @if ($plan->due_override)
                    <span class="badge bg-label-secondary">Due released by admin</span>
                @endif

                @if ($plan->payment_status === OrderPlan::PAID)
                    <span class="badge bg-label-success">
                        <i class="bx bx-check me-1"></i> সম্পূর্ণ আদায় হয়েছে
                    </span>
                @endif
            </div>

            @if ($activities->isNotEmpty())
                <details class="mt-3">
                    <summary class="text-muted small">Activity log ({{ $activities->count() }})</summary>
                    <ul class="small text-muted mb-0 mt-1">
                        @foreach ($activities as $activity)
                            <li>
                                {{ $activity->created_at?->format('d M Y, h:i A') }} —
                                <strong>{{ str_replace('_', ' ', $activity->action) }}</strong>
                                @if ($activity->admin) by {{ $activity->admin->name }} @endif
                                @if ($activity->note) · {{ $activity->note }} @endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        @endif
    </div>
</div>
