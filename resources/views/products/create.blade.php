{{-- resources/views/products/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Product - MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .upload-box { border: 3px dashed #0d6efd; border-radius: 16px; padding: 40px; text-align: center; background: #f8fbff; transition: all 0.3s; cursor: pointer; }
    .upload-box:hover { background: #e7f0ff; border-color: #0b5ed7; }
    .preview { max-height: 320px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    #placeholder { color: #6c757d; }
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
          <h2 class="text-center mb-5 fw-bold text-primary">Add New Product</h2>

          <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
              <label class="form-label fw-bold text-dark">Product Photo <span class="text-danger">*</span></label>
              <div class="upload-box" onclick="document.getElementById('image').click()">
                <img id="preview" class="preview img-fluid mb-3" style="display:none;" alt="Preview">
                <div id="placeholder">
                  <i class="bi bi-cloud-upload display-3 text-primary mb-3"></i>
                  <p class="mb-2 fw-bold">Click to upload photo</p>
                  <small>Supports JPG, PNG, WebP (Max 5MB)</small>
                </div>
                <input type="file" name="image" id="image" accept="image/*" required class="d-none">
              </div>
              @error('image') <div class="text-danger mt-2 fw-bold">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
              <div class="col-md-8">
                <input type="text" name="name" class="form-control form-control-lg rounded-pill" placeholder="Enter product name" value="{{ old('name') }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-md-4">
                <input type="number" name="price" class="form-control form-control-lg rounded-pill" placeholder="Price in ₹" value="{{ old('price') }}" required>
                @error('price') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-12">
                <select name="category" class="form-select form-select-lg rounded-pill" required>
                  <option value="">Choose Category</option>
                  @foreach(['Mobile Phones','Laptops','Fashion','Bikes','Fruits','Sports','Furniture','Books','Other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                  @endforeach
                </select>
                @error('category') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-12">
                <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Write product description (optional)">{{ old('description') }}</textarea>
              </div>

              <div class="col-12 text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">Add Product</button>
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
  const file = e.target.files[0], preview = document.getElementById('preview'), placeholder = document.getElementById('placeholder');
  if(file){ const r=new FileReader(); r.onload=ev=>{preview.src=ev.target.result; preview.style.display='block'; placeholder.style.display='none';}; r.readAsDataURL(file);}
  else { preview.style.display='none'; placeholder.style.display='block';}
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
