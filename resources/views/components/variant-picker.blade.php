@props([
    'groups' => [],
    'picks' => [],
    'availability' => [],
    'options' => [],
    'optionId' => null,
    'optionRequired' => false,
    'select' => 'selectPickerValue',
    'selectOption' => 'selectPickerOption',
])

@foreach ($groups as $group)
    @php
        $picked = $picks[$group['name']] ?? null;
    @endphp
    <div class="vp-group">
        <div class="vp-label">
            <span>{{ $group['name'] }}</span>
            @if (!empty($group['required']))
                <span class="vp-req">*</span>
            @else
                <span class="vp-opt">(optional)</span>
            @endif
            @if ($picked !== null && $picked !== '')
                <span class="vp-sel">{{ $picked }}</span>
            @endif
        </div>
        <div class="vp-opts">
            @foreach ($group['values'] as $value)
                @php
                    $isOut = !($availability[$group['name']][$value['value']] ?? true);
                    $isOn = $picked === $value['value'];
                @endphp
                @if ($group['type'] === 'swatch')
                    <button type="button" class="vp-swatch {{ $isOn ? 'is-on' : '' }}"
                        wire:click="{{ $select }}(@js($group['name']), @js($value['value']))"
                        @disabled($isOut)
                        title="{{ $value['value'] }}{{ $isOut ? ' — স্টকে নেই' : '' }}"
                        aria-label="Select {{ $group['name'] }} {{ $value['value'] }}"
                        aria-pressed="{{ $isOn ? 'true' : 'false' }}">
                        @if (!empty($value['image']))
                            <img src="{{ $value['image'] }}" alt="{{ $value['value'] }}" loading="lazy">
                        @else
                            <span style="background: {{ $value['color_hex'] ?: '#e5e7eb' }};"></span>
                        @endif
                    </button>
                @else
                    <button type="button" class="vp-chip {{ $isOn ? 'is-on' : '' }}"
                        wire:click="{{ $select }}(@js($group['name']), @js($value['value']))"
                        @disabled($isOut)
                        aria-pressed="{{ $isOn ? 'true' : 'false' }}">{{ $value['value'] }}</button>
                @endif
            @endforeach
        </div>
    </div>
@endforeach

@if (!empty($options))
    <div class="vp-group">
        <div class="vp-label">
            <span>Design / View</span>
            @if (!empty($optionRequired))
                <span class="vp-req">*</span>
            @else
                <span class="vp-opt">(optional)</span>
            @endif
        </div>
        <div class="vp-options">
            @foreach ($options as $option)
                <button type="button" class="vp-option {{ (int) $optionId === (int) $option['id'] ? 'is-on' : '' }}"
                    wire:click="{{ $selectOption }}({{ (int) $option['id'] }})"
                    @disabled(!$option['available'])
                    title="{{ $option['label'] }}{{ $option['reason'] ? ' — ' . $option['reason'] : '' }}">
                    <img src="{{ $option['image'] }}" alt="{{ $option['label'] }}" loading="lazy">
                    <em>{{ $option['label'] }}</em>
                </button>
            @endforeach
        </div>
    </div>
@endif
