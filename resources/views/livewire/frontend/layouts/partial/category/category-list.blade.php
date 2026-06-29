@php
$categories = $categories ?? collect();
$brands = $brands ?? collect();
@endphp
<section class="category-list">
    <ul class="categories-list">
        <h3 class="filter-title"><i class="fas fa-microchip"></i> Category</h3>
        @foreach ($categories->take(6) as $category)
        <li class="{{ $category->allChildren->count() ? 'cat-submenu' : '' }}">
            <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}">
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
                @include('livewire.frontend.layouts.partial.category.subcategory-list', ['categories' => $category->allChildren])
            </ul>
            @endif
        </li>
        @endforeach
        @if ($categories->count() > 6)
        <li><a wire:navigate href="{{ route('frontend.category.view') }}"><i class="fas fa-ellipsis-h"></i> View All Categories</a></li>
        @endif
    </ul>

    <ul class="categories-list">
        <h3 class="filter-title"><i class="fas fa-microchip"></i> Brand</h3>
        @foreach ($brands->take(6) as $brand)
        <li>
            <a wire:navigate href="{{ route('frontend.brands.products.view', $brand->slug) }}">

                @if($brand->logo)
                <img
                    src="{{ asset('storage/'.$brand->logo) }}"
                    alt="{{ $brand->name }}"
                    width="20"
                    height="20"
                    style="object-fit:cover; margin-right:5px;">
                @endif

                {{ $brand->name }}
            </a>
        </li>
        @endforeach

        @if ($brands->count() > 6)
        <li>
            <a wire:navigate href="{{ route('frontend.brand.view') }}">
                <i class="fas fa-ellipsis-h"></i> View All Brands
            </a>
        </li>
        @endif
    </ul>
</section>