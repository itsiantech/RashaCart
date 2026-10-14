<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Automation</h4>
        @if ($isSuperAdmin)
        <button type="button"
            wire:click="toggleMaster"
            wire:confirm="Master switch {{ $masterOn ? 'OFF' : 'ON' }} করবেন? {{ $masterOn ? 'সব automation থেমে যাবে।' : '' }}"
            class="btn btn-sm {{ $masterOn ? 'btn-danger' : 'btn-success' }}">
            <i class="bx bx-power-off me-1"></i>Master: {{ $masterOn ? 'ON' : 'OFF' }}
        </button>
        @else
        <span class="badge bg-{{ $masterOn ? 'success' : 'danger' }}">Master: {{ $masterOn ? 'ON' : 'OFF' }}</span>
        @endif
    </div>

    @if (!$masterOn)
    <div class="alert alert-danger">Master switch OFF — সব module থেমে আছে (emergency kill switch)।</div>
    @endif

    {{-- Budget widget --}}
    <div class="card mb-4">
        <div class="card-body d-flex gap-4 flex-wrap">
            <div><small class="text-muted">AI খরচ আজ</small>
                <h5 class="mb-0">৳{{ number_format($budget['today'], 2) }} / ৳{{ number_format($budget['daily_limit'], 2) }}</h5></div>
            <div><small class="text-muted">এই মাসে</small>
                <h5 class="mb-0">৳{{ number_format($budget['month'], 2) }} / ৳{{ number_format($budget['monthly_limit'], 2) }}</h5></div>
            @if ($budget['warn'])
            <div class="alert alert-warning py-1 px-2 mb-0">⚠️ বাজেট ৮০%+ শেষ — শীঘ্রই লিমিটে পৌঁছাবে।</div>
            @endif
        </div>
    </div>

    {{-- Module cards --}}
    <div class="row">
        @foreach ($cards as $c)
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0"><i class="{{ $c['icon'] }} me-1"></i>{{ $c['name'] }}</h6>
                        @if ($c['status'] === 'coming_soon')
                        <span class="badge bg-secondary">Coming soon</span>
                        @elseif ($c['mode'] === 'live')
                        <span class="badge bg-success">Live</span>
                        @elseif ($c['mode'] === 'shadow')
                        <span class="badge bg-warning text-dark">Shadow</span>
                        @else
                        <span class="badge bg-secondary">Off</span>
                        @endif
                    </div>
                    <p class="text-muted small">{{ $c['description'] }}</p>
                    <small class="text-muted d-block">
                        <span title="{{ $c['health_message'] }}">{{ $c['healthy'] ? '🟢' : '🔴' }}</span>
                        আজ {{ $c['today'] }}টি event
                        @if ($c['last']) • শেষ: {{ \Carbon\Carbon::parse($c['last'])->diffForHumans() }} @endif
                    </small>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="{{ route('admin.automation.module', $c['key']) }}" class="btn btn-sm btn-outline-primary">Settings</a>
                        @if ($isSuperAdmin)
                        <button type="button" wire:click="toggle('{{ $c['key'] }}')" class="btn btn-sm btn-outline-secondary">
                            {{ $c['mode'] === 'off' ? 'চালু (Shadow)' : 'বন্ধ' }}
                        </button>
                        @if ($c['mode'] !== 'off')
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" wire:click="setMode('{{ $c['key'] }}', 'shadow')"
                                class="btn {{ $c['mode'] === 'shadow' ? 'btn-warning' : 'btn-outline-warning' }}">Shadow</button>
                            <button type="button" wire:click="setMode('{{ $c['key'] }}', 'live')"
                                wire:confirm="Live mode চালু করবেন? Module এখন আসল action নেবে।"
                                class="btn {{ $c['mode'] === 'live' ? 'btn-success' : 'btn-outline-success' }}">Live</button>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Activity feed --}}
    <div class="card">
        <div class="card-header"><h6 class="mb-0">সাম্প্রতিক Activity (শেষ ২০)</h6></div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>সময়</th><th>Module</th><th>Event</th><th>Status</th><th>Message</th></tr></thead>
                <tbody>
                    @forelse ($feed as $f)
                    <tr>
                        <td><small>{{ $f->created_at?->diffForHumans() }}</small></td>
                        <td><small>{{ $f->module_key }}</small></td>
                        <td><small>{{ $f->event }}</small></td>
                        <td><span class="badge bg-{{ $f->status === 'failed' ? 'danger' : ($f->status === 'success' ? 'success' : 'secondary') }}">{{ $f->status }}</span></td>
                        <td><small>{{ $f->message }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">এখনো কোনো activity নেই।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
