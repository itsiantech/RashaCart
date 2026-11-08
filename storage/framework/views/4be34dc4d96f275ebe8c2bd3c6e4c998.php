<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Invoices - <?php echo e(date('Y-m-d H:i')); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .invoice-page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto 20px;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            page-break-after: always;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            @page {
                margin: 5mm;
            }
            
            body {
                margin: 0;
                padding: 0;
            }

            .invoice-page {
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                page-break-after: always;
            }

            .no-print {
                display: none;
            }
        }

        /* ── Header ────────────────────────────────────── */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 15px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-section img {
            max-width: 50px;
            max-height: 50px;
        }

        .company-info h1 {
            font-size: 16px;
            margin: 0;
            color: #1a1a1a;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta p {
            margin: 2px 0;
            font-size: 11px;
        }

        .invoice-no {
            font-size: 14px;
            font-weight: bold;
            color: #f97316;
        }

        /* ── Customer Info ────────────────────────────── */
        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 5px;
        }

        .customer-section div p {
            margin: 2px 0;
        }

        .customer-section label {
            font-weight: bold;
            font-size: 11px;
            color: #666;
        }

        /* ── Items Table ───────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background: #f3f4f6;
            border-top: 2px solid #d1d5db;
            border-bottom: 2px solid #d1d5db;
        }

        th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            color: #374151;
        }

        td {
            padding: 8px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .qty-col, .price-col {
            text-align: right;
        }

        .variant-info {
            font-size: 10px;
            color: #999;
            margin-top: 2px;
            font-style: italic;
        }

        /* ── Totals Section ───────────────────────────── */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .totals-box {
            width: 280px;
            border: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .total-row:last-child {
            border-bottom: none;
        }

        .total-row label {
            font-weight: normal;
            color: #666;
        }

        .total-row.grand-total {
            background: #f97316;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .total-row.grand-total label {
            color: white;
        }

        .total-amount {
            font-weight: bold;
        }

        /* ── Status & Notes ────────────────────────────– */
        .status-notes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .status-box {
            padding: 8px 12px;
            background: #f3f4f6;
            border-radius: 3px;
        }

        .status-box label {
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── Footer ────────────────────────────────────– */
        .invoice-footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .divider-page {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
            page-break-after: always;
        }

        /* ── Print Controls ────────────────────────────– */
        .print-controls {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.2s;
        }

        .btn-print {
            background: #f97316;
            color: white;
        }

        .btn-print:hover {
            background: #ea580c;
        }

        .btn-back {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-back:hover {
            background: #d1d5db;
        }

        @media print {
            .print-controls {
                display: none;
            }
        }
    </style>
</head>
<body>

    
    <div class="print-controls no-print">
        <button class="btn btn-print" onclick="window.print()">🖨️ Print All Invoices</button>
        <button class="btn btn-back" onclick="window.history.back()">← Back</button>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="invoice-page">
            
            <div class="invoice-header">
                <div class="logo-section">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(setting('app_logo')): ?>
                        <img src="<?php echo e(asset('storage/logo/' . setting('app_logo'))); ?>" alt="Logo">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="company-info">
                        <h1><?php echo e(setting('app_name', 'Bandrose')); ?></h1>
                        <p><?php echo e(setting('app_email') ?? 'support@bandrose.com'); ?></p>
                        <p><?php echo e(setting('app_phone') ?? '+1 234 567 8900'); ?></p>
                    </div>
                </div>
                <div class="invoice-meta">
                    <p class="invoice-no">Order No: #<?php echo e($order->id); ?></p>
                    <p class="invoice-no">Invoice #<?php echo e($order->invoice_no); ?></p>
                    <p><?php echo e($order->created_at?->format('d M Y')); ?></p>
                    <p>Time: <?php echo e($order->created_at?->format('h:i A')); ?></p>
                    <p>Shipping Method: <?php echo e($order->shipping_method); ?></p>
                </div>
            </div>

            
            <div class="customer-section">
                <div>
                    <label>Bill To:</label>
                    <p><strong><?php echo e($order->name); ?></strong></p>
                    <p>📞 <?php echo e($order->phone); ?></p>
                    <p><?php echo e($order->email ?? '—'); ?></p>
                </div>
                <div>
                    <label>Shipping Address:</label>
                    <p><?php echo e($order->shipping_address); ?></p>
                    <p><?php echo e($order->shipping_city ?? '—'); ?></p>
                    <p><?php echo e($order->shipping_state ?? '—'); ?> <?php echo e($order->shipping_zip ?? '—'); ?></p>
                </div>
            </div>

            
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Product</th>
                        <th class="qty-col" style="width: 15%;">Qty</th>
                        <th class="price-col" style="width: 15%;">Price</th>
                        <th class="price-col" style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product): ?>
                        <tr>
                            <td>
                                <strong><?php echo e(Str::limit($item->product->name, 50)); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant && $item->variant->variantAttributeValues): ?>
                                    <div class="variant-info">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->variant->variantAttributeValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v->attribute && $v->attributeValue): ?>
                                            <span><?php echo e($v->attribute->name); ?>: <?php echo e($v->attributeValue->value); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?><br><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="qty-col"><?php echo e($item->quantity); ?></td>
                            <td class="price-col">$<?php echo e(number_format($item->unit_price, 2)); ?></td>
                            <td class="price-col">$<?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?></td>
                        </tr>
                        <?php else: ?>
                        <tr style="opacity: 0.6;">
                            <td><em>Product deleted</em></td>
                            <td class="qty-col"><?php echo e($item->quantity); ?></td>
                            <td class="price-col">$<?php echo e(number_format($item->unit_price, 2)); ?></td>
                            <td class="price-col">$<?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>

            
            <div class="totals-section">
                <div class="totals-box">
                    <div class="total-row">
                        <label>Subtotal:</label>
                        <span>$<?php echo e(number_format($order->items->sum(fn($i) => $i->unit_price * $i->quantity), 2)); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount_amount): ?>
                        <div class="total-row">
                            <label>Discount:</label>
                            <span>-$<?php echo e(number_format($order->discount_amount, 2)); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->shipping_fee): ?>
                        <div class="total-row">
                            <label>Shipping:</label>
                            <span>$<?php echo e(number_format($order->shipping_fee, 2)); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->tax): ?>
                        <div class="total-row">
                            <label>Tax:</label>
                            <span>$<?php echo e(number_format($order->tax, 2)); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="total-row grand-total">
                        <label>TOTAL:</label>
                        <span class="total-amount">$<?php echo e(number_format($order->total_amount, 2)); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="status-notes">
                <div class="status-box">
                    <label>Order Status:</label>
                    <span class="status-badge status-<?php echo e($order->status); ?>">
                        <?php echo e(ucfirst($order->status)); ?>

                    </span>
                </div>
                <div class="status-box">
                    <label>Payment Method:</label>
                    <p><?php echo e(ucfirst($order->payment_method)); ?></p>
                </div>
                <div class="status-box">
                    <label>Amount Paid:</label>
                    <p>$<?php echo e(number_format($order->payment_amt ?? 0, 2)); ?></p>
                </div>
                <div class="status-box">
                    <label>Due Amount:</label>
                    <p>$<?php echo e(number_format(($order->total_amount - ($order->payment_amt ?? 0)), 2)); ?></p>
                </div>
            </div>

            
            <div class="invoice-footer">
                <p>Thank you for your business!</p>
                <p><?php echo e(setting('app_name', 'Bandrose')); ?> © <?php echo e(date('Y')); ?></p>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
            <div class="divider-page">📄 Next Invoice</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 5px;">
            <p>No invoices to display.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script>
        // Auto-trigger print dialog on page load (optional - comment out if not desired)
        // window.onload = () => setTimeout(() => window.print(), 500);
    </script>

</body>
</html>
<?php /**PATH C:\Users\user\bandrose\resources\views\admin\invoices\bulk-print.blade.php ENDPATH**/ ?>