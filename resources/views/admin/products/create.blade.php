{{-- resources/views/admin/products/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create Product • Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --card-bg: #1f1f1f;
      --page-bg: #131313;
      --input-bg: #2a2a2a;
      --border-color: #3b3b3b;
      --text-light: #dcdcdc;
      --accent: #0d6efd;
      --accent-light: #3a8bff;
      --danger: #ff4f4f;
    }

    body { background: var(--page-bg); color: var(--text-light); }

    .page-header{
      background:linear-gradient(135deg,#0d6efd 0%,#6f42c1 100%);
      color:#fff;border-radius:14px;padding:1rem 1.25rem;
      box-shadow:0 10px 25px rgba(0,0,0,.4)
    }

    .card-styled{
      background:var(--card-bg)!important;border-radius:16px;
      box-shadow:0 10px 25px rgba(0,0,0,.4);border:1px solid var(--border-color)
    }

    .form-label{color:var(--text-light);font-weight:500}
    .form-control,.form-select{
      background:var(--input-bg);color:var(--text-light);
      border-radius:10px;border:1px solid var(--border-color)
    }
    .form-control::placeholder{color:#9a9a9a}
    .form-control:focus,.form-select:focus{
      border-color:var(--accent);box-shadow:0 0 0 .25rem rgba(13,110,253,.25);
      background:#262626;color:var(--text-light)
    }
    .form-text{color:#9a9a9a}
    .divider{height:1px;background:var(--border-color);margin:1.25rem 0}
    .section-title{
      font-size:.95rem;font-weight:700;text-transform:uppercase;
      letter-spacing:.06em;color:#a7a7a7;margin-bottom:.5rem
    }

    /* Uploader */
    .uploader{
      position:relative;border:2px dashed var(--border-color);
      border-radius:14px;padding:18px;background:var(--input-bg);
      transition:.2s;text-align:center;cursor:pointer;color:var(--text-light);
      user-select:none
    }
    .uploader.dragover{border-color:var(--accent-light);background:#202738}
    .uploader .help{color:#bfbfbf}
    .uploader .preview-wrap{
      display:none;position:relative;margin-top:10px;border-radius:12px;
      overflow:hidden;background:#000
    }
    .uploader img.preview{width:100%;height:auto;display:block}
    .uploader .file-meta{font-size:.85rem;color:#c8c8c8;margin-top:.5rem}
    .uploader .actions{margin-top:.75rem;display:flex;gap:.5rem;justify-content:center}
    .small-muted{color:#9c9c9c}

    .btn-lg-rounded{border-radius:10px;padding:.65rem 1.1rem;font-weight:600}
    .btn-success{background:#0aa86e;border:0}
    .btn-success:hover{background:#09925f}
    .btn-outline-secondary{border-color:#7f7f7f;color:#d9d9d9}
    .btn-outline-secondary:hover{background:#3a3a3a}
    .btn-light{background:#e6e6e6;color:#111;font-weight:600}

    .alert-danger{background:#2a1616;border:1px solid #5b1f1f;color:#ffb4b4}

    /* Coupon table */
    .table-darkish{
      --bs-table-bg: #1b1b1b;
      --bs-table-border-color: var(--border-color);
      color: var(--text-light);
      border-color: var(--border-color);
    }
    .coupon-row .remove-row{visibility:hidden}
    .coupon-row:hover .remove-row{visibility:visible}

    /* Price preview pill */
    .price-pill{
      display:inline-flex;align-items:center;gap:.5rem;
      padding:.35rem .6rem;border:1px solid var(--border-color);
      border-radius:999px;background:#191919
    }
    .pill-badge{padding:.1rem .4rem;border-radius:6px;background:#0b3a8a}
  </style>
</head>
<body>
<div class="container py-4">

  <div class="d-flex align-items-center justify-content-between page-header mb-4">
    <div class="d-flex align-items-center gap-3">
      <span class="badge bg-light text-dark fw-semibold">Admin</span>
      <h1 class="h4 mb-0">Add Product</h1>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger card-styled p-3">
      <strong>Fix the errors below:</strong>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger card-styled p-3">
      <strong>Error:</strong> {{ session('error') }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="card card-styled p-3 p-md-4">
    @csrf

    <div class="row g-4">
      {{-- LEFT: core fields --}}
      <div class="col-12 col-lg-7">
        <div class="section-title">Basic Details</div>

        <div class="mb-3">
          <label class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control"
                 value="{{ old('name') }}" placeholder="eg. Apple iPhone 15 (128 GB, Blue)" required>
        </div>

        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-dark text-light border-secondary">₹</span>
              <input type="number" step="0.01" min="0" name="price" id="price" class="form-control"
                     value="{{ old('price') }}" placeholder="0.00" required>
            </div>
            <div class="form-text">Set base price before discounts.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Stock <span class="text-danger">*</span></label>
            <input type="number" min="0" name="stock" class="form-control"
                   value="{{ old('stock') }}" placeholder="eg. 50" required>
          </div>
        </div>

        <div class="divider"></div>

        {{-- CATEGORY + STATUS --}}
        @php
          $cats = isset($categories) && is_array($categories) && count($categories)
                  ? $categories
                  : ['Mobile Phones','Laptops','Tablets','Smart Watches','Headphones','Cameras','TVs','Gaming',
                     'Fashion','Shoes','Bags','Watches','Home Appliances','Kitchen','Books','Beauty',
                     'Groceries','Fitness','Toys','Automotive'];
        @endphp

        <div class="row g-3">
          <div class="col-md-7">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="" {{ old('category_id') ? '' : 'selected' }}>Select category</option>
              @if(isset($allCategories))
                  @foreach($allCategories as $parent)
                    <option value="{{ $parent->id }}" class="fw-bold" {{ old('category_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }} (All)</option>
                    @foreach($parent->children as $child)
                        <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>→ {{ $child->name }}</option>
                    @endforeach
                  @endforeach
              @endif
            </select>
            <div class="form-text">Select a parent or sub-category.</div>
          </div>

          <div class="col-md-5">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
              <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
              <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
          </div>
        </div>

        <div class="divider"></div>

        {{-- DISCOUNT SECTION --}}
        <div class="section-title d-flex align-items-center justify-content-between">
          <span>Discount</span>
          <span class="price-pill" id="afterPricePill" title="Price after discount">
            <span class="pill-badge">Final</span>
            <span id="finalPriceDisplay">₹0.00</span>
          </span>
        </div>

        <div class="row g-3">
          <div class="col-sm-5">
            <label class="form-label">Discount Type</label>
            <select name="discount_type" id="discount_type" class="form-select">
              <option value="" {{ old('discount_type') ? '' : 'selected' }}>No discount</option>
              <option value="percent" {{ old('discount_type')==='percent'?'selected':'' }}>% Percentage</option>
              <option value="amount"  {{ old('discount_type')==='amount'?'selected':'' }}>₹ Fixed Amount</option>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Value</label>
            <input type="number" step="0.01" min="0" name="discount_value" id="discount_value"
                   value="{{ old('discount_value') }}" class="form-control" placeholder="e.g. 10">
            <div class="form-text" id="discountHelp">—</div>
          </div>
          <div class="col-sm-3 d-flex align-items-end">
            <div class="w-100">
              <label class="form-label">Active?</label>
              <select name="discount_active" id="discount_active" class="form-select">
                <option value="0" {{ old('discount_active')==='0'?'selected':'' }}>No</option>
                <option value="1" {{ old('discount_active','1')==='1'?'selected':'' }}>Yes</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <label class="form-label">Starts At</label>
            <input type="datetime-local" name="discount_starts_at" id="discount_starts_at" class="form-control"
                   value="{{ old('discount_starts_at') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Ends At</label>
            <input type="datetime-local" name="discount_ends_at" id="discount_ends_at" class="form-control"
                   value="{{ old('discount_ends_at') }}">
          </div>
        </div>

        <div class="form-text mt-1">Percentage is capped at 100%. Fixed discount can’t reduce price below ₹0.</div>

        <div class="divider"></div>

        <div class="mb-2">
          <label class="form-label">Description</label>
          <textarea name="description" rows="6" class="form-control" id="descField"
                    placeholder="Short overview, key features, warranty, etc.">{{ old('description') }}</textarea>
          <div class="d-flex justify-content-between mt-1">
            <div class="small-muted">Tip: Use bullet points for features.</div>
            <div class="small-muted" id="descCount">0 characters</div>
          </div>
        </div>
      </div>

      {{-- RIGHT: image + COUPONS --}}
      <div class="col-12 col-lg-5">
        <div class="section-title">Product Image</div>

        <label class="uploader" id="uploader" for="imageInput">
          <input type="file" name="image" id="imageInput" class="d-none" accept="image/png,image/jpeg,image/webp">
          <div class="icon mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="currentColor" class="bi bi-cloud-arrow-up text-primary" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M7.5 13a3.5 3.5 0 1 0 0-7h-.07A5.002 5.002 0 0 0 0 9.5a3.5 3.5 0 0 0 3.5 3.5h4z"/>
              <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2.5 2.5a.5.5 0 1 1-.708.708L8.5 6.707V11.5a.5.5 0 0 1-1 0V6.707L5.854 8.354a.5.5 0 1 1-.708-.708l2.5-2.5z"/>
            </svg>
          </div>
          <div class="help">
            <strong>Click to upload</strong> or drag & drop<br>
            <span class="small-muted">JPG / PNG / WEBP • up to 2 MB</span>
          </div>

          <div class="preview-wrap" id="previewWrap">
            <img id="imgPreview" class="preview" alt="Preview">
          </div>

          <div class="file-meta" id="fileMeta"></div>

          <div class="actions">
            <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="btnChange">Change</button>
            <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnClear">Remove</button>
          </div>
        </label>

        <div class="small-muted mt-2">
          Recommended: 1200×1200 px square image, clear background, centered product.
        </div>

        <div class="divider"></div>

        {{-- COUPONS SECTION --}}
        <div class="section-title d-flex align-items-center justify-content-between">
          <span>Coupons</span>
          <button type="button" id="addCouponBtn" class="btn btn-sm btn-outline-secondary">+ Add Coupon</button>
        </div>

        <div class="table-responsive">
          <table class="table table-darkish align-middle mb-2" id="couponTable">
            <thead>
              <tr>
                <th style="min-width:130px">Code</th>
                <th style="min-width:110px">Type</th>
                <th style="min-width:110px">Value</th>
                <th style="min-width:110px">Min ₹</th>
                <th style="min-width:110px">Uses</th>
                <th style="min-width:170px">Expires</th>
                <th style="width:1%"></th>
              </tr>
            </thead>
            <tbody>
              {{-- If you want to repopulate from old() on validation error, loop here --}}
            </tbody>
          </table>
        </div>
        <div class="form-text">Coupon code: A–Z, 0–9, “-”, 4–20 chars (e.g., SAVE10, NEW-USER-50).</div>
      </div>
    </div>

    <div class="divider"></div>

    <div class="d-flex flex-wrap gap-2 justify-content-end">
      <a href="{{ route('admin.products.list') }}" class="btn btn-outline-secondary btn-lg-rounded">Cancel</a>
      <button type="submit" class="btn btn-success btn-lg-rounded">Create Product</button>
    </div>
  </form>
</div>

<script>
  (function() {
    // ---------- Image Preview ----------
    const input = document.getElementById('imageInput');
    const uploader = document.getElementById('uploader');
    const previewWrap = document.getElementById('previewWrap');
    const imgPreview = document.getElementById('imgPreview');
    const fileMeta = document.getElementById('fileMeta');
    const btnChange = document.getElementById('btnChange');
    const btnClear = document.getElementById('btnClear');

    const MAX_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED = ['image/jpeg','image/png','image/webp'];

    function humanFileSize(bytes) {
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
      return (bytes/(1024*1024)).toFixed(2) + ' MB';
    }

    function setPreview(file) {
      if (!file) return;
      if (!ALLOWED.includes(file.type)) {
        alert('Only JPG, PNG, or WEBP images are allowed.');
        clearPreview();
        return;
      }
      if (file.size > MAX_SIZE) {
        alert('File is too large. Max 2 MB.');
        clearPreview();
        return;
      }
      const reader = new FileReader();
      reader.onload = e => {
        imgPreview.src = e.target.result;
        previewWrap.style.display = 'block';
        fileMeta.textContent = file.name + ' • ' + humanFileSize(file.size);
        btnChange.classList.remove('d-none');
        btnClear.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    }

    function clearPreview() {
      input.value = '';
      imgPreview.src = '';
      previewWrap.style.display = 'none';
      fileMeta.textContent = '';
      btnChange.classList.add('d-none');
      btnClear.classList.add('d-none');
    }

    input.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (file) setPreview(file);
    });

    uploader.addEventListener('click', (e) => {
      if (e.target === btnClear || e.target === btnChange) return;
      input.click();
    });

    // Drag & drop
    ['dragenter','dragover'].forEach(ev => {
      uploader.addEventListener(ev, (e) => {
        e.preventDefault(); e.stopPropagation();
        uploader.classList.add('dragover');
      });
    });
    ['dragleave','drop'].forEach(ev => {
      uploader.addEventListener(ev, (e) => {
        e.preventDefault(); e.stopPropagation();
        uploader.classList.remove('dragover');
      });
    });
    uploader.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files[0]) {
        input.files = dt.files;
        setPreview(dt.files[0]);
      }
    });

    btnChange.addEventListener('click', () => input.click());
    btnClear.addEventListener('click', clearPreview);

    // ---------- Description Counter ----------
    const descField = document.getElementById('descField');
    const descCount = document.getElementById('descCount');
    const updateCount = () => {
      const n = (descField.value || '').length;
      descCount.textContent = n + ' characters';
    };
    descField.addEventListener('input', updateCount);
    updateCount();

    // ---------- Discount + Final Price Preview ----------
    const priceEl = document.getElementById('price');
    const dType = document.getElementById('discount_type');
    const dVal  = document.getElementById('discount_value');
    const dHelp = document.getElementById('discountHelp');
    const finalPriceDisplay = document.getElementById('finalPriceDisplay');

    function parseNum(el){
      const v = parseFloat((el?.value||'').trim());
      return isNaN(v) ? 0 : v;
    }
    function formatINR(n){
      return '₹' + (n||0).toFixed(2);
    }
    function computeFinal(){
      let price = parseNum(priceEl);
      let type = dType.value;
      let val  = parseNum(dVal);
      let active = document.getElementById('discount_active').value === '1';

      let final = price;
      if (active && price > 0 && type){
        if (type === 'percent'){
          if (val > 100) val = 100;
          if (val < 0) val = 0;
          final = price * (1 - (val/100));
          dHelp.textContent = `Takes ${val}% off the base price.`;
        } else if (type === 'amount'){
          if (val < 0) val = 0;
          final = price - val;
          dHelp.textContent = `Subtracts ₹${val.toFixed(2)} from the base price.`;
        }
        if (final < 0) final = 0;
      } else {
        dHelp.textContent = '—';
      }
      finalPriceDisplay.textContent = formatINR(final);
    }
    ['input','change'].forEach(evt=>{
      [priceEl,dType,dVal,document.getElementById('discount_active')].forEach(el=>{
        el.addEventListener(evt, computeFinal);
      });
    });
    computeFinal();

    // ---------- Coupons ----------
    const addBtn = document.getElementById('addCouponBtn');
    const tbody = document.querySelector('#couponTable tbody');

    function couponRowTemplate(i, data={}) {
      const code = data.code || '';
      const type = data.type || 'percent';
      const value = data.value || '';
      const min   = data.min   || '';
      const uses  = data.uses  || '';
      const exp   = data.expires_at || '';
      return `
        <tr class="coupon-row">
          <td>
            <input type="text" name="coupons[${i}][code]" class="form-control code-input"
                   placeholder="SAVE10" value="${code}" maxlength="20"
                   pattern="[A-Z0-9\\-]{4,20}" title="4-20 chars, A-Z, 0-9, dash">
          </td>
          <td>
            <select name="coupons[${i}][type]" class="form-select type-select">
              <option value="percent" ${type==='percent'?'selected':''}>%</option>
              <option value="amount"  ${type==='amount'?'selected':''}>₹</option>
            </select>
          </td>
          <td>
            <input type="number" step="0.01" min="0" name="coupons[${i}][value]" class="form-control value-input"
                   placeholder="e.g. 10" value="${value}">
          </td>
          <td>
            <input type="number" step="0.01" min="0" name="coupons[${i}][min_order]" class="form-control"
                   placeholder="0.00" value="${min}">
          </td>
          <td>
            <input type="number" min="0" name="coupons[${i}][usage_limit]" class="form-control"
                   placeholder="e.g. 100" value="${uses}">
          </td>
          <td>
            <input type="datetime-local" name="coupons[${i}][expires_at]" class="form-control"
                   value="${exp}">
          </td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">✕</button>
          </td>
        </tr>
      `;
    }

    function addCouponRow(prefill){
      const i = tbody.querySelectorAll('tr').length;
      tbody.insertAdjacentHTML('beforeend', couponRowTemplate(i, prefill));
    }

    addBtn.addEventListener('click', ()=> addCouponRow());

    tbody.addEventListener('click', (e)=>{
      if (e.target.closest('.remove-row')){
        const tr = e.target.closest('tr');
        tr?.remove();
        // reindex names so backend receives sequential arrays
        [...tbody.querySelectorAll('tr')].forEach((row, idx)=>{
          row.querySelectorAll('input,select').forEach(el=>{
            el.name = el.name.replace(/coupons\[\d+\]/, `coupons[${idx}]`);
          });
        });
      }
    });

    // Optional: validate coupon code uppercase
    tbody.addEventListener('input', (e)=>{
      const tgt = e.target;
      if (tgt.classList.contains('code-input')){
        tgt.value = tgt.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'');
      }
      // percent cap at 100
      if (tgt.classList.contains('value-input')){
        const row = tgt.closest('tr');
        const type = row.querySelector('.type-select').value;
        let v = parseFloat(tgt.value || '0');
        if (type==='percent' && v>100) tgt.value = 100;
        if (v<0) tgt.value = 0;
      }
    });

    // If you want to restore old coupons after validation error, you can pass them from the controller
    // and insert here by calling addCouponRow({...}) for each item.
    // Example with server-provided window.oldCoupons = [...]
    if (window.oldCoupons && Array.isArray(window.oldCoupons)) {
      window.oldCoupons.forEach(c => addCouponRow(c));
    }
    // Auto-dismiss alerts
    setTimeout(() => {
      document.querySelectorAll('.alert').forEach(el => {
        el.style.transition = 'opacity 0.5s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
      });
    }, 5000);
  })();
</script>
</body>
</html>
