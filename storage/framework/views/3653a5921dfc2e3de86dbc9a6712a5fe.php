

<div>

    
    <div class="mb-3">
        <h1 style="font-size:15px; font-weight:600; color:#111827; margin:0 0 2px;">Bulk Invoice Print</h1>
        <p style="font-size:13px; color:#6b7280; margin:0;">Select orders below and print invoices in bulk</p>
    </div>

    
    <div class="bip-card">
        <p class="bip-section-title">Filters</p>

        
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-4">
                <label class="bip-label">Search</label>
                <input wire:model.live.debounce.400ms="search"
                    type="text" placeholder="Order #, Invoice #, Phone…"
                    class="bip-input" />
            </div>
            <div class="col-12 col-sm-4">
                <label class="bip-label">Order status</label>
                <select wire:model.live="status" class="bip-input">
                    <option value="">All statuses</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>"><?php echo e(ucfirst($s)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label class="bip-label">Print status</label>
                <select wire:model.live="printingStatus" class="bip-input">
                    <option value="">All</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $printingStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
        </div>

        
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-3">
                <label class="bip-label">Product SKU</label>
                <input wire:model.live.debounce.400ms="productSku"
                    type="text" placeholder="e.g. SKU123…"
                    class="bip-input" />
            </div>
            <div class="col-12 col-sm-3">
                <label class="bip-label">Product name</label>
                <input wire:model.live.debounce.400ms="productName"
                    type="text" placeholder="e.g. Laptop…"
                    class="bip-input" />
            </div>
            <div class="col-12 col-sm-3">
                <label class="bip-label">Date from</label>
                <input wire:model.live="dateFrom" type="date" class="bip-input" />
            </div>
            <div class="col-12 col-sm-3">
                <label class="bip-label">Date to</label>
                <input wire:model.live="dateTo" type="date" class="bip-input" />
            </div>
        </div>

        
        <div class="d-flex flex-wrap align-items-end gap-3">
            <div>
                <label class="bip-label">Qty min</label>
                <input wire:model.live="qtyMin" type="number" min="0" placeholder="0"
                    class="bip-input" style="width:110px;" />
            </div>
            <div>
                <label class="bip-label">Qty max</label>
                <input wire:model.live="qtyMax" type="number" min="0" placeholder="999"
                    class="bip-input" style="width:110px;" />
            </div>
            <div class="ms-auto">
                <button wire:click="resetFilters" class="bip-reset-btn">Reset filters</button>
            </div>
        </div>
    </div>

    
    <div class="bip-card">
        <div class="bip-toolbar">
            <div class="d-flex align-items-center gap-2">
                <span class="bip-selected-count"><?php echo e(count($selectedOrders)); ?></span>
                <span class="bip-selected-label">order(s) selected</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedOrders) > 0): ?>
                <span style="color:#d1d5db;">·</span>
                <button wire:click="clearSelection" class="bip-clear-btn">Clear</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <form id="printForm" method="POST" action="<?php echo e(route('admin.invoices.bulk-print-store')); ?>">
                <?php echo csrf_field(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $selectedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="ids[]" value="<?php echo e($id); ?>" />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="submit"
                    <?php if(count($selectedOrders)===0): echo 'disabled'; endif; ?>
                    onclick="if (<?php echo e(count($selectedOrders)); ?> > 0) { this.form.target='_blank'; }"
                    class="bip-print-btn <?php echo e(count($selectedOrders) > 0 ? 'active' : 'disabled'); ?>">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <rect x="3" y="1" width="10" height="7" rx="1" fill="currentColor" opacity="0.6" />
                        <rect x="1" y="5" width="14" height="7" rx="1.5" fill="currentColor" />
                        <rect x="4" y="10" width="8" height="5" rx="1" fill="currentColor" opacity="0.6" />
                    </svg>
                    Print selected invoices
                </button>
            </form>
        </div>
    </div>

    
    <div class="bip-table-wrap">
        <div class="table-responsive">
            <table class="bip-table">
                <thead>
                    <tr>
                        <th style="width:42px;">
                            <input type="checkbox" wire:model.live="selectAll" />
                        </th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Print</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="<?php echo e(in_array((string)$order->id, array_map('strval', $selectedOrders)) ? 'selected' : ''); ?>"
                        x-bind:class="{ 'selected': $wire.selectedOrders.includes('<?php echo e($order->id); ?>') }">
                        <td>
                            <input type="checkbox"
                                wire:model.live="selectedOrders"
                                value="<?php echo e($order->id); ?>" />
                        </td>
                        <td><span class="bip-order-no"><?php echo e($order->order_number); ?></span></td>
                        <td><?php echo e($order->name ?? '—'); ?></td>
                        <td><span class="bip-date"><?php echo e($order->created_at->format('d M Y')); ?></span></td>
                        <td>
                            <?php
                            $statusClass = match($order->status) {
                            'completed' => 'bip-badge-completed',
                            'pending' => 'bip-badge-pending',
                            'cancelled' => 'bip-badge-cancelled',
                            default => 'bip-badge-default',
                            };
                            ?>
                            <span class="bip-badge <?php echo e($statusClass); ?>">
                                <?php echo e(ucfirst($order->status)); ?>

                            </span>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->is_printed): ?>
                            <span class="bip-badge bip-badge-printed">Printed</span>
                            <?php else: ?>
                            <span class="bip-badge bip-badge-notprinted">Not printed</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>TK<?php echo e(number_format($order->total_amount, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="bip-empty">No orders found.</td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="bip-pagination-wrap">
            <span class="bip-pagination-info">
                Showing <?php echo e($orders->firstItem() ?? 0); ?>–<?php echo e($orders->lastItem() ?? 0); ?>

                of <?php echo e($orders->total()); ?> orders
            </span>
            <?php echo e($orders->links()); ?>

        </div>
    </div>
    <style>
        .bip-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
            display: block;
        }

        .bip-section-title {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .bip-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 10px;
        }

        .bip-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .bip-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .bip-reset-btn {
            font-size: 12px;
            color: #9ca3af;
            background: none;
            border: none;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            transition: color 0.15s;
        }

        .bip-reset-btn:hover {
            color: #ef4444;
        }

        /* Toolbar */
        .bip-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .bip-selected-count {
            font-size: 22px;
            font-weight: 600;
            color: #4f46e5;
            line-height: 1;
        }

        .bip-selected-label {
            font-size: 13px;
            color: #6b7280;
        }

        .bip-clear-btn {
            font-size: 12px;
            color: #9ca3af;
            background: none;
            border: none;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            transition: color 0.15s;
        }

        .bip-clear-btn:hover {
            color: #ef4444;
        }

        .bip-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }

        .bip-print-btn.active {
            background: #4f46e5;
            color: #fff;
        }

        .bip-print-btn.active:hover {
            background: #4338ca;
        }

        .bip-print-btn.disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Table */
        .bip-table-wrap {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .bip-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .bip-table thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .bip-table thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }

        .bip-table thead th:last-child {
            text-align: right;
        }

        .bip-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.1s;
        }

        .bip-table tbody tr:last-child {
            border-bottom: none;
        }

        .bip-table tbody tr:hover {
            background: #f9fafb;
        }

        .bip-table tbody tr.selected {
            background: #eef2ff;
        }

        .bip-table tbody tr.selected:hover {
            background: #e0e7ff;
        }

        .bip-table td {
            padding: 10px 14px;
            vertical-align: middle;
            color: #374151;
        }

        .bip-table td:last-child {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }

        .bip-order-no {
            font-family: monospace;
            font-size: 12px;
            font-weight: 600;
            color: #1f2937;
        }

        .bip-date {
            font-size: 12px;
            color: #9ca3af;
        }

        .bip-empty {
            padding: 40px 16px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }

        /* Badges */
        .bip-badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 500;
            white-space: nowrap;
        }

        .bip-badge-completed {
            background: #dcfce7;
            color: #166534;
        }

        .bip-badge-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .bip-badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .bip-badge-default {
            background: #f3f4f6;
            color: #4b5563;
        }

        .bip-badge-printed {
            background: #dbeafe;
            color: #1e40af;
        }

        .bip-badge-notprinted {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Pagination */
        .bip-pagination-wrap {
            padding: 10px 14px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .bip-pagination-info {
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</div><?php /**PATH C:\Users\user\bandrose\resources\views/livewire/admin/order/bulk-invoice-print.blade.php ENDPATH**/ ?>