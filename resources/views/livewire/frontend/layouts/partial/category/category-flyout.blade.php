{{-- 3-level flyout category menu (Daraz-style). $categories = top-level with children.children eager loaded. --}}
@once
<style>
    .hp-fly-card { position: relative; background: #fff; border-radius: 14px; border: 1px solid #eef0f4; height: 400px; box-shadow: 0 2px 14px rgba(18, 34, 91, 0.06); }
    /* Must override .hp-cat-card's overflow-y:auto or flyout panels get clipped */
    .hp-fly-card.hp-cat-card { overflow: visible; }
    .hp-fly-list { height: 100%; overflow-y: auto; border-radius: 14px; scrollbar-width: thin; scrollbar-color: #d5d9e2 transparent; padding: 8px; display: flex; flex-direction: column; gap: 2px; }
    .hp-fly-l1 { position: static; }
    .hp-fly-l1 > a.hp-cat-item {
        display: flex; align-items: center; gap: 10px; padding: 10px 12px;
        font-size: .86rem; font-weight: 500; color: #333a45; text-decoration: none;
        border-radius: 0; border-bottom: 1px solid #f1f3f6;
        transition: background .15s, color .15s, padding-left .15s;
    }
    .hp-fly-l1:last-child > a.hp-cat-item { border-bottom: none; }
    .hp-fly-l1 > a.hp-cat-item span { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hp-fly-l1 > a.hp-cat-item > i.fa-chevron-right { font-size: .62rem; color: #c3c9d4; }
    .hp-fly-l1:hover > a.hp-cat-item { background: #f7f8fb; color: #7A0000; padding-left: 16px; }
    .hp-fly-l1:hover > a.hp-cat-item > i.fa-chevron-right { color: #7A0000; }
    .hp-fly-panel {
        display: none; position: absolute; top: 0; left: 100%; z-index: 60;
        min-width: 230px; max-width: 270px; max-height: 400px; overflow-y: auto;
        background: #fff; border: 1px solid #eef0f4; border-radius: 14px;
        box-shadow: 12px 12px 32px rgba(18, 34, 91, 0.12); padding: 8px;
    }
    /* Invisible hover bridge: panel touches the item, plus a 12px overlap zone */
    .hp-fly-panel::before {
        content: ''; position: absolute; top: 0; bottom: 0; left: -12px; width: 12px;
    }
    .hp-fly-l1:hover > .hp-fly-panel,
    .hp-fly-l1:focus-within > .hp-fly-panel,
    .hp-fly-l1.js-open > .hp-fly-panel { display: block; }
    .hp-fly-link {
        display: flex; align-items: center; gap: 9px; padding: 8px 10px;
        font-size: .83rem; font-weight: 500; color: #3f4652; text-decoration: none; border-radius: 8px; white-space: nowrap;
    }
    .hp-fly-link:hover { background: #fdf1f0; color: #7A0000; }
    .hp-fly-link img { width: 24px; height: 24px; object-fit: cover; border-radius: 6px; }
    .hp-fly-link .hp-fly-ic { width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; color: #7A0000; background: #faf0ef; border-radius: 6px; }
    .hp-fly-link .fa-chevron-right { margin-left: auto; font-size: .6rem; color: #c3c9d4; }
    .hp-fly-l2 { position: relative; }
    .hp-fly-l2 > .hp-fly-panel { left: 100%; }
    .hp-fly-l2:hover > .hp-fly-panel,
    .hp-fly-l2:focus-within > .hp-fly-panel,
    .hp-fly-l2.js-open > .hp-fly-panel { display: block; }
    /* L3 escapes the scrollable L2 panel (overflow-y:auto would clip it) */
    .hp-fly-l2 > .hp-fly-panel.fx { position: fixed; left: auto; top: auto; max-height: 80vh; }
    .hp-fly-l2:hover > .hp-fly-link { background: #fdf1f0; color: #7A0000; }
    /* Soft rotating icon tints */
</style>
@endonce
<div class="hp-fly-card hp-cat-card">
    <div class="hp-fly-list">
        @forelse($categories as $cat)
        <div class="hp-fly-l1{{ $cat->children->count() ? ' has-kids' : '' }}">
            <a href="{{ route('frontend.category.products.view', $cat->slug) }}" class="hp-cat-item"
                @if ($cat->children->count()) aria-haspopup="true" aria-expanded="false" @endif>
                <span>{{ $cat->name }}</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            @if ($cat->children->count())
            <div class="hp-fly-panel" role="menu">
                @foreach ($cat->children as $sub)
                <div class="hp-fly-l2{{ $sub->children->count() ? ' has-kids' : '' }}">
                    <a href="{{ route('frontend.category.products.view', $sub->slug) }}" class="hp-fly-link"
                        @if ($sub->children->count()) aria-haspopup="true" aria-expanded="false" @endif>
                        @if ($sub->image)
                        <img src="{{ asset('storage/' . $sub->image) }}" alt="{{ $sub->name }}">
                        @else
                        <i class="fas fa-angle-right hp-fly-ic"></i>
                        @endif
                        <span>{{ $sub->name }}</span>
                        @if ($sub->children->count())
                        <i class="fas fa-chevron-right"></i>
                        @endif
                    </a>
                    @if ($sub->children->count())
                    <div class="hp-fly-panel" role="menu">
                        @foreach ($sub->children as $leaf)
                        <a href="{{ route('frontend.category.products.view', $leaf->slug) }}" class="hp-fly-link">
                            <i class="fas fa-minus hp-fly-ic" style="font-size:.5rem"></i>
                            <span>{{ $leaf->name }}</span>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div class="p-3 text-muted text-center" style="font-size:.85rem">No categories</div>
        @endforelse
    </div>
</div>
@once
<script>
(function () {
    if (window.__hpFlyInit) return;
    window.__hpFlyInit = true;

    var HIDE_MS = 200;   // hide delay (req #2)
    var SWITCH_MS = 150; // hover-intent grace when sweeping across items (req #4)

    function itemOf(el) {
        return el ? el.closest('.hp-fly-l1, .hp-fly-l2') : null;
    }
    function panelOf(item) {
        if (!item) return null;
        for (var i = 0; i < item.children.length; i++) {
            if (item.children[i].classList && item.children[i].classList.contains('hp-fly-panel')) return item.children[i];
        }
        return null;
    }
    function linkOf(item) {
        if (!item) return null;
        for (var i = 0; i < item.children.length; i++) {
            if (item.children[i].tagName === 'A') return item.children[i];
        }
        return null;
    }
    function setOpen(item, on) {
        if (!item) return;
        if (on) item._hpOpenedAt = Date.now();
        item.classList.toggle('js-open', !!on);
        var a = linkOf(item);
        if (a && a.hasAttribute('aria-haspopup')) a.setAttribute('aria-expanded', on ? 'true' : 'false');
        // L3 panels escape the scrollable L2 panel via fixed positioning
        if (on && item.classList.contains('hp-fly-l2')) positionL3(item);
        else if (item.classList.contains('hp-fly-l2')) unpositionL3(item);
    }
    // Fixed-position an open L3 panel next to its row (clamped to viewport)
    function positionL3(item) {
        var panel = panelOf(item);
        if (!panel) return;
        panel.classList.add('fx');
        var r = item.getBoundingClientRect();
        var w = panel.offsetWidth || 240;
        var h = panel.offsetHeight || 200;
        var left = r.right + 2;
        if (left + w > window.innerWidth - 8) left = Math.max(8, r.left - w - 2);
        var top = Math.max(8, Math.min(r.top, window.innerHeight - h - 8));
        panel.style.left = left + 'px';
        panel.style.top = top + 'px';
    }
    function unpositionL3(item) {
        var panel = panelOf(item);
        if (!panel) return;
        panel.classList.remove('fx');
        panel.style.left = '';
        panel.style.top = '';
    }
    function repositionOpenL3() {
        Array.prototype.forEach.call(document.querySelectorAll('.hp-fly-l2.js-open'), function (item) {
            positionL3(item);
        });
    }
    window.addEventListener('scroll', repositionOpenL3, true);
    window.addEventListener('resize', repositionOpenL3);
    function isOpen(item) { return !!item && item.classList.contains('js-open'); }
    function clearTimers(item) {
        if (!item) return;
        clearTimeout(item._hpHide);
        clearTimeout(item._hpSwitch);
    }
    function closeSiblings(item) {
        var parent = item.parentElement;
        if (!parent) return;
        Array.prototype.forEach.call(parent.children, function (sib) {
            if (sib !== item && sib.classList && (sib.classList.contains('hp-fly-l1') || sib.classList.contains('hp-fly-l2'))) {
                clearTimers(sib);
                setOpen(sib, false);
            }
        });
    }
    function anySiblingOpen(item) {
        var parent = item.parentElement;
        if (!parent) return false;
        return Array.prototype.some.call(parent.children, function (sib) {
            return sib !== item && sib.classList && sib.classList.contains('js-open');
        });
    }

    // Hover in: cancel pending hide; open now, or after SWITCH_MS if a sibling
    // panel is open (lets diagonal moves toward the open panel win).
    document.addEventListener('mouseover', function (e) {
        var item = itemOf(e.target);
        if (!item || !panelOf(item)) return;
        clearTimers(item);
        if (isOpen(item)) return;
        if (anySiblingOpen(item)) {
            clearTimeout(item._hpSwitch);
            item._hpSwitch = setTimeout(function () { closeSiblings(item); setOpen(item, true); }, SWITCH_MS);
        } else {
            closeSiblings(item);
            setOpen(item, true);
        }
    });

    // Hover out: delayed hide; re-entry cancels via mouseover above.
    document.addEventListener('mouseout', function (e) {
        var item = itemOf(e.target);
        if (!item || !panelOf(item)) return;
        if (e.relatedTarget && item.contains(e.relatedTarget)) return;
        clearTimers(item);
        item._hpHide = setTimeout(function () {
            if (item.matches && item.matches(':hover')) return;
            setOpen(item, false);
        }, HIDE_MS);
    });

    // Keyboard focus opens too (with L3 fixed positioning, unlike pure CSS)
    document.addEventListener('focusin', function (e) {
        var item = itemOf(e.target);
        if (!item || !panelOf(item)) return;
        clearTimers(item);
        closeSiblings(item);
        setOpen(item, true);
    });
    document.addEventListener('focusout', function (e) {
        var item = itemOf(e.target);
        if (!item || !panelOf(item)) return;
        if (e.relatedTarget && item.contains(e.relatedTarget)) return;
        clearTimers(item);
        item._hpHide = setTimeout(function () { setOpen(item, false); }, HIDE_MS);
    });

    // Touch/mouse click: on touch screens the tap's own mouseover opens the
    // panel first, so a tap within 800ms of opening only opens (no navigation);
    // the next tap navigates. Mouse users navigate normally. (req #5)
    var coarsePointer = window.matchMedia && window.matchMedia('(hover: none)').matches;
    document.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('.hp-fly-l1 > a, .hp-fly-l2 > a') : null;
        if (!a) return;
        var item = itemOf(a);
        if (!item || !panelOf(item)) return;
        if (!isOpen(item)) {
            e.preventDefault();
            clearTimers(item);
            closeSiblings(item);
            setOpen(item, true);
        } else if (coarsePointer && (Date.now() - (item._hpOpenedAt || 0)) < 800) {
            e.preventDefault();
        }
    });

    // Keyboard: Enter/Space toggle, Esc closes, arrows move (req #5).
    document.addEventListener('keydown', function (e) {
        var a = e.target.closest ? e.target.closest('.hp-fly-card a') : null;
        if (!a) return;
        var item = itemOf(a);
        var card = a.closest('.hp-fly-card');
        function visibleLinks(root) {
            return Array.prototype.filter.call(
                root.querySelectorAll(':scope a[href]'),
                function (x) { return x.offsetParent !== null; }
            );
        }
        if (e.key === 'Escape') {
            if (!card) return;
            Array.prototype.forEach.call(card.querySelectorAll('.js-open'), function (o) { clearTimers(o); setOpen(o, false); });
            a.blur();
        } else if ((e.key === 'Enter' || e.key === ' ') && item && panelOf(item) && !isOpen(item)) {
            e.preventDefault();
            clearTimers(item);
            closeSiblings(item);
            setOpen(item, true);
        } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            var list = item && item.parentElement ? visibleLinks(item.parentElement) : [];
            var i = list.indexOf(a);
            var n = list[i + (e.key === 'ArrowDown' ? 1 : -1)];
            if (n) { e.preventDefault(); n.focus(); }
        } else if (e.key === 'ArrowRight' && item && panelOf(item)) {
            e.preventDefault();
            clearTimers(item);
            closeSiblings(item);
            setOpen(item, true);
            var f = panelOf(item).querySelector('a[href]');
            if (f) f.focus();
        } else if (e.key === 'ArrowLeft' && item) {
            var parentItem = item.parentElement ? itemOf(item.parentElement) : null;
            clearTimers(item);
            setOpen(item, false);
            if (parentItem) {
                var pa = linkOf(parentItem);
                if (pa) { e.preventDefault(); pa.focus(); }
            }
        }
    });
})();
</script>
@endonce
