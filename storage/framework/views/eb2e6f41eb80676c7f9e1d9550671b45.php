<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .thank-you-box {
            margin-top: 80px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .thank-you-icon {
            font-size: 3rem;
            color: #ffc107;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="thank-you-box text-center col-md-8">
        <div class="thank-you-icon mb-3">
            😊
        </div>
        <h1 class="mb-3">Thank You!</h1>
        <p class="fs-5">Your payment has been successfully processed.</p>
        <?php if(isset($orderId)): ?>
            <p class="text-muted">Order ID: <strong><?php echo e($orderId); ?></strong></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
<?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/success.blade.php ENDPATH**/ ?>