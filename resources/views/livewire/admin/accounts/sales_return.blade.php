@extends('livewire.admin.layouts.app')

@section('page-title')
    Accounts | Sales & Return Cost
@endsection

@section('content')
<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts /</span> Sales & Category Cost Breakdown</h4>

    <!-- Category Sales Summary -->
    <div class="card mb-4">
        <h5 class="card-header"><i class="bx bx-category me-2"></i> Category-wise Sales & Profitability</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Category Name</th>
                        <th>Units Sold</th>
                        <th>Total Sales Value</th>
                        <th>Product Cost</th>
                        <th>Return Cost</th>
                        <th>Gross Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Clothing & Apparel</strong></td>
                        <td>420 Pcs</td>
                        <td>৳ 450,000</td>
                        <td>৳ 260,000</td>
                        <td class="text-danger">৳ 12,000</td>
                        <td class="text-success fw-bold">৳ 178,000</td>
                    </tr>
                    <tr>
                        <td><strong>Home Decor</strong></td>
                        <td>180 Pcs</td>
                        <td>৳ 210,000</td>
                        <td>৳ 110,000</td>
                        <td class="text-danger">৳ 4,500</td>
                        <td class="text-success fw-bold">৳ 95,500</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Returned & Waste Product Log -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <h5 class="card-header text-danger"><i class="bx bx-undo me-2"></i> Return Product Cost Log</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Order ID</th><th>Product</th><th>Cost</th><th>Reason</th></tr></thead>
                        <tbody>
                            <tr><td>#ORD-8921</td><td>Tent-Style Decor</td><td>৳ 2,500</td><td>Damaged in Courier</td></tr>
                            <tr><td>#ORD-8854</td><td>Pillow Cover Set</td><td>৳ 1,200</td><td>Size Mismatch</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <h5 class="card-header text-warning"><i class="bx bx-trash me-2"></i> Waste & Damage Cost Log</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Date</th><th>Item Description</th><th>Qty</th><th>Total Cost</th></tr></thead>
                        <tbody>
                            <tr><td>2026-10-02</td><td>Defective Fabric Waste</td><td>12 Kg</td><td>৳ 3,600</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection