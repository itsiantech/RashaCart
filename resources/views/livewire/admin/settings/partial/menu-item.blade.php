<li class="mn-item" data-menu-item data-id="{{ $row['id'] }}" wire:key="mn-item-{{ $row['id'] }}">
    <div class="mn-row {{ $row['missing'] ? 'mn-row-missing' : '' }}">
        <span class="mn-drag" title="Drag to reorder"><i class="bx bx-grid-vertical"></i></span>
        <i class="bx {{ $row['type'] === 'page' ? 'bx-file' : 'bx-category' }} mn-type-icon"></i>
        <div class="mn-label">
            <span class="mn-name">{{ $row['label'] }}</span>
            <span class="mn-url {{ $row['missing'] ? 'is-missing' : '' }}">
                {{ $row['missing'] ? 'Missing or inactive — hidden on the storefront' : $row['url'] }}
            </span>
        </div>
        <span class="badge bg-label-{{ $row['type'] === 'page' ? 'info' : 'warning' }}">{{ ucfirst($row['type']) }}</span>
        <button type="button" class="mn-remove" wire:click="removeItem('{{ $row['id'] }}')" wire:confirm="Remove this item from the menu?" title="Remove">
            <i class="bx bx-trash"></i>
        </button>
    </div>

    @if ($row['depth'] < 2)
        <ul class="mn-list mn-children{{ empty($row['children']) ? ' is-empty' : '' }}" data-menu-list data-parent="{{ $row['id'] }}"
            data-label="{{ $row['depth'] === 0 ? 'sub menu' : 'sub sub menu' }}"
            wire:key="mn-list-{{ $row['id'] }}">
            @foreach ($row['children'] as $child)
                @include('livewire.admin.settings.partial.menu-item', ['row' => $child])
            @endforeach
        </ul>
    @endif
</li>
