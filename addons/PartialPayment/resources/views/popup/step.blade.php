@php
    use Addons\PartialPayment\Support\Options;
    use Addons\PartialPayment\Support\PopupContext;

    $ppPlan = isset($component) ? PopupContext::plan($component) : null;
    $ppWants = $component->step === 'payment';

    if (! $ppWants || $ppPlan === null) {
        return;
    }

    $ppMethods = Options::availableMethods();
    $ppCurrency = setting('currency');
@endphp

<style>
    .pp-step-bar { display: flex; gap: .5rem; align-items: center; font-size: .78rem; color: #6b7280; margin: .35rem 0 .6rem; }
    .pp-step-bar b { color: #1d2b4f; }
    .pp-step-bar span.sep { opacity: .5; }
    .pp-card { background: #f6f8ff; border: 1px solid #dfe5ff; border-radius: 10px; padding: .75rem; margin: .35rem 0 .75rem; }
    .pp-card h4 { font-size: .92rem; margin: 0 0 .35rem; color: #1d2b4f; }
    .pp-recap { font-size: .8rem; color: #5a6577; margin: 0 0 .6rem; line-height: 1.5; }
    .pp-amount { font-size: 1.25rem; font-weight: 700; color: #1d2b4f; }
    .pp-methods { display: grid; gap: .45rem; margin-bottom: .6rem; }
    @media (min-width: 480px) { .pp-methods { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .pp-method { border: 1px solid #d8deee; border-radius: 9px; padding: .5rem; cursor: pointer; text-align: left; background: #fff; }
    .pp-method input { margin-right: .35rem; }
    .pp-method.is-on { border-color: #4f6ef7; background: #eef2ff; }
    .pp-method small { display: block; color: #5a6577; font-size: .74rem; word-break: break-all; }
    .pp-copy { border: 0; background: none; color: #4f6ef7; font-size: .72rem; padding: 0; cursor: pointer; text-decoration: underline; }
    .pp-empty { font-size: .82rem; color: #b42318; }
    .pp-field { margin-bottom: .6rem; }
    .pp-field label { display: block; font-size: .8rem; margin-bottom: .25rem; color: #3a4358; }
    .pp-field input { width: 100%; padding: .55rem .65rem; border: 1px solid #d8deee; border-radius: 8px; font-size: .9rem; }
    .pp-field input:focus { outline: 2px solid #c3ceff; border-color: #4f6ef7; }
    .pp-err { color: #b42318; font-size: .76rem; display: block; }
    .pp-note { font-size: .78rem; color: #5a6577; background: #fffaf0; border: 1px dashed #f0d9a8; border-radius: 8px; padding: .5rem; margin: .5rem 0 0; }
    .pp-back { background: none; border: 0; color: #4f6ef7; font-size: .82rem; padding: 0; cursor: pointer; text-decoration: underline; }
    .pp-row { display: flex; justify-content: space-between; gap: .5rem; font-size: .85rem; }
    .pp-row strong { color: #1d2b4f; }
    .pp-row--due strong { color: #067647; }
</style>

<div class="pp-step-bar">
    <span>{{ __('partial-payment::messages.bar_details') }}</span>
    <span class="sep">→</span>
    <b>{{ __('partial-payment::messages.step_bar') }}</b>
</div>

<div class="pp-card">
    <h4>{{ __('partial-payment::messages.step_title') }}</h4>

    <p class="pp-recap">
        {{ $component->name }} · {{ $component->phone }}<br>
        {{ $component->address }}
    </p>

    <p class="pp-amount">{{ $ppCurrency }} {{ number_format($ppPlan['advance'], 0) }}</p>

    @if ($ppMethods === [])
        <p class="pp-empty">{{ __('partial-payment::messages.no_number') }}</p>
    @else
        <div class="pp-methods">
            @foreach ($ppMethods as $ppKey => $ppLabel)
                @php $ppNumber = Options::number($ppKey); @endphp
                <label class="pp-method {{ ($component->addon['method'] ?? '') === $ppKey ? 'is-on' : '' }}">
                    <input type="radio" wire:model.live="addon.method" value="{{ $ppKey }}">
                    <span>{{ $ppLabel }}</span>
                    <small x-data="{ copied: false }">
                        {{ $ppNumber }}
                        <button type="button" class="pp-copy"
                            x-on:click="navigator.clipboard && navigator.clipboard.writeText('{{ $ppNumber }}'); copied = true; setTimeout(() => copied = false, 1400)"
                            x-text="copied ? {{ json_encode(__('partial-payment::messages.copied')) }} : {{ json_encode(__('partial-payment::messages.copy')) }}"></button>
                    </small>
                </label>
            @endforeach
        </div>

        @error('addon.method') <span class="pp-err">{{ $message }}</span> @enderror
    @endif

    <div class="pp-field">
        <label for="pp-sender">{{ __('partial-payment::messages.sender') }}</label>
        <input id="pp-sender" type="tel" inputmode="numeric" maxlength="14" placeholder="01712345678"
            wire:model.trim="addon.sender_number">
        @error('addon.sender_number') <span class="pp-err">{{ $message }}</span> @enderror
    </div>

    <div class="pp-field">
        <label for="pp-trx">{{ __('partial-payment::messages.trx') }}</label>
        <input id="pp-trx" type="text" maxlength="20" autocomplete="off"
            placeholder="{{ __('partial-payment::messages.trx_hint') }}"
            wire:model.trim="addon.trx_id" style="text-transform: uppercase">
        @error('addon.trx_id') <span class="pp-err">{{ $message }}</span> @enderror
    </div>

    @php $ppInstructions = Options::instructions(); @endphp
    @if ($ppInstructions !== '')
        <p class="pp-note">{{ $ppInstructions }}</p>
    @endif
</div>

<button type="button" class="pp-back" wire:click="gotoStep('details')">
    <i class="fas fa-arrow-left"></i> {{ __('partial-payment::messages.back') }}
</button>
