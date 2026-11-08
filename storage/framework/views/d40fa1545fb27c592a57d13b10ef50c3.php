<?php
$courier = new \App\Services\CourierService();
?>
<style>
    /* Order Manage: use the full content width instead of the narrow xxl container */
    .content-wrapper > .container-xxl:has(.order-manage-page) {
        max-width: 100% !important;
        width: 100% !important;
    }

    .order-manage-page .order-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.82rem;
    }

    .order-manage-page .order-table > :not(caption) > * > * {
        padding: 0.6rem 0.45rem;
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
    }

    .order-manage-page .order-table thead th {
        white-space: nowrap;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        color: #6b7280;
    }

    .order-manage-page .order-table .form-select {
        min-width: 125px;
    }

    /* Below xl: turn every row into a readable card (no horizontal scrolling) */
    @media (max-width: 1199.98px) {
        .order-manage-page .order-table thead {
            display: none;
        }

        .order-manage-page .order-table,
        .order-manage-page .order-table tbody,
        .order-manage-page .order-table tr,
        .order-manage-page .order-table td {
            display: block;
            width: 100%;
        }

        .order-manage-page .order-table tr {
            border: 1px solid #e5e7eb;
            border-radius: 0.65rem;
            margin-bottom: 0.75rem;
            padding: 0.35rem 0.75rem;
            background: #fff;
        }

        .order-manage-page .order-table td {
            display: flex;
            gap: 0.75rem;
            border: 0;
            padding: 0.35rem 0;
        }

        .order-manage-page .order-table td::before {
            content: attr(data-label);
            flex: 0 0 110px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            padding-top: 0.15rem;
        }

        .order-manage-page .order-table td[data-label="Actions"] {
            align-items: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .order-manage-page .order-table td {
            flex-direction: column;
            gap: 0.15rem;
        }

        .order-manage-page .order-table td::before {
            flex: none;
        }

        .order-manage-page .order-table .form-select {
            min-width: 0;
            width: 100%;
        }
    }
</style>
<div class="col-xxl order-manage-page">
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Order Manage</h5>

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

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button"
                            wire:click="filterByStatus('<?php echo e($status); ?>')"
                            class="btn <?php echo e($statusFilter == $status ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            <?php echo e(str_replace('_', ' ', ucfirst($status))); ?> (<?php echo e($statusCounts[$status] ?? 0); ?>)
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Channel Filter -->
                    <select class="form-select" style="max-width: 160px;"
                        wire:model.live="channelFilter" aria-label="Filter by channel">
                        <option value="all">All Channels</option>
                        <option value="checkout">Website</option>
                        <option value="cod">Popup COD</option>
                        <option value="whatsapp">Popup WhatsApp</option>
                    </select>

                    <?php echo \App\Support\Hooks::renderSlot('admin.orders.filters', ['af' => $af]); ?>

                    <button type="button" class="btn btn-outline-secondary" wire:click="exportCsv">
                        <i class="bx bx-download"></i> Export CSV
                    </button>

                </div>
            </div>
        </div>

        <div class="position-relative" style="min-height: 200px;">

            <!-- Loader -->
            <div wire:loading.flex
                wire:target="search,statusFilter,channelFilter,updateStatus,filterByStatus,sendToCourier,exportCsv"
                class="position-absolute top-50 start-50 translate-middle p-3 rounded shadow">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <table class="table table-hover mt-3 order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Customer</th>
                        <th>Origin</th>
                        <th>Vendor</th>
                        <th>Items</th>
                        <th>Total AMT</th>
                        <th>Payment Method</th>
                        <th>Channel</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $addonColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($col['label']); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <!-- Order ID -->
                        <td data-label="Order ID">
                            <strong>#<?php echo e($order->id); ?></strong><br>
                            <small class="text-muted"><?php echo e($order->invoice_no); ?></small>
                        </td>

                        <!-- Order Date -->
                        <td data-label="Order Date" title="<?php echo e($order->created_at?->format('d M Y, h:i A')); ?>">
                            <?php echo e($order->order_date_display); ?>

                        </td>

                        <!-- Customer -->
                        <td data-label="Customer">
                            <?php echo e($order->name); ?> <br>
                            <span class="text-muted">
                                <a href="tel:<?php echo e($order->phone); ?>"><?php echo e($order->phone); ?></a>
                            </span><br>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->email): ?>
                            <span class="text-muted"><?php echo e($order->email); ?></span><br>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="text-muted">
                                Shipping addr: <?php echo e($order->shipping_address); ?>

                            </span>
                        </td>

                        <!-- Origin -->
                        <td data-label="Origin">
                            <?php
                            $originBadge = match ($order->origin) {
                                'Google' => 'bg-danger',
                                'Facebook' => 'bg-primary',
                                'TikTok' => 'bg-dark',
                                'Direct' => 'bg-secondary',
                                default => 'bg-info',
                            };
                            ?>
                            <span class="badge <?php echo e($originBadge); ?>"><?php echo e($order->origin); ?></span>
                        </td>

                        <!-- Vendor -->
                        <td data-label="Vendor">
                            <?php
                            $vendors = $order->items
                            ->map(fn($item) => $item->product?->user)
                            ->filter()
                            ->unique('id');
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <span class="badge bg-info"><?php echo e($vendor->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <span class="badge bg-secondary">Admin</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>

                        <!-- Items -->
                        <td data-label="Items"><?php echo e($order->items->count()); ?></td>

                        <!-- Total -->
                        <td data-label="Total"><?php echo e(setting('currency')); ?> <?php echo e(number_format($order->total_amount, 2)); ?></td>

                        <!-- Payment -->
                        <td data-label="Payment Method">
                            <?php echo e($order->payment_method); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_amt): ?>
                            - <?php echo e(setting('currency')); ?> <?php echo e($order->payment_amt); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>

                        <!-- Channel -->
                        <td data-label="Channel">
                            <?php
                            $channelBadge = match ($order->channel) {
                                'cod' => ['Popup COD', 'bg-primary'],
                                'whatsapp' => ['Popup WhatsApp', 'bg-success'],
                                default => ['Website', 'bg-secondary'],
                            };
                            ?>
                            <span class="badge <?php echo e($channelBadge[1]); ?>"><?php echo e($channelBadge[0]); ?></span>
                        </td>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $addonColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td data-label="<?php echo e($col['label']); ?>"><?php echo \App\Support\Hooks::renderSlot('admin.orders.cell.'.$col['key'], ['order' => $order]); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <!-- Status -->
                        <td data-label="Order Status">
                            <select wire:change="updateStatus(<?php echo e($order->id); ?>, $event.target.value)"
                                wire:loading.attr="disabled"
                                class="form-select form-select-sm">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['pending','Call Not Received','completed', 'shipped', 'delivered', 'cancelled', 'returned','Pending Return','Pending Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php echo e($order->status == $status ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($status)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </td>

                        <!-- Actions -->
                        <td data-label="Actions">
                            <div class="d-flex gap-2 flex-wrap">

                                <!-- View -->
                                <a href="<?php echo e(route('admin.order.view', $order->id)); ?>"
                                    class="btn btn-sm btn-info"
                                    title="View Order">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Edit -->
                                <button type="button"
                                    wire:click="openEdit(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-warning"
                                    title="Edit Order">
                                    <i class="bx bx-edit"></i>
                                </button>

                                <!-- Print -->
                                <a href="<?php echo e(route('admin.orders.print', $order->id)); ?>"
                                    target="_blank"
                                    class="btn btn-sm btn-danger"
                                    title="Print Invoice">
                                    <i class='bx bxs-printer'></i>
                                </a>

                                <!-- Delete -->
                                <button type="button"
                                    wire:click="confirmDelete(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-danger"
                                    title="Delete Order">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <!-- Courier -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courier->canSend() && !in_array($order->status, ['shipped', 'delivered'])): ?>
                                <button type="button"
                                    wire:click="sendToCourier(<?php echo e($order->id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="sendToCourier(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-primary">
                                    <span wire:loading.remove wire:target="sendToCourier(<?php echo e($order->id); ?>)">
                                        Send to Courier <i class='bx bxs-plane-alt'></i>
                                    </span>
                                    <span wire:loading wire:target="sendToCourier(<?php echo e($order->id); ?>)">
                                        Sending...
                                    </span>
                                </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <!-- Mark Completed (Vendor Orders Only) -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->vendor): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->status !== 'completed'): ?>
                                <button type="button"
                                    wire:click="markCompleted(<?php echo e($order->id); ?>)"
                                    class="btn btn-sm btn-success">
                                    Mark Completed
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-success" disabled>Completed</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                <span class="badge bg-warning">Admin Order</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(10 + count($addonColumns)); ?>" class="text-center">No orders found.</td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-3"><?php echo e($orders->links()); ?></div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showEditModal): ?>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="modal <?php echo e($showEditModal ? 'show' : 'fade'); ?>"
        tabindex="-1"
        aria-modal="<?php echo e($showEditModal ? 'true' : 'false'); ?>"
        role="dialog"
        style="<?php echo e($showEditModal ? 'display:block;' : 'display:none;'); ?>">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Order #<?php echo e($editingOrder); ?></h5>
                    <button type="button" class="btn-close" wire:click="closeEdit"></button>
                </div>

                <div class="modal-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingOrder): ?> 
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <input type="text"
                                class="form-control <?php $__errorArgs = ['editForm.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.name"
                                placeholder="Enter customer name">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text"
                                class="form-control <?php $__errorArgs = ['editForm.phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.phone"
                                placeholder="Enter phone number">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Shipping Address -->
                        <div class="col-12">
                            <label class="form-label">Shipping Address</label>
                            <textarea class="form-control <?php $__errorArgs = ['editForm.shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.shipping_address"
                                placeholder="Enter shipping address"
                                rows="3"></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <input type="text"
                                class="form-control <?php $__errorArgs = ['editForm.payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.payment_method"
                                placeholder="e.g., Cash on Delivery">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Payment Amount -->
                        <div class="col-md-6">
                            <label class="form-label">Payment Amount</label>
                            <input type="number"
                                class="form-control <?php $__errorArgs = ['editForm.payment_amt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.payment_amt"
                                placeholder="0.00"
                                step="0.01">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.payment_amt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Total Amount -->
                        <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number"
                                class="form-control <?php $__errorArgs = ['editForm.total_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.total_amount"
                                placeholder="0.00"
                                step="0.01">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.total_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Order Status</label>
                            <select class="form-select <?php $__errorArgs = ['editForm.status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                wire:model="editForm.status">
                                <option value="">Select Status</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['pending', 'Call Not Received', 'completed', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned', 'Pending Return', 'Pending Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editForm.status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEdit">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveEdit">
                        <i class="bx bx-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\order\order-index.blade.php ENDPATH**/ ?>