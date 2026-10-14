<?php
$courier = new \App\Services\CourierService();
?>
<div class="container-fluid py-4">
    <!-- Header & Search -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-4">
            <h3 class="fw-bold text-dark">Seller Orders</h3>
            <p class="text-muted">Manage and track seller-specific orders</p>
        </div>
        <div class="col-md-8">
            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        class="form-control border-start-0 shadow-none"
                        placeholder="Invoice, Name or Phone...">
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Cards -->
    <div class="row g-3 mb-4">
        <?php
        $statuses = [
        'all' => ['label' => 'Total Orders', 'color' => 'primary', 'icon' => 'list-ul'],
        'pending' => ['label' => 'Pending', 'color' => 'warning', 'icon' => 'clock'],
        'shipped' => ['label' => 'Shipped', 'color' => 'info', 'icon' => 'truck'],
        'completed' => ['label' => 'Completed', 'color' => 'success', 'icon' => 'check-circle'],
        'cancelled' => ['label' => 'Cancelled', 'color' => 'danger', 'icon' => 'x-circle'],
        'returned' => ['label' => 'Returned', 'color' => 'secondary', 'icon' => 'arrow-counterclockwise'],
        ];
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-2 col-6">
            <div wire:click="filterByStatus('<?php echo e($key); ?>')"
                class="card border-0 shadow-sm cursor-pointer hover-shadow <?php echo e($statusFilter === $key ? 'ring-2 border-'.$meta['color'] : ''); ?>"
                style="cursor: pointer;">
                <div class="card-body p-3 text-center">
                    <div class="text-<?php echo e($meta['color']); ?> mb-1">
                        <i class="bi bi-<?php echo e($meta['icon']); ?> fs-4"></i>
                    </div>
                    <h6 class="mb-0 fw-bold"><?php echo e($key === 'all' ? array_sum($statusCounts) : ($statusCounts[$key] ?? 0)); ?></h6>
                    <small class="text-muted"><?php echo e($meta['label']); ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Invoice #</th>
                            <th>Seller</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?php echo e($order->invoice_no); ?></td>
                            <td>
                                <?php
                                $vendorProduct = $order->items->first()?->product;
                                $vendor = $vendorProduct?->user;
                                ?>

                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">
                                        <?php echo e($vendor?->vendorDetails?->shop_name ?? 'Unknown Shop'); ?>

                                    </span>

                                    <span class="small text-muted">
                                        <?php echo e($vendor?->name ?? 'N/A'); ?>

                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?php echo e($order->name); ?></span>
                                    <small class="text-muted"><?php echo e($order->phone); ?></small>
                                </div>
                            </td>

                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="fw-semibold text-primary">
                                    ৳<?php echo e(number_format($item->product?->sale_price ?? 0, 2)); ?> x <?php echo e($item->quantity); ?>

                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product?->discount_price): ?>
<div class="text-danger small mt-1">
    <i class="bx bx-discount"></i>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product?->discount_type == 'percentage'): ?>
        <?php echo e($item->product?->discount_price); ?>% OFF
    <?php else: ?>
        ৳<?php echo e(number_format($item->product?->discount_price, 2)); ?> OFF
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="text-muted small">
                                    Cost: ৳<?php echo e(number_format($item->product?->buying_price, 2)); ?>

                                </div>


                                <hr class="my-1">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <select class="form-select form-select-sm w-auto"
                                    wire:change="updateStatus(<?php echo e($order->id); ?>, $event.target.value)">
                                    <option value="pending" <?php echo e($order->status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                    <option value="shipped" <?php echo e($order->status == 'shipped' ? 'selected' : ''); ?>>Shipped</option>
                                    <option value="completed" <?php echo e($order->status == 'completed' ? 'selected' : ''); ?>>Completed</option>
                                    <option value="cancelled" <?php echo e($order->status == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                                    <option value="returned" <?php echo e($order->status == 'returned' ? 'selected' : ''); ?>>Returned</option>
                                </select>
                            </td>
                            <td><?php echo e($order->created_at->format('d M, Y')); ?></td>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courier->canSend() && !in_array($order->status, ['shipped', 'delivered'])): ?>
                                    <button wire:click="sendToCourier(<?php echo e($order->id); ?>)"
                                        wire:loading.attr="disabled"
                                        wire:target="sendToCourier(<?php echo e($order->id); ?>)"
                                        class="btn btn-sm btn-primary">
                                        <span wire:loading.remove
                                            wire:target="sendToCourier(<?php echo e($order->id); ?>)">
                                            Send to Courier <i class='bx bxs-plane-alt'></i>
                                        </span>
                                        <span wire:loading
                                            wire:target="sendToCourier(<?php echo e($order->id); ?>)">
                                            Sending...
                                        </span>
                                    </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                </div>
                            </td>

                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No orders found matching your criteria.
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <?php echo e($orders->links()); ?>

        </div>
    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/order/order-vendor.blade.php ENDPATH**/ ?>