<div>
    <div class="content-header mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Order Settings</h4>
                <p class="text-muted mb-0">Control the product-page popup order form</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Popup Order Form</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between border rounded p-3">
                                    <div>
                                        <div class="fw-semibold">Enable popup order</div>
                                        <small class="text-muted">Replaces the product-page Buy Now checkout flow</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="switchPopupOrder"
                                            wire:model="popup_order_enabled">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between border rounded p-3">
                                    <div>
                                        <div class="fw-semibold">Variant picker popup</div>
                                        <small class="text-muted">Ask size, color &amp; design in a popup on Add to Cart and Order Now</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="switchVariantPopup"
                                            wire:model="variant_popup_enabled">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between border rounded p-3">
                                    <div>
                                        <div class="fw-semibold">Show WhatsApp button</div>
                                        <small class="text-muted">Second button inside the popup</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="switchPopupWhatsApp"
                                            wire:model="popup_order_whatsapp">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="popupOrderTitle">Popup title</label>
                                <input type="text" class="form-control" id="popupOrderTitle" maxlength="60"
                                    wire:model="popup_order_title">
                                @error('popup_order_title')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="popupOrderZone">Default delivery area</label>
                                <select class="form-select" id="popupOrderZone" wire:model="popup_order_default_zone">
                                    <option value="inside">Inside City</option>
                                    <option value="outside">Outside City</option>
                                </select>
                                @error('popup_order_default_zone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mb-3">
                            <div class="small">
                                Delivery charges come from Business Settings:
                                <strong>Inside {{ setting('currency') }} {{ number_format($dcInside, 0) }}</strong>,
                                <strong>Outside {{ setting('currency') }} {{ number_format($dcOutside, 0) }}</strong>.
                                <br>
                                WhatsApp button uses the store contact number:
                                <strong>{{ $contactPhone !== '' ? $contactPhone : 'not set (Business Settings → Contact Phone)' }}</strong>.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">Save Settings</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
