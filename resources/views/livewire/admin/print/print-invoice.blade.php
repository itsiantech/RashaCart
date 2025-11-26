<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->invoice_no }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 80mm;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header img {
            max-width: 100px;
            max-height: 40px;
        }
        .info {
            margin-bottom: 5px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        th, td {
            text-align: left;
            padding: 2px 0;
        }
        th {
            border-bottom: 1px solid #000;
        }
        .total {
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .center {
            text-align: center;
        }
        .variant {
            font-size: 11px;
            color: #555;
        }
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">

    <div class="header">
        <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" alt="Logo"><br>
        <strong>Invoice #{{ $order->invoice_no }}</strong><br>
        <small>{{ $order->created_at->format('d M Y, h:i A') }}</small>
    </div>

    <div class="info">
        <p><strong>{{ $order->name }}</strong></p>
        <p>📞 <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></p>
        <p>📦 {{ $order->shipping_address }}</p>
        <p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="center">Qty</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product->name }}
                        @if($item->variant)
                            <div class="variant">
                                @foreach($item->variant->variantAttributeValues as $v)
                                    {{ $v->attribute->name }}: {{ $v->attributeValue->value }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Subtotal: ${{ number_format($order->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}</p>
        <p>Total: <strong>${{ number_format($order->total_amount, 2) }}</strong></p>
        <p>Paid: ${{ number_format($order->payment_amt ?? 0, 2) }}</p>
        <p>Due: ${{ number_format(($order->total_amount - ($order->payment_amt ?? 0)), 2) }}</p>
        <p>Payment: {{ ucfirst($order->payment_method) }}</p>
    </div>

    <div class="center" style="margin-top:5px;">
        <small>Thank you for shopping with {{ setting('app_name') ?? 'Our Store' }}!</small>
    </div>
</body>
</html>
