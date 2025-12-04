{{-- resources/views/seller/manage-products.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Products • Seller</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap & Icons (CDN) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- CSRF meta for any JS needs --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { background:#0b0c10; color:#e9ecef; }
    .table thead th { color:#adb5bd; font-weight:600; }
    .modal-content { color:#212529; }
    .price-old { text-decoration: line-through; opacity:.7; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">MyStore</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
      <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
        <button class="btn btn-warning btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
      </form>
    </div>
  </div>
</nav>

<main class="container py-4">
  <h2 class="mb-3">Manage Products</h2>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <div class="fw-semibold mb-1">
        <i class="bi bi-exclamation-triangle me-1"></i>There were some problems:
      </div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form class="row g-2 mb-3" method="GET" action="">
    <div class="col-sm-8">
      <input name="q" class="form-control" placeholder="Search by name or ID" value="{{ $q ?? '' }}">
    </div>
    <div class="col-sm-4 text-end">
      <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
        <i class="bi bi-plus-circle me-1"></i>Add Product
      </a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>Price</th><th>Stock</th><th>Seller</th><th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      @forelse($products as $p)
        @php
          $base = (float)($p->price ?? 0);
          $final = $base;
          $now = \Illuminate\Support\Carbon::now();
          $inWindow = true;
          if (!empty($p->discount_starts_at) && !empty($p->discount_ends_at)) {
            $inWindow = $now->between($p->discount_starts_at, $p->discount_ends_at);
          }
          if (($p->is_discount_active ?? false) && ($p->discount_type ?? null) && ($p->discount_value ?? null) && $inWindow) {
            $dv = (float)$p->discount_value;
            $final = $p->discount_type === 'percent'
              ? max(0, $base - ($base * ($dv / 100)))
              : max(0, $base - $dv);
          }
        @endphp
        <tr>
          <td>{{ $p->id }}</td>
          <td>{{ $p->name }}</td>
          <td>
            @if($final < $base)
              <span class="price-old">₹ {{ number_format($base, 2) }}</span>
              <strong class="ms-1">₹ {{ number_format($final, 2) }}</strong>
              <span class="badge bg-success ms-2">
                @if(($p->discount_type ?? null) === 'percent')
                  -{{ rtrim(rtrim(number_format((float)$p->discount_value,2), '0'), '.') }}%
                @else
                  -₹{{ number_format((float)$p->discount_value,2) }}
                @endif
              </span>
            @else
              ₹ {{ number_format($base, 2) }}
            @endif
          </td>
          <td>{{ $p->stock }}</td>
          <td>{{ optional($p->user)->name ?? '-' }}</td>
          <td class="text-end">
            <form class="d-inline" method="POST" action="{{ route('seller.products.destroy', $p) }}">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete product?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>

            {{-- EDIT BUTTON with discount data attributes --}}
            <button class="btn btn-sm btn-outline-primary"
               data-bs-toggle="modal" data-bs-target="#editProductModal"
               data-id="{{ $p->id }}"
               data-name="{{ $p->name }}"
               data-price="{{ $p->price }}"
               data-stock="{{ $p->stock }}"
               data-discount_type="{{ $p->discount_type }}"
               data-discount_value="{{ $p->discount_value }}"
               data-discount_starts="{{ optional($p->discount_starts_at)->format('Y-m-d\TH:i') }}"
               data-discount_ends="{{ optional($p->discount_ends_at)->format('Y-m-d\TH:i') }}"
               data-discount_active="{{ ($p->is_discount_active ?? false) ? 1 : 0 }}"
            >
               <i class="bi bi-pencil-square"></i>
            </button>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">No products found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-3">
    {{ $products->withQueryString()->links() }}
  </div>
</main>

{{-- Create Modal --}}
<div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('seller.products.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Add Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Price (₹)</label>
          <input name="price" type="number" step="0.01" min="0" class="form-control" value="{{ old('price') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Stock</label>
          <input name="stock" type="number" min="0" class="form-control" value="{{ old('stock') }}" required>
        </div>

        {{-- DISCOUNT FIELDS (Create) --}}
        <div class="mb-3">
          <label class="form-label">Discount Type</label>
          <select name="discount_type" class="form-select">
            <option value="">None</option>
            <option value="percent" @selected(old('discount_type')==='percent')>Percent (%)</option>
            <option value="flat" @selected(old('discount_type')==='flat')>Flat (₹)</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Discount Value</label>
          <input name="discount_value" type="number" step="0.01" min="0" class="form-control" value="{{ old('discount_value') }}">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Discount Starts</label>
            <input name="discount_starts_at" type="datetime-local" class="form-control" value="{{ old('discount_starts_at') }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Discount Ends</label>
            <input name="discount_ends_at" type="datetime-local" class="form-control" value="{{ old('discount_ends_at') }}">
          </div>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="is_discount_active" value="1" id="discActiveCreate" @checked(old('is_discount_active'))>
          <label class="form-check-label" for="discActiveCreate">Discount Active</label>
        </div>
        {{-- /DISCOUNT FIELDS --}}
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editProductForm" class="modal-content" method="POST">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input id="edit-name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Price (₹)</label>
          <input id="edit-price" name="price" type="number" step="0.01" min="0" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Stock</label>
          <input id="edit-stock" name="stock" type="number" min="0" class="form-control" required>
        </div>

        {{-- DISCOUNT FIELDS (Edit) --}}
        <div class="mb-3">
          <label class="form-label">Discount Type</label>
          <select id="edit-discount-type" name="discount_type" class="form-select">
            <option value="">None</option>
            <option value="percent">Percent (%)</option>
            <option value="flat">Flat (₹)</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Discount Value</label>
          <input id="edit-discount-value" name="discount_value" type="number" step="0.01" min="0" class="form-control">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Discount Starts</label>
            <input id="edit-discount-starts" name="discount_starts_at" type="datetime-local" class="form-control">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Discount Ends</label>
            <input id="edit-discount-ends" name="discount_ends_at" type="datetime-local" class="form-control">
          </div>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="is_discount_active" value="1" id="discActiveEdit">
          <label class="form-check-label" for="discActiveEdit">Discount Active</label>
        </div>
        {{-- /DISCOUNT FIELDS --}}
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

{{-- Bootstrap JS (bundle includes Popper) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
  const editModal = document.getElementById('editProductModal');
  if (!editModal) return;

  editModal.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    if (!b) return;

    // basic fields
    document.getElementById('edit-name').value  = b.dataset.name || '';
    document.getElementById('edit-price').value = b.dataset.price || '';
    document.getElementById('edit-stock').value = b.dataset.stock || '';

    // discount fields
    document.getElementById('edit-discount-type').value   = b.dataset.discount_type ?? '';
    document.getElementById('edit-discount-value').value  = b.dataset.discount_value ?? '';
    document.getElementById('edit-discount-starts').value = b.dataset.discount_starts ?? '';
    document.getElementById('edit-discount-ends').value   = b.dataset.discount_ends ?? '';
    document.getElementById('discActiveEdit').checked     = (b.dataset.discount_active === '1');

    // Set action to seller update route (PUT /seller/products/{id})
    const form = document.getElementById('editProductForm');
    form.action = `{{ url('seller/products') }}/${b.dataset.id}`;
  });
})();
</script>
</body>
</html>
