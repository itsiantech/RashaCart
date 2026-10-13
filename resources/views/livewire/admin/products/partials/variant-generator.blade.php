{{-- Variant combination generator (shared: Add + Edit Product) --}}
<div class="border rounded p-3 mb-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h6 class="mb-0">Attributes (Variants) <small class="text-muted">(Generate combinations)</small></h6>
        <label class="form-check-label small">
            <input type="checkbox" class="form-check-input me-1" wire:model.live="has_attributes">
            এই product-এ attributes ব্যবহার হবে
            <small class="text-muted d-block">OFF থাকলে নিচের variants save হবে না (simple product)।</small>
        </label>
    </div>
    <small class="text-muted d-block mb-3">Attribute টিক দিন → value সিলেক্ট করুন → "Generate" চাপুন। আগের variant ডুপ্লিকেট হবে না। <b>*</b> = customer-কে বাছতেই হবে (required)।</small>

    @if ($has_attributes)

    <div class="row g-3">
        @foreach ($productAttributes as $attribute)
        @if ($attribute->is_active ?? true)
        <div class="col-md-4">
            <div class="border rounded p-2 h-100">
                <label class="form-check-label fw-bold d-block mb-2">
                    <input type="checkbox" class="form-check-input me-1"
                        wire:click="toggleGenAttr({{ $attribute->id }})"
                        {{ isset($genAttrs[$attribute->id]) ? 'checked' : '' }}>
                    {{ $attribute->name }}
                    <small class="text-muted">({{ $attribute->type }})</small>
                </label>
                <label class="small d-block mb-2">
                    <input type="checkbox" class="form-check-input me-1"
                        wire:model="attrRequired.{{ $attribute->id }}">
                    Required <b class="text-danger">*</b>
                </label>
                <div class="d-flex flex-wrap gap-1">
                    @foreach ($attribute->values as $av)
                    <button type="button"
                        wire:click="toggleGenValue({{ $attribute->id }}, {{ $av->id }})"
                        class="btn btn-sm {{ in_array($av->id, $genValues[$attribute->id] ?? []) ? 'btn-primary' : 'btn-outline-secondary' }}">
                        @if ($av->color_hex)
                        <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:{{ $av->color_hex }};vertical-align:-1px;"></span>
                        @endif
                        {{ $av->value }}
                    </button>
                    @endforeach
                    <button type="button" wire:click="openQuickAdd({{ $attribute->id }})"
                        class="btn btn-sm btn-outline-success" title="নতুন value যোগ করুন">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
        <button type="button" wire:click="generateVariants" class="btn btn-success btn-sm">
            <i class="fas fa-magic me-1"></i> Generate Variants
        </button>
        <a href="{{ route('admin.attribute.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-cog me-1"></i> Attributes ম্যানেজ করুন
        </a>
        <span class="text-muted small">|</span>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text">৳</span>
            <input type="number" step="0.01" min="0" class="form-control" placeholder="Bulk price" id="bulkPriceInput">
            <button type="button" class="btn btn-outline-secondary"
                onclick="Livewire.dispatch('bulk-price')">Apply</button>
        </div>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <input type="number" step="1" min="0" class="form-control" placeholder="Bulk stock" id="bulkStockInput">
            <button type="button" class="btn btn-outline-secondary"
                onclick="Livewire.dispatch('bulk-stock')">Apply</button>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm"
            wire:click="autoVariantPrices" title="সব variant-এর দাম খালি করে product দাম follow করাও">
            Auto price
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm"
            wire:click="copyProductStockToVariants" title="Product-এর stock সংখ্যাটা সব variant-এ বসাও">
            Product stock →
        </button>
    </div>

    {{-- Quick-add value modal (no page reload) --}}
    @if ($showQuickAdd)
    <div class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,.4);">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0">নতুন value যোগ করুন</h6>
                    <button type="button" class="btn-close" wire:click="$set('showQuickAdd', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small">Value নাম <span class="text-danger">*</span></label>
                        <input type="text" wire:model="quickValue" class="form-control form-control-sm" placeholder="e.g. Yellow">
                        @error('quickValue') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Color (optional)</label>
                        <input type="color" wire:model="quickColor" class="form-control form-control-color form-control-sm" value="#000000">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$set('showQuickAdd', false)">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" wire:click="quickAddValue">Add</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endif {{-- has_attributes --}}
</div>

@script
<script>
    $wire.on('bulk-price', () => {
        const v = document.getElementById('bulkPriceInput').value;
        if (v !== '') $wire.bulkSetVariantPrice(parseFloat(v));
    });
    $wire.on('bulk-stock', () => {
        const v = document.getElementById('bulkStockInput').value;
        if (v !== '') $wire.bulkSetVariantStock(parseInt(v));
    });
</script>
@endscript
