@extends('livewire.admin.layouts.app')

@section('page-title')
    Accounts | Void & Cancel Logs
@endsection

@section('content')
<div>
    <h4 class="fw-bold py-3 mb-4 text-danger"><i class="bx bx-shield-x me-2"></i> Audit & Void Transaction Logs</h4>

    <div class="card border-top border-danger border-3">
        <h5 class="card-header text-danger">All Canceled & Voided Entries (Cannot be Deleted)</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Void Date</th>
                        <th>Original Entry Name</th>
                        <th>Original Amount</th>
                        <th>Voided By (Admin)</th>
                        <th>Reason for Void</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-10-05 14:30</td>
                        <td>Office Refreshment Cost</td>
                        <td>৳ 1,500</td>
                        <td><strong>Admin User</strong></td>
                        <td>Wrong amount entered by staff</td>
                        <td><span class="badge bg-danger">VOIDED</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection