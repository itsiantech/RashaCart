<div>
    <h5 class="mb-3">Partial Payment settings</h5>

    <form wire:submit="save">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Calculate advance on</label>
                <select class="form-select" wire:model="pp_base">
                    <option value="total">Total (with delivery charge)</option>
                    <option value="subtotal">Subtotal (product price only)</option>
                </select>
                <small class="text-muted">
                    Delivery charge সবসময় ক্যাশ অন ডেলিভারিতে থাকে।
                </small>
            </div>

            <div class="col-md-6">
                <label class="form-label">Advance payment window (hours)</label>
                <input type="number" min="0" max="720" class="form-control" wire:model="pp_expiry_hours">
                <small class="text-muted">
                    0 = কখনো এক্সপায়ার করবে না। সময় শেষ হলে অর্ডার নিজে থেকেই cancelled।
                </small>
                @error('pp_expiry_hours') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">bKash number</label>
                <input type="text" class="form-control" wire:model="pp_bkash_number" placeholder="01XXXXXXXXX">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nagad number</label>
                <input type="text" class="form-control" wire:model="pp_nagad_number" placeholder="01XXXXXXXXX">
            </div>

            <div class="col-md-4">
                <label class="form-label">Rocket number</label>
                <input type="text" class="form-control" wire:model="pp_rocket_number" placeholder="01XXXXXXXXX-1">
            </div>

            <div class="col-12">
                <label class="form-label">Instructions for the customer</label>
                <textarea class="form-control" rows="3" wire:model="pp_instructions"></textarea>
                @error('pp_instructions') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">Save settings</button>
        </div>
    </form>
</div>
