@once
<style>
    /* Flyout for legacy recursive category lists (desktop hover, keyboard focus, mobile tap-to-open) */
    .cat-submenu { position: relative; }
    .cat-submenu > .submenu {
        display: none; position: absolute; left: 100%; top: 0; z-index: 60;
        min-width: 210px; background: #fff; border: 1px solid #e5e7eb; border-radius: 0 10px 10px 0;
        box-shadow: 8px 8px 24px rgba(0, 0, 0, 0.10); padding: 6px; list-style: none; margin: 0;
    }
    .cat-submenu:hover > .submenu,
    .cat-submenu:focus-within > .submenu,
    .cat-submenu.open > .submenu { display: block; }
    .cat-submenu > .submenu a { display: flex; align-items: center; gap: 6px; padding: .45rem .6rem; font-size: .82rem; color: #4b5563; text-decoration: none; border-radius: 6px; }
    .cat-submenu > .submenu a:hover { background: #f7f8fb; color: #7A0000; }
    .cat-submenu > a .arrow { cursor: pointer; padding: 4px 6px; margin: -4px -6px; }
</style>
@endonce
@foreach ($categories as $category)
<li class="{{ $category->allChildren->count() ? 'cat-submenu' : '' }}">

    <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}">
        @if($category->image)
        <img src="{{ asset('storage/'.$category->image) }}" width="20">
        @endif

        {{ $category->name }}

        @if ($category->allChildren->count())
        <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        @endif
    </a>

    @if ($category->allChildren->count())
    <ul class="submenu">
        @include('livewire.frontend.layouts.partial.category.subcategory-list', [
            'categories' => $category->allChildren
        ])
    </ul>
    @endif

</li>
@endforeach
