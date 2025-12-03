{{-- resources/views/products/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $product->name }} - MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .product-img { height: 500px; object-fit: cover; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
    .price { font-size: 3rem; color: #28a745; }
    .btn-cart { background: linear-gradient(135deg, #ff9f00, #fb641b); border: none; padding: 14px 32px; font-size: 1.2rem; color:#fff; }
    .btn-buynow { background: linear-gradient(135deg, #28a745, #20c997); border: none; padding: 14px 40px; font-size: 1.25rem; font-weight: 700; color:#fff; }
    .back-btn { position: fixed; top: 20px; left: 20px; z-index: 9999; background: rgba(255,255,255,.95); color:#333; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.4rem; box-shadow:0 8px 25px rgba(0,0,0,.2); border:1px solid rgba(0,0,0,.08); }
    .back-btn:hover { background:#0d6efd; color:#fff; transform:scale(1.06); }
  </style>
</head>
<body>

<a href="{{ route('products.index') }}" class="back-btn" title="Back to Products"><i class="bi bi-arrow-left"></i></a>

<div class="container py-5">
  <div class="row g-5">
    <div class="col-lg-6">
      @if($product->image && \Storage::disk('public')->exists($product->image))
        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid product-img w-100" alt="{{ $product->name }}">
      @else
        <div class="bg-white d-flex align-items-center justify-content-center product-img w-100 shadow">
          <div class="text-center">
            <img src="https://img.icons8.com/ios-filled/120/0d6efd/box.png" alt="No image">
            <p class="mt-3 text-muted fs-5">No Image Available</p>
          </div>
        </div>
      @endif
    </div>

    <div class="col-lg-6">
      <h1 class="display-5 fw-bold mb-2">{{ $product->name }}</h1>
      <p class="lead text-muted mb-3">{{ $product->category }}</p>
      <div class="price fw-bold mb-4">₹{{ number_format($product->price) }}</div>

      <div class="bg-light p-4 rounded shadow-sm mb-4">
        <h4 class="fw-bold">Description</h4>
        <p class="fs-5 text-dark">{{ $product->description ?: 'No description available.' }}</p>
      </div>

      <div class="d-flex gap-3 flex-wrap">
        <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">@csrf
          <button type="submit" class="btn btn-cart rounded-pill shadow"> {{ session('cart.' . $product->id) ? 'Added to Cart' : 'Add to Cart' }} </button>
        </form>
        <form action="{{ route('checkout.single', $product->id) }}" method="GET" class="d-inline">
          <button type="submit" class="btn btn-buynow rounded-pill shadow">Buy Now</button>
        </form>
      </div>

      <div class="mt-4">
        <small class="text-muted">Added on {{ $product->created_at->format('d M Y') }}</small>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
  <div class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert">
    <div class="d-flex">
      <div class="toast-body fs-6 fw-bold">{{ session('success') }}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', ()=>{ const t = new bootstrap.Toast(document.querySelector('.toast.show'), { delay:3000 }); t.show(); });
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
