<div>
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Best Offers</li>
            </ol>
        </nav>
        <button wire:click="$set('showForm', true)" class="btn btn-danger btn-sm">
            <i class="bx bx-plus"></i> Add Best Offer
        </button>
    </div>

    {{-- Add Form --}}
    @if($showForm)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Add New Best Offer</h6>
            <button wire:click="$set('showForm', false)" class="btn-close"></button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Product --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                    <select wire:model.live="product_id" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ setting('currency') }}{{ $p->sale_price ?? $p->price }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Discount Type --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Discount Type</label>
                    <select wire:model.live="discount_type" class="form-select">
                        <option value="percent">Percent (%)</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                {{-- Discount Value --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Discount Value <span class="text-danger">*</span></label>
                    <input type="number" wire:model.live="discount_value" min="0"
                        class="form-control @error('discount_value') is-invalid @enderror"
                        placeholder="{{ $discount_type === 'percent' ? 'e.g. 20' : 'e.g. 100' }}">
                    @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Days --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                    <input type="number" wire:model="days" min="1" max="365"
                        class="form-control @error('days') is-invalid @enderror" placeholder="7">
                    @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Preview --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Offer Price Preview</label>
                    <div class="form-control bg-light fw-bold text-success">
                        @if($this->offerPricePreview !== null)
                        {{ setting('currency') }}{{ number_format($this->offerPricePreview, 2) }}
                        @else
                        --
                        @endif
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button wire:click="store" wire:loading.attr="disabled" class="btn btn-danger">
                        <span wire:loading.remove wire:target="store">Save Offer</span>
                        <span wire:loading wire:target="store"><i class="bx bx-loader bx-spin"></i> Saving...</span>
                    </button>
                    <button wire:click="$set('showForm', false)" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0">All Best Offers</h6>
            <input type="text" wire:model.live.debounce.300ms="search"
                class="form-control" style="max-width:250px" placeholder="Search by product name...">
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Original Price</th>
                        <th class="text-center">Discount</th>
                        <th class="text-center">Offer Price</th>
                        <th class="text-center">Expires At</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                    <tr class="{{ $offer->isExpired() ? 'table-secondary text-muted' : '' }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($offer->product->thumbnail)
                                <img src="{{ asset('storage/' . $offer->product->thumbnail) }}"
                                    style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                @endif
                                <span class="fw-semibold">{{ $offer->product->name }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ setting('currency') }}{{ number_format($offer->product->sale_price ?? $offer->product->price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">
                                {{ $offer->discount_value }}{{ $offer->discount_type === 'percent' ? '%' : ' '.setting('currency') }} OFF
                            </span>
                        </td>
                        <td class="text-center fw-bold text-danger">{{ setting('currency') }}{{ number_format($offer->offer_price, 2) }}</td>
                        <td class="text-center">
                            <span class="{{ $offer->isExpired() ? 'text-danger' : 'text-success' }}">
                                <i class="bx bx-time-five me-1"></i>
                                {{ $offer->expires_at->format('d M Y, h:i A') }}
                                @if($offer->isExpired())
                                <span class="badge bg-danger ms-1">Expired</span>
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox"
                                    wire:click="toggleActive({{ $offer->id }})"
                                    @checked($offer->is_active)>
                            </div>
                        </td>
                        <td class="text-center">
                            <button wire:click="confirmDelete({{ $offer->id }})" class="btn btn-sm btn-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No best offers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $offers->links() }}</div>
        </div>
    </div>
</div>