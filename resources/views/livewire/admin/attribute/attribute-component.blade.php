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
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($attributeList as $key => $attribute)
                        <tr>
                            <td>{{ $loop->iteration }}</td> <!-- SL Column -->
                            <td><strong>{{ $attribute->name }}</strong></td>
                            <td>{{ $attribute->slug }}</td>
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
                            </td>
                            <td colspan="1"></td>
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
                            <td class="ps-5" colspan="2">
                                <input type="text" wire:model.defer="attribute_value_name" class="form-control"
                                    placeholder="{{ $isValueEdit ? 'Edit value...' : 'New value name' }}">
                                @error('attribute_value_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <button wire:click="saveAttributeValue({{ $attribute->id }})" class="btn btn-sm btn-success">
                                    {{ $isValueEdit ? 'Update' : 'Add' }}
                                </button>
                            </td>
                        </tr>
                        @endif

                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No attributes found.</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>