<div>
    <div class="col-xxl">

        {{-- Attribute Form --}}
        @if($showForm)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ $isEdit ? 'Edit Attribute' : 'Add New Attribute' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveAttribute">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Attribute Name</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="name" class="form-control" placeholder="e.g. Size">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Slug</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="slug" class="form-control" readonly>
                            @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Type</label>
                        <div class="col-sm-10">
                            <select wire:model="type" class="form-select">
                                <option value="button">Button (Size ইত্যাদি)</option>
                                <option value="color_swatch">Color Swatch (রঙের গোল বাটন)</option>
                                <option value="image_swatch">Image Swatch (ছবির বাটন)</option>
                                <option value="dropdown">Dropdown</option>
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Sort Order</label>
                        <div class="col-sm-10">
                            <input type="number" min="0" wire:model="sort_order" class="form-control" style="max-width:150px;">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2 d-flex gap-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="is_filterable" class="form-check-input" id="attrFilter">
                                <label class="form-check-label" for="attrFilter">Filterable (listing-এ ফিল্টার)</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" wire:model="is_active" class="form-check-input" id="attrActive">
                                <label class="form-check-label" for="attrActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary me-2">{{ $isEdit ? 'Update' : 'Save' }}</button>
                            <button type="button" class="btn btn-outline-secondary" wire:click="$set('showForm', false)">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Attribute & Value List --}}
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h5 class="mb-0">Attribute & Attribute Value List</h5>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <div class="flex-grow-1" style="min-width: 150px;">
                                <input type="text" class="form-control" wire:model.live="search" placeholder="Search attributes...">
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-primary d-flex align-items-center" wire:click="showAddForm" style="white-space: nowrap;">
                                    <i class="bx bx-plus me-1"></i> Add New
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Attribute</th>
                            <th>Type / Usage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($attributeList as $key => $attribute)
                        <tr>
                            <td>{{ $loop->iteration }}</td> <!-- SL Column -->
                            <td><strong>{{ $attribute->name }}</strong><br><small class="text-muted">{{ $attribute->slug }}</small></td>
                            <td>
                                <span class="badge bg-info">{{ $attribute->type }}</span>
                                <small class="d-block text-muted">{{ $attribute->usageCount() }} variants ব্যবহার করে</small>
                            </td>
                            <td>
                                <button class="btn btn-sm {{ $attribute->is_active ? 'btn-success' : 'btn-secondary' }}"
                                    wire:click="toggleAttributeActive({{ $attribute->id }})"
                                    title="Active/Deactive">
                                    {{ $attribute->is_active ? 'ON' : 'OFF' }}
                                </button>
                            </td>
                            <td class="d-flex align-items-center gap-1">
                                <button class="btn btn-sm btn-primary" wire:click="showEditForm({{ $attribute->id }})">
                                    <i class="bx bx-edit"></i>
                                </button>

                                <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $attribute->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <button class="btn btn-sm btn-success" wire:click="toggleValueForm({{ $attribute->id }})">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Attribute Values --}}
                        @foreach($attribute->values as $value)
                        <tr>
                            <td class="ps-5"></td> <!-- Blank SL for child row -->
                            <td class="ps-5">
                                <i class="bx bx-subdirectory-right me-2"></i>{{ $value->value }}
                                @if($value->color_hex)
                                <span class="d-inline-block rounded-circle border ms-1" style="width:14px;height:14px;background:{{ $value->color_hex }};vertical-align:-2px;"></span>
                                @endif
                                @if($value->swatch_image)
                                <img src="{{ asset('storage/' . $value->swatch_image) }}" width="22" height="22" class="rounded ms-1">
                                @endif
                                <small class="text-muted">({{ $value->usageCount() }} used)</small>
                            </td>
                            <td colspan="2"></td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-outline-primary me-1" wire:click="editValue({{ $value->id }})">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        wire:confirm="Are you sure you want to delete this value?"
                                        wire:click="deleteValue({{ $value->id }})">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        {{-- Conditional Value Input Form --}}
                        @if($showValueFormForAttributeId === $attribute->id)
                        <tr>
                            <td class="ps-5">—</td>
                            <td class="ps-5" colspan="3">
                                <div class="d-flex flex-wrap gap-2 align-items-end">
                                    <div>
                                        <input type="text" wire:model.defer="attribute_value_name" class="form-control form-control-sm"
                                            placeholder="{{ $isValueEdit ? 'Edit value...' : 'New value name' }}" style="min-width:160px;">
                                        @error('attribute_value_name') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Color</label>
                                        <input type="color" wire:model.defer="value_color_hex" class="form-control form-control-color form-control-sm" value="#000000">
                                        @error('value_color_hex') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Swatch image</label>
                                        <input type="file" wire:model.defer="value_swatch_image" class="form-control form-control-sm">
                                        @if($existing_swatch_image)
                                        <img src="{{ asset('storage/' . $existing_swatch_image) }}" width="28" height="28" class="rounded mt-1">
                                        @endif
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Sort</label>
                                        <input type="number" min="0" wire:model.defer="value_sort_order" class="form-control form-control-sm" style="width:80px;">
                                    </div>
                                    <div>
                                        <button wire:click="saveAttributeValue({{ $attribute->id }})" class="btn btn-sm btn-success">
                                            {{ $isValueEdit ? 'Update' : 'Add' }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif

                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No attributes found.</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>