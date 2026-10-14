<?php $__env->startSection('page-title'); ?>
    Accounts | Salary & Investment
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts /</span> Salary Payroll & Investments</h4>

    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#navs-payroll"><i class="bx bx-user me-1"></i> Employee Payroll</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#navs-investor"><i class="bx bx-briefcase me-1"></i> Investor Capital & Share</button></li>
        </ul>
        <div class="tab-content">
            <!-- Tab 1: Payroll -->
            <div class="tab-pane fade show active" id="navs-payroll">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Employee Name</th><th>Designation</th><th>Monthly Salary</th><th>Paid Date</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><strong>Rahim Ahmed</strong></td>
                                <td>Store Manager</td>
                                <td>৳ 25,000</td>
                                <td>2026-10-01</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Tab 2: Investor -->
            <div class="tab-pane fade" id="navs-investor">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Investor Name</th><th>Total Investment</th><th>Investment Date</th><th>Profit Share %</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><strong>Hasif Chowdhury</strong></td>
                                <td>৳ 500,000</td>
                                <td>2025-06-15</td>
                                <td>15%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('livewire.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/accounts/payroll_investment.blade.php ENDPATH**/ ?>