<div>

    <div class="card-header bg-primary text-white mb-3">Contact Settings</div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="update" id="contactSettingsForm" autocomplete="off">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="contactPhone" class="form-label">Phone Number</label>
                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contactPhone"
                    wire:model.defer="contact_phone" placeholder="+1 (234) 567-890">
                @error('contact_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="contactEmail" class="form-label">Contact Email</label>
                <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                    id="contactEmail" wire:model.defer="contact_email" placeholder="contact@example.com">
                @error('contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="contactAddress" class="form-label">Address</label>
            <textarea class="form-control @error('contact_address') is-invalid @enderror" id="contactAddress"
                wire:model.defer="contact_address" rows="2"></textarea>
            @error('contact_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="businessHours" class="form-label">Business Hours</label>
                <input type="text" class="form-control @error('business_hours') is-invalid @enderror"
                    id="businessHours" wire:model.defer="business_hours" placeholder="Mon-Fri: 9AM-5PM">
                @error('business_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">Save Changes</span>
                <span wire:loading wire:target="update">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                </span>
            </button>

        </div>

    </form>
</div>
