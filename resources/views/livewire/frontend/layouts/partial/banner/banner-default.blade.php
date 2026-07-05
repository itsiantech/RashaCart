<div class="">
    <div class="row">
        <div class="col-3" id="filter-sidebar">

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
        <div class="col-12" id="hero-slider">

            <div class="hero-slider">
                <div class="slider">
                    <div class="slides">
                        @foreach ($banners as $banner)
                        <div class="slide">
                            <img src="{{ $banner->image ? asset('storage/' . $banner->image) : 'https://placehold.co/1000x500' }}"
                                alt="Banner"
                                loading="lazy">
                        </div>
                        @endforeach
                    </div>
                    <!-- <button class="prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
                    <button class="next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button> -->
                </div>
            </div>

        </div>
    </div>

</div>