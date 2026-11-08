<?php $__env->startSection('page-title'); ?>
    Accounts | Dashboard Overview
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts /</span> Dashboard Overview</h4>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label">Filter Period</label>
                    <select class="form-select">
                        <option>This Month</option>
                        <option>This Week</option>
                        <option>Today</option>
                        <option>This Year</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" value="<?php echo e(date('Y-m-01')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                </div>
                <div class="col-md-3 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt me-1"></i> Filter Stats</button>
                </div>
            </form>
        </div>
    </div>

    <!-- At a Glance KPI Cards -->
    <div class="row">
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card bg-label-primary text-center">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded-circle bg-primary"><i class="bx bx-dollar fs-3"></i></span></div>
                    <span class="d-block mb-1 text-muted">Total Sales</span>
                    <h5 class="card-title mb-0">৳ 1,250,000</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card bg-label-info text-center">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded-circle bg-info"><i class="bx bx-shopping-bag fs-3"></i></span></div>
                    <span class="d-block mb-1 text-muted">Order Amount</span>
                    <h5 class="card-title mb-0">৳ 1,180,000</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card bg-label-success text-center">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded-circle bg-success"><i class="bx bx-check-circle fs-3"></i></span></div>
                    <span class="d-block mb-1 text-muted">Paid Amount</span>
                    <h5 class="card-title mb-0">৳ 1,100,000</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card bg-label-warning text-center">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded-circle bg-warning"><i class="bx bx-time-five fs-3"></i></span></div>
                    <span class="d-block mb-1 text-muted">Due Amount</span>
                    <h5 class="card-title mb-0">৳ 80,000</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card bg-label-secondary text-center">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded-circle bg-secondary"><i class="bx bx-cart-download fs-3"></i></span></div>
                    <span class="d-block mb-1 text-muted">Purchase Cost</span>
                    <h5 class="card-title mb-0">৳ 720,000</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card bg-label-danger text-center">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded-circle bg-danger"><i class="bx bx-wallet fs-3"></i></span></div>
                    <span class="d-block mb-1 text-muted">Total Expenses</span>
                    <h5 class="card-title mb-0">৳ 145,000</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('livewire.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\accounts\dashboard.blade.php ENDPATH**/ ?>