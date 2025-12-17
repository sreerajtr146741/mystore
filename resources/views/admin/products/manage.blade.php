{{-- resources/views/admin/products/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Products • Admin • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    @include('partials.premium-styles')
    <style>
        .control-h { height:42px; }
        .rounded-14 { border-radius:14px; }
        .toolbar { gap:.5rem; flex-wrap:nowrap; }
        @media (max-width: 768px){
            .toolbar { flex-wrap:wrap; }
            .search-wrap, #categorySelect, .choices { width:100%!important; }
        }
        .search-input{
            height:42px; padding-left:38px; padding-right:38px;
            background:#0f182b!important; border-radius:14px; color:#e8eefb!important;
            border:1px solid #21314a; width:360px; max-width:100%;
        }
        .search-input:focus{
            background:#0f182b!important; color:#ffffff!important;
            border-color:var(--brand);
            box-shadow:0 0 0 0.25rem rgba(96,165,250,0.25);
        }
        .search-input::placeholder{ color:#8aa0c1!important; }
        .form-control::placeholder{ color:#8aa0c1!important; opacity:1; }
        .search-icon{ left:12px; top:10px; color:#9fb2d3; }
        .clear-icon{ right:12px; top:10px; cursor:pointer; color:#8aa0c1; }
        #categorySelect {
            color:#e8eefb!important; background:#0d1526!important;
            border:1px solid #21314a; border-radius:14px; height:42px;
            min-width:220px; max-width:100%;
        }
        #categorySelect option{ background:#0d1526; color:#e8eefb; }
        .choices__inner{
            background:#0f182b!important; color:#e8eefb!important;
            border:1px solid #21314a!important; border-radius:14px!important; min-height:42px!important;
            padding-top:6px!important; padding-bottom:6px!important;
        }
        .choices[data-type*=select-one] .choices__input{
            background:#0f182b!important; color:#e8eefb!important;
        }
        .choices__item--selectable{ color:#e8eefb!important; }
        .choices__placeholder{ color:#8aa0c1!important; opacity:1!important; }
        .choices__list--dropdown{ background:#0d1526!important; border-color:#21314a!important; }
        .choices__list--dropdown .choices__item{ color:#e8eefb!important; }
        /* Fix hover/highlight visibility */
        .choices__list--dropdown .choices__item--choice.is-highlighted {
            background-color: var(--field)!important; 
            color: var(--brand)!important;
        }
        /* Fix Constant Size */
        .choices {
            width: 260px !important;
            min-width: 260px !important;
            max-width: 260px !important;
            margin-bottom: 0 !important;
        }
        .table-wrap{ background:var(--table); border-radius:16px; border:1px solid #22304a; }
        .table thead th{ color:white; background:var(--table); border-color:#22304a; }
        .img-thumb{ width:56px; height:56px; border-radius:12px; object-fit:cover; }
        .btn-act{ padding:.3rem .55rem; border-radius:10px; }
        .btn-edit{ background:rgba(59,130,246,.18); border:1px solid rgba(59,130,246,.35); color:#dbeafe; }
        .btn-del{ background:rgba(239,68,68,.16); border:1px solid rgba(239,68,68,.35); color:#fecaca; }
        .btn-add{
            height:42px; padding:.45rem .8rem; border-radius:12px;
            display:inline-flex; align-items:center; gap:.45rem;
        }
        .discount-card {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
@php
use Illuminate\Support\Facades\Storage;
$resolveImg = function($path){
    if(!$path) return null;
    if(filter_var($path, FILTER_VALIDATE_URL)) return $path;
    return Storage::url($path);
};
@endphp

<nav class="navbar navbar-dark">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}"><i class="bi bi-bag-fill me-2"></i>MyStore</a>
        <div class="d-flex gap-2">
            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">@csrf
                <button class="btn btn-warning btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="row-gap:.5rem;">
        @if(auth()->user()?->isAdmin() || auth()->user()?->isSeller())
        <div class="d-flex w-100 justify-content-between gap-2">
            <form method="GET" class="d-flex align-items-center toolbar flex-grow-1" id="searchForm">
                <select id="categorySelect" name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach(($categories ?? []) as $c)
                        <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm control-h rounded-14 px-3" type="submit">
                    Filter
                </button>
            </form>
            <div>
                 <button class="btn btn-success btn-sm control-h rounded-14 px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#categoryDiscountModal">
                    <i class="bi bi-percent me-1"></i> Category Discount
                 </button>
                 <a href="{{ route('admin.products.create') }}" class="btn btn-add btn-primary text-white text-decoration-none rounded-14 px-3 fw-bold">
                    <i class="bi bi-plus-lg"></i> Add Product
                 </a>
            </div>
        </div>
        @endif
    </div>

    <!-- PRODUCTS TABLE -->
    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Stock</th>
                        <th>Status</th>
                        <th>Seller</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="product-rows">
                    @include('admin.products.partials.row', ['products' => $products])
                </tbody>
            </table>
        </div>
    </div>

    <!-- Infinite Scroll Elements -->
    @if($products->hasMorePages())
        <div id="loading-spinner" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
        <div id="sentinel" style="height:20px;"></div>
        <div id="pagination-data" data-next-url="{{ $products->nextPageUrl() }}" style="display:none;"></div>
    @endif
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let nextUrl = document.getElementById('pagination-data')?.dataset.nextUrl;
    const sentinel = document.getElementById('sentinel');
    const spinner = document.getElementById('loading-spinner');
    const container = document.getElementById('product-rows');
    let isLoading = false;

    if (sentinel && nextUrl) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !isLoading && nextUrl) {
                loadMore();
            }
        }, { rootMargin: '200px' });
        observer.observe(sentinel);

        function loadMore() {
            isLoading = true;
            spinner.classList.remove('d-none');
            fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                spinner.classList.add('d-none');
                if (html.trim()) {
                    container.insertAdjacentHTML('beforeend', html);
                    const currentUrl = new URL(nextUrl);
                    const p = parseInt(currentUrl.searchParams.get('page')||1) + 1;
                    currentUrl.searchParams.set('page', p);
                    nextUrl = currentUrl.toString();
                    isLoading = false;
                } else {
                    observer.disconnect();
                    sentinel.remove();
                }
            })
            .catch(()=> { spinner.classList.add('d-none'); isLoading = false; });
        }
    }
});
</script>

{{-- Category Discount Modal --}}
<div class="modal fade" id="categoryDiscountModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.discount.category') }}" style="background:var(--panel); border:1px solid #334155; color:var(--ink);">
      @csrf
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title fw-bold"><i class="bi bi-percent me-2"></i>Category Discount Manager</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-primary bg-opacity-10 border-primary text-primary">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Tip:</strong> Apply discounts to all products in a category at once!
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold">Select Category</label>
          <select name="category_id" class="form-select" required style="background:var(--field); color:white; border-color:#334155;">
            <option value="">Choose a category...</option>
            @foreach($allCategories as $parent)
              <option value="{{ $parent->id }}" class="fw-bold">{{ $parent->name }} (All)</option>
              @foreach($parent->children as $child)
                <option value="{{ $child->id }}">→ {{ $child->name }}</option>
              @endforeach
            @endforeach
          </select>
          <div class="form-text mt-1" style="color:var(--ink-60)">
            Select a parent category to discount all items, or a specific subcategory.
          </div>
          
          <label class="form-label fw-semibold mt-3">Discount Percentage</label>
          <div class="input-group">
            <input name="discount_percent" type="number" min="0" max="100" step="1" class="form-control" placeholder="e.g., 20" required style="background:var(--field); color:white; border-color:#334155;">
            <span class="input-group-text bg-secondary text-white border-secondary">%</span>
          </div>
          <div class="form-text mt-2 text-warning">
            <i class="bi bi-exclamation-circle me-1"></i> Enter <strong>0</strong> to remove discount from this category.
          </div>

          <label class="form-label fw-semibold mt-3">Expires At (Optional)</label>
          <input name="discount_expires_at" type="datetime-local" class="form-control" style="background:var(--field); color:white; border-color:#334155;">
          <div class="form-text mt-1" style="color:var(--ink-60)">
             Leave blank for no expiration.
          </div>
        </div>
      </div>
      <div class="modal-footer border-top border-secondary">
        <button class="btn btn-outline-light" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-success px-4 fw-bold">Apply Discount</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const catSelect = document.getElementById('categorySelect');
    if (catSelect) {
        new Choices(catSelect, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            removeItemButton: false,
            placeholder: true
        });
    }

    if (catSelect) {
        new Choices(catSelect, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            removeItemButton: false,
            placeholder: true
        });
    }

});
// Auto-dismiss alerts
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(el => {
    el.style.transition = 'opacity 0.5s ease';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  });
}, 5000);
</script>
</body>
</html>