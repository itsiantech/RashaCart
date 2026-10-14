<?php $__env->startSection('page-title'); ?>
    Accounts | Office Operating Cost
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 m-0"><span class="text-muted fw-light">Accounts /</span> Office Operating Cost</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal"><i class="bx bx-plus"></i> Add New Cost Entry</button>
    </div>

    <!-- Expense Table -->
    <div class="card">
        <h5 class="card-header">Office Expenses Log</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cost Date</th>
                        <th>Costing Name</th>
                        <th>Amount</th>
                        <th>Purpose Note</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-10-05</td>
                        <td><strong>Office Rent (October)</strong></td>
                        <td>৳ 35,000</td>
                        <td>Monthly office premises rent payment</td>
                        <td><span class="badge bg-label-success">Active</span></td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></button>
                            <button class="btn btn-sm btn-icon btn-outline-warning" title="Void Entry"><i class="bx bx-block"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>2026-10-03</td>
                        <td><strong>Stationery Items</strong></td>
                        <td>৳ 2,400</td>
                        <td>Printer paper & pens purchase</td>
                        <td><span class="badge bg-label-success">Active</span></td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></button>
                            <button class="btn btn-sm btn-icon btn-outline-warning" title="Void Entry"><i class="bx bx-block"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add Expense -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Office Expense Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Costing Name *</label>
                            <input type="text" class="form-control" placeholder="e.g. Monihari / Tea & Snacks / Rent">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cost Date *</label>
                            <input type="date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (৳) *</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Purpose Note</label>
                            <textarea class="form-control" rows="2" placeholder="Brief note about the expense..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Cost Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('livewire.admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/accounts/office_expenses.blade.php ENDPATH**/ ?>