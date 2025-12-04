{{-- resources/views/admin/products/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Product • Admin • MyStore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body{ background:#070c14; color:#e6edf7; }
    .navbar{ background: linear-gradient(90deg, #0a1426, #0b1220 40%, #0b1220); border-bottom:1px solid rgba(255,255,255,.08); }
    .card-dark {
        background:#0f1626;
        border:1px solid #22304a;
        border-radius:16px;
        color:#e6edf7;
        padding:25px;
    }
    label { font-weight:600; color:#c9d4e3; }
    .form-control, .form-select {
        background:#0d1526 !important;
        border:1px solid #21314a !important;
        color:#e6edf7 !important;
        border-radius:12px;
    }
    .form-control:focus, .form-select:focus {
        border-color:#3b82f6 !important;
        box-shadow:0 0 0 .15rem rgba(59,130,246,.25) !important;
    }
    .btn-save {
        background:linear-gradient(135deg,#22d3ee,#60a5fa);
        border:0; font-weight:700;
        border-radius:12px;
        padding:10px 24px;
        color:#07101a !important;
        box-shadow:0 10px 22px rgba(96,165,250,.25);
    }
    .img-preview {
        width:140px;
        height:140px;
        object-fit:cover;
        border-radius:14px;
        border:1px solid rgba(255,255,255,.15);
        background:#0b1324;
    }
    .img-preview.empty {
        display:flex; align-items:center; justify-content:center;
        color:#8aa0c1; font-size:.9rem;
    }
    .btn-outline-light{ border-color:rgba(255,255,255,.35)!important; color:#e6edf7!important; }
    .btn-outline-light:hover{ background:rgba(255,255,255,.08)!important; }

    .btn-dark-soft{
      background:#0d1526; border:1px solid #21314a; color:#e6edf7;
      border-radius:10px; padding:.45rem .7rem;
    }
    .btn-dark-soft:hover{ background:#121c31; }

    .coupon-row{
      background:#0b1427; border:1px solid #22304a; border-radius:12px; padding:14px;
    }
    .section-title{ font-weight:700; color:#8ed0ff; letter-spacing:.3px; }
    .muted{ color:#9fb0c9; }
    .kbd{
      background:#091324; border:1px solid #22304a; padding:.15rem .4rem; border-radius:.4rem; font-size:.85rem;
      color:#cfe3ff;
    }
    .card-subtle{ background:#0d1526; border:1px dashed #22304a; border-radius:12px; padding:12px; }
  </style>
</head>
<body>

@php
    use Illuminate\Support\Facades\Storage;
    $coupons = $coupons ?? [];
@endphp

<!-- Navigation -->
<nav class="navbar navbar-dark">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">MyStore</a>
    <div class="d-flex gap-2">
      <a href="{{ url('/admin/products/manage') }}" class="btn btn-outline-light btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf
        <button class="btn btn-warning btn-sm">Logout</button>
      </form>
    </div>
  </div>
</nav>

<div class="container py-4">

  <h2 class="mb-4 fw-bold text-white">Edit Product</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-warning">{{ $errors->first() }}</div>
  @endif

  <div class="card-dark">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
      @csrf
      @method('PUT')

      {{-- PRODUCT NAME --}}
      <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
      </div>

      {{-- CATEGORY --}}
      <div class="mb-3">
        <label>Category</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $product->category) }}" required>
      </div>

      {{-- DESCRIPTION --}}
      <div class="mb-3">
        <label>Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea>
      </div>

      {{-- PRICE + STOCK --}}
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>Price (₹)</label>
          <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required id="priceInput">
        </div>

        <div class="col-md-6 mb-3">
          <label>Stock</label>
          <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
        </div>
      </div>

      {{-- DISCOUNT --}}
      <div class="d-flex justify-content-between align-items-center mt-4">
        <h5 class="section-title m-0">Discount</h5>
        <span class="muted small">Shown on product page</span>
      </div>
      <div class="row mt-2">
        <div class="col-md-6 mb-3">
          <label>Discount Type</label>
          <select name="discount_type" class="form-select" id="discountType">
            <option value="">None</option>
            <option value="percent" {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
            <option value="flat"    {{ old('discount_type', $product->discount_type) === 'flat' ? 'selected' : '' }}>Flat (₹)</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label>Discount Value</label>
          <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $product->discount_value) }}" id="discountValue">
        </div>
      </div>

      {{-- STATUS (REQUIRED) --}}
      <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select" required>
          <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
          <option value="draft"  {{ old('status', $product->status) === 'draft'  ? 'selected' : '' }}>Draft</option>
        </select>
      </div>

      {{-- IMAGE + LIVE PREVIEW --}}
      <div class="mb-3">
        <label>Image</label>
        <div class="d-flex align-items-center gap-3 flex-wrap">
          {{-- Current / Preview box --}}
          <div>
            @if($product->image)
              <img
                src="{{ Storage::url($product->image) }}"
                class="img-preview"
                id="imgPreview"
                alt="Product image">
            @else
              <div class="img-preview empty" id="imgPreviewEmpty">No image</div>
              <img src="" alt="" id="imgPreview" class="img-preview d-none">
            @endif
            <div class="small muted mt-2" id="imgMeta">Max 3MB. JPG/PNG/WebP.</div>
          </div>

          {{-- File input + actions --}}
          <div class="flex-grow-1" style="min-width:260px;">
            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
            <div class="d-flex gap-2 mt-2">
              <button type="button" class="btn btn-dark-soft btn-sm" id="btnClearImage">
                <i class="bi bi-trash"></i> Clear
              </button>
              <button type="button" class="btn btn-dark-soft btn-sm" id="btnRestoreImage" {{ $product->image ? '' : 'disabled' }}>
                <i class="bi bi-arrow-counterclockwise"></i> Restore Current
              </button>
            </div>
          </div>
        </div>
      </div>

      {{-- LIVE PRICE PREVIEW --}}
      <div class="card-subtle my-3">
        <div class="d-flex justify-content-between align-items-center">
          <div class="muted">Live price after discount</div>
          <div>
            <span class="muted me-2">Original:</span>
            <span class="kbd me-3" id="originalPrice">₹{{ number_format($product->price,2) }}</span>
            <span class="muted me-2">Final:</span>
            <span class="kbd" id="finalPrice">₹{{ number_format($product->price,2) }}</span>
          </div>
        </div>
      </div>

      {{-- COUPONS --}}
      <div class="d-flex justify-content-between align-items-center mt-4">
        <h5 class="section-title m-0">Coupons</h5>
        <button class="btn btn-dark-soft btn-sm" type="button" id="addCouponBtn">
          <i class="bi bi-plus-lg"></i> Add Coupon
        </button>
      </div>
      <p class="muted small mt-2 mb-3">
        Create coupon codes customers can apply at checkout.
      </p>

      <div id="couponsContainer" class="d-grid gap-3">
        @forelse($coupons as $idx => $c)
          <div class="coupon-row" data-row>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong>Coupon</strong>
              <button class="btn btn-outline-light btn-sm" type="button" data-remove>
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <div class="row g-3">
              <div class="col-md-3">
                <label>Code</label>
                <input type="text" class="form-control" data-field="code" value="{{ $c['code'] ?? '' }}" placeholder="e.g. SAVE10">
              </div>
              <div class="col-md-2">
                <label>Type</label>
                <select class="form-select" data-field="type">
                  <option value="percent" {{ ($c['type'] ?? '')==='percent'?'selected':'' }}>Percent</option>
                  <option value="flat"    {{ ($c['type'] ?? '')==='flat'   ?'selected':'' }}>Flat (₹)</option>
                </select>
              </div>
              <div class="col-md-2">
                <label>Value</label>
                <input type="number" step="0.01" class="form-control" data-field="value" value="{{ $c['value'] ?? '' }}">
              </div>
              <div class="col-md-2">
                <label>Min Order (₹)</label>
                <input type="number" step="0.01" class="form-control" data-field="min_amount" value="{{ $c['min_amount'] ?? '' }}">
              </div>
              <div class="col-md-3">
                <label>Usage Limit (total)</label>
                <input type="number" class="form-control" data-field="usage_limit" value="{{ $c['usage_limit'] ?? '' }}" placeholder="Leave blank for unlimited">
              </div>
              <div class="col-md-3">
                <label>Starts At</label>
                <input type="datetime-local" class="form-control" data-field="starts_at" value="{{ isset($c['starts_at']) ? \Illuminate\Support\Carbon::parse($c['starts_at'])->format('Y-m-d\TH:i') : '' }}">
              </div>
              <div class="col-md-3">
                <label>Ends At</label>
                <input type="datetime-local" class="form-control" data-field="ends_at" value="{{ isset($c['ends_at']) ? \Illuminate\Support\Carbon::parse($c['ends_at'])->format('Y-m-d\TH:i') : '' }}">
              </div>
              <div class="col-md-3">
                <label>Per-User Limit</label>
                <input type="number" class="form-control" data-field="per_user_limit" value="{{ $c['per_user_limit'] ?? '' }}" placeholder="Optional">
              </div>
              <div class="col-md-3">
                <label>Status</label>
                <select class="form-select" data-field="is_active">
                  <option value="1" {{ !empty($c['is_active']) ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ empty($c['is_active'])  ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>
            </div>
          </div>
        @empty
        @endforelse
      </div>

      <input type="hidden" name="coupons_json" id="couponsJson">

      <div class="d-flex gap-2 mt-4">
        <button class="btn-save" type="submit">Save Changes</button>
        <a href="{{ route('admin.products.manage') }}" class="btn btn-outline-light">Cancel</a>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  // ------- Live price preview -------
  const priceEl = document.getElementById('priceInput');
  const typeEl  = document.getElementById('discountType');
  const valEl   = document.getElementById('discountValue');
  const originalPriceEl = document.getElementById('originalPrice');
  const finalPriceEl    = document.getElementById('finalPrice');

  function fmt(n){ return '₹' + Number(n||0).toFixed(2); }
  function recalc(){
    const base = parseFloat(priceEl.value || 0);
    originalPriceEl.textContent = fmt(base);
    const t = (typeEl.value || '').trim();
    const v = parseFloat(valEl.value || 0);
    let final = base;
    if (t === 'percent' && v > 0) final = base - (base * (v/100));
    if (t === 'flat' && v > 0)    final = base - v;
    if (final < 0) final = 0;
    finalPriceEl.textContent = fmt(final);
  }
  ['input','change'].forEach(evt=>{
    priceEl.addEventListener(evt, recalc);
    typeEl.addEventListener(evt, recalc);
    valEl.addEventListener(evt, recalc);
  });
  recalc();

  // ------- Image live preview -------
  const fileInput = document.getElementById('imageInput');
  const imgEl = document.getElementById('imgPreview');
  const emptyBox = document.getElementById('imgPreviewEmpty');
  const meta = document.getElementById('imgMeta');
  const btnClear = document.getElementById('btnClearImage');
  const btnRestore = document.getElementById('btnRestoreImage');

  const hasCurrent = !!imgEl?.getAttribute('src');
  const originalSrc = imgEl ? imgEl.getAttribute('src') : '';

  function showImage(url){
    if (!imgEl) return;
    imgEl.src = url;
    imgEl.classList.remove('d-none');
    if (emptyBox) emptyBox.classList.add('d-none');
  }

  function showEmpty(){
    if (!imgEl) return;
    imgEl.src = '';
    imgEl.classList.add('d-none');
    if (emptyBox) emptyBox.classList.remove('d-none');
  }

  fileInput?.addEventListener('change', function(){
    const file = this.files && this.files[0] ? this.files[0] : null;
    if (!file){ meta && (meta.textContent = 'Max 3MB. JPG/PNG/WebP.'); showEmpty(); return; }

    // basic checks
    const okTypes = ['image/jpeg','image/png','image/webp'];
    if (!okTypes.includes(file.type)){
      meta && (meta.textContent = 'Unsupported type. Use JPG/PNG/WebP.');
      this.value = '';
      if (hasCurrent && originalSrc) { showImage(originalSrc); } else { showEmpty(); }
      return;
    }
    if (file.size > 3 * 1024 * 1024){
      meta && (meta.textContent = 'File too large. Max 3MB.');
      this.value = '';
      if (hasCurrent && originalSrc) { showImage(originalSrc); } else { showEmpty(); }
      return;
    }

    meta && (meta.textContent = `${(file.size/1024).toFixed(0)} KB · ${file.type}`);

    // preview via FileReader
    const reader = new FileReader();
    reader.onload = e => { showImage(e.target.result); };
    reader.readAsDataURL(file);
  });

  btnClear?.addEventListener('click', ()=>{
    if (!fileInput) return;
    fileInput.value = '';
    meta && (meta.textContent = 'Max 3MB. JPG/PNG/WebP.');
    if (hasCurrent && originalSrc){ showImage(originalSrc); }
    else { showEmpty(); }
  });

  btnRestore?.addEventListener('click', ()=>{
    if (!originalSrc) return;
    fileInput.value = '';
    meta && (meta.textContent = 'Restored current image.');
    showImage(originalSrc);
  });

  // ------- Coupons dynamic list -------
  const container = document.getElementById('couponsContainer');
  const addBtn = document.getElementById('addCouponBtn');
  const hiddenJson = document.getElementById('couponsJson');
  const form = document.getElementById('productForm');

  function rowTemplate(){
    const wrap = document.createElement('div');
    wrap.className = 'coupon-row';
    wrap.setAttribute('data-row','');
    wrap.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Coupon</strong>
        <button class="btn btn-outline-light btn-sm" type="button" data-remove>
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="row g-3">
        <div class="col-md-3">
          <label>Code</label>
          <input type="text" class="form-control" data-field="code" placeholder="e.g. SAVE10">
        </div>
        <div class="col-md-2">
          <label>Type</label>
          <select class="form-select" data-field="type">
            <option value="percent">Percent</option>
            <option value="flat">Flat (₹)</option>
          </select>
        </div>
        <div class="col-md-2">
          <label>Value</label>
          <input type="number" step="0.01" class="form-control" data-field="value">
        </div>
        <div class="col-md-2">
          <label>Min Order (₹)</label>
          <input type="number" step="0.01" class="form-control" data-field="min_amount">
        </div>
        <div class="col-md-3">
          <label>Usage Limit (total)</label>
          <input type="number" class="form-control" data-field="usage_limit" placeholder="Leave blank for unlimited">
        </div>
        <div class="col-md-3">
          <label>Starts At</label>
          <input type="datetime-local" class="form-control" data-field="starts_at">
        </div>
        <div class="col-md-3">
          <label>Ends At</label>
          <input type="datetime-local" class="form-control" data-field="ends_at">
        </div>
        <div class="col-md-3">
          <label>Per-User Limit</label>
          <input type="number" class="form-control" data-field="per_user_limit" placeholder="Optional">
        </div>
        <div class="col-md-3">
          <label>Status</label>
          <select class="form-select" data-field="is_active">
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
    `;
    return wrap;
  }

  function serializeCoupons(){
    const rows = container.querySelectorAll('[data-row]');
    const list = [];
    rows.forEach(r=>{
      const get = sel => r.querySelector(`[data-field="${sel}"]`);
      const obj = {
        code: (get('code')?.value || '').trim(),
        type: (get('type')?.value || 'percent'),
        value: parseFloat(get('value')?.value || 0) || 0,
        min_amount: parseFloat(get('min_amount')?.value || 0) || 0,
        usage_limit: get('usage_limit')?.value ? parseInt(get('usage_limit').value,10) : null,
        per_user_limit: get('per_user_limit')?.value ? parseInt(get('per_user_limit').value,10) : null,
        starts_at: get('starts_at')?.value || null,
        ends_at: get('ends_at')?.value || null,
        is_active: (get('is_active')?.value || '1') === '1' ? 1 : 0,
      };
      if (obj.code && obj.value > 0) list.push(obj);
    });
    hiddenJson.value = JSON.stringify(list);
  }

  addBtn?.addEventListener('click', ()=> container.appendChild(rowTemplate()));
  container.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-remove]');
    if (!btn) return;
    const row = btn.closest('[data-row]');
    if (row) row.remove();
  });
  form.addEventListener('submit', serializeCoupons);
})();
</script>
</body>
</html>
