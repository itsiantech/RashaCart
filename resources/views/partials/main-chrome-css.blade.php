{{-- Shared storefront chrome CSS (copied verbatim from homepage so every page matches).
     Homepage keeps its own inline copy untouched (zero regression risk). --}}
@once
<style>
    :root {
        --primary: #7A0000;
        --primary-dark: #5a0000;
        --navy: #12225B;
        --accent: #f5a623;
        --red: #dc2626;
        --orange: #f97316;
        --line: #e5e7eb;
    }

    body.hp-store { font-family: var(--body-font, 'Segoe UI', sans-serif); background: #F3F4F6; }

    /* ── Layout container (max-w-7xl mx-auto px-4) ── */
    .hp-container { max-width: 1280px; margin: 0 auto; padding: 0 16px; width: 100%; }

    /* ── PLP filter sidebar theme (navy header, #F3F4F6 borders, no title icons) ── */
    .categories-category-sidebar { background: #fff; border: 1px solid #F3F4F6; border-radius: 10px; overflow: hidden; }
    .categories-category-sidebar .categories-header { background-color: #12225B; color: #fff; padding: 12px 16px; }
    .categories-category-sidebar .categories-header h3 { color: #fff; margin: 0; font-size: 1rem; }
    .categories-category-sidebar .filter-card { border-bottom: 1px solid #F3F4F6; }
    .categories-category-sidebar .filter-card:last-child { border-bottom: none; }

    /* ── Navbar ── */
    .navbar-main { background: var(--hd-bg, #12225B); }
    .hp-navbar-row { display: flex; align-items: center; gap: 18px; height: 62px; }
    .hp-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
    .hp-logo img { height: 40px; width: auto; object-fit: contain; }
    .hp-logo i { color: #fff; font-size: 1.4rem; }
    .hp-logo span { color: #fff; font-weight: 700; font-size: 1.3rem; }
    .hp-search { flex: 1; display: flex; }
    .hp-search input.form-control { border: none; border-radius: 4px 0 0 4px; font-size: .9rem; background: var(--hd-search-bg, #fff); }
    .hp-search .btn { background: var(--hd-search-btn, #f5a623); border: none; color: var(--hd-search-icon, #fff); border-radius: 0 4px 4px 0; padding: 0 18px; }
    .hp-nav-icons { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
    .nav-icon { color: var(--hd-icon, rgba(255,255,255,.85)); font-size: 1.1rem; position: relative; }
    .nav-icon:hover { color: var(--hd-icon, #fff); }
    .nav-icon .badge { font-size: .6rem; position: absolute; top: -6px; right: -8px; }
    .nav-icon .badge.bg-warning { background-color: var(--hd-badge, #ffc107) !important; }

    /* Desktop logo size; mobile keeps the compact default above. */
    @media (min-width: 768px) {
        .hp-navbar-row { height: 120px; }
        .hp-logo img { height: 120px; width: 230px; }
    }

    /* ── Add to Cart buttons (dynamic; unset = each context keeps its look) ── */
    .product-card .cart-button {
        background: var(--cart-btn-bg, rgb(241, 69, 6));
        color: var(--cart-btn-text, white);
    }
    .product-card .cart-button i { color: var(--cart-btn-icon, var(--icon-color)); }

    /* ── Sub Nav ── */
    .subnav { background: var(--hd-bg, #12225B); border-top: 1px solid rgba(255,255,255,.12); }
    .subnav .nav-link { color: var(--hd-nav, rgba(255,255,255,.85)) !important; font-size: .85rem; padding: .4rem .75rem; border-radius: 6px; }
    .subnav .nav-link:hover { color: #fff !important; background: var(--hd-nav-hover, transparent); }
    .subnav .btn-sell { background: var(--hd-search-btn, #f5a623); color: #fff; font-size: .8rem; border-radius: 20px; padding: .25rem .85rem; }

    /* ── Footer ── */
    .hp-footer { background: var(--ft-bg, #12225B); color: var(--ft-text, #fff); margin-top: 32px; padding: 40px 0 18px; }
    .f-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
    @media(min-width: 768px) { .f-grid { grid-template-columns: repeat(3, 1fr); } }
    @media(min-width: 992px) { .f-grid { grid-template-columns: 1.35fr 1fr 1fr 1fr .95fr; } }
    .hp-footer h6 { color: var(--ft-text, #fff); font-weight: 600; font-size: .92rem; margin-bottom: .8rem; }
    .hp-footer a { color: var(--ft-nav, rgba(255,255,255,.75)); text-decoration: none; font-size: .85rem; }
    .hp-footer a:hover { color: var(--ft-nav, #fff); }
    .hp-footer ul { list-style: none; padding: 0; margin: 0; }
    .hp-footer ul li { margin-bottom: .4rem; }
    .f-contact p { font-size: .82rem; color: var(--ft-text, rgba(255,255,255,.75)); margin: 0; line-height: 1.9; }
    .f-social { display: flex; gap: 10px; }
    .f-social a { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.12); display: inline-flex; align-items: center; justify-content: center; color: var(--ft-social, #fff); }
    .f-social a:hover { background: var(--accent); }
    .f-app-btn { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); border-radius: 8px; padding: 7px 12px; margin-bottom: 8px; max-width: 190px; }
    .f-app-btn i { font-size: 1.3rem; color: #fff; }
    .f-app-btn small { display: block; font-size: .6rem; color: rgba(255,255,255,.65); line-height: 1.1; }
    .f-app-btn strong { font-size: .78rem; color: #fff; line-height: 1.1; }
    .f-bottom { display: flex; flex-direction: column; align-items: center; gap: 8px; border-top: 1px solid rgba(255,255,255,.15); margin-top: 26px; padding-top: 16px; font-size: .82rem; color: rgba(255,255,255,.6); }
    @media(min-width: 768px) { .f-bottom { flex-direction: row; justify-content: space-between; } }

    /* ── Mobile bottom nav ── */
    .mobile-bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: 0 -2px 10px rgba(0,0,0,.1); z-index: 1000; padding: .5rem 0; }
    @media(max-width:768px) { .mobile-bottom-nav { display: flex; justify-content: space-around; } }
    .mobile-bottom-nav a { color: #555; font-size: .7rem; text-align: center; flex: 1; text-decoration: none; }
    .mobile-bottom-nav a i { font-size: 1.2rem; display: block; }
    .mobile-bottom-nav a.active { color: var(--primary); }
    @media(max-width:768px) { body.hp-store { padding-bottom: 60px; } }
</style>
@endonce
