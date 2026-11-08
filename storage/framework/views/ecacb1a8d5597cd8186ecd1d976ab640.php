<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo e($order->invoice_no); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 80mm;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header img {
            max-width: 100px;
            max-height: 40px;
        }
        .info {
            margin-bottom: 5px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        th, td {
            text-align: left;
            padding: 2px 0;
        }
        th {
            border-bottom: 1px solid #000;
        }
        .total {
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .center {
            text-align: center;
        }
        .variant {
            font-size: 11px;
            color: #555;
        }
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">

    <div class="header">
        <img src="<?php echo e(asset('storage/logo/' . setting('app_logo'))); ?>" alt="Logo"><br>
        <strong>Order No: #<?php echo e($order->id); ?></strong><br>
        <small>INV: <?php echo e($order->invoice_no); ?></small><br>
        <small><?php echo e($order->created_at?->format('d M Y, h:i A')); ?></small>
    </div>

    <div class="info">
        <p><strong><?php echo e($order->name); ?></strong></p>
        <p>📞 <a href="tel:<?php echo e($order->phone); ?>"><?php echo e($order->phone); ?></a></p>
        <p>📦 <?php echo e($order->shipping_address); ?></p>
        <p>Status: <strong><?php echo e(ucfirst($order->status)); ?></strong></p>
        <p>Shipping Method: <strong><?php echo e($order->shipping_method); ?></strong></p>
        <p>Shop Name: <?php echo e($order->items->first()?->product?->user?->vendorDetails?->shop_name ?? 'N/A'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="center">Qty</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <?php echo e($item->product->name); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                            <div class="variant">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($v->attribute->name); ?>: <?php echo e($v->attributeValue->value); ?><?php echo e(!$loop->last ? ',' : ''); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="center"><?php echo e($item->quantity); ?></td>
                    <td class="right">$<?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <div class="total">
        <p>Subtotal: $<?php echo e(number_format($order->items->sum(fn($i) => $i->unit_price * $i->quantity), 2)); ?></p>
        <p>Total: <strong>$<?php echo e(number_format($order->total_amount, 2)); ?></strong></p>
        <p>Paid: $<?php echo e(number_format($order->payment_amt ?? 0, 2)); ?></p>
        <p>Due: $<?php echo e(number_format(($order->total_amount - ($order->payment_amt ?? 0)), 2)); ?></p>
        <p>Payment: <?php echo e(ucfirst($order->payment_method)); ?></p>
    </div>

    <div class="center" style="margin-top:5px;">
        <small>Thank you for shopping with <?php echo e(setting('app_name') ?? 'Our Store'); ?>!</small>
    </div>
</body>
</html>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\admin\print\print-invoice.blade.php ENDPATH**/ ?>