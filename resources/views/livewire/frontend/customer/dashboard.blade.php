{{-- Customer dashboard: homepage-style chrome via layout, sidebar (desktop) + bottom nav (mobile). --}}
<div class="cust-dash">
    <div class="hp-container cust-wrap">
    @if($needsPhone)
        <div class="alert alert-warning d-flex flex-wrap gap-2 align-items-center" role="alert">
            <i class="fas fa-phone"></i>
            <span class="flex-grow-1">Please add your phone number to complete your account.</span>
            <div class="d-flex gap-2 w-100">
                <label class="visually-hidden" for="needsPhone">Phone number</label>
                <input id="needsPhone" type="tel" class="form-control" wire:model.defer="phone" placeholder="01XXXXXXXXX" autocomplete="tel">
                <button class="btn btn-warning flex-shrink-0" wire:click="savePhoneStep">Save</button>
            </div>
            @error('phone')<div class="text-danger small w-100">{{ $message }}</div>@enderror
        </div>
    @endif

    <div class="row g-3">
        {{-- Sidebar (desktop) --}}
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="card cust-side shadow-sm">
                <div class="cust-user text-center p-3">
                    <div class="cust-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}</div>
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                </div>
                <nav class="list-group list-group-flush" aria-label="Dashboard">
                    @foreach(['overview' => 'fa-gauge-high|Overview', 'profile' => 'fa-user|Profile', 'track' => 'fa-truck-fast|Track Order', 'orders' => 'fa-bag-shopping|Orders', 'payments' => 'fa-credit-card|Payments', 'invoices' => 'fa-file-invoice|Invoices', 'settings' => 'fa-gear|Settings'] as $key => $meta)
                        @php [$icon, $label] = explode('|', $meta); @endphp
                        <button wire:click="setTab('{{ $key }}')" class="list-group-item list-group-item-action {{ $tab === $key ? 'active' : '' }}" aria-current="{{ $tab === $key ? 'page' : 'false' }}">
                            <i class="fas {{ $icon }} me-2"></i>{{ $label }}
                        </button>
                    @endforeach
                    <form method="POST" action="{{ route('customer.logout') }}" class="p-2">
                        @csrf
                        <button class="btn btn-outline-danger w-100"><i class="fas fa-right-from-bracket me-2"></i>Logout</button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- Main --}}
        <div class="col-12 col-lg-9">
            {{-- Mobile tab scroller --}}
            <div class="cust-mtabs d-lg-none mb-3" role="tablist" aria-label="Dashboard sections">
                @foreach(['overview' => 'Overview', 'orders' => 'Orders', 'track' => 'Track', 'payments' => 'Payments', 'invoices' => 'Invoices', 'profile' => 'Profile', 'settings' => 'Settings'] as $key => $label)
                    <button wire:click="setTab('{{ $key }}')" class="cust-mtab {{ $tab === $key ? 'active' : '' }}" role="tab" aria-selected="{{ $tab === $key ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>

            <div wire:loading class="cust-skel" aria-hidden="true"><div class="skeleton"></div><div class="skeleton"></div><div class="skeleton"></div></div>

            <div wire:loading.remove>
            @if($tab === 'overview')
                {{-- Summary cards --}}
                <div class="row g-2 mb-3">
                    @foreach([['Total Orders', $summary['total'], 'fa-bag-shopping', 'primary'], ['Pending', $summary['pending'], 'fa-clock', 'warning'], ['Delivered', $summary['delivered'], 'fa-circle-check', 'success'], ['Total Spent', '৳'.number_format($summary['spent']), 'fa-sack-dollar', 'info']] as [$label, $val, $icon, $color])
                        <div class="col-6 col-md-3">
                            <div class="card shadow-sm text-center p-3">
                                <i class="fas {{ $icon }} text-{{ $color }} fs-4"></i>
                                <div class="fs-5 fw-bold">{{ $val }}</div>
                                <div class="text-muted small">{{ $label }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Recent Orders</strong>
                        <button class="btn btn-sm btn-outline-primary" wire:click="setTab('orders')">View all</button>
                    </div>
                    <div class="card-body p-0">
                        @forelse($recentOrders as $o)
                            <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                                <div><div class="fw-bold">#{{ $o->invoice_no ?? $o->id }}</div><div class="text-muted small">{{ $o->created_at?->format('d M Y') }} • ৳{{ number_format($o->total_amount) }}</div></div>
                                <span class="badge {{ \App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($o->status) }}">{{ ucfirst($o->status) }}</span>
                            </div>
                        @empty
                            <div class="text-center p-4 text-muted"><i class="fas fa-bag-shopping fs-2 d-block mb-2"></i>No orders yet. <a href="{{ route('home') }}">Start shopping</a></div>
                        @endforelse
                    </div>
                </div>

            @elseif($tab === 'profile')
                <div class="card shadow-sm"><div class="card-header"><strong>Profile</strong></div><div class="card-body">
                    <form wire:submit.prevent="saveProfile" class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="pf-name">Name *</label><input id="pf-name" class="form-control" wire:model.defer="name" autocomplete="name">@error('name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label" for="pf-user">Username</label><input id="pf-user" class="form-control" wire:model.defer="username" autocomplete="username">@error('username')<div class="text-danger small">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label" for="pf-email">Email {{ $identifier !== 'phone' ? '*' : '' }}</label><input id="pf-email" type="email" class="form-control" wire:model.defer="email" autocomplete="email">@error('email')<div class="text-danger small">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label" for="pf-phone">Phone {{ $identifier !== 'email' ? '*' : '' }}</label><input id="pf-phone" type="tel" class="form-control" wire:model.defer="phone" autocomplete="tel">@error('phone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><label class="form-label" for="pf-addr">Address</label><textarea id="pf-addr" class="form-control" rows="2" wire:model.defer="address" autocomplete="street-address"></textarea></div>
                        <div class="col-12"><button class="btn btn-primary" type="submit" wire:loading.attr="disabled">Save Changes</button></div>
                    </form>
                </div></div>
                <div class="card shadow-sm mt-3"><div class="card-header"><strong>Change Password</strong></div><div class="card-body">
                    <form wire:submit.prevent="changePassword" class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="pw-cur">Current password *</label><input id="pw-cur" type="password" class="form-control" wire:model.defer="current_password" autocomplete="current-password">@error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror</div>
                        <div class="col-md-4"><label class="form-label" for="pw-new">New password *</label><input id="pw-new" type="password" class="form-control" wire:model.defer="new_password" autocomplete="new-password">@error('new_password')<div class="text-danger small">{{ $message }}</div>@enderror</div>
                        <div class="col-md-4"><label class="form-label" for="pw-conf">Confirm *</label><input id="pw-conf" type="password" class="form-control" wire:model.defer="new_password_confirmation" autocomplete="new-password"></div>
                        <div class="col-12"><button class="btn btn-outline-primary" type="submit">Update Password</button></div>
                    </form>
                </div></div>

            @elseif($tab === 'track')
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><strong><i class="fas fa-truck-fast me-2" style="color:#12225B"></i>Track Order</strong></div>
                    <div class="card-body p-3 p-md-4">
                        <form wire:submit.prevent="trackOrder" class="trk-form">
                            <div class="trk-input">
                                <i class="fas fa-magnifying-glass"></i>
                                <input id="trk" class="form-control" wire:model.defer="trackInvoice" placeholder="Enter order ID or invoice no (e.g. INV-26101601)" aria-label="Order ID or invoice number">
                            </div>
                            <button class="btn trk-btn flex-shrink-0" type="submit" wire:loading.attr="disabled" wire:target="trackOrder">
                                <span wire:loading.remove wire:target="trackOrder"><i class="fas fa-location-crosshairs me-1"></i>Track</span>
                                <span wire:loading wire:target="trackOrder"><span class="spinner-border spinner-border-sm me-1"></span>Checking…</span>
                            </button>
                        </form>
                        @error('trackInvoice')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                        @if($trackedOrder)
                            @php $tl = \App\Livewire\Frontend\Customer\CustomerDashboard::timeline($trackedOrder); @endphp
                            <div class="trk-result mt-4">
                                <div class="trk-head">
                                    <div>
                                        <div class="trk-invoice">#{{ $trackedOrder->invoice_no ?? $trackedOrder->id }}</div>
                                        <div class="text-muted small"><i class="far fa-calendar me-1"></i>Placed {{ $trackedOrder->created_at?->format('d M Y, h:i A') }}</div>
                                    </div>
                                    <span class="badge trk-badge {{ \App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($trackedOrder->status) }}">{{ ucfirst($trackedOrder->status) }}</span>
                                </div>

                                @if($tl['cancelled'])
                                    <div class="alert alert-danger mt-3 mb-0"><i class="fas fa-ban me-2"></i>This order was {{ $trackedOrder->status }}.</div>
                                @else
                                    <ol class="trk-steps mt-4 mb-0">
                                        @foreach($tl['steps'] as $i => $step)
                                            <li class="{{ $i < $tl['current'] ? 'done' : ($i === $tl['current'] ? 'now' : '') }}">
                                                <span class="ic"><i class="fas {{ $i < $tl['current'] ? 'fa-check' : ($i === $tl['current'] ? 'fa-truck-fast' : 'fa-ellipsis') }}"></i></span>
                                                <span class="lbl">{{ ucfirst($step) }}</span>
                                                @if($i === $tl['current'])<span class="when">Current status</span>@endif
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif

                                <div class="trk-items mt-4">
                                    <h6 class="trk-sub"><i class="fas fa-box-open me-2"></i>Items ({{ $trackedOrder->items->sum('quantity') }})</h6>
                                    @foreach($trackedOrder->items as $item)
                                        <div class="trk-item">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold small">{{ $item->product?->name ?? 'Product' }}</div>
                                                @if($item->variant_name || $item->option_label)<div class="text-muted" style="font-size:.72rem">{{ trim($item->variant_name.' '.$item->option_label) }}</div>@endif
                                            </div>
                                            <div class="text-muted small me-2">৳{{ number_format($item->unit_price) }} × {{ $item->quantity }}</div>
                                            <div class="fw-bold small">৳{{ number_format($item->unit_price * $item->quantity) }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="trk-meta">
                                    <div class="trk-meta-row"><span>Payment Method</span><strong>{{ strtoupper($trackedOrder->payment_method ?? 'COD') }}</strong></div>
                                    @if($trackedOrder->transaction_id)<div class="trk-meta-row"><span>Txn ID</span><strong>{{ $trackedOrder->transaction_id }}</strong></div>@endif
                                    <div class="trk-meta-row"><span>Delivery Address</span><strong class="text-end">{{ $trackedOrder->shipping_address ?? '—' }}</strong></div>
                                    <div class="trk-meta-row"><span>Delivery Charge</span><strong>৳{{ number_format($trackedOrder->delivery_charge ?? 0) }}</strong></div>
                                    <div class="trk-meta-row total"><span>Grand Total</span><strong>৳{{ number_format($trackedOrder->total_amount) }}</strong></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($tab === 'orders')
                <div class="card shadow-sm"><div class="card-header"><strong>Order History</strong></div><div class="card-body">
                    <div class="cust-filters mb-3" role="tablist" aria-label="Filter by status">
                        @foreach(['all' => 'All', 'pending' => 'Pending', 'on_hold' => 'On Hold', 'received' => 'Received/Processing', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $key => $label)
                            @php $fkey = $key === 'delivered' ? 'x' : $key; @endphp
                            <button wire:click="setStatusFilter('{{ $key === 'delivered' ? 'delivered' : $key }}')" class="btn btn-sm {{ $statusFilter === $key || ($key === 'delivered' && in_array($statusFilter, ['delivered'])) ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    @forelse($orders as $o)
                        <div class="border rounded p-3 mb-2 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <div><div class="fw-bold">#{{ $o->invoice_no ?? $o->id }}</div><div class="text-muted small">{{ $o->created_at?->format('d M Y') }} • {{ $o->items->sum('quantity') }} items • ৳{{ number_format($o->total_amount) }}</div></div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge {{ \App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($o->status) }}">{{ ucfirst($o->status) }}</span>
                                <button class="btn btn-sm btn-outline-primary" wire:click="openOrderDetails('{{ $o->invoice_no ?? $o->id }}')"><i class="fas fa-eye me-1"></i>Details</button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-4 text-muted"><i class="fas fa-receipt fs-2 d-block mb-2"></i>No orders in this status.</div>
                    @endforelse
                    <div class="mt-3">{{ $orders->links() }}</div>
                </div></div>

            @elseif($tab === 'payments')
                <div class="card shadow-sm"><div class="card-header"><strong>Payments</strong></div>
                    <div class="table-responsive"><table class="table mb-0">
                        <thead><tr><th>Order</th><th>Method</th><th class="text-end">Amount</th><th>Txn ID</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($payments as $p)
                            <tr><td>#{{ $p->invoice_no ?? $p->id }}</td><td>{{ strtoupper($p->payment_method ?? 'COD') }}</td><td class="text-end">৳{{ number_format($p->pay_amt ?: $p->total_amount) }}</td><td class="small">{{ $p->transaction_id ?? '—' }}</td><td class="small">{{ $p->created_at?->format('d M y') }}</td><td><span class="badge {{ \App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($p->payment_status ?? $p->status) }}">{{ ucfirst($p->payment_status ?? $p->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-4">No payments yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table></div>
                    <div class="card-footer">{{ $payments->links() }}</div>
                </div>

            @elseif($tab === 'invoices')
                <div class="card shadow-sm"><div class="card-header"><strong>Invoices</strong></div><div class="card-body">
                    @forelse($invoices as $inv)
                        <div class="border rounded p-3 mb-2 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <div><div class="fw-bold">#{{ $inv->invoice_no }}</div><div class="text-muted small">{{ $inv->created_at?->format('d M Y') }} • ৳{{ number_format($inv->total_amount) }}</div></div>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="{{ route('customer.invoice.print', $inv) }}?auto=1"><i class="fas fa-print me-1"></i>Print</a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener" href="{{ route('customer.invoice.print', $inv) }}"><i class="fas fa-file-lines me-1"></i>View / PDF</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-4 text-muted"><i class="fas fa-file-invoice fs-2 d-block mb-2"></i>No invoices yet.</div>
                    @endforelse
                    <div class="mt-3">{{ $invoices->links() }}</div>
                </div></div>

            @elseif($tab === 'settings')
                <div class="card shadow-sm"><div class="card-header"><strong>Settings</strong></div><div class="card-body">
                    <h6>Saved Address</h6>
                    <form wire:submit.prevent="saveProfile" class="mb-4"><label class="visually-hidden" for="set-addr">Saved address</label><textarea id="set-addr" class="form-control mb-2" rows="2" wire:model.defer="address"></textarea><button class="btn btn-sm btn-primary" type="submit">Save Address</button></form>
                    <h6>Notification Preferences</h6>
                    <form wire:submit.prevent="saveNotifications" class="mb-4">
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="nt-sms" wire:model="notify_sms"><label class="form-check-label" for="nt-sms">SMS notifications</label></div>
                        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="nt-email" wire:model="notify_email"><label class="form-check-label" for="nt-email">Email notifications</label></div>
                        <button class="btn btn-sm btn-primary" type="submit">Save Preferences</button>
                    </form>
                    <h6>Change Password</h6>
                    <form wire:submit.prevent="changePassword" class="row g-2 mb-4">
                        <div class="col-md-4"><input type="password" class="form-control" placeholder="Current" wire:model.defer="current_password"></div>
                        <div class="col-md-4"><input type="password" class="form-control" placeholder="New" wire:model.defer="new_password"></div>
                        <div class="col-md-4"><input type="password" class="form-control" placeholder="Confirm" wire:model.defer="new_password_confirmation"></div>
                        <div class="col-12"><button class="btn btn-sm btn-outline-primary" type="submit">Update Password</button></div>
                    </form>
                    <form method="POST" action="{{ route('customer.logout') }}">@csrf<button class="btn btn-danger"><i class="fas fa-right-from-bracket me-2"></i>Logout</button></form>
                </div></div>
            @endif
            </div>
        </div>
    </div>
    </div>{{-- /hp-container --}}

    {{-- Mobile bottom nav --}}
    <nav class="cust-bottomnav d-lg-none" aria-label="Dashboard">
        @foreach(['overview' => 'fa-gauge-high|Home', 'orders' => 'fa-bag-shopping|Orders', 'track' => 'fa-truck-fast|Track', 'profile' => 'fa-user|Account'] as $key => $meta)
            @php [$icon, $label] = explode('|', $meta); @endphp
            <button wire:click="setTab('{{ $key }}')" class="{{ $tab === $key ? 'active' : '' }}" aria-label="{{ $label }}"><i class="fas {{ $icon }}"></i><span>{{ $label }}</span></button>
        @endforeach
    </nav>
</div>

@push('styles')
<style>
.cust-dash{padding-bottom:76px}.cust-avatar{width:56px;height:56px;border-radius:50%;background:#12225B;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700}.cust-side .list-group-item.active{background:#12225B;border-color:#12225B}.cust-mtabs{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px}.cust-mtab{flex:0 0 auto;border:1px solid #dee2e6;background:#fff;border-radius:20px;padding:6px 14px;font-size:.85rem}.cust-mtab.active{background:#12225B;color:#fff;border-color:#12225B}.cust-timeline{list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:4px}.cust-timeline li{flex:1 1 90px;display:flex;align-items:center;gap:6px;font-size:.8rem;color:#999}.cust-timeline .dot{width:14px;height:14px;border-radius:50%;background:#dee2e6;flex-shrink:0}.cust-timeline li.done{color:#198754}.cust-timeline li.done .dot{background:#198754}.cust-timeline li.now{color:#12225B;font-weight:700}.cust-timeline li.now .dot{background:#12225B;box-shadow:0 0 0 4px rgba(18,34,91,.15)}.cust-filters{display:flex;gap:6px;flex-wrap:wrap}.cust-bottomnav{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:1px solid #dee2e6;display:flex;z-index:1030;padding:6px 0 calc(6px + env(safe-area-inset-bottom))}.cust-bottomnav button{flex:1;border:0;background:none;display:flex;flex-direction:column;align-items:center;gap:2px;font-size:.7rem;color:#666}.cust-bottomnav button.active{color:#12225B;font-weight:700}.cust-skel .skeleton{height:64px;border-radius:8px;background:linear-gradient(90deg,#eee,#f7f7f7,#eee);margin-bottom:8px}
@media(min-width:992px){.cust-dash{padding-bottom:0}}
.cust-wrap{padding-top:22px;padding-bottom:44px}
.mobile-bottom-nav{display:none!important}
.trk-form{display:flex;gap:10px}
.trk-input{flex:1;display:flex;align-items:center;gap:10px;background:#f5f6fa;border:1px solid #e5e7eb;border-radius:10px;padding:0 14px}
.trk-input:focus-within{border-color:#12225B;background:#fff;box-shadow:0 0 0 3px rgba(18,34,91,.12)}
.trk-input i{color:#9ca3af}
.trk-input .form-control{background:transparent;border:0;box-shadow:none;padding:11px 0}
.trk-btn{background:var(--cart-btn-bg, #12225B);border-color:var(--cart-btn-bg, #12225B);color:var(--cart-btn-text, #fff);border-radius:10px;padding:10px 22px}
.trk-btn:hover{background:var(--cart-btn-bg, #0e1b47);color:var(--cart-btn-text, #fff);filter:brightness(.92)}
.trk-head{display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center;background:#f5f6fa;border:1px solid #eef0f4;border-radius:12px;padding:14px 16px}
.trk-invoice{font-weight:800;font-size:1.05rem;color:#12225B}
.trk-badge{font-size:.8rem;padding:.45em .8em;border-radius:8px}
.trk-steps{list-style:none;margin:0;padding:0;position:relative;max-width:420px}
.trk-steps::before{content:"";position:absolute;left:17px;top:20px;bottom:20px;width:2px;background:#e5e7eb}
.trk-steps li{position:relative;display:flex;align-items:center;gap:14px;padding:9px 0;color:#9ca3af}
.trk-steps li .ic{width:36px;height:36px;border-radius:50%;background:#fff;border:2px solid #e5e7eb;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;position:relative;z-index:1;flex-shrink:0}
.trk-steps li.done{color:#198754;font-weight:600}
.trk-steps li.done .ic{background:#198754;border-color:#198754;color:#fff}
.trk-steps li.now{color:#12225B;font-weight:700}
.trk-steps li.now .ic{background:#12225B;border-color:#12225B;color:#fff;box-shadow:0 0 0 5px rgba(18,34,91,.15)}
.trk-steps li .when{margin-left:auto;font-size:.72rem;font-weight:400;color:#9ca3af}
.trk-sub{font-size:.85rem;font-weight:700;color:#374151;margin-bottom:10px}
.trk-item{display:flex;align-items:center;gap:12px;border:1px solid #eef0f4;border-radius:10px;padding:10px 14px;margin-bottom:8px}
.trk-meta{border:1px dashed #d7dbe4;border-radius:12px;padding:6px 16px;margin-top:16px;background:#fbfcfe}
.trk-meta-row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px solid #eef0f4;font-size:.85rem}
.trk-meta-row:last-child{border-bottom:0}
.trk-meta-row span{color:#6b7280}
.trk-meta-row.total{font-size:.95rem}
.trk-meta-row.total strong{color:#12225B}
</style>
@endpush
