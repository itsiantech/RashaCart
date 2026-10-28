@php $sfMenuItems = header_menu(); @endphp

@if (! empty($sfMenuItems))
    @once
        <style>
            .sf-menu-nav { margin: 12px 120px 0; }
            .sf-menu { display: flex; align-items: center; gap: 2px; margin: 0; padding: 0; list-style: none; }
            .sf-menu-item { position: relative; }
            .sf-menu > .sf-menu-item > a {
                display: inline-flex; align-items: center; gap: 7px;
                color: var(--hd-icon, #fff); font-size: .85rem; font-weight: 500;
                padding: .45rem .8rem; border-radius: 6px; text-decoration: none;
                transition: background .15s, color .15s;
            }
            .sf-menu > .sf-menu-item > a:hover,
            .sf-menu > .sf-menu-item > a:focus { color: var(--hd-badge, #fff); background: rgba(255, 255, 255, .1); }
            .sf-menu .sf-caret { font-size: .55rem; opacity: .7; }

            .sf-submenu {
                position: absolute; top: 100%; left: 0; z-index: 1200;
                min-width: 210px; margin: 0; padding: 6px; list-style: none;
                background: #fff; border-radius: 10px;
                box-shadow: 0 14px 34px rgba(10, 15, 40, .18);
                opacity: 0; visibility: hidden; transform: translateY(6px);
                transition: opacity .15s, transform .15s, visibility .15s;
            }
            .sf-menu-item:hover > .sf-submenu,
            .sf-menu-item:focus-within > .sf-submenu { opacity: 1; visibility: visible; transform: translateY(0); }
            .sf-submenu .sf-submenu { top: -6px; left: 100%; }
            .sf-submenu a {
                display: flex; align-items: center; justify-content: space-between; gap: 10px;
                padding: 8px 12px; border-radius: 7px;
                color: #2a2f3a; font-size: .85rem; text-decoration: none; white-space: nowrap;
            }
            .sf-submenu a:hover { background: #faf3f2; color: #7A0000; }
            .sf-submenu .sf-caret { color: #b7bdc9; }
            @media (max-width: 991.98px) { .sf-menu-nav { display: none; } }
        </style>
    @endonce

    <nav class="sf-menu-nav" aria-label="Header menu">
        <ul class="sf-menu">
            @foreach ($sfMenuItems as $sfMenuItem)
                @include('livewire.frontend.layouts.partial.header.menu-node', ['item' => $sfMenuItem, 'depth' => 0])
            @endforeach
        </ul>
    </nav>
@endif
