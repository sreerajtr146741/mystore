<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Seller Dashboard • MyStore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:#f8f9fa; color:#212529; } /* Light theme for differentiation */
    .table thead th { font-weight:600; }
    .price-old { text-decoration: line-through; opacity:.7; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">MyStore <span class="badge bg-white text-primary ms-1">Seller</span></a>
    <div class="d-flex gap-3 align-items-center">
        <span class="text-white">Welcome, {{ auth()->user()->first_name }}</span>
        <a href="{{ route('seller.profile') }}" class="btn btn-outline-light btn-sm">
            <i class="bi bi-person-circle me-1"></i> Profile
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button class="btn btn-light btn-sm fw-bold text-primary">Logout</button>
        </form>
    </div>
  </div>
</nav>

<main class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">My Products</h2>
      <div class="d-flex gap-2">
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryDiscountModal">
            <i class="bi bi-percent me-1"></i> Manage Discounts
          </button>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Product
          </button>
      </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
      <div class="fw-semibold mb-1">Please fix the following errors:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow-sm border-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th class="text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
          @forelse($products as $p)
            <tr>
              <td class="ps-4">
                  @if($p->image)
                    <img src="{{ \Illuminate\Support\Str::startsWith($p->image, 'http') ? $p->image : asset('storage/'.$p->image) }}" 
                         class="rounded border bg-white" style="width:48px;height:48px;object-fit:cover;">
                  @else
                    <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-image text-muted"></i>
                    </div>
                  @endif
              </td>
              <td class="fw-semibold">{{ $p->name }}</td>
              <td>₹{{ number_format($p->price, 2) }}</td>
              <td>
                  <span class="badge {{ $p->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                      {{ $p->stock }} In Stock
                  </span>
              </td>
              <td>
                  <span class="badge bg-secondary">Active</span>
              </td>
              <td class="text-end pe-4">
                <button class="btn btn-sm btn-outline-primary me-1"
                   data-bs-toggle="modal" data-bs-target="#editProductModal"
                   data-id="{{ $p->id }}"
                   data-name="{{ $p->name }}"
                   data-price="{{ $p->price }}"
                   data-stock="{{ $p->stock }}"
                   data-description="{{ $p->description }}"
                   data-catid="{{ $p->category_id }}"
                >
                   <i class="bi bi-pencil"></i> Edit
                </button>
                
                <form class="d-inline" method="POST" action="{{ route('seller.products.destroy', $p->id) }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-5 text-muted">You haven't added any products yet.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
  </div>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
</main>

{{-- Category Discount Modal --}}
<div class="modal fade" id="categoryDiscountModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('seller.category.discount') }}">
      @csrf
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-percent me-2"></i>Category Discount Manager</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Tip:</strong> Apply discounts to all products in a category at once!
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold">Select Category</label>
          <select name="category_id" class="form-select" required>
            <option value="">Choose a category...</option>
            @foreach($categories->whereNull('parent_id') as $parent)
              <option value="{{ $parent->id }}" class="fw-bold">{{ $parent->name }} (All)</option>
              @foreach($categories->where('parent_id', $parent->id) as $child)
                <option value="{{ $child->id }}">→ {{ $child->name }}</option>
              @endforeach
            @endforeach
          </select>
          <div class="form-text">
            Select a parent category (e.g., Vehicles) to discount all items, or a specific subcategory (e.g., BMW)
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Discount Percentage</label>
          <div class="input-group">
            <input name="discount_percent" type="number" min="0" max="100" step="1" class="form-control" placeholder="e.g., 20" required>
            <span class="input-group-text">%</span>
          </div>
          <div class="form-text">Enter 0 to remove discount from this category</div>
        </div>

        <div class="card bg-light">
          <div class="card-body">
            <h6 class="card-title"><i class="bi bi-lightbulb text-warning me-2"></i>How it works:</h6>
            <ul class="small mb-0">
              <li><strong>Parent Category:</strong> Applies to ALL products in that category and its subcategories</li>
              <li><strong>Subcategory:</strong> Applies only to products in that specific subcategory</li>
              <li>Category discounts work alongside individual product discounts</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-success px-4 fw-bold">Apply Discount</button>
      </div>
    </form>
  </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input name="name" class="form-control" required placeholder="e.g. Wireless Headphones">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Price (₹)</label>
              <input name="price" type="number" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Stock Quantity</label>
              <input name="stock" type="number" min="0" class="form-control" required value="10">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach($categories->whereNull('parent_id') as $parent)
                    <optgroup label="{{ $parent->name }}">
                        @foreach($categories->where('parent_id', $parent->id) as $child)
                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Discount Percentage (Optional)</label>
            <div class="input-group">
                <input name="discount_value" type="number" min="0" max="100" step="1" class="form-control" placeholder="0">
                <span class="input-group-text">%</span>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="is_discount_active" id="discountActive" value="1">
                <label class="form-check-label" for="discountActive">Enable Discount</label>
            </div>
        </div>
        <div class="mb-3">
             <label class="form-label">Description</label>
             <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
             <label class="form-label">Product Image</label>
             <input type="file" name="image" id="createImageInput" class="form-control" accept="image/*" required>
             <div class="mt-3 text-center">
                 <img id="createImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
             </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary px-4 fw-bold">Create Product</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="editProductForm" class="modal-content" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input id="edit-name" name="name" class="form-control" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Price (₹)</label>
              <input id="edit-price" name="price" type="number" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Stock</label>
              <input id="edit-stock" name="stock" type="number" min="0" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select id="edit-category" name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach($categories->whereNull('parent_id') as $parent)
                    <optgroup label="{{ $parent->name }}">
                        @foreach($categories->where('parent_id', $parent->id) as $child)
                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Discount Percentage (Optional)</label>
            <div class="input-group">
                <input id="edit-discount" name="discount_value" type="number" min="0" max="100" step="1" class="form-control" placeholder="0">
                <span class="input-group-text">%</span>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="is_discount_active" id="editDiscountActive" value="1">
                <label class="form-check-label" for="editDiscountActive">Enable Discount</label>
            </div>
        </div>
        <div class="mb-3">
             <label class="form-label">Description</label>
             <textarea id="edit-description" name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
             <label class="form-label">Change Image (Optional)</label>
             <input type="file" name="image" id="editImageInput" class="form-control" accept="image/*">
             <div class="form-text">Leave empty to keep current image.</div>
             <div class="mt-3 text-center">
                 <img id="editImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
             </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary px-4 fw-bold">Update Changes</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  const editModal = document.getElementById('editProductModal');
  if (!editModal) return;

  editModal.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    if (!b) return;

    document.getElementById('edit-name').value  = b.dataset.name || '';
    document.getElementById('edit-price').value = b.dataset.price || '';
    document.getElementById('edit-stock').value = b.dataset.stock || '';
    document.getElementById('edit-description').value = b.dataset.description || '';
    document.getElementById('edit-category').value = b.dataset.catid || '';

    const form = document.getElementById('editProductForm');
    form.action = `{{ url('seller/products') }}/${b.dataset.id}`;
  });
})();

// Live Image Preview for Create Modal
document.getElementById('createImageInput')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById('createImagePreview');
  
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none';
  }
});

// Live Image Preview for Edit Modal
document.getElementById('editImageInput')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById('editImagePreview');
  
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none';
  }
});
</script>
</body>
</html>
