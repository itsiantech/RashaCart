<div>
    <h4 class="fw-bold mb-1">Style Settings <small class="text-muted">(Style Customizer)</small></h4>
    <p class="text-muted">Header, navigation, search, footer, font আর flash sales card — সব এখান থেকে dynamic বদলান। Save করলেই পুরো website-এ লাগবে।</p>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                @foreach ($fields as $key => $meta)
                <div class="col-md-4">
                    <label class="form-label">{{ $meta['label'] }}</label>
                    @if ($meta['type'] === 'color')
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" wire:model="form.{{ $key }}"
                            class="form-control form-control-color" value="{{ $meta['default'] ?? '#000000' }}">
                        <code>{{ $form[$key] ?? 'unset' }}</code>
                        @if (!empty($meta['optional']))
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            wire:click="$set('form.{{ $key }}', null)">Clear</button>
                        @endif
                    </div>
                    @else
                    <select class="form-select" wire:model="form.{{ $key }}">
                        @if ($key === 'flash_font')
                        <option value="">Body font-ই ব্যবহার হবে</option>
                        @endif
                        @foreach ($fonts as $fkey => $font)
                        <option value="{{ $fkey }}">{{ $font['label'] }}</option>
                        @endforeach
                    </select>
                    @endif
                    @error('form.' . $key) <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>
                @endforeach
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="button" wire:click="save" class="btn btn-primary">Save Style</button>
                <button type="button" wire:click="resetDefaults" class="btn btn-outline-secondary">Reset to default</button>
            </div>
        </div>
    </div>
</div>
