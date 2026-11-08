@php
    use Addons\PartialPayment\Services\PlanCalculator;

    $ppPlan = PlanCalculator::preview(
        (int) ($product_id ?? 0),
        (float) ($unit_price ?? 0),
        max(1, (int) ($qty ?? 1)),
        (float) ($shipping ?? 0)
    );
@endphp

@if (! empty($ppPlan))
    <div class="op-row pp-row">
        <span>{{ __('partial-payment::messages.pay_now') }}</span>
        <strong>{{ $currency ?? setting('currency') }} {{ number_format($ppPlan['advance'], 0) }}</strong>
    </div>
    <div class="op-row pp-row pp-row--due">
        <span>{{ __('partial-payment::messages.pay_later') }}</span>
        <strong>{{ $currency ?? setting('currency') }} {{ number_format($ppPlan['due'], 0) }}</strong>
    </div>

    <p class="pp-note">
        @if ($ppPlan['advance_type'] === 'percent')
            {{ __('partial-payment::messages.note_percent', [
                'pct' => rtrim(rtrim(number_format($ppPlan['advance_value'], 2), '0'), '.'),
                'amount' => number_format($ppPlan['advance'], 0),
            ]) }}
        @else
            {{ __('partial-payment::messages.note_fixed', ['amount' => number_format($ppPlan['advance'], 0)]) }}
        @endif
        {{ __('partial-payment::messages.note_rest', ['amount' => number_format($ppPlan['due'], 0)]) }}
    </p>
@endif
