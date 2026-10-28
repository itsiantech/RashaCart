{{-- Mobile (<768px) category drawer. Works for both home_category_sidebar states. --}}
@once
<style>
    /* visibility (not just pointer-events) keeps closed drawer links out of the tab order */
    .hp-catdrawer {
        position: fixed; inset: 0; z-index: 1080; display: none;
        pointer-events: none; visibility: hidden;
        transition: visibility 0s linear .3s;
    }
    @media (max-width: 767.98px) { .hp-catdrawer { display: block; } }
    .hp-catdrawer.is-open { pointer-events: auto; visibility: visible; transition-delay: 0s; }

    .hp-catdrawer-backdrop {
        position: absolute; inset: 0; background: rgba(9, 13, 22, .55);
        opacity: 0; transition: opacity .25s ease;
    }
    .hp-catdrawer.is-open .hp-catdrawer-backdrop { opacity: 1; }

    .hp-catdrawer-panel {
        position: absolute; top: 0; bottom: 0; left: 0;
        width: 85%; max-width: 320px;
        display: flex; flex-direction: column;
        background: #fff; box-shadow: 8px 0 32px rgba(9, 13, 22, .3);
        transform: translateX(-102%);
        transition: transform .28s cubic-bezier(.22, .61, .36, 1);
    }
    .hp-catdrawer.is-open .hp-catdrawer-panel { transform: none; }

    .hp-catdrawer-head {
        flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between;
        padding: .8rem 1rem; background: var(--hd-bg, #10100a); color: #fff;
    }
    .hp-catdrawer-head span { font-size: .95rem; font-weight: 700; letter-spacing: .02em; }
    .hp-catdrawer-x {
        width: 36px; height: 36px; border: 0; border-radius: 10px;
        background: rgba(255, 255, 255, .1); color: #fff; font-size: .95rem;
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
    }
    .hp-catdrawer-x:hover { background: rgba(255, 255, 255, .2); }
    .hp-catdrawer-x:focus-visible { outline: 2px solid #ffd479; outline-offset: 2px; }

    .hp-catdrawer-body { flex: 1; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #d5d9e2 transparent; }
    .hp-catdrawer-body::-webkit-scrollbar { width: 6px; }
    .hp-catdrawer-body::-webkit-scrollbar-thumb { background: #d5d9e2; border-radius: 99px; }

    .hp-acc { border-bottom: 1px solid #f1f3f6; }
    .hp-acc-row { display: flex; align-items: center; }
    .hp-acc-link {
        flex: 1; min-height: 48px; display: flex; align-items: center;
        padding: 0 1rem; font-size: .92rem; font-weight: 500; color: #2b3442; text-decoration: none;
    }
    .hp-acc-link:active { color: var(--primary, #7A0000); background: #fdf1f0; }
    .hp-acc-btn {
        width: 48px; height: 48px; border: 0; background: transparent;
        color: #98a0b0; font-size: .72rem; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .hp-acc-btn i { transition: transform .22s; }
    .hp-acc-btn[aria-expanded="true"] { color: var(--primary, #7A0000); }
    .hp-acc-btn[aria-expanded="true"] i { transform: rotate(180deg); }
    .hp-acc-btn:focus-visible { outline: 2px solid #7A0000; outline-offset: -4px; border-radius: 8px; }

    .hp-acc-sub { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .26s ease; background: #fbfbfd; }
    .hp-acc-sub > .hp-acc-sub-inner { overflow: hidden; min-height: 0; }
    .hp-acc.is-open > .hp-acc-sub { grid-template-rows: 1fr; }

    .hp-acc-link-sub { min-height: 44px; font-size: .86rem; color: #4b5563; padding-left: 2rem; }
    .hp-acc-link-l3 {
        min-height: 42px; display: flex; align-items: center;
        padding: 0 1rem 0 3rem; font-size: .82rem; color: #5b6472; text-decoration: none;
        border-top: 1px solid #f4f5f8; background: #f7f8fb;
    }
    .hp-acc-link-l3:active { color: var(--primary, #7A0000); }

    body.hp-catdrawer-locked { overflow: hidden; }

    .hp-burger { display: none; }
    @media (max-width: 767.98px) {
        .hp-burger {
            display: inline-flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; flex: 0 0 auto;
            border: 0; border-radius: 10px; padding: 0;
            background: rgba(255, 255, 255, .08); color: var(--hd-icon, #fff);
            font-size: 1.05rem; cursor: pointer;
        }
        .hp-burger:active { background: rgba(255, 255, 255, .2); }
        .hp-burger:focus-visible { outline: 2px solid #ffd479; outline-offset: 2px; }
    }
</style>
@endonce

<div class="hp-catdrawer" id="hpCatDrawer" aria-hidden="true">
    <div class="hp-catdrawer-backdrop" data-hp-drawer-close></div>

    <aside class="hp-catdrawer-panel" role="dialog" aria-modal="true" aria-label="Categories">
        <div class="hp-catdrawer-head">
            <span>Categories</span>
            <button type="button" class="hp-catdrawer-x" data-hp-drawer-close aria-label="Close categories">
                <i class="fas fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <div class="hp-catdrawer-body">
            @foreach ($categories as $cat)
                <div class="hp-acc">
                    <div class="hp-acc-row">
                        <a class="hp-acc-link" href="{{ route('frontend.category.products.view', $cat->slug) }}">{{ $cat->name }}</a>
                        @if ($cat->children->count())
                            <button type="button" class="hp-acc-btn" data-hp-acc
                                aria-expanded="false" aria-controls="hpAcc{{ $cat->id }}"
                                aria-label="Show {{ $cat->name }} subcategories">
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </button>
                        @endif
                    </div>

                    @if ($cat->children->count())
                        <div class="hp-acc-sub" id="hpAcc{{ $cat->id }}">
                            <div class="hp-acc-sub-inner">
                                @foreach ($cat->children as $sub)
                                    @if ($sub->children->count())
                                        <div class="hp-acc">
                                            <div class="hp-acc-row">
                                                <a class="hp-acc-link hp-acc-link-sub" href="{{ route('frontend.category.products.view', $sub->slug) }}">{{ $sub->name }}</a>
                                                <button type="button" class="hp-acc-btn" data-hp-acc
                                                    aria-expanded="false" aria-controls="hpAcc{{ $sub->id }}"
                                                    aria-label="Show {{ $sub->name }} subcategories">
                                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div class="hp-acc-sub" id="hpAcc{{ $sub->id }}">
                                                <div class="hp-acc-sub-inner">
                                                    @foreach ($sub->children as $leaf)
                                                        <a class="hp-acc-link-l3" href="{{ route('frontend.category.products.view', $leaf->slug) }}">{{ $leaf->name }}</a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <a class="hp-acc-link hp-acc-link-sub" href="{{ route('frontend.category.products.view', $sub->slug) }}">{{ $sub->name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </aside>
</div>

@once
<script>
(function () {
    if (window.__hpCatDrawerInit) return;
    window.__hpCatDrawerInit = true;

    var drawer = document.getElementById('hpCatDrawer');
    var toggle = document.getElementById('hpCatToggle');
    if (!drawer || !toggle) return;

    var body = document.body;

    function isOpen() { return drawer.classList.contains('is-open'); }

    function open() {
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
        body.classList.add('hp-catdrawer-locked');
        var x = drawer.querySelector('.hp-catdrawer-x');
        if (x) x.focus();
    }

    function close() {
        if (!isOpen()) return;
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        body.classList.remove('hp-catdrawer-locked');
        toggle.focus();
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        if (isOpen()) close(); else open();
    });

    Array.prototype.forEach.call(drawer.querySelectorAll('[data-hp-drawer-close]'), function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            close();
        });
    });

    // Chevron toggles the accordion; the row link still navigates
    drawer.addEventListener('click', function (e) {
        var accBtn = e.target.closest('[data-hp-acc]');
        if (!accBtn) return;
        e.preventDefault();
        var acc = accBtn.closest('.hp-acc');
        var opened = acc.classList.toggle('is-open');
        accBtn.setAttribute('aria-expanded', opened ? 'true' : 'false');
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) {
            e.preventDefault();
            close();
        }
    });
})();
</script>
@endonce
