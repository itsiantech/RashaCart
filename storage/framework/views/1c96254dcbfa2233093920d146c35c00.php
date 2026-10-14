<?php $__env->startSection('page-title'); ?>
    Accounts | Export Reports
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div>
    <h4 class="fw-bold py-3 mb-4 text-success"><i class="bx bx-file me-2"></i> Download Accounts Excel Statements</h4>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="bx bx-spreadsheet me-2"></i> Full Monthly Financial Ledger</h5>
                    <p class="card-text text-muted">Export complete record including Sales, Purchase Cost, Office Expenses, Tech & Marketing in .XLSX format.</p>
                    <form class="row g-2 mb-3">
                        <div class="col-6"><input type="date" class="form-control" value="<?php echo e(date('Y-m-01')); ?>"></div>
                        <div class="col-6"><input type="date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>"></div>
                    </form>
                    <button class="btn btn-success w-100"><i class="bx bx-download me-1"></i> Download Excel Report</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="bx bx-spreadsheet me-2"></i> Supplier Ledger Report</h5>
                    <p class="card-text text-muted">Export supplier-wise purchase history, weight/size specs and pending dues in Excel.</p>
                    <button class="btn btn-outline-primary w-100 mt-4"><i class="bx bx-download me-1"></i> Export Supplier Statement</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('livewire.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/accounts/export_reports.blade.php ENDPATH**/ ?>