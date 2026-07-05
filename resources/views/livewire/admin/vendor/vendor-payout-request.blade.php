<div class="card">
    <div class="card-header">
        <h5>Payout Requests</h5>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Requested Amount</th>
                    <th>admin Commission</th>
                    <th>Available Balance</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($requests as $request)
                <tr>
                    <td>{{ $request->vendor->name }}</td>

                    <td>{{ number_format($request->amount, 2) }}</td>

                    <td>
                        <span class="text-info">
                            {{ number_format($request->total_admin_commission, 2) }}
                        </span>
                    </td>

                    <td>
                        <span class="text-success">
                            {{ number_format($request->available_after_withdraw, 2) }}
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-{{
            $request->status === 'pending' ? 'warning' :
            ($request->status === 'approved' ? 'info' :
            ($request->status === 'paid' ? 'success' : 'danger'))
        }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>

                    <td>
                        @if($request->status === 'pending')
                        <button wire:click="approve({{ $request->id }})"
                            class="btn btn-success btn-sm">
                            Approve
                        </button>

                        <button wire:click="reject({{ $request->id }})"
                            class="btn btn-danger btn-sm">
                            Reject
                        </button>
                        @else
                        <span class="text-muted">No Action</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>