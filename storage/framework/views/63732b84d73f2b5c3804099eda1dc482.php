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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(session()->has('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <?php
                $totalEarned = round($earnings[$vendor->id] ?? 0, 2);

                $totalWithdrawn = $withdrawnTotals[$vendor->id] ?? 0;

                $available = $totalEarned - $totalWithdrawn;
                ?>

                <tr>
                    <td><?php echo e($vendor->name); ?></td>
                    <td>TK <?php echo e(number_format($totalEarned, 2)); ?></td>
                    <td>
                        <span>TK <?php echo e(number_format($totalWithdrawn, 2)); ?></span>
                        <span class="<?php echo e($vendor->payouts->last()?->status == 'approved' ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($vendor->payouts->last()?->status ?? 'N/A'); ?>

                        </span>
                    </td>
                    <td>
                        <div>
                            <span>TK <?php echo e(number_format($available, 2)); ?></span>

                            <span class="<?php echo e($available >= 500 ? 'text-success' : 'text-danger'); ?>">
                                <?php echo e($available >= 500 ? 'Available' : 'Minimum 500 TK required'); ?>

                            </span>

                            <button
                                type="button"
                                wire:click.prevent="withdraw(<?php echo e($vendor->id); ?>)"
                                class="border border-dark text-black px-2 py-0.5 bg-white mx-2 rounded"
                                <?php if($available < 500): ?> disabled <?php endif; ?>>
                                Withdraw
                            </button>
                        </div>
                    </td>
                </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center">No vendors found.</td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        <?php echo e($vendors->links()); ?>

    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/vendor/vendor-payout.blade.php ENDPATH**/ ?>