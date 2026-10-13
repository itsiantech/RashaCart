<div class="row mb-3">
    <label class="col-sm-2 col-form-label">Page Sections</label>
    <div class="col-sm-10">
        <div class="border rounded p-3">
            <p class="text-muted small mb-3 mb-md-2">Enable a section to show it on the public landing page. Disabled sections stay hidden even if content exists.</p>
            <div class="row">
                @foreach (\App\Models\LandingPage::SECTIONS as $field => $label)
                    <div class="col-md-6 mb-2">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="{{ $field }}" id="{{ $field }}" class="form-check-input">
                            <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
