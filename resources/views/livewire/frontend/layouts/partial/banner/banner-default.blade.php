<div class="container">
    <div class="row">
        <div class="col-3" id="filter-sidebar">

            <div class="categories-sidebar">
                <div class="categories-header">
                    <h3><i class="fas fa-list"></i> Top Categories</h3>
                </div>
                @include('livewire.frontend.layouts.partial.category.category-list', [
                    'categories' => $categories->where('parent_id', null),
                ])
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('.cat-submenu > a').forEach(item => {
                        item.addEventListener('click', e => {
                            e.preventDefault();
                            const parent = item.parentElement;
                            parent.classList.toggle('open');
                        });
                    });
                });
            </script>

        </div>
        <div class="col-9" id="hero-slider">

            <div class="hero-slider">
                <div class="slider">
                    <div class="slides">
                        @foreach ($banners as $banner)
                            <div class="slide">
                                <img src="{{ $banner->image ? asset('storage/' . $banner->image) : 'https://placehold.co/1000x500' }}"
                                    alt="Banner 1" loading="lazy">
                            </div>
                        @endforeach
                    </div>
                    <button class="prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
                    <button class="next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

        </div>
    </div>

</div>
