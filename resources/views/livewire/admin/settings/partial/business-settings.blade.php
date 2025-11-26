<div>

    <div class="card-header bg-primary text-white mb-3">Business Settings</div>
    <form wire:submit.prevent="update" enctype="multipart/form-data">

        {{-- currency  --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="currency" class="form-label">currency Icon</label>
                <select class="form-control @error('currency') is-invalid @enderror" id="currency"
                    wire:model.defer="currency">
                    <option value="">Select Currency</option>
                    <option value="৳">BDT - Bangladeshi Taka (৳)</option>
                    <option value="$">USD - US Dollar ($)</option>
                    <option value="€">EUR - Euro (€)</option>
                    <option value="£">GBP - British Pound (£)</option>
                    <option value="¥">JPY - Japanese Yen (¥)</option>
                    <option value="₹">INR - Indian Rupee (₹)</option>
                    <option value="₩">KRW - South Korean Won (₩)</option>
                    <option value="₽">RUB - Russian Ruble (₽)</option>
                    <option value="₺">TRY - Turkish Lira (₺)</option>
                    <option value="₫">VND - Vietnamese Dong (₫)</option>
                    <option value="₱">PHP - Philippine Peso (₱)</option>
                    <option value="₪">ILS - Israeli Shekel (₪)</option>
                    <option value="฿">THB - Thai Baht (฿)</option>
                    <option value="₦">NGN - Nigerian Naira (₦)</option>
                </select>
                @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Delivery Charge Section --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="dc_inside" class="form-label">Inside City Delivery Charge</label>
                <input type="number" class="form-control @error('dc_inside') is-invalid @enderror" id="dc_inside"
                    wire:model.defer="dc_inside" min="1" max="500" placeholder="0">
                @error('dc_inside')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="dc_outside" class="form-label">Outside City Delivery Charge</label>
                <input type="number" class="form-control @error('dc_outside') is-invalid @enderror" id="dc_outside"
                    wire:model.defer="dc_outside" min="1" max="500" placeholder="0">
                @error('dc_outside')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" wire:model="attribute_system" id="attribute_system"
                value="1">
            <label class="form-check-label" for="attribute_system">Attribute System</label>
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
