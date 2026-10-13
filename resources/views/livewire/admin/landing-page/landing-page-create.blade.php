<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.landing-page.index') }}">Landing Page</a>
            </li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Create Landing Page</h5>
            <a href="{{ route('admin.landing-page.index') }}" wire:navigate
                class="btn btn-primary btn-sm d-inline-flex align-items-center">
                <i class='bx bx-arrow-back'></i> Go back
            </a>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="store" enctype="multipart/form-data">
                {{-- Title --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="title">Title <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" wire:model.live="title" id="title"
                            class="form-control @error('title') is-invalid @enderror" placeholder="Landing Page Title">
                        @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Slug --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="slug">Slug <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="slug" id="slug"
                            class="form-control @error('slug') is-invalid @enderror" placeholder="landing-page-slug">
                        @error('slug')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Products Multi-select --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="product_ids">Products</label>
                    <div class="col-sm-10">
                        <select wire:model="product_ids" id="product_ids" class="form-select" multiple>
                            @foreach ($availableProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} - ৳{{ $product->price }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple products</small>
                    </div>
                </div>

                {{-- Main Title --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="main_title">Main Title</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="main_title" id="main_title"
                            class="form-control @error('main_title') is-invalid @enderror" placeholder="বিচি মুক্ত সিডলেস খেজুর">
                        @error('main_title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Hero Image --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="hero_image">Hero Image</label>
                    <div class="col-sm-10">
                        <input type="file" wire:model="hero_image" id="hero_image"
                            class="form-control @error('hero_image') is-invalid @enderror">
                        @error('hero_image')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <div class="mt-2">
                            @if ($hero_image)
                                <img src="{{ $hero_image->temporaryUrl() }}" alt="Preview" style="max-height: 200px;">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Countdown End Date --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="countdown_end_date">Countdown End Date</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" wire:model="countdown_end_date" id="countdown_end_date"
                            class="form-control @error('countdown_end_date') is-invalid @enderror">
                        @error('countdown_end_date')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Regular Price --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="regular_price">Regular Price (৳)</label>
                    <div class="col-sm-10">
                        <input type="number" step="0.01" wire:model="regular_price" id="regular_price"
                            class="form-control @error('regular_price') is-invalid @enderror" placeholder="1000.00">
                        @error('regular_price')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Discount Price --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="discount_price">Discount Price (৳)</label>
                    <div class="col-sm-10">
                        <input type="number" step="0.01" wire:model="discount_price" id="discount_price"
                            class="form-control @error('discount_price') is-invalid @enderror" placeholder="900.00">
                        @error('discount_price')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Benefits Title --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="benefits_title">Benefits Title</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="benefits_title" id="benefits_title"
                            class="form-control @error('benefits_title') is-invalid @enderror" placeholder="বিচি মুক্ত সিডলেস খেজুর কেন খাবেন?">
                        @error('benefits_title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Benefits List Icon --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Benefits List Icon</label>
                    <div class="col-sm-10">
                        @include('livewire.admin.landing-page.partials.list-icon-picker', [
                            'model' => 'benefits_icon',
                            'value' => $benefits_icon,
                        ])
                    </div>
                </div>

                {{-- Benefits List (Repeater) --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Benefits List</label>
                    <div class="col-sm-10">
                        @foreach ($benefits_list as $index => $benefit)
                            <div class="input-group mb-2">
                                <input type="text" wire:model="benefits_list.{{ $index }}"
                                    class="form-control" placeholder="Enter benefit">
                                <button type="button" wire:click="removeBenefit({{ $index }})"
                                    class="btn btn-outline-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addBenefit" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-plus"></i> Add Benefit
                        </button>
                    </div>
                </div>

                {{-- Trust Title --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="trust_title">Trust Title</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="trust_title" id="trust_title"
                            class="form-control @error('trust_title') is-invalid @enderror" placeholder="আমাদের কাছে কেন কিনবেন?">
                        @error('trust_title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Trust List Icon --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trust List Icon</label>
                    <div class="col-sm-10">
                        @include('livewire.admin.landing-page.partials.list-icon-picker', [
                            'model' => 'trust_icon',
                            'value' => $trust_icon,
                        ])
                    </div>
                </div>

                {{-- Trust List (Repeater) --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trust List</label>
                    <div class="col-sm-10">
                        @foreach ($trust_list as $index => $trust)
                            <div class="input-group mb-2">
                                <input type="text" wire:model="trust_list.{{ $index }}"
                                    class="form-control" placeholder="Enter trust point">
                                <button type="button" wire:click="removeTrust({{ $index }})"
                                    class="btn btn-outline-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addTrust" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-plus"></i> Add Trust Point
                        </button>
                    </div>
                </div>

                {{-- Secondary Image --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="secondary_image">Secondary Image</label>
                    <div class="col-sm-10">
                        <input type="file" wire:model="secondary_image" id="secondary_image"
                            class="form-control @error('secondary_image') is-invalid @enderror">
                        @error('secondary_image')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <div class="mt-2">
                            @if ($secondary_image)
                                <img src="{{ $secondary_image->temporaryUrl() }}" alt="Preview" style="max-height: 200px;">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contact Number --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="contact_number">Contact Number</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="contact_number" id="contact_number"
                            class="form-control @error('contact_number') is-invalid @enderror" placeholder="01869721691">
                        @error('contact_number')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                @include('livewire.admin.landing-page.partials.section-toggles')

                {{-- Status --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="status">Status</label>
                    <div class="col-sm-10 d-flex align-items-center">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="status" id="status" class="form-check-input" value="1">
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Create Landing Page
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
