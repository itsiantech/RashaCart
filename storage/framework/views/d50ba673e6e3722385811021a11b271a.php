<div>
    <!-- Order Tracking Form -->
    <div class="order-tracking py-5">
        <div class="container">
            <h2 class="mb-4">Track Your Order</h2>

            <form wire:submit.prevent="trackOrder" class="mb-4">
                <div class="form-group mb-3">
                    <input type="text" wire:model.defer="invoiceNo" class="form-control" placeholder="Enter your invoice number" required>
                </div>
                <button type="submit" class="track-btn" >Track Order</button>
            </form>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order): ?>
                <div class="order-details mt-4">
                    <h4>Order Details (Invoice: <?php echo e($order->invoice_no); ?>)</h4>
                    <p><strong>Placed on:</strong> <?php echo e($order->created_at->format('d M Y')); ?></p>
                    <p><strong>Status:</strong> <span class="bg-danger text-light p-2"><?php echo e(ucfirst($order->status)); ?></span></p>                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->items->count()): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo e($item->product->thumbnail ? asset('storage/' . $item->product->thumbnail) : asset('assets/admin/img/avatars/user-avater.png')); ?>" width="50" class="img-thumbnail">
                                            </td>
                                            <td><?php echo e($item->product->name); ?></td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vAttr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="text-success me-1">
                                                            <?php echo e($vAttr->attribute->name ?? ''); ?>: <?php echo e($vAttr->attributeValue->value ?? ''); ?>

                                                        </span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td><?php echo e(setting('currency')); ?> <?php echo e(number_format($item->unit_price, 2)); ?></td>
                                            <td><?php echo e($item->quantity); ?></td>
                                            <td><?php echo e(setting('currency')); ?> <?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <?php
                                        $subtotal = $order->items->sum(fn($i) => $i->unit_price * $i->quantity);
                                        $delivery = $order->delivery_charge ?? 0;
                                        $paid = $order->pay_amt ?? 0;
                                        $total = $subtotal + $delivery;
                                        $due = $total - $paid;
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                        <td><?php echo e(setting('currency')); ?> <?php echo e(number_format($subtotal, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Delivery Charge:</td>
                                        <td><?php echo e(setting('currency')); ?><?php echo e(number_format($delivery, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total Amount:</td>
                                        <td><?php echo e(setting('currency')); ?><?php echo e(number_format($total, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Paid:</td>
                                        <td><?php echo e(setting('currency')); ?><?php echo e(number_format($paid, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold text-danger">Amount Due:</td>
                                        <td class="text-danger"><?php echo e(setting('currency')); ?><?php echo e(number_format($due, 2)); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/order-tracking.blade.php ENDPATH**/ ?>