{{-- resources/views/seller/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seller Dashboard • MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('seller.dashboard') }}">MyStore</a>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">My Products</a>
  </div>
</nav>

<div class="container py-5">
  <h1 class="display-6 fw-bold text-primary">Seller Dashboard</h1>

  <div class="row mt-4">
    <div class="col-md-4">
      <div class="card p-4 shadow">
        <h3 class="h5 text-muted mb-2">Total Products</h3>
        <h2 class="text-primary">{{ $productsCount }}</h2>
      </div>
    </div>
  </div>

  <a href="{{ route('seller.my-products') }}" class="btn btn-primary mt-4">Manage Products →</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
