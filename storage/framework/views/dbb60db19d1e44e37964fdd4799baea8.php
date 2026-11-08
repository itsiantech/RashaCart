<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label <?php echo e($order->invoice_no); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5mm;
            color: #333;
            background: white;
            font-size: 10px;
            line-height: 1.2;
        }
        .label-container {
            width: 80mm;
            height: 60mm;
            margin: 0 auto;
            border: 1px dashed #ccc;
            padding: 3mm;
            page-break-after: always;
        }
        .company-header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .company-name {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2mm;
        }
        .customer-info {
            margin-bottom: 2mm;
        }
        .customer-name {
            font-weight: bold;
            font-size: 11px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 2mm;
        }
        .items-table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 1mm;
            text-align: left;
        }
        .items-table td {
            border: 1px solid #ddd;
            padding: 1mm;
        }
        .price-column {
            text-align: right;
        }
        .totals {
            border-top: 1px solid #333;
            padding-top: 2mm;
            font-size: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .amount-due {
            font-weight: bold;
            color: #dc3545;
        }
        .delivery-info {
            background-color: #f8f9fa;
            padding: 1mm;
            border-radius: 2px;
            margin-bottom: 1mm;
            font-size: 9px;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .label-container { border: none; padding: 3mm; }
            .no-print { display: none !important; }
        }
        .labels-page {
            display: grid;
            grid-template-columns: repeat(2, 80mm);
            grid-gap: 5mm;
            justify-content: center;
        }
        .variant-info {
            font-size: 8px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body onload="window.print()">

<?php
    $subtotal = $order->items->sum(fn($item) => $item->unit_price * $item->quantity);
    $deliveryCharge = $order->delivery_charge ?? 0;
    $couponDiscount = $order->coupon_discount_amount ?? 0;
    $totalAfterDiscount = $subtotal - $couponDiscount;
    $grandTotal = $totalAfterDiscount + $deliveryCharge;
    $amountDue = $grandTotal - ($order->pay_amt ?? 0);
?>

<div class="labels-page">

    <!-- LOOP TWO LABELS -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 2; $i++): ?>
    <div class="label-container">

        <div class="company-header">
            <p class="company-name"><?php echo e(setting('app_name') ?? config('app.name')); ?></p>
            <p style="margin: 0; font-size: 9px;">Phone: +1234567890</p>
        </div>

        <div class="invoice-info">
            <div>
                <strong>Order No: #<?php echo e($order->id); ?></strong><br>
                INV: <?php echo e($order->invoice_no); ?><br>
                Date: <?php echo e($order->created_at?->format('d/m/Y, h:i A')); ?>

            </div>
            <div>
                Status: <?php echo e(strtoupper($order->status)); ?><br>
                Payment: <?php echo e($order->payment_method); ?><br>
                Shipping Method: <?php echo e($order->shipping_method); ?><br>
                Shop Name: <?php echo e($order->items->first()?->product?->user?->vendorDetails?->shop_name ?? 'N/A'); ?>

            </div>
        </div>

        <div class="customer-info">
            <div class="customer-name"><?php echo e($order->name); ?></div>
            <div>📞 <?php echo e($order->phone); ?></div>
            <div style="font-size: 8px;"><?php echo e($order->shipping_address); ?></div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->delivery_zone): ?>
            <div class="delivery-info">
                <strong>Delivery Zone:</strong> <?php echo e(ucfirst($order->delivery_zone)); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Sub</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <?php echo e($item->product->name ?? 'N/A'); ?>


                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant && $item->variant->variantAttributeValues): ?>
                            <div class="variant-info">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vAttr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($vAttr->attribute->name ?? ''); ?>:
                                    <?php echo e($vAttr->attributeValue->value ?? ''); ?>

                                    <?php echo e(!$loop->last ? ', ' : ''); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    <td style="text-align: center;"><?php echo e($item->quantity); ?></td>
                    <td class="price-column"><?php echo e(setting('currency')); ?><?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td class="price-column"><?php echo e(setting('currency')); ?><?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span><?php echo e(setting('currency')); ?><?php echo e(number_format($subtotal, 2)); ?></span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->coupon_code): ?>
            <div class="total-row">
                <span>Coupon (<?php echo e($order->coupon_code); ?>):</span>
                <span>- <?php echo e(setting('currency')); ?><?php echo e(number_format($couponDiscount, 2)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($deliveryCharge > 0): ?>
            <div class="total-row">
                <span>Delivery Charge:</span>
                <span>+ <?php echo e(setting('currency')); ?><?php echo e(number_format($deliveryCharge, 2)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="total-row">
                <span><strong>Total:</strong></span>
                <span><strong><?php echo e(setting('currency')); ?><?php echo e(number_format($grandTotal, 2)); ?></strong></span>
            </div>

            <div class="total-row">
                <span>Paid:</span>
                <span><?php echo e(setting('currency')); ?><?php echo e(number_format($order->pay_amt ?? 0, 2)); ?></span>
            </div>

            <div class="total-row amount-due">
                <span>Due:</span>
                <span><strong><?php echo e(setting('currency')); ?><?php echo e(number_format($amountDue, 2)); ?></strong></span>
            </div>
        </div>

    </div>
    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div class="no-print" style="text-align: center; margin-top: 20px;">
    <button onclick="window.print()" style="padding: 5px 10px; font-size: 12px;">Print Label</button>
    <button onclick="window.close()" style="padding: 5px 10px; font-size: 12px;">Close</button>
</div>

<script>
    window.onafterprint = () => setTimeout(() => window.close(), 500);
</script>

</body>
</html>
<?php /**PATH C:\Users\user\bandrose\resources\views\print\invoice-print.blade.php ENDPATH**/ ?>