<div class="container">
     <section class="categories-section">
            <div class="section-header">
                <h2>Shop by Brands</h2>
                <a wire:navigate href="{{ route('frontend.brand.view') }}" class="view-all">
                    View All Brands <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="categories-grid">
                @foreach ($featuredBrands as $brand)
                    <a wire:navigate href="{{ route('frontend.brand.view', $brand->slug) }}"
                        class="category-card">
                        <div class="category-image">
                            <img src="{{ $brand->logo ? asset('storage/' . $brand->logo) : 'https://placehold.co/500x500' }}"
                                alt="{{ $brand->name }}" loading="lazy">
                        </div>
                        <h3 class="category-name">{{ $brand->name }}</h3>
                    </a>
                @endforeach
            </div>
        </section>
</div>