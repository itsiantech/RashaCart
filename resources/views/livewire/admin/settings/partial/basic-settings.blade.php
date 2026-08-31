<div>
    <div class="card-header bg-primary text-white mb-3">Basic Settings</div>

    <form wire:submit.prevent="update" enctype="multipart/form-data">

        {{-- Application Logo --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="app_logo" class="form-label">Application Logo</label>
                <input type="file" class="form-control @error('app_logo') is-invalid @enderror" id="app_logo"
                    wire:model="app_logo">
                @error('app_logo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Loading Indicator --}}
                <div wire:loading wire:target="app_logo" class="mt-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Uploading...</span>
                    </div>
                    <small class="text-muted">Uploading logo...</small>
                </div>

                {{-- Current Logo Preview --}}
                @if ($app_logo)
                {{-- New uploaded logo preview (before save) --}}
                <div class="mt-2">
                    <img src="{{ $app_logo->temporaryUrl() }}" alt="Preview Logo" width="100">
                    <div><small class="text-muted">Preview</small></div>
                </div>
                @elseif ($current_app_logo)
                {{-- Saved logo — always fresh, no cache dependency --}}
                <div class="mt-2">
                    <img src="{{ asset('storage/logo/' . $current_app_logo) }}?v={{ time() }}" alt="Current Logo" width="100">
                    <div><small class="text-muted">Current logo</small></div>
                </div>
                @endif
            </div>
        </div>


        {{-- Application fev --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="app_logo" class="form-label">Fev Icon</label>
                <input type="file" class="form-control @error('fev_icon') is-invalid @enderror" id="fev_icon"
                    wire:model="fev_icon">
                @error('fev_icon')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Loading Indicator --}}
                <div wire:loading wire:target="fev_icon" class="mt-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Uploading...</span>
                    </div>
                    <small class="text-muted">Uploading logo...</small>
                </div>

                {{-- Current Logo Preview --}}
                @if ($fev_icon)
                {{-- New uploaded fev preview (before save) --}}
                <div class="mt-2">
                    <img src="{{ $fev_icon->temporaryUrl() }}" alt="Preview fev" width="100">
                    <div><small class="text-muted">Preview</small></div>
                </div>
                @elseif ($current_fev_icon)
                {{-- Saved fev icon — always fresh, no cache dependency --}}
                <div class="mt-2">
                    <img src="{{ asset('storage/logo/' . $current_fev_icon) }}?v={{ time() }}" alt="Current fev" width="100">
                    <div><small class="text-muted">Current logo</small></div>
                </div>
                @endif
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-6">
                <label for="theme" class="form-label">Application Theme</label>
                <select class="form-control @error('theme') is-invalid @enderror" id="theme"
                    wire:model.defer="theme">
                    <option value="">Select Theme</option>
                    <option value="default">Default </option>
                    <option value="theme_1">Theme-1 </option>
                    <option value="theme_2">Theme-2 </option>

                </select>
                @error('theme')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Application Description --}}
        <div class="mb-3">
            <label for="appDescription" class="form-label">Company Description</label>
            <textarea class="form-control @error('app_description') is-invalid @enderror" id="appDescription" rows="3"
                wire:model.defer="app_description"></textarea>
            @error('app_description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Items Per Page --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="itemsPerPage" class="form-label">Items Per Page</label>
                <input type="number" class="form-control @error('items_per_page') is-invalid @enderror"
                    id="itemsPerPage" wire:model.defer="items_per_page" min="5" max="100">
                @error('items_per_page')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        {{-- Location --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="google_map_location" class="form-label">Office Google Location</label>
                <input type="text" class="form-control @error('google_map_location') is-invalid @enderror"
                    id="itemsPerPage" wire:model.defer="google_map_location" min="5" max="100">
                @error('google_map_location')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">
                    Save Changes
                </span>
                <span wire:loading wire:target="update">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>