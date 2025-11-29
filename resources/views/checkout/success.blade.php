<!DOCTYPE html>
<html>
<head>
    <title>Payment Success - MyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="text-center p-5 bg-white rounded-4 shadow-lg">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 100px;"></i>
        <h1 class="display-4 fw-bold text-success mt-4">Payment Successful!</h1>
        <p class="lead text-muted mb-5">Thank you for your purchase. Your order is confirmed.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5 rounded-pill">
            Continue Shopping
        </a>
    </div>
</body>
</html>