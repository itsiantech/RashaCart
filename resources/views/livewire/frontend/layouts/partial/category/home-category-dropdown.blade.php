{{-- Header "Categories" button + overlay panel. Rendered only when home_category_sidebar is OFF. --}}
@once
<style>
    .hp-catnav { position: relative; display: inline-flex; align-items: center; flex: 0 0 auto; }

    .hp-catnav-btn {
        display: inline-flex; align-items: center; gap: 7px;
        height: 30px; padding: 0 .8rem;
        border: 0; border-radius: 8px;
        background: var(--primary, #7A0000); color: #fff;
        font-family: inherit; font-size: .82rem; font-weight: 600; line-height: 1;
        cursor: pointer; white-space: nowrap;
        transition: background .18s, box-shadow .18s, transform .18s;
    }
    .hp-catnav-btn:hover { background: #8f0d0d; }
    .hp-catnav-btn:active,
    .hp-catnav-btn.is-open { background: var(--primary-dark, #5a0000); box-shadow: inset 0 2px 6px rgba(0, 0, 0, .35); }
    .hp-catnav-btn:focus-visible { outline: 2px solid #ffd479; outline-offset: 2px; }
    .hp-catnav-bars { font-size: .78rem; }
    .hp-catnav-chev { font-size: .58rem; transition: transform .22s; }
    .hp-catnav-btn.is-open .hp-catnav-chev { transform: rotate(180deg); }

    /* Overlay card — same design as the sidebar, never resizes the slider */
    .hp-catnav-panel {
        position: absolute; top: calc(100% + 8px); left: 0; z-index: 1050;
        width: 250px; max-width: calc(100vw - 32px);
        opacity: 0; visibility: hidden; transform: translateY(-6px);
        transition: opacity .22s ease, transform .22s ease, visibility .22s;
    }
    .hp-catnav-panel.is-open { opacity: 1; visibility: visible; transform: none; }

    /* The flyout card ships with a fixed 400px height; the panel is content-sized */
    .hp-catnav-panel .hp-fly-card.hp-cat-card { height: auto; max-height: 70vh; }
    .hp-catnav-panel .hp-fly-list { height: auto; max-height: calc(70vh - 2px); }

    @media (max-width: 767.98px) { .hp-catnav { display: none; } }
</style>
@endonce

<div class="hp-catnav">
    <button type="button" class="hp-catnav-btn" id="hpCatBtn"
        aria-expanded="false" aria-controls="hpCatPanel" aria-haspopup="true">
        <i class="fas fa-bars hp-catnav-bars" aria-hidden="true"></i>
        <span>Categories</span>
        <i class="fas fa-chevron-down hp-catnav-chev" aria-hidden="true"></i>
    </button>

    <div class="hp-catnav-panel" id="hpCatPanel" role="menu" aria-label="All categories" aria-hidden="true">
        @include('livewire.frontend.layouts.partial.category.category-flyout', ['categories' => $categories])
    </div>
</div>

@once
<script>
(function () {
    if (window.__hpCatNavInit) return;
    window.__hpCatNavInit = true;

    var btn = document.getElementById('hpCatBtn');
    var panel = document.getElementById('hpCatPanel');
    if (!btn || !panel) return;

    function isOpen() { return panel.classList.contains('is-open'); }

    function open(focusFirst) {
        panel.classList.add('is-open');
        btn.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
        panel.setAttribute('aria-hidden', 'false');
        if (focusFirst) {
            var first = panel.querySelector('a[href]');
            if (first) first.focus();
        }
    }

    function close(refocus) {
        if (!isOpen()) return;
        panel.classList.remove('is-open');
        btn.classList.remove('is-open');
        btn.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
        if (refocus) btn.focus();
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (isOpen()) close(false); else open(false);
    });

    // Selecting a category closes the panel (navigation is not prevented)
    panel.addEventListener('click', function (e) {
        if (e.target.closest('a[href]')) close(false);
    });

    // Keyboard: ArrowDown opens, arrows move, Escape closes
    btn.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            open(true);
        }
    });

    panel.addEventListener('keydown', function (e) {
        var links = Array.prototype.filter.call(
            panel.querySelectorAll('a[href]'),
            function (a) { return a.offsetParent !== null; }
        );
        var i = links.indexOf(document.activeElement);

        if (e.key === 'Escape') {
            e.preventDefault();
            close(true);
        } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            var next = links[i + (e.key === 'ArrowDown' ? 1 : -1)];
            if (next) next.focus();
        } else if (e.key === 'Tab' && !e.shiftKey && i === links.length - 1) {
            close(false);
        }
    });

    document.addEventListener('click', function (e) {
        if (isOpen() && !e.target.closest('.hp-catnav')) close(false);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) close(true);
    });
})();
</script>
@endonce
