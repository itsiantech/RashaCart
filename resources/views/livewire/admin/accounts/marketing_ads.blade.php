@extends('livewire.admin.layouts.app')

@section('page-title')
    Accounts | Marketing & Ads Cost
@endsection

@section('content')
<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts /</span> Ads & Marketing Expenses</h4>

    <div class="row">
        <!-- Monthly Summary Cards -->
        <div class="col-md-3 mb-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <span class="d-block text-muted">Facebook Ads</span>
                    <h4 class="card-title text-primary mb-0">৳ 45,000</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <span class="d-block text-muted">Google Ads</span>
                    <h4 class="card-title text-danger mb-0">৳ 22,000</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <span class="d-block text-muted">Influencer / PR</span>
                    <h4 class="card-title text-warning mb-0">৳ 15,000</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <span class="d-block text-muted">Total Ads Spend</span>
                    <h4 class="card-title text-success mb-0">৳ 82,000</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Ad Expense Log -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Date-wise Marketing Campaign Spend Note</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMarketingExpenseModal">
                <i class="bx bx-plus me-1"></i> Add Ads & Marketing Expenses
            </button>
        </div>
        <div class="card-body">
            <!-- Search/Filter Options -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Filter By</label>
                    <select class="form-select" id="filterType">
                        <option value="all">All</option>
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="date">Date-wise</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="filterExpenses()">
                        <i class="bx bx-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Platform</th>
                        <th>Campaign Name</th>
                        <th>Amount Spent</th>
                        <th>Campaign Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-10-04</td>
                        <td><span class="badge bg-primary">Facebook</span></td>
                        <td>New Arrival Campaign Boost</td>
                        <td>৳ 12,000</td>
                        <td>Targeted Dhaka & Chittagong audience</td>
                        <td><button class="btn btn-sm btn-outline-primary"><i class="bx bx-edit"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Marketing Expense Modal -->
<div class="modal fade" id="addMarketingExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Ads & Marketing Expenses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="marketingExpenseForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Platform</label>
                            <select class="form-select" name="platform" required>
                                <option value="">Select Platform</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Google">Google</option>
                                <option value="Instagram">Instagram</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="TikTok">TikTok</option>
                                <option value="Influencer">Influencer / PR</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Campaign Name</label>
                            <input type="text" class="form-control" name="campaign_name" placeholder="Enter campaign name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount Spent (৳)</label>
                            <input type="number" class="form-control" name="amount_spent" placeholder="Enter amount" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Campaign Note</label>
                            <input type="text" class="form-control" name="campaign_note" placeholder="Enter campaign note">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMarketingExpense()">Save Expense</button>
            </div>
        </div>
    </div>
</div>

<script>
function filterExpenses() {
    const filterType = document.getElementById('filterType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    console.log('Filter Type:', filterType);
    console.log('Start Date:', startDate);
    console.log('End Date:', endDate);
    
    // Add your filtering logic here
}

function saveMarketingExpense() {
    const form = document.getElementById('marketingExpenseForm');
    const formData = new FormData(form);
    
    console.log('Form Data:', Object.fromEntries(formData));
    
    // Add your save logic here
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('addMarketingExpenseModal'));
    modal.hide();
}
</script>
@endsection