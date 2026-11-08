<div class="card p-3">

    
    <div class="row mb-3">

        <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">

            <div>
                <h4>Invoice #<?php echo e($order->invoice_no); ?></h4>
                <p class="mb-0">
                    <strong>Order No:</strong> #<?php echo e($order->id); ?>

                    <span class="ms-3"><strong>Date:</strong> <?php echo e($order->created_at?->format('d M Y, h:i A')); ?></span>
                </p>
                <p class="mb-0"><strong>Shipping Method:</strong> <?php echo e($order->shipping_method); ?></p>
                <p class="mb-0"><strong>Customer:</strong> <?php echo e($order->name); ?></p>
                <p class="mb-0"><strong>Phone:</strong> <a href="tel:<?php echo e($order->phone); ?>"><?php echo e($order->phone); ?></a></p>
                <p class="mb-0"><strong>Shipping:</strong> <?php echo e($order->shipping_address); ?></p>
                <p class="mb-0"><strong>Note:</strong> <?php echo e($order->note ?? '-'); ?></p>
                <p class="mb-0"><strong>Shop Name:</strong> <?php echo e($order->items->first()?->product?->user?->vendorDetails?->shop_name ?? 'N/A'); ?></p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->coupon_code): ?>
                <p class="mb-0 mt-1"><strong>Coupon:</strong>
                    <?php echo e($order->coupon_code); ?>

                    ( -<?php echo e(setting('currency')); ?><?php echo e(number_format($order->coupon_discount_amount, 2)); ?> )
                </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="text-end">
                <div class="card bg-light">
                    <div class="card-body py-2">

                        <?php
                        $subtotal = $order->items->sum(fn($i) => $i->unit_price * $i->quantity);
                        $delivery = $order->delivery_charge ?? 0;
                        $couponDiscount = $order->coupon_discount_amount ?? 0;
                        $paid = $order->pay_amt ?? 0;

                        // Total after coupon discount
                        $afterDiscount = $subtotal - $couponDiscount;

                        // Final Grand Total
                        $total = $afterDiscount + $delivery;

                        // Due Amount
                        $due = $total - $paid;
                        ?>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Subtotal:</span>
                            <strong><?php echo e(setting('currency')); ?> <?php echo e(number_format($subtotal, 2)); ?></strong>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->coupon_code): ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Coupon (<?php echo e($order->coupon_code); ?>):</span>
                            <strong class="text-success">- <?php echo e(setting('currency')); ?>

                                <?php echo e(number_format($couponDiscount, 2)); ?></strong>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="d-flex justify-content-between mb-1">
                            <span>After Discount:</span>
                            <strong><?php echo e(setting('currency')); ?> <?php echo e(number_format($afterDiscount, 2)); ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Delivery Charge:</span>
                            <strong><?php echo e(setting('currency')); ?> <?php echo e(number_format($delivery, 2)); ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span><strong>Total Amount:</strong></span>
                            <strong><?php echo e(setting('currency')); ?> <?php echo e(number_format($total, 2)); ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Paid:</span>
                            <span><?php echo e(setting('currency')); ?> <?php echo e(number_format($paid, 2)); ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Amount Due:</span>
                            <strong class="text-danger">
                                <?php echo e(setting('currency')); ?> <?php echo e(number_format($due, 2)); ?>

                            </strong>
                        </div>

                        <a href="<?php echo e(route('admin.orders.print', $order->id)); ?>" target="_blank"
                            class="btn btn-primary btn-sm w-100 mt-1">
                            <i class="bx bx-printer"></i> Print Invoice
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>



    
    
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th width="60">Image</th>
                    <th>Product</th>
                    <th>Variant</th>
                    <th width="110" class="text-end">Unit Price</th>
                    <th width="120" class="text-center">Quantity</th>
                    <th width="110" class="text-end">Subtotal</th>
                    <th width="70" class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    
                    <td>
                        <img src="<?php echo e($item->display_image); ?>" width="40" class="img-thumbnail">
                    </td>

                    
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product): ?>
                        <div class="fw-semibold">
                            <?php echo e($item->product->name); ?>

                        </div>
                        <small class="text-muted">
                            SKU: <?php echo e($item->product->sku ?? 'N/A'); ?>

                        </small>
                        <?php else: ?>
                        <span class="text-danger">
                            Product Deleted
                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant->variantAttributeValues->count()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vAttr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vAttr->attribute && $vAttr->attributeValue): ?>
                        <span class="badge bg-secondary me-1">
                            <?php echo e($vAttr->attribute->name); ?>: <?php echo e($vAttr->attributeValue->value); ?>

                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                        <span class="text-muted">No attributes</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php elseif($item->variant_name): ?>
                        <span class="badge bg-secondary me-1"><?php echo e($item->variant_name); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_sku): ?><small class="text-muted d-block"><?php echo e($item->variant_sku); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                        -
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td class="text-end fw-semibold">
                        <?php echo e(setting('currency')); ?>

                        <?php echo e(number_format($item->unit_price, 2)); ?>

                    </td>

                    
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                wire:click="decreaseQuantity(<?php echo e($item->id); ?>)">
                                -
                            </button>

                            <span class="mx-2">
                                <?php echo e($item->quantity); ?>

                            </span>

                            <button
                                class="btn btn-sm btn-outline-secondary"
                                wire:click="increaseQuantity(<?php echo e($item->id); ?>)">
                                +
                            </button>
                        </div>
                    </td>

                    
                    <td class="text-end fw-semibold">
                        <?php echo e(setting('currency')); ?>

                        <?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?>

                    </td>

                    
                    <td class="text-center">
                        <button
                            class="btn btn-sm btn-outline-danger"
                            wire:click="removeItem(<?php echo e($item->id); ?>)"
                            title="Remove">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No items found for this order.
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>


    
    <div class="mb-3">
        <label class="form-label">Add Product</label>
        <input type="text" class="form-control mb-2" placeholder="Search product..."
            wire:model.live.debounce.300ms="searchProduct">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchResults): ?>
        <ul class="list-group mb-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex justify-content-between align-items-center"
                wire:click="selectProduct(<?php echo e($product->id); ?>)" style="cursor:pointer;">
                <?php echo e($product->name); ?> -
                <?php echo e(setting('currency')); ?><?php echo e(number_format($product->price, 2)); ?>

            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct): ?>
        <div class="mb-2">
            <p><strong>Selected Product:</strong> <?php echo e($selectedProduct->name); ?></p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedProduct->variants->count()): ?>
            <label>Variant:</label>
            <select class="form-select mb-2" wire:model="selectedVariant">
                <option value="">Select Variant</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $selectedProduct->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($variant->id); ?>">
                    <?php echo e($variant->sku); ?> -
                    <?php echo e(setting('currency')); ?><?php echo e(number_format($variant->price, 2)); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="d-flex gap-2">
                <input type="number" class="form-control w-auto" min="1" wire:model="newItemQuantity">
                <button class="btn btn-success" wire:click="addItem">Add Item</button>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php echo \App\Support\Hooks::renderSlot('admin.order.details.panels', ['order' => $order]); ?>

</div><?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\order\order-view.blade.php ENDPATH**/ ?>