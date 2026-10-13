@extends('livewire.admin.layouts.app')

@section('page-title')
    Accounts | Technology & Server Cost
@endsection

@section('content')
<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts /</span> Technology & Server Maintenance</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <h5 class="card-header">Monthly Tech & Server Expense List</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2026-10-01</td>
                                <td><strong>cPanel & Cloud Server</strong></td>
                                <td><span class="badge bg-label-info">Server Cost</span></td>
                                <td>৳ 12,500</td>
                                <td><button class="btn btn-sm btn-outline-primary"><i class="bx bx-edit"></i></button></td>
                            </tr>
                            <tr>
                                <td>2026-09-28</td>
                                <td><strong>Website Maintenance Service</strong></td>
                                <td><span class="badge bg-label-primary">Maintenance</span></td>
                                <td>৳ 15,000</td>
                                <td><button class="btn btn-sm btn-outline-primary"><i class="bx bx-edit"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header">Add Tech Cost</h5>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Service / Vendor Name</label>
                            <input type="text" class="form-control" placeholder="e.g. AWS / cPanel / ITsian">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select">
                                <option>Hosting & Server</option>
                                <option>Domain Renewal</option>
                                <option>SaaS / API Service</option>
                                <option>Website Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cost Amount (৳)</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                        <button class="btn btn-primary w-100">Save Tech Expense</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection