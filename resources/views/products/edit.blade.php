{{-- resources/views/products/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Product - MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .upload-box { border: 3px dashed #ffc107; border-radius: 16px; padding: 40px; text-align: center; background: #fffbeb; transition: all .3s; cursor: pointer;}
    .upload-box:hover { background:#fff8d1; border-color:#ffb300; }
    .preview { max-height: 350px; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .current-img { max-height: 280px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
    #placeholder { color:#856404; }
  </style>
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm"><div class="container">
  <a class="navbar-brand fw-bold" href="{{ route('products.index') }}">MyStore</a>
</div></nav>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-body p-5">
          <h2 class="text-center mb-5 fw-bold text-warning">Edit Product</h2>

          <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            @if($product->image)
            <div class="text-center mb-5">
              <p class="text-muted mb-3 fw-bold">Current Photo:</p>
              <img src="{{ asset('storage/' . $product->image) }}" class="current-img img-fluid" alt="Current Image">
            </div>
            @endif

            <div class="mb-4">
              <label class="form-label fw-bold text-warning">Change Photo (Optional)</label>
              <div class="upload-box" onclick="document.getElementById('image').click()">
                <img id="preview" class="preview img-fluid mb-3" style="display:none;" alt="New Preview">
                <div id="placeholder">
                  <i class="bi bi-camera-fill display-3 text-warning mb-3"></i>
                  <p class="mb-2 fw-bold">Click to choose new photo</p>
                  <small class="text-muted">Leave empty to keep current photo</small>
                </div>
                <input type="file" name="image" id="image" accept="image/*" class="d-none">
              </div>
              @error('image') <div class="text-danger mt-2 fw-bold">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
              <div class="col-md-8">
                <input type="text" name="name" class="form-control form-control-lg rounded-pill"
                       value="{{ old('name', $product->name) }}" placeholder="Product Name" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-md-4">
                <input type="number" name="price" class="form-control form-control-lg rounded-pill"
                       value="{{ old('price', $product->price) }}" placeholder="Price ₹" required>
                @error('price') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-12">
                <select name="category" class="form-select form-select-lg rounded-pill" required>
                  <option value="">Choose Category</option>
                  @foreach(['Mobile Phones','Laptops','Fashion','Bikes','Fruits','Sports','Furniture','Books','Other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $product->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12">
                <textarea name="description" class="form-control rounded-3" rows="4"
                          placeholder="Product description (optional)">{{ old('description', $product->description) }}</textarea>
              </div>
              <div class="col-12 text-center mt-5">
                <button type="submit" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-lg">Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg px-5 py-3 rounded-pill ms-2">Cancel</a>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e){
  const f=e.target.files[0], p=document.getElementById('preview'), ph=document.getElementById('placeholder');
  if(f){ const r=new FileReader(); r.onload=ev=>{p.src=ev.target.result;p.style.display='block';ph.style.display='none';}; r.readAsDataURL(f);}
  else { p.style.display='none'; ph.style.display='block'; }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
