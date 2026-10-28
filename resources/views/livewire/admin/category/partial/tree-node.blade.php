@php $hasChildren = count($row['children']) > 0; @endphp

<div class="cat-node cat-depth-{{ $row['depth'] }}" wire:key="cat-{{ $row['id'] }}"
    data-id="{{ $row['id'] }}" data-parent="{{ $row['parent_id'] ?? '' }}">
    <div class="cat-item">
        <div class="cat-cell">
            <div class="d-flex align-items-center gap-2" style="padding-left: {{ $row['depth'] * 26 }}px;">
                @if($sortable)
                <span class="cat-drag" title="Drag to reorder"><i class='bx bx-menu'></i></span>
                @endif
                <span class="cat-sign">{{ $row['depth'] ? str_repeat('- ', $row['depth']) : '' }}</span>
                @if($row['image'])
                <img src="{{ asset('storage/'.$row['image']) }}" alt="{{ $row['name'] }}" class="cat-thumb">
                @else
                <span class="cat-thumb-placeholder"><i class='bx bx-image'></i></span>
                @endif
                <span class="cat-name">{{ $row['name'] }}</span>
                @if(!empty($row['parent_name']))
                <small class="text-muted">({{ $row['parent_name'] }})</small>
                @endif
                @if($row['is_featured'])
                <i class='bx bxs-star cat-featured' title="Featured on homepage"></i>
                @endif
            </div>
        </div>
        <div class="cat-cell"><span class="text-muted small">{{ $row['slug'] }}</span></div>
        <div class="cat-cell text-center"><span class="cat-products-badge">{{ $row['products'] }}</span></div>
        <div class="cat-cell">
            <button type="button" class="cat-status-pill {{ $row['status'] ? 'cat-active' : 'cat-inactive' }}"
                wire:click="toggleStatus({{ $row['id'] }})"
                title="Click to {{ $row['status'] ? 'deactivate' : 'activate' }}">
                <span class="dot"></span> {{ $row['status'] ? 'Active' : 'Inactive' }}
            </button>
        </div>
        <div class="cat-cell text-end">
            <button class="btn btn-sm btn-icon btn-label-primary me-1"
                wire:click="showEditForm({{ $row['id'] }})" title="Edit">
                <i class="bx bx-edit-alt"></i>
            </button>
            <button class="btn btn-sm btn-icon btn-label-danger"
                wire:click="confirmDelete({{ $row['id'] }})" title="Delete">
                <i class="bx bx-trash"></i>
            </button>
        </div>
    </div>

    @if($hasChildren)
    <div class="cat-children" wire:key="kids-{{ $row['id'] }}" data-parent="{{ $row['id'] }}"
        @if($sortable) data-sortable="1" @endif>
        @foreach($row['children'] as $child)
        @include('livewire.admin.category.partial.tree-node', ['row' => $child, 'sortable' => $sortable])
        @endforeach
    </div>
    @endif
</div>
