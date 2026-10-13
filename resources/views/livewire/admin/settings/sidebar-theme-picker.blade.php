<div>
    <button type="button" id="sb-theme-fab" wire:click="$toggle('open')" title="Sidebar theme" aria-label="Sidebar theme">
        <i class="bx bx-palette"></i>
    </button>

    @if ($open)
    <div id="sb-theme-panel">
        <h6>Sidebar Theme</h6>
        <div class="sb-dots">
            @foreach ($themes as $key => $t)
            <button type="button" class="sb-dot {{ $theme === $key ? 'sel' : '' }}"
                style="background:{{ $t['swatch'] }}" title="{{ $t['label'] }}"
                aria-label="{{ $t['label'] }}"
                wire:click="selectTheme('{{ $key }}')"></button>
            @endforeach
        </div>
        <h6>Menu Font Size</h6>
        <div class="sb-row">
            <button type="button" wire:click="stepFont(-1)" @disabled($fontSize <= $min) aria-label="Decrease font size">A-</button>
            <span class="sb-size">{{ $fontSize }}px</span>
            <button type="button" wire:click="stepFont(1)" @disabled($fontSize >= $max) aria-label="Increase font size">A+</button>
            <button type="button" wire:click="resetTheme" title="Reset to default" aria-label="Reset">↺</button>
        </div>
    </div>
    @endif
</div>
