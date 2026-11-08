<!DOCTYPE html>
<html>

<head>
    <title>Registration Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <h2 class="fw-bold mb-3">Registration Successful 🎉</h2>
                <p class="text-muted">
                    Your vendor account is under review.
                    You will be able to log in once admin approves your request.
                </p>

                <a href="<?php echo e(route('vendor.login.form')); ?>" class="btn btn-danger mt-3">
                    Go to Vendor Login
                </a>
            </div>
        </div>
    </div>

</body>

</html><?php /**PATH C:\Users\user\bandrose\resources\views\auth\vendor-success.blade.php ENDPATH**/ ?>