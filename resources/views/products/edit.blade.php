{{-- resources/views/products/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Product • MyStore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{ background:#0b0f1a; color:#e5e7eb; }
    .card-glass{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:16px; }
    .form-control, .form-select { background:#0f1626; color:#e5e7eb; border-color:#1f2a3b; }
    .form-control:focus, .form-select:focus { background:#0f1626; color:#fff; }
    .thumb{ width:180px; height:auto; border-radius:12px; border:1px solid rgba(255,255,255,.12); }
    .muted{ color:#9fb1cc; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark" style="background:#0b1220;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">MyStore</a>
    <span class="navbar-text text-white-50">Edit Product</span>
  </div>
</nav>

<main class="container py-4">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card-glass p-4">
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $product->name) }}" required>
        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Price (₹)</label>
        <input type="number" step="0.01" name="price" class="form-control"
               value="{{ old('price', $product->price) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select">
          <option value="">-- Select --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ old('category', $product->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Discount Type</label>
          <select name="discount_type" class="form-select">
            <option value="">None</option>
            <option value="percent" {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>Percent</option>
            <option value="flat"    {{ old('discount_type', $product->discount_type) === 'flat' ? 'selected' : '' }}>Flat</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Discount Value</label>
          <input type="number" step="0.01" name="discount_value" class="form-control"
                 value="{{ old('discount_value', $product->discount_value) }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_discount_active" value="1"
                   {{ old('is_discount_active', $product->is_discount_active) ? 'checked' : '' }}>
            <label class="form-check-label">Discount Active</label>
          </div>
        </div>
      </div>

      <div class="mb-3 mt-3">
        <label class="form-label">Image</label>
        <input id="imageInput" type="file" name="image" class="form-control" accept="image/*">
        <div class="row mt-2 g-3">
          @if($product->image)
            <div class="col-auto">
              <div class="muted small">Current</div>
              <img src="{{ Storage::url($product->image) }}" alt="Current image" class="thumb">
            </div>
          @endif
          <div class="col-auto" id="previewWrap" style="display:none;">
            <div class="muted small">Preview</div>
            <img id="previewImg" class="thumb" alt="Preview">
          </div>
        </div>
        @error('image') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
      </div>

      <button class="btn btn-primary">Update Product</button>
      <a href="{{ url('/dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
  </div>
</main>

<script>
  const input = document.getElementById('imageInput');
  const wrap  = document.getElementById('previewWrap');
  const img   = document.getElementById('previewImg');

  input?.addEventListener('change', () => {
    const file = input.files && input.files[0];
    if (!file) { wrap.style.display = 'none'; img.src = ''; return; }
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      wrap.style.display = 'block';
    };
    reader.readAsDataURL(file);
  });
</script>
</body>
</html>
