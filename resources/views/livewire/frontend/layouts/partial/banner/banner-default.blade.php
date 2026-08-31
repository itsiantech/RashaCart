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
                <div class="slider" id="heroSliderTrack">
                    <div class="slides">
                        @foreach ($banners as $banner)
                        <div class="slide">
                            <img src="{{ $banner->image ? asset('storage/' . $banner->image) : 'https://placehold.co/1000x500' }}"
                                alt="Banner"
                                loading="lazy">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dots -->
                <div class="hero-dots" id="heroDots"></div>
            </div>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('heroSliderTrack');
        const slidesWrap = track?.querySelector('.slides');
        const dotsWrap = document.getElementById('heroDots');
        if (!track || !slidesWrap) return;

        const slides = Array.from(slidesWrap.children);
        if (slides.length <= 1) return;

        let activeIndex = 0;
        let autoTimer = null;
        let resumeTimer = null;
        const AUTO_DELAY = 4000; // 4s per slide
        const RESUME_DELAY = 6000; // resume auto-slide 6s after manual scroll

        // Build dots
        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'hero-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            dot.addEventListener('click', () => goToSlide(i));
            dotsWrap.appendChild(dot);
        });
        const dots = Array.from(dotsWrap.children);

        function updateDots(index) {
            dots.forEach((d, i) => d.classList.toggle('active', i === index));
        }

        function goToSlide(index, behavior = 'smooth') {
            activeIndex = index;
            slidesWrap.scrollTo({ left: index * track.clientWidth, behavior });
            updateDots(index);
        }

        function startAutoSlide() {
            clearInterval(autoTimer);
            autoTimer = setInterval(() => {
                const next = (activeIndex + 1) % slides.length;
                goToSlide(next);
            }, AUTO_DELAY);
        }

        function pauseAutoSlide() {
            clearInterval(autoTimer);
            clearTimeout(resumeTimer);
            resumeTimer = setTimeout(startAutoSlide, RESUME_DELAY);
        }

        // Detect manual scroll -> update active dot + pause auto-slide
        let scrollDebounce = null;
        slidesWrap.addEventListener('scroll', () => {
            pauseAutoSlide();
            clearTimeout(scrollDebounce);
            scrollDebounce = setTimeout(() => {
                const index = Math.round(slidesWrap.scrollLeft / track.clientWidth);
                activeIndex = index;
                updateDots(index);
            }, 80);
        });

        startAutoSlide();
    });
</script>

<style>
    #hero-slider {
    padding: 5px 15px;
    box-sizing: border-box;
}

.hero-slider {
    position: relative;
    width: 100%;
}

.slider {
    width: 100%;
    overflow: hidden;
    border-radius: 8px;
}

.slides {
    display: flex;
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.slides::-webkit-scrollbar {
    display: none;
}

.slide {
    flex: 0 0 100%;
    width: 100%;
    scroll-snap-align: center;
    scroll-snap-stop: always;
}

.slide img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    border-radius: 8px;
}

.hero-dots {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 10;
}

.hero-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    border: none;
    padding: 0;
    cursor: pointer;
    transition: all 0.25s ease;
}

.hero-dot.active {
    width: 22px;
    border-radius: 4px;
    background: orangered;
}
</style>