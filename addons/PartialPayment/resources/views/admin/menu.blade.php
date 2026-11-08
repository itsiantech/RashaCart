@php
    $ppWaiting = \Addons\PartialPayment\Models\Payment::query()
        ->where('type', 'advance')->where('status', 'pending')->count();
@endphp

<li class="menu-item {{ request()->is('admin/addons/partial-payment*') ? 'active' : '' }}">
    <a wire:navigate href="{{ route('admin.addons.partial-payment.refunds') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-wallet"></i>
        <div>Partial Payment</div>
        @if ($ppWaiting > 0)
            <div class="badge rounded bg-label-warning">{{ $ppWaiting }}</div>
        @endif
    </a>
</li>
