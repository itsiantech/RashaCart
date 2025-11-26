@extends('emails.template')

@section('title', 'Order Status Updated - ' . setting('app_name'))

@section('header')
    <h2 style="margin: 10px 0 5px 0; color: #333;">Order Status Updated</h2>
@endsection

@section('content')

<div style="font-family: Arial, sans-serif;">

    <p>Hello <strong>{{ $order->name }}</strong>,</p>

    <!-- Dynamic Message Based on Status -->
    @php
        $status = strtolower($order->status);

        $messages = [
            'pending' => 'We have received your order and it is waiting for confirmation.',
            'processing' => 'Your order is now being prepared.',
            'shipped' => 'Great news! Your order is on the way.',
            'delivered' => 'Your order has been successfully delivered.',
            'cancelled' => 'Unfortunately, your order has been cancelled.',
        ];

        $statusMessage = $messages[$status] ?? 'Your order status has been updated.';
    @endphp

    <p>{{ $statusMessage }}</p>


    <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px; margin: 15px 0;">
        <p style="margin: 0;"><strong>Order Number:</strong> {{ $order->invoice_no }}</p>
    </div>


    <div style="background: #f8f9fa; padding: 15px;
                border-radius: 6px; border-left: 4px solid #ff0000; margin: 20px 0;">
        <p style="margin: 0; font-size: 16px;">
            <strong>Current Status:</strong> 
            <span style="color: #ff0000; font-weight: bold; text-transform: capitalize;">
                {{ $order->status }}
            </span>
        </p>
    </div>

    <p>We will notify you when there are further updates regarding your order.</p>

    <p>Thanks,<br>
        <strong>{{ setting('app_name') ?? config('app.name') }}</strong>
    </p>

</div>

@endsection
