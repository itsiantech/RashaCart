

<div class="col-xxl">
    <div class="card">

        <!-- Card Header -->
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Inhouse Order Manage</h5>

                <div class="d-flex flex-wrap gap-2 align-items-center">

                    <!-- Search -->
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <input type="text"
                            class="form-control"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Search orders...">
                    </div>

                    <!-- Status Filters -->
                    <div class="btn-group flex-wrap" role="group">
                        <button type="button"
                            wire:click="filterByStatus('all')"
                            class="btn <?php echo e($statusFilter == 'all' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            All (<?php echo e(array_sum($statusCounts) ?? 0); ?>)
                        </button>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['pending','Call Not Received','completed', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button"
                            wire:click="filterByStatus('<?php echo e($status); ?>')"
                            class="btn <?php echo e($statusFilter == $status ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            <?php echo e(ucfirst($status)); ?> (<?php echo e($statusCounts[$status] ?? 0); ?>)
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive text-nowrap position-relative" style="min-height: 200px;">

            <!-- Loader -->
            <div wire:loading.flex
                wire:target="search,statusFilter,updateStatus,filterByStatus,sendToCourier"
                class="position-absolute top-50 start-50 translate-middle p-3 rounded shadow">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <table class="table table-hover mt-3">
                <thead>
                    <tr>
                        <th>INV</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total AMT</th>
                        <th>Payment Method</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_null($order->vendor_id)): ?>
                    <tr>

                        <!-- Invoice -->
                        <td><?php echo e($order->invoice_no); ?></td>

                        <!-- Customer -->
                        <td>
                            <?php echo e($order->name); ?> <br>
                            <span class="text-muted">
                                <a href="tel:<?php echo e($order->phone); ?>"><?php echo e($order->phone); ?></a>
                            </span><br>
                            <span class="text-muted">
                                Shipping addr: <?php echo e($order->shipping_address); ?>

                            </span>
                        </td>

                        <!-- Items -->
                        <td><?php echo e($order->items->count()); ?></td>

                        <!-- Total -->
                        <td>
                            <?php echo e(setting('currency')); ?>

                            <?php echo e(number_format($order->total_amount, 2)); ?>

                        </td>

                        <!-- Payment -->
                        <td>
                            <?php echo e($order->payment_method); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_amt): ?>
                            - <?php echo e(setting('currency')); ?> <?php echo e($order->payment_amt); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td>
                            <select wire:change="updateStatus(<?php echo e($order->id); ?>, $event.target.value)"
                                wire:loading.attr="disabled"
                                class="form-select form-select-sm">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['pending','Call Not Received','completed', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>"
                                    <?php echo e($order->status == $status ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($status)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </select>
                        </td>

                        <!-- Actions -->
                        <td>
                            <div class="d-flex gap-2">

                                <!-- View -->
                                <a href="<?php echo e(route('admin.order.view', $order->id)); ?>"
                                    class="btn btn-sm btn-info">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Print -->
                                <a href="<?php echo e(route('admin.orders.print', $order->id)); ?>"
                                    target="_blank"
                                    class="btn btn-sm btn-danger">
                                    <i class='bx bxs-printer'></i>
                                </a>

                                <!-- Courier -->
                                <?php
                                $pathaoDriver = new \App\Services\Couriers\PathaoDriver();
                                $steadfastDriver = new \App\Services\Couriers\SteadfastDriver();
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($order->status, ['shipped', 'delivered'])): ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pathaoDriver->canSend()): ?>
                                <button wire:click="sendToPathao('<?php echo e($order->id); ?>')"
                                    wire:loading.attr="disabled"
                                    wire:target="sendToPathao(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-warning">
                                    <span wire:loading.remove wire:target="sendToPathao(<?php echo e($order->id); ?>)">
                                        Pathao <i class='bx bxs-plane-alt'></i>
                                    </span>
                                    <span wire:loading wire:target="sendToPathao(<?php echo e($order->id); ?>)">
                                        Sending...
                                    </span>
                                </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($steadfastDriver->canSend()): ?>
                                <button wire:click="sendToSteadfast('<?php echo e($order->id); ?>')"
                                    wire:loading.attr="disabled"
                                    wire:target="sendToSteadfast(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-success">
                                    <span wire:loading.remove wire:target="sendToSteadfast(<?php echo e($order->id); ?>)">
                                        Steadfast <i class='bx bxs-plane-alt'></i>
                                    </span>
                                    <span wire:loading wire:target="sendToSteadfast(<?php echo e($order->id); ?>)">
                                        Sending...
                                    </span>
                                </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <!-- Mark Completed -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->status !== 'confirmed'): ?>
                                <button wire:click="markCompleted(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-success">
                                    Mark Completed
                                </button>
                                <?php else: ?>
                                <button class="btn btn-sm btn-success" disabled>
                                    Completed
                                </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>
                        </td>

                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            No orders found.
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-3">
                <?php echo e($orders->links()); ?>

            </div>

        </div>
    </div>
    <div
        x-data="{ show: false, message: '', type: '' }"
        x-on:success.window="message = $event.detail.message; type = 'success'; show = true; setTimeout(() => show = false, 4000)"
        x-on:error.window="message = $event.detail.message; type = 'danger'; show = true; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        x-cloak
        :class="`alert alert-${type} shadow`"
        style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        <span x-text="message"></span>
    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/order/order-inhouse.blade.php ENDPATH**/ ?>