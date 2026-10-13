<div>
    <h4 class="fw-bold mb-4">Tracking Event Logs</h4>

    {{-- Dashboard widget --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Events sent today</small>
                <h4 class="mb-0">{{ $stats['sentToday'] }}</h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Success rate (today)</small>
                <h4 class="mb-0">{{ $stats['successRate'] }}%</h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Failed (today)</small>
                <h4 class="mb-0 text-danger">{{ $stats['failedToday'] }}</h4>
            </div></div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card"><div class="card-body">
                <small class="text-muted">Last event</small>
                <h6 class="mb-0">{{ $stats['lastEventAt'] ? $stats['lastEventAt']->diffForHumans() : '—' }}</h6>
            </div></div>
        </div>
    </div>

    @if($stats['stuckJobs'] > 0)
        <div class="alert alert-warning">
            <i class="bx bx-error me-1"></i>
            {{ $stats['stuckJobs'] }} tracking job(s) waiting in queue. If this number keeps growing,
            the queue worker may not be running — start it with
            <code>php artisan queue:work --queue=tracking</code> (see README).
        </div>
    @endif

    {{-- Resend for order --}}
    <div class="card mb-4">
        <div class="card-body d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label">Resend Purchase for order</label>
                <input type="number" class="form-control" placeholder="Order ID" wire:model="resendOrderId">
                @error('resendOrderId') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button wire:click="resendOrder" class="btn btn-outline-primary">
                <i class="bx bx-send me-1"></i>Resend
            </button>
            <small class="text-muted w-100">Rebuilds fresh event data from the order and queues it. Cancelled/returned orders are never sent.</small>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body row g-2">
            <div class="col-md-2">
                <select class="form-select" wire:model.live="platform">
                    <option value="">All platforms</option>
                    @foreach($platforms as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.live="event">
                    <option value="">All events</option>
                    @foreach($events as $e)<option value="{{ $e }}">{{ $e }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.live="status">
                    <option value="">All statuses</option>
                    @foreach(['pending','success','failed','skipped','duplicate'] as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Order ID" wire:model.live.debounce.500ms="orderId">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.live="date">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th><th>Platform</th><th>Event</th><th>Event ID</th><th>Order</th>
                        <th>Status</th><th>HTTP</th><th>Attempts</th><th>Time</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->platform->platform ?? '—' }}</td>
                            <td>{{ $log->event_name }}</td>
                            <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($log->event_id, 22) }}</small></td>
                            <td>{{ $log->order_id ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td>{{ $log->http_status ?? '—' }}</td>
                            <td>{{ $log->attempts }}</td>
                            <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                            <td class="text-nowrap">
                                <button wire:click="toggleExpand({{ $log->id }})" class="btn btn-sm btn-outline-secondary">View</button>
                                @if($log->status === 'failed' && $log->order_id)
                                    <button wire:click="retry({{ $log->id }})" class="btn btn-sm btn-outline-primary">Retry</button>
                                @endif
                            </td>
                        </tr>
                        @if($expandedId === $log->id)
                            <tr>
                                <td colspan="10">
                                    @if($log->error_message)<div class="alert alert-danger py-1 mb-2"><small>{{ $log->error_message }}</small></div>@endif
                                    <div class="row">
                                        <div class="col-md-6"><strong>Payload (PII masked)</strong><pre class="bg-light p-2 small">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div>
                                        <div class="col-md-6"><strong>Response</strong><pre class="bg-light p-2 small">{{ json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No events logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $logs->links() }}</div>
    </div>
</div>
