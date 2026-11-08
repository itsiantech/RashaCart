<?php $__env->startSection('title', 'Order Status Updated - ' . setting('app_name')); ?>

<?php $__env->startSection('header'); ?>
    <h2 style="margin: 10px 0 5px 0; color: #333;">Order Status Updated</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div style="font-family: Arial, sans-serif;">

    <p>Hello <strong><?php echo e($order->name); ?></strong>,</p>

    <!-- Dynamic Message Based on Status -->
    <?php
        $status = strtolower($order->status);

        $messages = [
            'pending' => 'We have received your order and it is waiting for confirmation.',
            'processing' => 'Your order is now being prepared.',
            'shipped' => 'Great news! Your order is on the way.',
            'delivered' => 'Your order has been successfully delivered.',
            'cancelled' => 'Unfortunately, your order has been cancelled.',
        ];

        $statusMessage = $messages[$status] ?? 'Your order status has been updated.';
    ?>

    <p><?php echo e($statusMessage); ?></p>


    <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px; margin: 15px 0;">
        <p style="margin: 0;"><strong>Order Number:</strong> <?php echo e($order->invoice_no); ?></p>
    </div>


    <div style="background: #f8f9fa; padding: 15px;
                border-radius: 6px; border-left: 4px solid #ff0000; margin: 20px 0;">
        <p style="margin: 0; font-size: 16px;">
            <strong>Current Status:</strong> 
            <span style="color: #ff0000; font-weight: bold; text-transform: capitalize;">
                <?php echo e($order->status); ?>

            </span>
        </p>
    </div>

    <p>We will notify you when there are further updates regarding your order.</p>

    <p>Thanks,<br>
        <strong><?php echo e(setting('app_name') ?? config('app.name')); ?></strong>
    </p>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\bandrose\resources\views\emails\orders\status.blade.php ENDPATH**/ ?>