<div class="list-icon-picker d-flex flex-wrap gap-2">
    @foreach (\App\Models\LandingPage::LIST_ICONS as $icon)
        <label
            class="list-icon-option {{ $value === $icon ? 'is-selected' : '' }}"
            title="{{ $icon }}"
        >
            <input
                type="radio"
                class="d-none"
                wire:model.live="{{ $model }}"
                value="{{ $icon }}"
            >
            <i class="bx {{ $icon }}"></i>
        </label>
    @endforeach
</div>
<small class="text-muted d-block mt-2">All items in this list will use the same icon.</small>

@once
<style>
    .list-icon-option {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #d9dee3;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
        margin: 0;
        color: #566a7f;
        transition: all .15s ease;
    }
    .list-icon-option i {
        font-size: 1.35rem;
        line-height: 1;
    }
    .list-icon-option:hover {
        border-color: #71dd37;
        color: #71dd37;
    }
    .list-icon-option.is-selected {
        border-color: #71dd37;
        background: #e8fadf;
        color: #71dd37;
        box-shadow: 0 0 0 2px rgba(113, 221, 55, .25);
    }
</style>
@endonce
