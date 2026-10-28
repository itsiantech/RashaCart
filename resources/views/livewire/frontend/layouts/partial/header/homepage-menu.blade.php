@php $hpMenuItems = $items ?? header_menu(); @endphp

@if (! empty($hpMenuItems))
    @once
        <style>
            .hp-menu-nav { width: 100%; }
            .hp-menu { display: flex; flex-wrap: wrap; align-items: center; gap: 2px; margin: 0; padding: 0; list-style: none; }
            .hp-menu-item { position: relative; }
            .hp-menu-link {
                display: inline-flex; align-items: center; gap: 6px;
                color: var(--hd-nav, rgba(255, 255, 255, .85)) !important;
                font-size: .85rem; text-decoration: none;
                padding: .4rem .75rem; border-radius: 6px;
                transition: background .15s, color .15s;
            }
            .hp-menu-link:hover,
            .hp-menu-link:focus {
                color: #fff !important;
                background: var(--hd-nav-hover, rgba(255, 255, 255, .12));
            }
            .hp-menu-caret { font-size: .55rem; opacity: .65; }

            .hp-menu-drop {
                position: absolute; top: 100%; left: 0; z-index: 1200;
                min-width: 215px; margin: 0; padding: 6px; list-style: none;
                background: #fff; border-radius: 10px;
                box-shadow: 0 14px 34px rgba(10, 15, 40, .18);
                opacity: 0; visibility: hidden; transform: translateY(6px);
                transition: opacity .15s, transform .15s, visibility .15s;
            }
            .hp-menu-item:hover > .hp-menu-drop,
            .hp-menu-item:focus-within > .hp-menu-drop { opacity: 1; visibility: visible; transform: translateY(0); }
            .hp-menu-drop .hp-menu-drop { top: -6px; left: 100%; }
            .hp-menu-drop a {
                display: flex; align-items: center; justify-content: space-between; gap: 10px;
                padding: 8px 12px; border-radius: 7px;
                color: #2a2f3a; font-size: .85rem; text-decoration: none; white-space: nowrap;
            }
            .hp-menu-drop a:hover { background: #faf3f2; color: #7A0000; }
            .hp-menu-drop .hp-menu-link { color: #2a2f3a !important; }
            .hp-menu-drop .hp-menu-link:hover,
            .hp-menu-drop .hp-menu-link:focus { background: #faf3f2; color: #7A0000 !important; }
            .hp-menu-drop .hp-menu-caret { color: #b7bdc9; }
        </style>
    @endonce

    <nav class="d-flex align-items-center flex-wrap gap-1 py-1 hp-menu-nav" aria-label="Header menu">
        <ul class="hp-menu">
            @foreach ($hpMenuItems as $hpMenuItem)
                @include('livewire.frontend.layouts.partial.header.homepage-menu-node', ['item' => $hpMenuItem, 'depth' => 0])
            @endforeach
        </ul>
    </nav>
@endif
