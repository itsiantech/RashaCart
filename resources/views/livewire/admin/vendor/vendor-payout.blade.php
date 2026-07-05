<div class="container-fluid py-4">
    <!-- Search & Per Page Row -->
    <div class="row mb-3 g-2">
        <div class="col-md-8">
            <input
                type="text"
                wire:model.live.debounce.500ms="search"
                placeholder="Search vendor..."
                class="form-control">
        </div>
        <div class="col-md-4">
            <select wire:model="perPage" class="form-select">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
            </select>
        </div>
    </div>

    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- Table -->
    <div class="table-responsive bg-white shadow rounded">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vendor</th>
                    <th>Total Earned</th>
                    <th>Total Withdrawn</th>
                    <th>Available Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)

                @php
                $totalEarned = round($earnings[$vendor->id] ?? 0, 2);

                $totalWithdrawn = $withdrawnTotals[$vendor->id] ?? 0;

                $available = $totalEarned - $totalWithdrawn;
                @endphp

                <tr>
                    <td>{{ $vendor->name }}</td>
                    <td>TK {{ number_format($totalEarned, 2) }}</td>
                    <td>
                        <span>TK {{ number_format($totalWithdrawn, 2) }}</span>
                        <span class="{{ $vendor->payouts->last()?->status == 'approved' ? 'text-success' : 'text-danger' }}">
                            {{ $vendor->payouts->last()?->status ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div>
                            <span>TK {{ number_format($available, 2) }}</span>

                            <span class="{{ $available >= 500 ? 'text-success' : 'text-danger' }}">
                                {{ $available >= 500 ? 'Available' : 'Minimum 500 TK required' }}
                            </span>

                            <button
                                type="button"
                                wire:click.prevent="withdraw({{ $vendor->id }})"
                                class="border border-dark text-black px-2 py-0.5 bg-white mx-2 rounded"
                                @if($available < 500) disabled @endif>
                                Withdraw
                            </button>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="text-center">No vendors found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $vendors->links() }}
    </div>
</div>