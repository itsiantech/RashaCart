{{-- Dynamic style vars (Style Settings page). Falls back gracefully everywhere. --}}
@php
    $btnAllBg = \App\Services\StyleSettings::get('cart_btn_bg') ?: '#f4511e';
    $btnAllText = \App\Services\StyleSettings::get('cart_btn_text') ?: '#ffffff';
@endphp
<link href="{{ \App\Services\StyleSettings::FONT_LINK }}" rel="stylesheet" />
<style id="style-customizer-vars">
    :root {
        @foreach (\App\Services\StyleSettings::cssVars() as $k => $v){!! $k !!}: {!! $v !!};
        @endforeach
        --icon-color: {!! \App\Services\StyleSettings::get('nav_icon') !!};
        --btn-all-bg: {!! $btnAllBg !!};
        --btn-all-text: {!! $btnAllText !!};
    }
</style>
<style id="style-customizer-globals">
    /* Icon color (full website): header, nav, footer, floating cart & action buttons */
    .main-header i, .navbar-main i, .subnav i, .main-footer i, .hp-footer i, .hp-logo i,
    .cart-sidebar .cart-icon i,
    .btn-order-now i, .cart-button i, .btn-add-cart i, .pd-btn i, .pd-rel-cart i,
    .ck-submit i, .cs-checkout i, .ty-btn i, .track-btn i, .trk-btn i,
    .load-more-btn i, .product-card .btn-buy i, .product-card .btn-cart i {
        color: var(--icon-color);
    }

    /* Dedicated icon settings keep precedence */
    .hp-search .btn i,
    .search-container button i,
    .main-header .s button[type="submit"] i { color: var(--hd-search-icon, var(--icon-color)); }
    .f-social a i { color: var(--ft-social, var(--icon-color)); }
    .cart-button i, .btn-add-cart i, .pd-btn--cart i, .pd-rel-cart i,
    .ck-submit i { color: var(--cart-btn-icon, var(--icon-color)); }

    /* Icons on white bars keep their contextual color so they never turn invisible */
    .bottom-nav a i, .mobile-bottom-nav a i, .hp-see-more i, .hp-stars i,
    .product-rating .stars i { color: inherit; }

    /* All action buttons follow Style Settings → Button color */
    .cart-button, .btn-add-cart, .pd-btn--cart, .pd-rel-cart,
    .ck-submit, .cs-checkout, .btn-checkout, .ty-btn, .track-btn, .trk-btn,
    .load-more-btn, .btn-continue-shopping, .product-card .btn-buy,
    .product-card .btn-cart {
        background-color: var(--btn-all-bg) !important;
        color: var(--btn-all-text) !important;
    }
    .cart-button:hover, .btn-add-cart:hover,
    .pd-btn--cart:hover, .pd-rel-cart:hover, .ck-submit:hover, .cs-checkout:hover,
    .btn-checkout:hover, .ty-btn:hover, .track-btn:hover, .trk-btn:hover,
    .load-more-btn:hover, .btn-continue-shopping:hover, .product-card .btn-buy:hover,
    .product-card .btn-cart:hover {
        filter: brightness(.92);
    }
</style>
