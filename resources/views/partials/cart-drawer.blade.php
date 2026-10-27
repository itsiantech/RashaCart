@php
    $cdEnabled = \App\Services\PurchaseActions::cartDrawerEnabled();
    $cdHidden = request()->routeIs('frontend.cart.view', 'frontend.checkout', 'frontend.order.success');
    $cdCartItems = session('cart', []);
    $cdCartItems = is_array($cdCartItems) ? $cdCartItems : [];
    $cdCount = array_sum(array_column($cdCartItems, 'qty'));
    $cdTotal = array_sum(array_map(
        fn ($item) => ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 1)),
        $cdCartItems
    ));
@endphp

@if ($cdEnabled && ! $cdHidden)
<div class="cd-root {{ $cdCount < 1 ? 'cd-empty' : '' }}" data-cd-root
    @if (session('open_cart_drawer')) data-cd-autoopen="1" @endif>
    <button type="button" class="cd-fab" data-cd-open-btn aria-label="Open cart" aria-controls="cdPanel"
        aria-expanded="false">
        <i class="fas fa-shopping-bag"></i>
        <span class="cd-fab-count" data-cd-count @if ($cdCount < 1) hidden @endif>{{ $cdCount }}</span>
    </button>

    <div class="cd-overlay" data-cd-overlay></div>

    <aside id="cdPanel" class="cd-panel" role="dialog" aria-modal="true" aria-labelledby="cdPanelTitle"
        aria-hidden="true">
        <header class="cd-head">
            <h4 id="cdPanelTitle">
                <span class="cd-head-icon"><i class="fas fa-shopping-bag"></i></span>
                Your Cart
                <span class="cd-head-count" data-cd-count>{{ $cdCount }}</span>
            </h4>
            <button type="button" class="cd-close" data-cd-close aria-label="Close cart">&times;</button>
        </header>

        <div class="cd-body">
            <livewire:frontend.components.cart-items :cartItems="$cdCartItems" />
        </div>

        <div class="cd-foot" data-cd-foot @if ($cdCount < 1) hidden @endif>
            <div class="cd-subtotal">
                <span>Subtotal</span>
                <strong>{{ setting('currency') }} <span data-cd-subtotal>{{ number_format($cdTotal) }}</span></strong>
            </div>
            <div class="cd-foot-note">Shipping &amp; taxes calculated at checkout</div>
            <a href="{{ route('frontend.checkout') }}" class="cd-checkout" wire:navigate>
                CHECKOUT NOW <i class="fas fa-arrow-right"></i>
            </a>
            <a href="{{ route('frontend.cart.view') }}" class="cd-view-cart" wire:navigate>View full cart</a>
        </div>
    </aside>
</div>

<style>
    .cd-root [hidden] { display: none !important; }

    .cd-fab {
        position: fixed;
        right: 18px;
        bottom: 24px;
        z-index: 9970;
        width: 56px;
        height: 56px;
        border: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--btn-all-bg, #f4511e);
        color: var(--btn-all-text, #fff);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .22);
        cursor: pointer;
        transition: transform .2s ease, opacity .2s ease;
    }

    .cd-fab:hover { transform: translateY(-2px); }
    .cd-fab i {
        font-size: 1.25rem;
        color: var(--cart-btn-icon, var(--icon-color, var(--btn-all-text, #fff)));
    }

    .cd-fab-count {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 21px;
        height: 21px;
        padding: 0 5px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--hd-badge, #ffc107);
        color: #111;
        font-size: 11px;
        font-weight: 800;
        line-height: 1;
    }

    .cd-overlay {
        position: fixed;
        inset: 0;
        z-index: 9980;
        background: rgba(15, 23, 42, .5);
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s ease, visibility 0s linear .25s;
    }

    .cd-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 9990;
        width: min(420px, 100%);
        display: flex;
        flex-direction: column;
        background: #fff;
        box-shadow: -12px 0 40px rgba(0, 0, 0, .18);
        transform: translateX(102%);
        visibility: hidden;
        transition: transform .3s cubic-bezier(.22, .61, .36, 1), visibility 0s linear .3s;
    }

    .cd-root.cd-open .cd-overlay {
        opacity: 1;
        visibility: visible;
        transition: opacity .25s ease;
    }

    .cd-root.cd-open .cd-panel {
        transform: translateX(0);
        visibility: visible;
        transition: transform .3s cubic-bezier(.22, .61, .36, 1);
    }

    .cd-root.cd-open .cd-fab { opacity: 0; pointer-events: none; }

    .cd-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #eef0f3;
        background: #fff;
    }

    .cd-head h4 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 1.02rem;
        font-weight: 700;
        color: #111827;
    }

    .cd-head-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--btn-all-bg, #f4511e);
    }

    .cd-head-icon i {
        font-size: .95rem;
        color: var(--cart-btn-icon, var(--icon-color, var(--btn-all-text, #fff)));
    }

    .cd-head-count {
        min-width: 22px;
        height: 22px;
        padding: 0 7px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #374151;
        font-size: 11px;
        font-weight: 700;
    }

    .cd-close {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 50%;
        background: #f3f4f6;
        color: #374151;
        font-size: 1.45rem;
        line-height: 1;
        cursor: pointer;
    }

    .cd-close:hover { background: #e5e7eb; }

    .cd-body {
        flex: 1;
        overflow-y: auto;
        padding: 14px 16px;
        background: #fafbfc;
        -webkit-overflow-scrolling: touch;
    }

    .cd-body .cart-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cd-body .cart-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        background: #fff;
    }

    .cd-body .col-product {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        width: 100%;
        min-width: 0;
    }

    .cd-body .col-product > img {
        flex: 0 0 68px;
        width: 68px;
        height: 68px;
        object-fit: cover;
        border: 1px solid #eef0f3;
        border-radius: 10px;
        background: #fff;
    }

    .cd-body .product-details { flex: 1; min-width: 0; }

    .cd-body .ci-title {
        margin-bottom: 4px;
        font-size: .86rem;
        font-weight: 600;
        line-height: 1.35;
        color: #111827;
    }

    .cd-body .ci-variant {
        font-size: .72rem !important;
        color: #6b7280 !important;
        margin-bottom: 4px !important;
    }

    .cd-body .ci-variant strong,
    .cd-body .ci-variant span { font-size: .72rem; }

    .cd-body .ci-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px 10px;
        margin-top: 8px;
    }

    .cd-body .quantity-controls {
        display: inline-flex;
        align-items: center;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        background: #fff;
    }

    .cd-body .quantity-controls button {
        width: 28px;
        height: 28px;
        border: 0;
        background: #f9fafb;
        color: #374151;
        font-size: .9rem;
        line-height: 1;
        cursor: pointer;
    }

    .cd-body .quantity-controls button:hover { background: #eef0f3; }

    .cd-body .quantity-controls .ci-input {
        width: 36px;
        height: 28px;
        border: 0;
        background: #fff;
        color: #111827;
        font-size: .8rem;
        font-weight: 600;
        text-align: center;
        -moz-appearance: textfield;
    }

    .cd-body .quantity-controls .ci-input::-webkit-outer-spin-button,
    .cd-body .quantity-controls .ci-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .cd-body .ci-price {
        font-size: .74rem;
        color: #6b7280;
    }

    .cd-body .ci-total {
        font-size: .8rem;
        font-weight: 700;
        color: #111827;
    }

    .cd-body .ci-remove {
        margin-left: auto;
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fee2e2;
        color: #dc2626;
        font-size: .8rem;
        cursor: pointer;
    }

    .cd-body .ci-remove:hover { background: #fecaca; }

    .cd-body .cart-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 48px 18px;
        text-align: center;
        color: #6b7280;
    }

    .cd-body .cart-empty i {
        margin-bottom: 8px;
        font-size: 2.7rem;
        color: #d1d5db;
    }

    .cd-body .cart-empty p {
        margin: 0;
        font-size: .82rem;
        line-height: 1.55;
    }

    .cd-body .cart-empty p:first-of-type {
        font-size: .95rem;
        font-weight: 700;
        color: #374151;
    }

    .cd-body .btn-continue-shopping {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 12px;
        padding: 9px 18px;
        border-radius: 8px;
        background: var(--btn-all-bg, #f4511e);
        color: var(--btn-all-text, #fff) !important;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none;
    }

    .cd-foot {
        padding: 14px 18px calc(16px + env(safe-area-inset-bottom));
        border-top: 1px solid #eef0f3;
        background: #fff;
    }

    .cd-subtotal {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .9rem;
        color: #374151;
    }

    .cd-subtotal strong { font-size: 1rem; color: #111827; }

    .cd-foot-note {
        margin: 3px 0 12px;
        font-size: .7rem;
        color: #9ca3af;
    }

    .cd-checkout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        background: var(--btn-all-bg, #f4511e);
        color: var(--btn-all-text, #fff) !important;
        font-size: .9rem;
        font-weight: 700;
        letter-spacing: .02em;
        text-decoration: none;
    }

    .cd-checkout:hover { filter: brightness(.94); }

    .cd-checkout i {
        font-size: .8rem;
        color: var(--cart-btn-icon, var(--icon-color, var(--btn-all-text, #fff)));
    }

    .cd-view-cart {
        display: block;
        margin-top: 9px;
        text-align: center;
        font-size: .78rem;
        color: #4b5563;
        text-decoration: none;
    }

    .cd-view-cart:hover { color: #111827; text-decoration: underline; }

    @media (max-width: 768px) {
        .cd-fab {
            right: 14px;
            bottom: 74px;
            width: 50px;
            height: 50px;
        }

        .cd-panel { width: 100%; }
    }
</style>

<script>
    (function () {
        var prevOverflow = null;
        var lastFocus = null;
        var pendingOpen = false;
        var pendingOpenTimer = null;

        function payload(event) {
            if (!event) return {};
            if (Array.isArray(event)) return event[0] || {};
            if (event.detail && typeof event.detail === 'object') return event.detail;
            return event;
        }

        function formatNumber(value) {
            var n = Number(value);
            if (!isFinite(n)) return '';
            return Math.round(n).toLocaleString('en-US');
        }

        function currentRoot() {
            return document.querySelector('[data-cd-root]');
        }

        function updateTotals(event) {
            var root = currentRoot();
            if (!root) return;

            var data = payload(event);
            var count = data && data.count !== undefined ? Number(data.count) : null;
            var total = data && data.total !== undefined ? Number(data.total) : null;

            if (count !== null && !isNaN(count)) {
                root.querySelectorAll('[data-cd-count]').forEach(function (el) {
                    el.textContent = count;
                });

                var fabCount = root.querySelector('.cd-fab-count');
                if (fabCount) fabCount.hidden = count <= 0;

                var foot = root.querySelector('[data-cd-foot]');
                if (foot) foot.hidden = count <= 0;

                root.classList.toggle('cd-empty', count <= 0);

                document.querySelectorAll('[data-cd-header-count]').forEach(function (el) {
                    el.textContent = count;
                });
            }

            if (total !== null && !isNaN(total)) {
                var subtotal = root.querySelector('[data-cd-subtotal]');
                if (subtotal) subtotal.textContent = formatNumber(total);
            }
        }

        function openDrawer() {
            var root = currentRoot();
            if (!root) return;

            pendingOpen = false;
            if (pendingOpenTimer) {
                clearTimeout(pendingOpenTimer);
                pendingOpenTimer = null;
            }

            var panel = root.querySelector('.cd-panel');
            var toggle = root.querySelector('[data-cd-open-btn]');
            var close = root.querySelector('[data-cd-close]');

            if (!root.classList.contains('cd-open')) {
                lastFocus = document.activeElement;
                prevOverflow = document.body.style.overflow;
            }

            root.classList.add('cd-open');
            if (panel) panel.setAttribute('aria-hidden', 'false');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';

            if (close) {
                setTimeout(function () {
                    close.focus({ preventScroll: true });
                }, 60);
            }
        }

        function closeDrawer(silent) {
            var root = currentRoot();

            if (root) {
                var panel = root.querySelector('.cd-panel');
                var toggle = root.querySelector('[data-cd-open-btn]');

                root.classList.remove('cd-open');
                if (panel) panel.setAttribute('aria-hidden', 'true');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            }

            document.body.style.overflow = prevOverflow || '';
            prevOverflow = null;

            if (!silent && lastFocus && typeof lastFocus.focus === 'function') {
                try { lastFocus.focus({ preventScroll: true }); } catch (e) {}
            }
            lastFocus = null;
        }

        function bindDrawer(root) {
            if (!root || root.dataset.cdBound === '1') return;
            root.dataset.cdBound = '1';

            var toggle = root.querySelector('[data-cd-open-btn]');
            var overlay = root.querySelector('[data-cd-overlay]');
            var close = root.querySelector('[data-cd-close]');
            var panel = root.querySelector('.cd-panel');

            if (toggle) toggle.addEventListener('click', openDrawer);
            if (overlay) overlay.addEventListener('click', function () { closeDrawer(); });
            if (close) close.addEventListener('click', function () { closeDrawer(); });

            if (panel) {
                panel.addEventListener('click', function (event) {
                    var link = event.target.closest('a[href]');
                    if (link) closeDrawer(true);
                });

                panel.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        event.stopPropagation();
                        closeDrawer();
                        return;
                    }

                    if (event.key !== 'Tab') return;

                    var focusable = panel.querySelectorAll('button, a[href], input');
                    if (!focusable.length) return;

                    var first = focusable[0];
                    var last = focusable[focusable.length - 1];

                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                });
            }

            if (root.dataset.cdAutoopen === '1') {
                setTimeout(openDrawer, 150);
            }
        }

        function setupLivewire() {
            if (!window.Livewire || window.__cdLivewireBound) return;
            window.__cdLivewireBound = true;

            Livewire.on('cart-updated', updateTotals);
            Livewire.on('cart-drawer-open', function (event) {
                updateTotals(event);

                // Wait for the nested cart-items component to finish reloading,
                // then slide in with the freshly added item already visible.
                pendingOpen = true;
                if (pendingOpenTimer) clearTimeout(pendingOpenTimer);
                pendingOpenTimer = setTimeout(openDrawer, 3000);
            });
            Livewire.on('cart-drawer-ready', function () {
                if (pendingOpen) openDrawer();
            });
        }

        function init() {
            bindDrawer(currentRoot());
            setupLivewire();
        }

        if (document.readyState !== 'loading') {
            init();
        }

        document.addEventListener('DOMContentLoaded', init);
        document.addEventListener('livewire:init', setupLivewire);
        document.addEventListener('livewire:navigated', function () {
            closeDrawer(true);
            bindDrawer(currentRoot());
        });
    })();
</script>
@endif
