@extends('livewire.admin.layouts.app')

@section('page-title')
    Accounts | Supplier Purchase Ledger
@endsection

@section('content')
<div>
    <!-- Page Header & Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">Accounts /</span> Supplier Purchase & Delivery Ledger</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bx bx-user-plus me-1"></i> + Add New Supplier
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                <i class="bx bx-cart-add me-1"></i> + Add New Purchase
            </button>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.accounts.supplier-ledger') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Supplier</label>
                    <select name="supplier_id" id="filter_supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} {{ $supplier->company ? '(' . $supplier->company . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Filter Period</label>
                    <select name="period" class="form-select">
                        <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Weekly (Last 7 Days)</option>
                        <option value="monthly" {{ request('period', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly (This Month)</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Yearly (This Year)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Keyword Search</label>
                    <input type="text" name="keyword" class="form-control" placeholder="Search Invoice / Specs / Supplier" value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Search</button>
                    @if(request()->hasAny(['supplier_id', 'period', 'keyword']))
                        <a href="{{ route('admin.accounts.supplier-ledger') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bx bx-reset"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Supplier Purchase & Delivery Ledger Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold"><i class="bx bx-receipt text-primary me-2"></i> Supplier Purchase & Delivery Ledger</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="bx bx-user-plus me-1"></i> + Add New Supplier
                </button>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                    <i class="bx bx-plus me-1"></i> + Add New Purchase
                </button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0" id="supplierLedgerTable">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Supplier Name</th>
                        <th>Product Specs (Size/Weight)</th>
                        <th>Total Qty</th>
                        <th>Purchase Cost</th>
                        <th>Delivery Cost</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    @forelse($ledgers as $ledger)
                        <tr id="ledger-row-{{ $ledger->id }}" class="{{ $ledger->status === 'voided' ? 'table-light text-muted' : '' }}">
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($ledger->date)->format('Y-m-d') }}</strong>
                                @if($ledger->invoice_no)
                                    <br><small class="text-muted"><i class="bx bx-hash"></i>{{ $ledger->invoice_no }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $ledger->supplier?->name ?? 'N/A' }}</strong>
                                @if($ledger->supplier?->company)
                                    <br><small class="text-muted"><i class="bx bx-building me-1"></i>{{ $ledger->supplier->company }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 280px;" title="{{ $ledger->product_specs }}">
                                    {{ $ledger->product_specs ?: 'Purchased Goods' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ number_format($ledger->total_qty, 2) }}</span>
                            </td>
                            <td>৳ {{ number_format($ledger->purchase_cost, 2) }}</td>
                            <td>৳ {{ number_format($ledger->delivery_cost, 2) }}</td>
                            <td><span class="badge bg-label-success">৳ {{ number_format($ledger->paid_amount, 2) }}</span></td>
                            <td>
                                @if($ledger->due_amount > 0)
                                    <span class="badge bg-label-danger">৳ {{ number_format($ledger->due_amount, 2) }}</span>
                                @else
                                    <span class="badge bg-label-secondary">৳ 0.00</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-info view-details-btn" data-id="{{ $ledger->id }}" title="View Details">
                                        <i class="bx bx-show-alt"></i>
                                    </button>
                                    @if($ledger->status !== 'voided')
                                        <button type="button" class="btn btn-sm btn-outline-danger void-entry-btn" data-id="{{ $ledger->id }}" data-invoice="{{ $ledger->invoice_no }}" title="Void Entry">
                                            <i class="bx bx-block me-1"></i> Void
                                        </button>
                                    @else
                                        <span class="badge bg-label-secondary"><i class="bx bx-x-circle me-1"></i> Voided</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyLedgerRow">
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bx bx-receipt fs-1 d-block mb-2"></i>
                                <span>No supplier ledger records found. Click <strong>"+ Add New Purchase"</strong> to create one!</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $ledgers->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: ADD NEW SUPPLIER (AJAX)           -->
<!-- ========================================== -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-label-primary">
                <h5 class="modal-title fw-bold" id="addSupplierModalLabel">
                    <i class="bx bx-user-plus me-1 text-primary"></i> Register New Supplier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierForm" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="supplier_name" class="form-control" placeholder="e.g. Marvel Fabric Suppliers" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company" id="supplier_company" class="form-control" placeholder="e.g. Marvel Textiles Ltd">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" id="supplier_phone" class="form-control" placeholder="01711-XXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" id="supplier_email" class="form-control" placeholder="supplier@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Opening Balance (৳)</label>
                            <input type="number" step="0.01" min="0" name="opening_balance" id="supplier_opening_balance" class="form-control" placeholder="0.00" value="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address / Location</label>
                            <textarea name="address" id="supplier_address" class="form-control" rows="2" placeholder="Full address or warehouse location"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveSupplier">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="saveSupplierSpinner" role="status"></span>
                        <i class="bx bx-check-circle me-1"></i> Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: ADD NEW PURCHASE (AJAX)           -->
<!-- ========================================== -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-labelledby="addPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-label-primary">
                <h5 class="modal-title fw-bold" id="addPurchaseModalLabel">
                    <i class="bx bx-cart-add me-1 text-primary"></i> New Supplier Purchase Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="purchaseForm" autocomplete="off">
                @csrf
                <div class="modal-body p-4">
                    <!-- Basic Information Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Delivery / Arrival Date</label>
                            <input type="date" name="delivery_date" id="purchase_delivery_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Invoice / Bill No</label>
                            <input type="text" name="invoice_no" id="purchase_invoice_no" class="form-control" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="supplier_id" id="purchase_supplier_id" class="form-select" required>
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->name }} {{ $supplier->company ? '(' . $supplier->company . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="btnInlineAddSupplier" title="Quick Add Supplier">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Items Section -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="fw-bold"><i class="bx bx-package me-1 text-primary"></i> Product & Raw Material Items</span>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddItemRow">
                                <i class="bx bx-plus me-1"></i> + Add Another Item
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 align-middle" id="purchaseItemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 25%;">Product / Item Name</th>
                                            <th style="width: 25%;">Size / Weight / Specs</th>
                                            <th style="width: 15%;">Quantity</th>
                                            <th style="width: 15%;">Unit Cost (৳)</th>
                                            <th style="width: 15%;">Total Cost (৳)</th>
                                            <th style="width: 5%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseItemsBody">
                                        <!-- Item Row Template -->
                                        <tr class="item-row">
                                            <td>
                                                <select class="form-select form-select-sm item-product-select mb-1">
                                                    <option value="">-- Select Product or Custom --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}" data-name="{{ $product->name }}">
                                                            {{ $product->name }} (Stock: {{ $product->stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" class="form-control form-control-sm item-custom-name" placeholder="Item Name (e.g. Cotton Roll)">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm item-specs" placeholder="e.g. Cotton Roll (50 Kg / Roll)">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm item-qty text-end" min="0.01" step="any" value="1" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm item-unit-cost text-end" min="0" step="any" value="0.00" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm item-total-cost text-end bg-light fw-bold" readonly value="0.00">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-item" title="Remove Row">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Costs & Calculations Section -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-header bg-light py-2 px-3 fw-bold">Payment & Delivery Details</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold">Delivery Cost (৳)</label>
                                            <input type="number" name="delivery_cost" id="purchase_delivery_cost" class="form-control text-end" min="0" step="any" value="0.00">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                                            <select name="payment_status" id="purchase_payment_status" class="form-select" required>
                                                <option value="due">Full Due (৳ 0 Paid)</option>
                                                <option value="paid">Full Paid</option>
                                                <option value="partial">Partial Payment</option>
                                            </select>
                                        </div>
                                        <div class="col-12" id="paidAmountContainer">
                                            <label class="form-label fw-semibold">Paid Amount (৳)</label>
                                            <input type="number" name="paid_amount" id="purchase_paid_amount" class="form-control text-end" min="0" step="any" value="0.00">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Note / Remarks</label>
                                            <textarea name="note" id="purchase_note" class="form-control" rows="2" placeholder="Vehicle no, gate pass, transport notes..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Real-time Cost Calculation Box -->
                        <div class="col-md-6">
                            <div class="card h-100 border bg-light">
                                <div class="card-header py-2 px-3 fw-bold text-primary">
                                    <i class="bx bx-calculator me-1"></i> Live Cost Summary
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush rounded shadow-none">
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                            <span class="text-muted">Total Quantity / Weight:</span>
                                            <strong id="calc_total_qty" class="fs-6 text-dark">0.00</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                            <span class="text-muted">Total Purchase Cost:</span>
                                            <strong class="fs-6">৳ <span id="calc_purchase_cost">0.00</span></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                            <span class="text-muted">Delivery / Transport Cost:</span>
                                            <strong class="fs-6">৳ <span id="calc_delivery_cost">0.00</span></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 border-top border-2">
                                            <span class="fw-bold text-dark fs-6">Net Total Amount:</span>
                                            <strong class="text-primary fs-5">৳ <span id="calc_net_total">0.00</span></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                            <span class="text-muted">Paid Amount:</span>
                                            <strong class="text-success fs-6">৳ <span id="calc_paid_amount">0.00</span></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 border-top">
                                            <span class="fw-bold text-danger fs-6">Due Amount:</span>
                                            <strong class="text-danger fs-5">৳ <span id="calc_due_amount">0.00</span></strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSavePurchase">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="savePurchaseSpinner" role="status"></span>
                        <i class="bx bx-check-double me-1"></i> Save Purchase & Update Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: VIEW PURCHASE / LEDGER DETAILS    -->
<!-- ========================================== -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-label-info">
                <h5 class="modal-title fw-bold" id="viewDetailsTitle">
                    <i class="bx bx-file me-1"></i> Purchase & Ledger Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Inline Template for New Item Rows -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-select form-select-sm item-product-select mb-1">
                <option value="">-- Select Product or Custom --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}" data-name="{{ $product->name }}">
                        {{ $product->name }} (Stock: {{ $product->stock }})
                    </option>
                @endforeach
            </select>
            <input type="text" class="form-control form-control-sm item-custom-name" placeholder="Item Name (e.g. Cotton Roll)">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-specs" placeholder="e.g. Cotton Roll (50 Kg / Roll)">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm item-qty text-end" min="0.01" step="any" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm item-unit-cost text-end" min="0" step="any" value="0.00" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm item-total-cost text-end bg-light fw-bold" readonly value="0.00">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-item" title="Remove Row">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
(function() {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // ── Helper: Format Currency ──
    function formatMoney(amount) {
        return Number(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ── 1. Calculate Row and Overall Totals in Purchase Modal ──
    function calculateTotals() {
        let totalQty = 0;
        let totalPurchaseCost = 0;

        document.querySelectorAll('#purchaseItemsBody .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
            const unitCost = parseFloat(row.querySelector('.item-unit-cost')?.value) || 0;
            const rowTotal = qty * unitCost;

            const totalField = row.querySelector('.item-total-cost');
            if (totalField) {
                totalField.value = rowTotal.toFixed(2);
            }

            totalQty += qty;
            totalPurchaseCost += rowTotal;
        });

        const deliveryCost = parseFloat(document.getElementById('purchase_delivery_cost')?.value) || 0;
        const netTotal = totalPurchaseCost + deliveryCost;

        const paymentStatus = document.getElementById('purchase_payment_status')?.value || 'due';
        const paidAmountInput = document.getElementById('purchase_paid_amount');
        let paidAmount = parseFloat(paidAmountInput?.value) || 0;

        if (paymentStatus === 'paid') {
            paidAmount = netTotal;
            if (paidAmountInput) paidAmountInput.value = netTotal.toFixed(2);
        } else if (paymentStatus === 'due') {
            paidAmount = 0;
            if (paidAmountInput) paidAmountInput.value = '0.00';
        } else if (paidAmount > netTotal) {
            paidAmount = netTotal;
            if (paidAmountInput) paidAmountInput.value = netTotal.toFixed(2);
        }

        const dueAmount = Math.max(0, netTotal - paidAmount);

        // Update live summary
        const elQty = document.getElementById('calc_total_qty');
        const elPurchaseCost = document.getElementById('calc_purchase_cost');
        const elDeliveryCost = document.getElementById('calc_delivery_cost');
        const elNetTotal = document.getElementById('calc_net_total');
        const elPaid = document.getElementById('calc_paid_amount');
        const elDue = document.getElementById('calc_due_amount');

        if (elQty) elQty.textContent = totalQty.toFixed(2);
        if (elPurchaseCost) elPurchaseCost.textContent = formatMoney(totalPurchaseCost);
        if (elDeliveryCost) elDeliveryCost.textContent = formatMoney(deliveryCost);
        if (elNetTotal) elNetTotal.textContent = formatMoney(netTotal);
        if (elPaid) elPaid.textContent = formatMoney(paidAmount);
        if (elDue) elDue.textContent = formatMoney(dueAmount);
    }

    // ── 2. Add / Remove Item Rows ──
    document.addEventListener('click', function(e) {
        // Add row
        if (e.target.closest('#btnAddItemRow')) {
            const template = document.getElementById('itemRowTemplate');
            if (template) {
                const clone = template.content.cloneNode(true);
                document.getElementById('purchaseItemsBody').appendChild(clone);
                calculateTotals();
            }
        }

        // Remove row
        const removeBtn = e.target.closest('.btn-remove-item');
        if (removeBtn) {
            const tbody = document.getElementById('purchaseItemsBody');
            if (tbody.querySelectorAll('.item-row').length > 1) {
                removeBtn.closest('.item-row').remove();
                calculateTotals();
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.warning('At least one item row is required in the purchase.');
                }
            }
        }

        // Inline Quick Add Supplier from Purchase Modal
        if (e.target.closest('#btnInlineAddSupplier')) {
            const purchaseModalEl = document.getElementById('addPurchaseModal');
            const supplierModalEl = document.getElementById('addSupplierModal');

            if (purchaseModalEl && supplierModalEl) {
                const purchaseModal = bootstrap.Modal.getInstance(purchaseModalEl);
                if (purchaseModal) purchaseModal.hide();

                const supplierModal = new bootstrap.Modal(supplierModalEl);
                supplierModal.show();
            }
        }
    });

    // ── 3. Real-time input change listeners for calculations ──
    document.addEventListener('input', function(e) {
        if (e.target.closest('#purchaseForm')) {
            if (e.target.classList.contains('item-qty') ||
                e.target.classList.contains('item-unit-cost') ||
                e.target.id === 'purchase_delivery_cost' ||
                e.target.id === 'purchase_paid_amount') {
                calculateTotals();
            }
        }
    });

    // Handle product selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-product-select')) {
            const selectedOpt = e.target.options[e.target.selectedIndex];
            const price = selectedOpt.getAttribute('data-price');
            const name = selectedOpt.getAttribute('data-name');
            const row = e.target.closest('.item-row');

            const customNameInput = row.querySelector('.item-custom-name');
            const specsInput = row.querySelector('.item-specs');
            const unitCostInput = row.querySelector('.item-unit-cost');

            if (selectedOpt.value) {
                if (customNameInput) customNameInput.value = name || '';
                if (specsInput && !specsInput.value) specsInput.value = name || '';
                if (price && unitCostInput) unitCostInput.value = parseFloat(price).toFixed(2);
            }
            calculateTotals();
        }

        // Payment status change
        if (e.target.id === 'purchase_payment_status') {
            const paidInput = document.getElementById('purchase_paid_amount');
            if (e.target.value === 'due') {
                if (paidInput) {
                    paidInput.value = '0.00';
                    paidInput.readOnly = true;
                }
            } else if (e.target.value === 'paid') {
                if (paidInput) paidInput.readOnly = true;
            } else {
                if (paidInput) paidInput.readOnly = false;
            }
            calculateTotals();
        }
    });

    // ── 4. AJAX: Save New Supplier ──
    const supplierForm = document.getElementById('supplierForm');
    if (supplierForm) {
        supplierForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const btnSave = document.getElementById('btnSaveSupplier');
            const spinner = document.getElementById('saveSupplierSpinner');
            btnSave.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData(supplierForm);

            fetch('{{ route('admin.accounts.suppliers.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnSave.disabled = false;
                spinner.classList.add('d-none');

                if (data.success) {
                    if (typeof toastr !== 'undefined') toastr.success(data.message || 'Supplier added successfully!');

                    // Add new option to BOTH dropdowns
                    const s = data.supplier;
                    const optText = `${s.name} ${s.company ? '(' + s.company + ')' : ''}`;

                    const filterSelect = document.getElementById('filter_supplier_id');
                    const purchaseSelect = document.getElementById('purchase_supplier_id');

                    if (filterSelect) {
                        const opt = new Option(optText, s.id);
                        filterSelect.add(opt);
                    }

                    if (purchaseSelect) {
                        const opt = new Option(optText, s.id, true, true);
                        purchaseSelect.add(opt);
                    }

                    // Close Supplier Modal
                    const supplierModalEl = document.getElementById('addSupplierModal');
                    const supplierModal = bootstrap.Modal.getInstance(supplierModalEl);
                    if (supplierModal) supplierModal.hide();
                    supplierForm.reset();

                    // Re-open purchase modal if it was open before
                    const purchaseModalEl = document.getElementById('addPurchaseModal');
                    if (purchaseModalEl) {
                        const purchaseModal = new bootstrap.Modal(purchaseModalEl);
                        purchaseModal.show();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(data.message || 'Validation error');
                }
            })
            .catch(err => {
                btnSave.disabled = false;
                spinner.classList.add('d-none');
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error('Failed to save supplier. Please check inputs.');
            });
        });
    }

    // ── 5. AJAX: Save New Purchase ──
    const purchaseForm = document.getElementById('purchaseForm');
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const supplierId = document.getElementById('purchase_supplier_id').value;
            if (!supplierId) {
                if (typeof toastr !== 'undefined') toastr.warning('Please select a supplier.');
                return;
            }

            // Gather items
            const items = [];
            document.querySelectorAll('#purchaseItemsBody .item-row').forEach(row => {
                const prodSelect = row.querySelector('.item-product-select');
                const customName = row.querySelector('.item-custom-name')?.value;
                const specs = row.querySelector('.item-specs')?.value;
                const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
                const unitCost = parseFloat(row.querySelector('.item-unit-cost')?.value) || 0;
                const totalCost = qty * unitCost;

                if (qty > 0) {
                    items.push({
                        product_id: prodSelect?.value || null,
                        product_name: customName || (prodSelect?.value ? prodSelect.options[prodSelect.selectedIndex].getAttribute('data-name') : 'Raw Material'),
                        specs: specs || '',
                        quantity: qty,
                        unit_cost: unitCost,
                        total_cost: totalCost
                    });
                }
            });

            if (items.length === 0) {
                if (typeof toastr !== 'undefined') toastr.warning('Please add at least one item with valid quantity.');
                return;
            }

            const btnSave = document.getElementById('btnSavePurchase');
            const spinner = document.getElementById('savePurchaseSpinner');
            btnSave.disabled = true;
            spinner.classList.remove('d-none');

            const payload = {
                purchase_date: document.getElementById('purchase_date').value,
                delivery_date: document.getElementById('purchase_delivery_date').value,
                invoice_no: document.getElementById('purchase_invoice_no').value,
                supplier_id: supplierId,
                delivery_cost: parseFloat(document.getElementById('purchase_delivery_cost').value) || 0,
                payment_status: document.getElementById('purchase_payment_status').value,
                paid_amount: parseFloat(document.getElementById('purchase_paid_amount').value) || 0,
                note: document.getElementById('purchase_note').value,
                items: items
            };

            fetch('{{ route('admin.accounts.purchases.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btnSave.disabled = false;
                spinner.classList.add('d-none');

                if (data.success) {
                    if (typeof toastr !== 'undefined') toastr.success(data.message || 'Purchase recorded successfully!');

                    // Prepend new row into ledger table
                    const ledger = data.ledger;
                    const emptyRow = document.getElementById('emptyLedgerRow');
                    if (emptyRow) emptyRow.remove();

                    const newRowHtml = `
                        <tr id="ledger-row-${ledger.id}" class="table-success" style="transition: background-color 2s ease;">
                            <td>
                                <strong>${ledger.date}</strong>
                                ${ledger.invoice_no ? `<br><small class="text-muted"><i class="bx bx-hash"></i>${ledger.invoice_no}</small>` : ''}
                            </td>
                            <td>
                                <strong>${ledger.supplier ? ledger.supplier.name : 'N/A'}</strong>
                                ${ledger.supplier && ledger.supplier.company ? `<br><small class="text-muted"><i class="bx bx-building me-1"></i>${ledger.supplier.company}</small>` : ''}
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 280px;" title="${ledger.product_specs || ''}">
                                    ${ledger.product_specs || 'Purchased Goods'}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-info">${parseFloat(ledger.total_qty || 0).toFixed(2)}</span>
                            </td>
                            <td>৳ ${formatMoney(ledger.purchase_cost)}</td>
                            <td>৳ ${formatMoney(ledger.delivery_cost)}</td>
                            <td><span class="badge bg-label-success">৳ ${formatMoney(ledger.paid_amount)}</span></td>
                            <td>
                                ${parseFloat(ledger.due_amount) > 0 ?
                                    `<span class="badge bg-label-danger">৳ ${formatMoney(ledger.due_amount)}</span>` :
                                    `<span class="badge bg-label-secondary">৳ 0.00</span>`}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-info view-details-btn" data-id="${ledger.id}" title="View Details">
                                        <i class="bx bx-show-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger void-entry-btn" data-id="${ledger.id}" data-invoice="${ledger.invoice_no || ''}" title="Void Entry">
                                        <i class="bx bx-block me-1"></i> Void
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;

                    const tbody = document.getElementById('ledgerTableBody');
                    tbody.insertAdjacentHTML('afterbegin', newRowHtml);

                    // Fade back from table-success highlight
                    setTimeout(() => {
                        const tr = document.getElementById(`ledger-row-${ledger.id}`);
                        if (tr) tr.classList.remove('table-success');
                    }, 2500);

                    // Close Modal & Reset Form
                    const purchaseModalEl = document.getElementById('addPurchaseModal');
                    const purchaseModal = bootstrap.Modal.getInstance(purchaseModalEl);
                    if (purchaseModal) purchaseModal.hide();

                    purchaseForm.reset();
                    // Keep one clean row in the table
                    document.getElementById('purchaseItemsBody').innerHTML = '';
                    const template = document.getElementById('itemRowTemplate');
                    if (template) {
                        document.getElementById('purchaseItemsBody').appendChild(template.content.cloneNode(true));
                    }
                    calculateTotals();
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(data.message || 'Failed to record purchase.');
                }
            })
            .catch(err => {
                btnSave.disabled = false;
                spinner.classList.add('d-none');
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error('An error occurred while saving the purchase.');
            });
        });
    }

    // ── 6. AJAX: View Purchase & Ledger Details ──
    document.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-details-btn');
        if (viewBtn) {
            const id = viewBtn.getAttribute('data-id');
            const modalEl = document.getElementById('viewDetailsModal');
            const contentEl = document.getElementById('viewDetailsContent');
            const titleEl = document.getElementById('viewDetailsTitle');

            contentEl.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading purchase details...</p>
                </div>
            `;

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            fetch(`/admin/accounts/purchases/${id}/details`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.ledger) {
                    const l = data.ledger;
                    const p = l.purchase;
                    const s = l.supplier;

                    titleEl.innerHTML = `<i class="bx bx-receipt me-1"></i> Invoice: ${l.invoice_no || 'N/A'}`;

                    let itemsHtml = '';
                    if (p && p.items && p.items.length > 0) {
                        itemsHtml = p.items.map((item, idx) => `
                            <tr>
                                <td>${idx + 1}</td>
                                <td><strong>${item.product_name || (item.product ? item.product.name : 'Raw Material')}</strong></td>
                                <td>${item.specs || '-'}</td>
                                <td class="text-end">${parseFloat(item.quantity).toFixed(2)}</td>
                                <td class="text-end">৳ ${formatMoney(item.unit_cost)}</td>
                                <td class="text-end fw-bold">৳ ${formatMoney(item.total_cost)}</td>
                            </tr>
                        `).join('');
                    } else {
                        itemsHtml = `
                            <tr>
                                <td colspan="6" class="text-center text-muted">${l.product_specs || 'Single Ledger Entry'}</td>
                            </tr>
                        `;
                    }

                    contentEl.innerHTML = `
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Supplier Details</span>
                                    <h6 class="mb-0 fw-bold">${s ? s.name : 'N/A'}</h6>
                                    <small class="text-muted">${s && s.company ? s.company + ' | ' : ''} Phone: ${s && s.phone ? s.phone : 'N/A'}</small>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <span class="text-muted d-block">Purchase & Delivery Dates</span>
                                    <strong>Date:</strong> ${l.date}<br>
                                    ${p && p.delivery_date ? `<strong>Arrival:</strong> ${p.delivery_date}<br>` : ''}
                                    <span class="badge ${l.status === 'voided' ? 'bg-danger' : 'bg-success'}">${l.status.toUpperCase()}</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Specs</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHtml}
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush border rounded">
                                    <li class="list-group-item d-flex justify-content-between py-2">
                                        <span>Purchase Cost:</span>
                                        <strong>৳ ${formatMoney(l.purchase_cost)}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between py-2">
                                        <span>Delivery Cost:</span>
                                        <strong>৳ ${formatMoney(l.delivery_cost)}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between py-2 bg-light">
                                        <span class="fw-bold">Net Total:</span>
                                        <strong class="text-primary">৳ ${formatMoney(parseFloat(l.purchase_cost) + parseFloat(l.delivery_cost))}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between py-2">
                                        <span>Paid Amount:</span>
                                        <strong class="text-success">৳ ${formatMoney(l.paid_amount)}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between py-2">
                                        <span class="text-danger fw-bold">Due Amount:</span>
                                        <strong class="text-danger">৳ ${formatMoney(l.due_amount)}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        ${l.note ? `
                            <div class="mt-3 p-2 bg-light rounded border">
                                <small class="text-muted d-block fw-bold">Note / Remarks:</small>
                                <small>${l.note}</small>
                            </div>
                        ` : ''}
                    `;
                }
            })
            .catch(err => {
                contentEl.innerHTML = `<div class="alert alert-danger">Failed to load details.</div>`;
            });
        }
    });

    // ── 7. SweetAlert2: Void Ledger Entry ──
    document.addEventListener('click', function(e) {
        const voidBtn = e.target.closest('.void-entry-btn');
        if (voidBtn) {
            const id = voidBtn.getAttribute('data-id');
            const invoice = voidBtn.getAttribute('data-invoice') || '';

            const confirmTitle = 'Void this purchase entry?';
            const confirmText = invoice ?
                `Are you sure you want to void invoice "${invoice}"? Inventory stock additions and supplier due balance will be atomically reversed.` :
                'Are you sure you want to void this entry? Stock and dues will be reversed.';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: confirmTitle,
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#696cff',
                    confirmButtonText: 'Yes, Void Entry!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performVoid(id, voidBtn);
                    }
                });
            } else {
                if (confirm(confirmText)) {
                    performVoid(id, voidBtn);
                }
            }
        }
    });

    function performVoid(id, btnElement) {
        btnElement.disabled = true;

        fetch(`/admin/accounts/purchases/${id}/void`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof toastr !== 'undefined') toastr.success(data.message);

                const row = document.getElementById(`ledger-row-${id}`);
                if (row) {
                    row.classList.add('table-light', 'text-muted');
                    const actionCell = row.querySelector('td:last-child');
                    if (actionCell) {
                        actionCell.innerHTML = `
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-info view-details-btn" data-id="${id}" title="View Details">
                                    <i class="bx bx-show-alt"></i>
                                </button>
                                <span class="badge bg-label-secondary"><i class="bx bx-x-circle me-1"></i> Voided</span>
                            </div>
                        `;
                    }
                }
            } else {
                btnElement.disabled = false;
                if (typeof toastr !== 'undefined') toastr.error(data.message || 'Could not void entry.');
            }
        })
        .catch(err => {
            btnElement.disabled = false;
            console.error(err);
            if (typeof toastr !== 'undefined') toastr.error('Failed to void entry.');
        });
    }

    // Initial calculation on load
    calculateTotals();
})();
</script>
@endpush
@endsection