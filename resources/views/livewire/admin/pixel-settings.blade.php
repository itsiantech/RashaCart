<div>
    <h4 class="fw-bold mb-4">Pixel Tracking Settings</h4>

    <div class="row">
        {{-- Facebook --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bxl-facebook-circle text-primary me-2"></i>Facebook Pixel</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                            wire:model="platforms.facebook.is_active">
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pixel ID</label>
                        <input type="text" class="form-control" placeholder="e.g. 123456789012345"
                            wire:model="platforms.facebook.pixel_id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Access Token <small class="text-muted">(Conversions API — optional)</small></label>
                        <input type="password" class="form-control" placeholder="EAAG..."
                            wire:model="platforms.facebook.access_token">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Test Event Code <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control" placeholder="TEST12345"
                            wire:model="platforms.facebook.test_event_code">
                    </div>
                </div>
            </div>
        </div>

        {{-- TikTok --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bxl-tiktok me-2"></i>TikTok Pixel</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                            wire:model="platforms.tiktok.is_active">
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pixel ID</label>
                        <input type="text" class="form-control" placeholder="e.g. CXXXXXXXXXXXXXXXXXXX"
                            wire:model="platforms.tiktok.pixel_id">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Access Token <small class="text-muted">(Events API — optional)</small></label>
                        <input type="password" class="form-control" placeholder="Access token"
                            wire:model="platforms.tiktok.access_token">
                    </div>
                </div>
            </div>
        </div>

        {{-- Google / GA4 --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bxl-google me-2"></i>Google (GA4 / YouTube Ads)</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                            wire:model="platforms.google.is_active">
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-0">
                        <label class="form-label">Measurement ID</label>
                        <input type="text" class="form-control" placeholder="G-XXXXXXXXXX"
                            wire:model="platforms.google.pixel_id">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="save">Save Pixel Settings</span>
        <span wire:loading wire:target="save">Saving...</span>
    </button>
</div>