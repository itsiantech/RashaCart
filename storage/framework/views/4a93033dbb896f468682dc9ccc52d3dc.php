<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title><?php echo $__env->yieldContent('title', setting('app_name')); ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #f0f0f0; 
            padding-bottom: 20px; 
            margin-bottom: 20px;
        }
        .logo { 
            max-width: 150px; 
            max-height: 50px; 
            margin-bottom: 15px;
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #f0f0f0; 
            color: #666; 
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="<?php echo e(asset('storage/logo/' . setting('app_logo'))); ?>" alt="<?php echo e(setting('app_name')); ?>" class="logo">
            <?php echo $__env->yieldContent('header'); ?>
        </div>

        <!-- Main Content -->
        <div class="content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">Thank you for choosing <strong><?php echo e(setting('app_name')); ?></strong>!</p>
            <p style="margin: 5px 0 0 0;">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(setting('app_name')); ?>. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\user\bandrose\resources\views\emails\template.blade.php ENDPATH**/ ?>