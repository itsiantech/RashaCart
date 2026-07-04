@foreach ($categories as $category)
<li class="{{ $category->allChildren->count() ? 'cat-submenu' : '' }}">

    <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}">
        @if($category->image)
        <img src="{{ asset('storage/'.$category->image) }}" width="20" onerror="this.style.display='none'">
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
