@php $hasChildren = ! empty($item['children']); @endphp
<li class="hp-menu-item{{ $hasChildren ? ' has-sub' : '' }}">
    <a class="hp-menu-link" href="{{ $item['url'] }}">
        <span>{{ $item['label'] }}</span>
        @if ($hasChildren)
            <i class="fas {{ $depth === 0 ? 'fa-chevron-down' : 'fa-chevron-right' }} hp-menu-caret" aria-hidden="true"></i>
        @endif
    </a>
    @if ($hasChildren)
        <ul class="hp-menu-drop hp-depth-{{ $depth + 1 }}">
            @foreach ($item['children'] as $child)
                @include('livewire.frontend.layouts.partial.header.homepage-menu-node', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
