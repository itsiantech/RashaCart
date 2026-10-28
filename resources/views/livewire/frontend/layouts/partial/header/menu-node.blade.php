@php $hasChildren = ! empty($item['children']); @endphp
<li class="sf-menu-item{{ $hasChildren ? ' has-children' : '' }}">
    <a wire:navigate href="{{ $item['url'] }}">
        <span>{{ $item['label'] }}</span>
        @if ($hasChildren)
            <i class="fas {{ $depth === 0 ? 'fa-chevron-down' : 'fa-chevron-right' }} sf-caret" aria-hidden="true"></i>
        @endif
    </a>
    @if ($hasChildren)
        <ul class="sf-submenu sf-depth-{{ $depth + 1 }}">
            @foreach ($item['children'] as $child)
                @include('livewire.frontend.layouts.partial.header.menu-node', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
