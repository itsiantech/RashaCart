<div>
    @if (! $productId)
        <p class="text-muted mb-0">
            প্রোডাক্ট সেভ করার পর এখান থেকে এডভান্স সেট করুন।
        </p>
    @else
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">এডভান্স লাগবে</label>
                <select class="form-select" wire:model.live="mode">
                    <option value="none">না — সাধারণ ক্যাশ অন ডেলিভারি</option>
                    <option value="percent">শেয়ারক হিসেবে (%)</option>
                    <option value="fixed">নির্দিষ্ট টাকা</option>
                </select>
            </div>

            @if ($mode !== 'none')
                <div class="col-md-4">
                    <label class="form-label">{{ $mode === 'percent' ? 'শেয়ারক (%)' : 'টাকা' }}</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" wire:model.live="value"
                        placeholder="{{ $mode === 'percent' ? '30' : '500' }}">
                    @error('value') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>
            @endif

            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary" wire:click="save">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>

        @if ($example)
            <div class="alert alert-info mt-3 mb-0 py-2">
                <small class="d-block text-muted">উদাহরণ — {{ setting('currency') }}{{ number_format($example['price'], 2) }} পণ্য
                    + {{ setting('currency') }}{{ number_format($example['shipping'], 2) }} ডেলিভারি:</small>
                <strong>এখনই {{ setting('currency') }}{{ number_format($example['advance'], 0) }}</strong>
                <span class="mx-2">·</span>
                <span>ডেলিভারির সময় {{ setting('currency') }}{{ number_format($example['due'], 0) }}</span>
            </div>
        @elseif ($mode !== 'none')
            <div class="alert alert-warning mt-3 mb-0 py-2">
                <small>
                    এই সেটিংয়ে পুরোটাই এডভান্স হয়ে যাচ্ছে (বাকি টাকা থাকছে না) — শেয়ারক কমান, অথবা পণ্যের দাম বাড়ান।
                </small>
            </div>
        @endif
    @endif
</div>
