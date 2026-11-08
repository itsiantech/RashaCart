<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="#">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">customer</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Index</li>
            </ol>
        </nav>

        <div>

            

        </div>
    </div>
    <div class="card py-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 px-3 px-sm-4 mb-4">

            <div class="d-flex flex-column flex-sm-row align-items-center gap-3 w-100 w-sm-auto">
                <form class="flex-grow-1 flex-sm-grow-0" role="search" autocomplete="off">
                    <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" placeholder="Search..." wire:model.live.debounce.300ms="search"
                            class="form-control ps-5" style="max-width: 300px;">
                    </div>
                </form>
            </div>
        </div>

        <!-- customers Table -->
        <div class="table-responsive px-3 px-sm-4">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Name</th>
                        <th class="text-center text-nowrap">Order Success Rate</th>
                        <th class="text-center text-nowrap">Status</th>
                        <th class="text-center text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-nowrap">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">

                                        <img src="<?php echo e($customer->image ? asset('storage/' . $customer->image) : asset('assets/admin/img/avatars/user-avater.png')); ?>"
                                            alt="customer image" class="img-fluid">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?php echo e($customer->name); ?></div>
                                        <small class="text-muted">
                                            <div>Email: <?php echo e($customer->email); ?></div>
                                            <div>Phone: <?php echo e($customer->phone); ?></div>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="order-stats-container">
                                    <!-- Total Order Count -->
                                    <div class="total-orders mb-2">
                                        <strong><?php echo e($customer->orders->count()); ?></strong>
                                    </div>

                                    <!-- Status Progress Bar -->
                                    <div class="status-progress-bar">
                                        <?php
                                            $statuses = [
                                                'pending',
                                                'processing',
                                                'shipped',
                                                'delivered',
                                                'cancelled',
                                                'returned',
                                            ];
                                            $statusCounts = [];
                                            $totalOrders = $customer->orders->count();

                                            foreach ($statuses as $status) {
                                                $statusCounts[$status] = $customer->orders
                                                    ->where('status', $status)
                                                    ->count();
                                            }
                                        ?>

                                        <div class="progress-bar-container">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusCounts[$status] > 0): ?>
                                                    <?php
                                                        $percentage =
                                                            $totalOrders > 0
                                                                ? ($statusCounts[$status] / $totalOrders) * 100
                                                                : 0;
                                                        $colors = [
                                                            'pending' => '#ffc107',
                                                            'processing' => '#17a2b8',
                                                            'shipped' => '#007bff',
                                                            'delivered' => '#28a745',
                                                            'cancelled' => '#dc3545',
                                                            'returned' => '#6c757d',
                                                        ];
                                                    ?>
                                                    <div class="progress-segment"
                                                        style="width: <?php echo e($percentage); ?>%; background-color: <?php echo e($colors[$status]); ?>;"
                                                        title="<?php echo e(ucfirst($status)); ?>: <?php echo e($statusCounts[$status]); ?> (<?php echo e(number_format($percentage, 1)); ?>%)">
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        <!-- Status Legend -->
                                        <div class="status-legend mt-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusCounts[$status] > 0): ?>
                                                    <span class="status-badge status-<?php echo e($status); ?> me-1"
                                                        title="<?php echo e(ucfirst($status)); ?>: <?php echo e($statusCounts[$status]); ?>">
                                                        <?php echo e($statusCounts[$status]); ?>

                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <?php $__env->startPush('styles'); ?>
                            <style>
                                .order-stats-container {
                                    min-width: 120px;
                                }

                                .total-orders {
                                    font-size: 1.1rem;
                                    color: #2c3e50;
                                }

                                .progress-bar-container {
                                    height: 8px;
                                    background-color: #e9ecef;
                                    border-radius: 4px;
                                    display: flex;
                                    overflow: hidden;
                                }

                                .progress-segment {
                                    height: 100%;
                                    transition: all 0.3s ease;
                                    cursor: help;
                                }

                                .progress-segment:hover {
                                    opacity: 0.8;
                                    transform: scaleY(1.2);
                                }

                                .status-legend {
                                    display: flex;
                                    flex-wrap: wrap;
                                    justify-content: center;
                                    gap: 2px;
                                }

                                .status-badge {
                                    font-size: 0.7rem;
                                    padding: 1px 4px;
                                    border-radius: 3px;
                                    color: white;
                                    font-weight: bold;
                                    cursor: help;
                                }

                                .status-pending {
                                    background-color: #ffc107;
                                }

                                .status-processing {
                                    background-color: #17a2b8;
                                }

                                .status-shipped {
                                    background-color: #007bff;
                                }

                                .status-delivered {
                                    background-color: #28a745;
                                }

                                .status-cancelled {
                                    background-color: #dc3545;
                                }

                                .status-returned {
                                    background-color: #6c757d;
                                }
                            </style>
                            <?php $__env->stopPush(); ?>
                            <td class="text-center">

                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="statusSwitch<?php echo e($customer->id); ?>" <?php if($customer->status === 1): echo 'checked'; endif; ?>
                                        wire:click="toggleStatus(<?php echo e($customer->id); ?>)">
                                </div>

                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    
                                    <button type="button" class="btn btn-sm btn-danger" aria-label="Delete customer"
                                        onclick="if(confirm('Are you sure you want to delete this customer?')) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').deletecustomer(<?php echo e($customer->id); ?>) }">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No customers found.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>

            <div class="mt-4 mb-3">
                <?php echo e($customers->links()); ?>

            </div>
        </div>

    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/customer.blade.php ENDPATH**/ ?>