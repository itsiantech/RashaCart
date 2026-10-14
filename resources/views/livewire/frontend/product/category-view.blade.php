<section class="hero-section">
    <div class="container">
        <div class="catview-titlebar">All Categories</div>

        <div class="products-grid-container" style="width:100%">
            <div class="catview-grid">
                @forelse($categories as $category)
                <a wire:navigate href="{{ route('frontend.category.products.view', $category->slug) }}"
                    class="catview-card">
                    <span class="catview-ic">
                        @if ($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}"
                            alt="{{ $category->name }}" loading="lazy">
                        @else
                        <i class="fas fa-tag"></i>
                        @endif
                    </span>
                    <span class="catview-name">{{ $category->name }}</span>
                </a>
                @empty
                <div class="empty-products">
                    <div class="cart-empty">
                        <i class="fas fa-shopping-basket"></i>
                        <p>No categories found.</p>
                        <p>We couldn’t find any categories matching your criteria. Please adjust your filters or try a different search.</p>
                        <a wire:navigate href="{{ route('home')}}" class="btn-continue-shopping">Continue Shopping</a>
                    </div>
                </div>
                @endforelse
            </div>
            <style>
                .catview-titlebar {
                    background: #12225B; color: #fff; text-align: center;
                    font-size: 1.05rem; font-weight: 700; padding: 12px;
                    border-radius: 10px; margin: 16px 0;
                }
                .catview-grid {
                    display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;
                }
                @media (max-width: 768px) {
                    .catview-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
                }
                .catview-card {
                    background: #fff; border: 1px solid #eef0f4; border-radius: 12px;
                    padding: 22px 10px 18px; text-decoration: none;
                    display: flex; flex-direction: column; align-items: center; gap: 10px;
                    transition: box-shadow .15s, transform .15s;
                }
                .catview-card:hover { box-shadow: 0 8px 22px rgba(18, 34, 91, .10); transform: translateY(-2px); }
                .catview-ic {
                    width: 58px; height: 58px; border-radius: 50%;
                    background: #f1f3f9; color: #12225B;
                    display: inline-flex; align-items: center; justify-content: center;
                    font-size: 1.3rem; overflow: hidden; flex-shrink: 0;
                }
                .catview-ic img { width: 100%; height: 100%; object-fit: cover; }
                .catview-name { font-size: .83rem; font-weight: 500; color: #333a45; text-align: center; line-height: 1.35; }
                .catview-card:hover .catview-name { color: #7A0000; }
            </style>


                <div class="pagination-container">
                    {{ $categories->links() }}
                </div>

            </div>
        </div>
    </div>
</section>
