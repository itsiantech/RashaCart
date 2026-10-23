{{-- Print-friendly customer invoice. Standalone (no layout) so Print / Save-as-PDF is clean. --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice {{ $order->invoice_no ?? $order->id }}</title>
<style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',Arial,sans-serif;color:#1f2937;background:#f3f4f6;padding:24px}
    .sheet{max-width:800px;margin:0 auto;background:#fff;border-radius:10px;padding:36px;box-shadow:0 4px 16px rgba(0,0,0,.08)}
    .head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #12225B;padding-bottom:18px;margin-bottom:22px;gap:16px;flex-wrap:wrap}
    .brand{font-size:1.6rem;font-weight:800;color:#12225B}
    .brand small{display:block;font-size:.8rem;font-weight:400;color:#6b7280;margin-top:2px}
    .inv-meta{text-align:right}
    .inv-meta h2{color:#EB0000;font-size:1.2rem;margin-bottom:6px}
    .inv-meta p{font-size:.85rem;color:#4b5563}
    .cols{display:flex;gap:24px;flex-wrap:wrap;margin-bottom:22px}
    .cols>div{flex:1 1 220px}
    .cols h4{font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;margin-bottom:6px}
    .cols p{font-size:.9rem;line-height:1.5}
    table{width:100%;border-collapse:collapse;margin-bottom:22px}
    th{background:#12225B;color:#fff;font-size:.8rem;text-align:left;padding:10px 12px}
    td{padding:10px 12px;font-size:.9rem;border-bottom:1px solid #e5e7eb;vertical-align:top}
    tr:nth-child(even) td{background:#f9fafb}
    .num{text-align:right;white-space:nowrap}
    .totals{margin-left:auto;max-width:320px;width:100%}
    .totals div{display:flex;justify-content:space-between;padding:7px 12px;font-size:.9rem}
    .totals .grand{background:#12225B;color:#fff;font-weight:700;font-size:1rem;border-radius:6px;margin-top:4px}
    .badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:.75rem;font-weight:600;background:#e5e7eb;color:#374151}
    .badge.paid{background:#d1fae5;color:#065f46}
    .badge.pending{background:#fef3c7;color:#92400e}
    .toolbar{max-width:800px;margin:0 auto 14px;display:flex;gap:10px;justify-content:flex-end}
    .btn{border:0;border-radius:8px;padding:10px 18px;font-size:.9rem;font-weight:600;cursor:pointer;color:#fff;background:#12225B}
    .btn.ghost{background:#fff;color:#12225B;border:1px solid #12225B}
    .note{font-size:.8rem;color:#6b7280;margin-top:14px}
    .foot{text-align:center;font-size:.78rem;color:#9ca3af;margin-top:26px;border-top:1px solid #e5e7eb;padding-top:14px}
    @media print{
        body{background:#fff;padding:0}
        .sheet{box-shadow:none;border-radius:0;max-width:none}
        .toolbar,.no-print{display:none!important}
    }
</style>
</head>
<body>
<div class="toolbar no-print">
    <a class="btn ghost" href="{{ route('customer.dashboard', ['tab' => 'invoices']) }}">&larr; Back</a>
    <button class="btn" onclick="window.print()">Print / Save as PDF</button>
</div>

<div class="sheet">
    <div class="head">
        <div class="brand">
            {{ setting('app_name') ?: 'Bandrose' }}
            <small>
                {{ setting('app_address') }}{{ setting('app_phone') ? ' • ' . setting('app_phone') : '' }}
            </small>
        </div>
        <div class="inv-meta">
            <h2>INVOICE</h2>
            <p><strong>#{{ $order->invoice_no ?? $order->id }}</strong></p>
            <p>Date: {{ $order->created_at?->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="cols">
        <div>
            <h4>Billed To</h4>
            <p>
                <strong>{{ $order->name }}</strong><br>
                {{ $order->phone }}<br>
                {{ $order->shipping_address }}{{ $order->delivery_zone ? ', ' . $order->delivery_zone : '' }}
            </p>
        </div>
        <div>
            <h4>Payment</h4>
            <p>
                Method: <strong>{{ strtoupper($order->payment_method ?? 'COD') }}</strong><br>
                Txn: {{ $order->transaction_id ?? '—' }}<br>
                Status: <span class="badge {{ strtolower($order->payment_status ?? '') === 'paid' ? 'paid' : 'pending' }}">
                    {{ ucfirst($order->payment_status ?? $order->status) }}
                </span>
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th class="num">Qty</th>
                <th class="num">Unit Price</th>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
                @php $line = (float) $item->unit_price * (int) $item->quantity; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $item->product->name ?? ($item->variant_name ?: 'Item') }}
                        @if($item->variant_name)<div class="note" style="margin-top:2px">{{ $item->variant_name }}{{ $item->option_label ? ' • ' . $item->option_label : '' }}</div>@endif
                    </td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">৳{{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">৳{{ number_format($line, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        @php
            $subtotal = (float) $order->total_amount + (float) $order->delivery_charge + (float) $order->coupon_discount_amount;
        @endphp
        <div><span>Subtotal</span><span>৳{{ number_format($subtotal, 2) }}</span></div>
        @if((float) $order->coupon_discount_amount > 0)
            <div><span>Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</span><span>− ৳{{ number_format($order->coupon_discount_amount, 2) }}</span></div>
        @endif
        <div><span>Delivery Charge</span><span>৳{{ number_format($order->delivery_charge, 2) }}</span></div>
        <div class="grand"><span>Total</span><span>৳{{ number_format($order->total_amount, 2) }}</span></div>
    </div>

    @if($order->note)
        <p class="note"><strong>Note:</strong> {{ $order->note }}</p>
    @endif

    <div class="foot">Thank you for shopping with {{ setting('app_name') ?: 'Bandrose' }}!</div>
</div>

@if($auto)
<script>window.addEventListener('load', () => setTimeout(() => window.print(), 300));</script>
@endif
</body>
</html>
