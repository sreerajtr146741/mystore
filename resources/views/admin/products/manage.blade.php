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

  <style>
    :root{
      --bg:#070c14; --panel:#0b1220; --field:#0f1626;
      --muted:#1b2536; --ink:#e6edf7; --ink-60:#a9b6c9;
      --brand:#60a5fa; --cyan:#22d3ee; --table:#0f1626;
    }
    body { background:var(--bg); color:var(--ink); }
    .navbar{ background:#0b1220; border-bottom:1px solid rgba(255,255,255,.1); }

    /* compact controls */
    .control-h { height:42px; }
    .rounded-14 { border-radius:14px; }

    /* toolbar row */
    .toolbar { gap:.5rem; flex-wrap:nowrap; }
    @media (max-width: 768px){
      .toolbar { flex-wrap:wrap; }
      .search-wrap, #categorySelect, .choices { width:100%!important; }
    }

    /* search input */
    .search-input{
      height:42px; padding-left:38px; padding-right:38px;
      background:#0f182b; border-radius:14px; color:#e8eefb!important;
      border:1px solid #21314a; width:360px; max-width:100%;
    }
    .search-input::placeholder{ color:#8aa0c1!important; }
    .search-icon{ left:12px; top:10px; color:#9fb2d3; }
    .clear-icon{ right:12px; top:10px; cursor:pointer; color:#8aa0c1; }

    /* Category select (native) */
    #categorySelect {
      color:#e8eefb!important; background:#0d1526!important;
      border:1px solid #21314a; border-radius:14px; height:42px;
      min-width:220px; max-width:100%;
    }
    #categorySelect option{ background:#0d1526; color:#e8eefb; }

    /* Choices.js dark overrides */
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
    .choices__list--single .choices__item{ color:#e8eefb!important; }

    /* Table */
    .table-wrap{ background:var(--table); border-radius:16px; border:1px solid #22304a; }
    .table thead th{ color:white; background:var(--table); border-color:#22304a; }
    .img-thumb{ width:56px; height:56px; border-radius:12px; object-fit:cover; }

    .btn-act{ padding:.3rem .55rem; border-radius:10px; }
    .btn-edit{ background:rgba(59,130,246,.18); border:1px solid rgba(59,130,246,.35); color:#dbeafe; }
    .btn-del{ background:rgba(239,68,68,.16); border:1px solid rgba(239,68,68,.35); color:#fecaca; }

    /* Add product button (refined and smaller) */
    .btn-add{
      height:42px; padding:.45rem .8rem; border-radius:12px;
      display:inline-flex; align-items:center; gap:.45rem;
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
    <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">MyStore</a>
    <div class="d-flex gap-2">
      <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
      <form action="{{ route('logout') }}" method="POST">@csrf
        <button class="btn btn-warning btn-sm">Logout</button>
      </form>
    </div>
  </div>
</nav>

<main class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="row-gap:.5rem;">

    {{-- SEARCH + CATEGORY + FILTER (visible only to Admin or Seller) --}}
    @if(auth()->user()?->isAdmin() || auth()->user()?->isSeller())
    <form method="GET" class="d-flex align-items-center toolbar" id="searchForm">
      <div class="position-relative search-wrap" style="width:360px; max-width:100%;">
        <i class="bi bi-search position-absolute search-icon"></i>
        <input name="q" value="{{ request('q') }}" class="form-control search-input" placeholder="Search by ID / Name / Description" id="searchInput">
        @if(request('q'))
          <i class="bi bi-x position-absolute clear-icon" id="clearSearch"></i>
        @endif
      </div>

      <select id="categorySelect" name="category" class="form-select">
        <option value="">All Categories</option>
        @foreach(($categories ?? []) as $c)
          <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
      </select>

      <button class="btn btn-primary btn-sm control-h rounded-14 px-3" type="submit">
        <i class="bi bi-funnel me-1"></i> Filter
      </button>
    </form>
    @endif

    {{-- ADD PRODUCT (refined button; still Admin-only) --}}
    @if(auth()->user()?->isAdmin())
      <a class="btn btn-primary btn-add" href="{{ route('admin.products.create') }}">
        <i class="bi bi-plus-lg"></i><span>Add Product</span>
      </a>
    @endif

  </div>

  {{-- PRODUCTS TABLE --}}
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
        <tbody>
          @forelse($products as $p)
            @php
              $img = $resolveImg($p->image);
              $isActive = ($p->status === 'active') || ($p->is_active == 1);
            @endphp
            <tr>
              <td>#{{ $p->id }}</td>
              <td>
                @if($img)
                  <img src="{{ $img }}" class="img-thumb" alt="Product">
                @else
                  <div class="img-thumb bg-secondary d-flex align-items-center justify-content-center text-white-50">N/A</div>
                @endif
              </td>
              <td>{{ $p->name }}</td>
              <td>{{ $p->category }}</td>
              <td class="text-end">₹{{ number_format($p->price,2) }}</td>
              <td class="text-end">{{ $p->stock }}</td>
              <td>
                @if($isActive)
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">Hidden</span>
                @endif
              </td>
              <td>{{ $p->user->name ?? '—' }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" class="d-inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-del btn-act" onclick="return confirm('Delete this product?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-edit btn-act">
                  <i class="bi bi-pencil-square"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-white-50 py-5">No products found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $products->withQueryString()->links() }}
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const catSelect = document.getElementById('categorySelect');
  if (catSelect) {
    const choices = new Choices(catSelect,{
      searchEnabled:true, itemSelectText:'', shouldSort:false,
      removeItemButton:false, placeholder:true
    });
    // Ensure visible text color for selected item
    const el = catSelect.parentElement.querySelector('.choices');
    if (el) { el.style.minWidth = '220px'; el.style.maxWidth = '100%'; }
  }

  const clearBtn = document.getElementById('clearSearch');
  const searchInput = document.getElementById('searchInput');
  if (clearBtn && searchInput) {
    clearBtn.addEventListener('click', function(){
      searchInput.value = '';
      document.getElementById('searchForm').submit();
    });
  }
});
</script>

</body>
</html>
