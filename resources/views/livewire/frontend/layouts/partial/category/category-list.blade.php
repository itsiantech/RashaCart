<ul class="categories-list">
    @foreach ($categories->take(6) as $category)
        <li class="{{ $category->allChildren->count() ? 'cat-submenu' : '' }}">
            <a wire:navigate href="{{ route('frontend.category.view', $category->slug) }}">
                @if($category->image)
                    <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" width="20" height="20" style="object-fit:cover; margin-right:5px;">
                @endif
                {{ $category->name }}

                @if ($category->allChildren->count())
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                @endif
            </a>

            @if ($category->allChildren->count())
                <ul class="submenu">
                    @include('livewire.frontend.layouts.partial.category.category-list', ['categories' => $category->allChildren])
                </ul>
            @endif
        </li>
    @endforeach
    @if ($categories->count() > 6)    
        <li><a wire:navigate href="{{ route('frontend.category.view') }}" ><i class="fas fa-ellipsis-h"></i> View All Categories</a></li>
    @endif
</ul>



