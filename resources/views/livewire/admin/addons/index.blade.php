<div class="ao-page">
    <style>
        .ao-page .ao-head { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; }
        .ao-page .ao-title { margin: 0; font-weight: 600; color: #384551; }
        .ao-page .ao-sub { margin: .15rem 0 0; font-size: .8125rem; color: #a1acb8; }
        .ao-page .ao-count { font-size: .75rem; color: #697a8d; background: #f0f2f5; border-radius: 2rem; padding: .25rem .7rem; }

        .ao-page .ao-card { border: 1px solid #e5e7eb; border-radius: .55rem; box-shadow: 0 .125rem .35rem rgba(67, 89, 113, .05); height: 100%; transition: box-shadow .15s ease, border-color .15s ease; }
        .ao-page .ao-card:hover { box-shadow: 0 .35rem .9rem rgba(67, 89, 113, .10); }
        .ao-page .ao-card.is-active { border-color: rgba(113, 221, 55, .55); }
        .ao-page .ao-card .card-body { display: flex; flex-direction: column; padding: 1rem; }

        .ao-page .ao-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .5rem; }
        .ao-page .ao-ident { display: flex; align-items: center; gap: .6rem; min-width: 0; }
        .ao-page .ao-icon { flex: 0 0 auto; width: 40px; height: 40px; border-radius: .5rem; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #696cff; background: rgba(105, 108, 255, .12); }
        .ao-page .ao-name { font-size: .95rem; font-weight: 600; color: #384551; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ao-page .ao-meta { font-size: .75rem; color: #a1acb8; }
        .ao-page .ao-min-0 { min-width: 0; }

        .ao-page .ao-badge { flex: 0 0 auto; font-size: .68rem; font-weight: 600; letter-spacing: .02em; text-transform: uppercase; padding: .28rem .55rem; border-radius: 2rem; }
        .ao-page .ao-badge-not_installed { color: #697a8d; background: #f0f2f5; }
        .ao-page .ao-badge-installed { color: #1f5fa8; background: #e4f1ff; }
        .ao-page .ao-badge-active { color: #1e7a34; background: #e3f8e8; }
        .ao-page .ao-badge-inactive { color: #8a5a00; background: #fff2d6; }

        .ao-page .ao-desc { margin: .7rem 0 .5rem; font-size: .8125rem; line-height: 1.45; color: #697a8d; }

        .ao-page .ao-provides { margin: 0 0 .5rem; }
        .ao-page .ao-provides-label { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #a1acb8; display: block; margin-bottom: .3rem; }
        .ao-page .ao-chips { display: flex; flex-wrap: wrap; gap: .3rem; }
        .ao-page .ao-chip { font-size: .72rem; color: #566a7f; background: #f6f7fb; border: 1px solid #eceef4; border-radius: .35rem; padding: .18rem .45rem; }

        .ao-page .ao-problems { list-style: none; margin: .2rem 0 .5rem; padding: .5rem .6rem; border-radius: .45rem; background: #fff6f6; border: 1px solid #ffe0e0; }
        .ao-page .ao-problems li { display: flex; gap: .35rem; font-size: .75rem; color: #b4232c; line-height: 1.35; }
        .ao-page .ao-problems li + li { margin-top: .25rem; }
        .ao-page .ao-problems i { margin-top: .1rem; }

        .ao-page .ao-last-error { font-size: .75rem; color: #8a5a00; background: #fff8e6; border: 1px solid #ffe9b0; border-radius: .45rem; padding: .45rem .55rem; margin-bottom: .5rem; word-break: break-word; }

        .ao-page .ao-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .4rem; margin-top: auto; padding-top: .75rem; border-top: 1px dashed #eceef4; }
        .ao-page .ao-actions .btn { font-size: .78rem; padding: .32rem .7rem; display: inline-flex; align-items: center; gap: .25rem; }
        .ao-page .ao-switch { margin: 0; padding-left: 2.4rem; min-height: auto; }
        .ao-page .ao-switch .form-check-input { cursor: pointer; }
        .ao-page .ao-switch .form-check-input:disabled { cursor: not-allowed; opacity: .45; }

        .ao-page .ao-uninstall { width: 100%; background: #fff6f6; border: 1px solid #ffdada; border-radius: .45rem; padding: .65rem .7rem; }
        .ao-page .ao-uninstall p { font-size: .78rem; color: #8a5a00; margin: 0 0 .45rem; }
        .ao-page .ao-uninstall .form-check-label { font-size: .78rem; color: #566a7f; }
        .ao-page .ao-uninstall-actions { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .55rem; }

        .ao-page .ao-note { font-size: .75rem; color: #a1acb8; }
        .ao-page .alert { font-size: .8125rem; }
        .ao-page code { font-size: .78rem; }

        @media (max-width: 575.98px) {
            .ao-page .ao-card .card-body { padding: .85rem; }
            .ao-page .ao-actions .btn { flex: 1 1 auto; justify-content: center; }
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Add Ons</li>
        </ol>
    </nav>

    <div class="ao-head mb-3">
        <div>
            <h5 class="ao-title">Manage Add Ons</h5>
            <p class="ao-sub">Optional extensions. The shop keeps working exactly as before when an add-on is inactive.</p>
        </div>
        <span class="ao-count">{{ count($addons) }} {{ \Illuminate\Support\Str::plural('add-on', count($addons)) }}</span>
    </div>

    @if (session('addon_status'))
        <div class="alert alert-success d-flex align-items-start gap-2 py-2 px-3" role="status">
            <i class="bx bx-check-circle mt-1"></i>
            <div>{{ session('addon_status') }}</div>
        </div>
    @endif

    @if ($actionError)
        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3" role="alert">
            <i class="bx bx-error-circle mt-1"></i>
            <div>{{ $actionError }}</div>
        </div>
    @endif

    @if ($tableMissing)
        <div class="alert alert-warning" role="alert">
            <strong>The add-ons table is missing.</strong>
            Run <code>php artisan migrate</code> once, then reload this page. Until then add-ons cannot be installed or activated.
        </div>
    @endif

    @if (! empty($failedAddons))
        <div class="alert alert-danger" role="alert">
            <strong>An add-on was switched off automatically.</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($failedAddons as $failed)
                    <li><strong>{{ $failed['name'] }}</strong> — {{ $failed['last_error'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 g-xl-4">
        @forelse ($addons as $addon)
            <div class="col-12 col-md-6 col-xxl-4" wire:key="addon-card-{{ $addon['slug'] }}">
                <div class="card ao-card {{ $addon['active'] ? 'is-active' : '' }}">
                    <div class="card-body">
                        <div class="ao-card-top">
                            <div class="ao-ident">
                                <span class="ao-icon"><i class="bx bx-extension"></i></span>
                                <div class="ao-min-0">
                                    <div class="ao-name" title="{{ $addon['name'] }}">{{ $addon['name'] }}</div>
                                    <div class="ao-meta">
                                        v{{ $addon['version'] }}
                                        @if ($addon['author'])
                                            &middot; {{ $addon['author'] }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="ao-badge ao-badge-{{ $addon['status'] }}">
                                    {{ $statusLabels[$addon['status']] ?? ucfirst($addon['status']) }}
                                </span>

                                @if ($addon['installed'])
                                    <div class="form-check form-switch ao-switch" title="{{ $addon['active'] ? 'Deactivate' : 'Activate' }}">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="ao-switch-{{ $addon['slug'] }}"
                                            wire:click="toggle('{{ $addon['slug'] }}')"
                                            @checked($addon['active'])
                                            @disabled($addon['active'] ? ! $addon['can_deactivate'] : ! $addon['can_activate'])>
                                        <label class="visually-hidden" for="ao-switch-{{ $addon['slug'] }}">
                                            {{ $addon['active'] ? 'Deactivate' : 'Activate' }} {{ $addon['name'] }}
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="ao-desc">{{ $addon['description'] ?: 'No description provided.' }}</p>

                        @if (! empty($addon['provides']))
                            <div class="ao-provides">
                                <span class="ao-provides-label">Adds</span>
                                <div class="ao-chips">
                                    @foreach ($addon['provides'] as $provide)
                                        <span class="ao-chip">{{ $provide }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (! empty($addon['problems']))
                            <ul class="ao-problems">
                                @foreach ($addon['problems'] as $problem)
                                    <li><i class="bx bx-error-circle"></i><span>{{ $problem }}</span></li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($addon['last_error'])
                            <div class="ao-last-error">
                                <strong>Auto-deactivated:</strong> {{ $addon['last_error'] }}
                            </div>
                        @endif

                        <div class="ao-actions">
                            @if ($uninstallSlug === $addon['slug'])
                                <div class="ao-uninstall">
                                    <p>
                                        Uninstall <strong>{{ $addon['name'] }}</strong>? The add-on record is always removed.
                                        @if ($addon['path'])
                                            Its own database tables stay unless you tick the box below.
                                        @endif
                                    </p>

                                    @if ($addon['path'])
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="ao-drop-{{ $addon['slug'] }}"
                                                wire:model="dropData">
                                            <label class="form-check-label" for="ao-drop-{{ $addon['slug'] }}">
                                                Also delete this add-on's tables and data
                                            </label>
                                        </div>
                                    @endif

                                    <div class="ao-uninstall-actions">
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="uninstall"
                                            wire:loading.attr="disabled">
                                            <i class="bx bx-trash"></i> Yes, uninstall
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancelUninstall">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @else
                                @if (! $addon['installed'])
                                    <button type="button" class="btn btn-sm btn-primary"
                                        wire:click="install('{{ $addon['slug'] }}')"
                                        wire:loading.attr="disabled"
                                        @disabled($tableMissing || ! $addon['can_install'])>
                                        <i class="bx bx-download"></i> Install
                                    </button>
                                @elseif (! $addon['active'])
                                    <button type="button" class="btn btn-sm btn-primary"
                                        wire:click="activate('{{ $addon['slug'] }}')"
                                        wire:loading.attr="disabled"
                                        @disabled($tableMissing || ! $addon['can_activate'])>
                                        <i class="bx bx-power-off"></i> Activate
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        wire:click="deactivate('{{ $addon['slug'] }}')"
                                        wire:loading.attr="disabled"
                                        @disabled(! $addon['can_deactivate'])>
                                        <i class="bx bx-pause-circle"></i> Deactivate
                                    </button>
                                @endif

                                @if ($addon['installed'])
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto"
                                        wire:click="confirmUninstall('{{ $addon['slug'] }}')"
                                        @disabled(! $addon['can_uninstall'])>
                                        <i class="bx bx-trash"></i> Uninstall
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-package bx-lg text-muted d-block mb-2"></i>
                        <h6 class="mb-1">No add-ons found</h6>
                        <p class="ao-note mb-2">
                            Drop an add-on folder into <code>{{ $addonsPath }}</code> and reload this page.
                        </p>
                        <p class="ao-note mb-0">Core version: {{ $coreVersion }}</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <p class="ao-note mt-3 mb-0">
        <i class="bx bx-shield-quarter"></i>
        Add-ons run as PHP code on your server. Only install add-ons you trust.
        Inactive add-ons register no menu item, page, tab or hook.
    </p>
</div>
