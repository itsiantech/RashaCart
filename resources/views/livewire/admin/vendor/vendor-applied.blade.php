<div class="container-fluid py-4">

    <h5>Payment History</h5>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Shop Name</th>
                <th>Amount</th>
                <th>Approved Date</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->vendor->name ?? 'N/A' }}</td>
                    <td>{{ $payment->vendor->vendorDetails->shop_name ?? 'N/A' }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->updated_at?->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $payments->links() }}

</div>