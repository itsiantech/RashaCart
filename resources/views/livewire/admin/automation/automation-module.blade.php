<div>
    <a href="{{ route('admin.automation.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Automation</a>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0"><i class="{{ $module->icon() }} me-1"></i>{{ $module->name() }}</h4>
        @if ($module->status() === 'coming_soon')
        <span class="badge bg-secondary">Coming soon</span>
        @elseif ($mode === 'live')
        <span class="badge bg-success">Live</span>
        @elseif ($mode === 'shadow')
        <span class="badge bg-warning text-dark">Shadow</span>
        @else
        <span class="badge bg-secondary">Off</span>
        @endif
    </div>

    <p class="text-muted">{{ $module->description() }}</p>

    @if ($module->status() === 'coming_soon')
    {{-- Placeholder: what will come, what data it will use --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6>যা যা আসছে</h6>
            <ul>
                @foreach ($module->plannedFeatures() as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
            <small class="text-muted">এই module এখনো তৈরি হয়নি — চালু করলেও কোনো action হবে না।</small>
        </div>
    </div>
    @endif

    {{-- Generic schema-driven settings form (no custom UI per module) --}}
    <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">Settings</h6></div>
        <div class="card-body">
            @foreach ($module->settingsSchema() as $field)
            <div class="mb-3">
                <label class="form-label">{{ $field['label'] ?? $field['name'] }}</label>
                @if (($field['type'] ?? 'text') === 'toggle')
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" role="switch"
                        wire:model="form.{{ $field['name'] }}" @disabled(!$isSuperAdmin)>
                </div>
                @elseif (($field['type'] ?? '') === 'select')
                <select class="form-select" wire:model="form.{{ $field['name'] }}" @disabled(!$isSuperAdmin)>
                    @foreach ($field['options'] ?? [] as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                @elseif (($field['type'] ?? '') === 'textarea')
                <textarea class="form-control" rows="3" wire:model="form.{{ $field['name'] }}" @disabled(!$isSuperAdmin)></textarea>
                @else
                <input type="{{ ($field['type'] ?? 'text') === 'number' ? 'number' : 'text' }}"
                    class="form-control" wire:model="form.{{ $field['name'] }}" @disabled(!$isSuperAdmin)>
                @endif
                @if (!empty($field['help']))
                <small class="text-muted">{{ $field['help'] }}</small>
                @endif
                @error('form.' . $field['name']) <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>
            @endforeach
            @if ($isSuperAdmin)
            <button type="button" wire:click="save" class="btn btn-primary">Save Settings</button>
            @else
            <small class="text-muted">দেখার অনুমতি আছে, বদলানোর নয়।</small>
            @endif
        </div>
    </div>

    {{-- Collapsible Bangla guide --}}
    <div class="card">
        <div class="card-header">
            <button class="btn btn-link p-0 text-decoration-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#autoGuide">
                📖 গাইড (বাংলা)
            </button>
        </div>
        <div id="autoGuide" class="collapse">
            <div class="card-body">
                @foreach ($module->guide() as $title => $text)
                <p class="mb-2"><strong>{{ ucfirst($title) }}:</strong> {{ $text }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>
