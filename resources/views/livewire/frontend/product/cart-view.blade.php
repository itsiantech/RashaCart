<div>
     <div class="container">
        <h1 class="cart-title">আমার কার্ট</h1>

        <div class="cart-grid">
            <!-- Left: items list -->
            <section class="cart-left">
                <div class="cart-table">                  

                    <div class="ct-body P-3">
                        <livewire:frontend.components.cart-items :cartItems="$cartItems" />
                    </div>


                </div>
            </section>

            <!-- Right: summary -->
            <aside class="cart-right">
                <div class="cart-summary">
                    <div class="cs-title">অর্ডার সারাংশ</div>
                    <div class="cs-lines">
                        <div class="line"><span>সাব টোটাল</span><strong>{{setting('currency')}}  {{ number_format($subtotal) }}</strong>
                        </div>
                        <div class="line"><span> ডিসকাউন্ট</span><strong>-{{setting('currency')}}  {{ number_format($discount) }}</strong>
                        </div>
                        <div class="line total"><span>সর্বমোট</span><strong>{{setting('currency')}}  {{ number_format($total) }}</strong>
                        </div>
                    </div>
                    <a wire:navigate href="{{route('frontend.checkout')}}" class="cs-checkout">চেকআউট</a>
                </div>
            </aside>
        </div>

        <!-- You May Like -->
        <section class="cart-reco">
            <h2 class="reco-title">You May Like</h2>
            <div class="products-grid">
                @foreach ($mayLikeProducts as $product)
                    @include('livewire.frontend.layouts.partial.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    </div>

</div>