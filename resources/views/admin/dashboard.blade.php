{{-- resources/views/dashboard/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard • MyStore</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">

<style>
  :root{
    --glass-bg: rgba(255,255,255,0.08);
    --glass-bd: rgba(255,255,255,0.15);
    --ink-weak: #cbd5e1;
    --ink: #e2e8f0;
  }
  body {
    margin:0;
    background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#1e40af 100%);
    min-height:100vh; overflow-x:hidden; font-family:'Segoe UI',sans-serif;
  }
  .text-gradient{ background:linear-gradient(90deg,#00d4ff,#ff00c8); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  .card-glass{ background:var(--glass-bg); backdrop-filter:blur(16px); border:1px solid var(--glass-bd); border-radius:20px; }
  .btn-glass{ background:rgba(255,255,255,0.10); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.20); }
  .hover-lift:hover{ transform:translateY(-6px) scale(1.01); box-shadow:0 20px 44px rgba(0,0,0,0.45)!important; }
  .btn-3d:hover{ transform:translateY(-5px) scale(1.02); box-shadow:0 24px 48px rgba(0,0,0,0.4)!important; }
  .btn-cyan{ background:linear-gradient(135deg,#06b6d4,#0891b2); }
  .btn-indigo{ background:linear-gradient(135deg,#6366f1,#4f46e5); }
  .section-title { color:#fff; letter-spacing:.3px; }

  /* NAVBAR */
  .navbar-blur { background:rgba(15,23,42,0.35); backdrop-filter: blur(10px); border-bottom:1px solid rgba(255,255,255,0.12); }
  .navbar .form-control {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color:#fff;
  }
  .navbar .form-control::placeholder { color: rgba(255,255,255,0.75); }
  .navbar .input-group-text {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color:#fff;
  }
  .navbar .btn-outline-light { border-color: rgba(255,255,255,0.35); }

  /* content offset */
  .page-wrap { padding-top: 88px; }

  /* Avatar */
  .avatar {
    width: 28px; height: 28px; border-radius: 50%;
    display: inline-flex; align-items:center; justify-content:center;
    font-weight:700; color:#fff; user-select:none;
    border:2px solid #fff;
  }

  /* Search results panel */
  .search-panel {
    position: absolute; left: 50%; transform: translateX(-50%);
    width: min(720px, 92vw); z-index: 1050;
  }
  .search-card { background:#0b1220; border:1px solid rgba(255,255,255,0.15); border-radius:14px; }
  .search-item:hover { background:#0f172a; }

  /* Rings */
  .ring {
    --pct: 0;
    width: 110px; height: 110px; border-radius: 50%;
    background:
      radial-gradient(closest-side, rgba(15,17,21,.92) 78%, transparent 79% 100%),
      conic-gradient(#22d3ee var(--pct), rgba(255,255,255,.12) 0);
    display:grid; place-items:center;
    border:1px solid rgba(255,255,255,.15);
  }
  .ring .val { color:#e2e8f0; font-weight:700; font-size:1.25rem; line-height:1; }
  .ring .lbl { color:#cbd5e1; font-size:.8rem; opacity:.9; }
  .stat-pill { background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); border-radius:12px; }
  .welcome-sub { color:#e5e7eb; opacity:.9; }
</style>
</head>
<body>

{{-- TOP NAVBAR: brand • centered search • avatar profile --}}
<nav class="navbar navbar-expand-lg navbar-dark navbar-blur fixed-top">
  <div class="container position-relative">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">MyStore</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="topbar">
      {{-- Centered searchbar (dashboard-only; shows panel) --}}
      <form id="dashSearchForm" class="mx-auto" action="#" method="GET" style="max-width:520px; width:100%;" autocomplete="off">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="dashSearchInput" name="q" class="form-control" placeholder="Search dashboard: users / products / orders / discount / revenue…">
          <button class="btn btn-outline-light" type="submit">Go</button>
        </div>
      </form>

      {{-- Right: Dashboard (disabled) + Profile --}}
      <div class="d-flex align-items-center gap-2 ms-lg-3">
        {{-- Dashboard stays on same page (disabled) --}}
        <span class="btn btn-outline-light btn-sm disabled">
          <i class="bi bi-speedometer2 me-1"></i>Dashboard
        </span>

        {{-- Profile dropdown with avatar (photo → fallback initials) --}}
        <div class="dropdown">
          <button class="btn btn-glass d-flex align-items-center gap-2 shadow-sm" type="button" data-bs-toggle="dropdown">
            @php
              $photo = auth()->user()->profile_photo_url ?? null;
              $name  = trim(auth()->user()->name ?? 'User');
              $initials = collect(explode(' ', $name))->map(fn($p)=>mb_substr($p,0,1))->take(2)->implode('');
              $palette = ['#0ea5e9','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444','#14b8a6','#84cc16'];
              $color = $palette[(crc32($name) % count($palette))];
            @endphp
            @if($photo)
              <img src="{{ $photo }}" class="rounded-circle border border-white border-2" width="28" height="28" alt="avatar">
            @else
              <span class="avatar" style="background: {{ $color }}">{{ $initials }}</span>
            @endif
            <span class="text-white small fw-semibold">{{ $name }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
            <li>
              <a href="{{ route('profile.edit') }}" class="dropdown-item py-2 d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-5 text-info"></i> My Profile
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                  <i class="bi bi-box-arrow-right fs-5"></i> Logout
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>

      {{-- Search results panel (absolute; toggled by JS) --}}
      <div id="dashSearchPanel" class="search-panel mt-2 d-none">
        <div class="search-card p-2">
          <div id="dashResults" class="list-group list-group-flush"></div>
        </div>
      </div>

    </div>
  </div>
</nav>

<div class="page-wrap container position-relative">

  {{-- WELCOME --}}
  <div class="text-center text-white mb-5">
    <h1 class="display-5 fw-bold mb-2">
      @if(method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
        Welcome back, <span class="text-gradient">Super Admin</span>!
      @else
        Welcome back, <span class="text-gradient">{{ auth()->user()->name }}</span>!
      @endif
    </h1>
    <p class="lead welcome-sub mb-0">
      <span class="text-info fw-bold">MyStore</span> command center • 
      <span class="text-warning">
        @if(($alerts['low_stock'] ?? 0) > 0)
          {{ $alerts['low_stock'] }} low-stock products need attention
        @else
          All systems normal
        @endif
      </span>
    </p>
  </div>

  {{-- ADMIN PANEL --}}
  @if(method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
  <div class="mb-5">
    <h2 class="section-title mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Admin Dashboard</h2>

    {{-- KPI CARDS — NON-CLICKABLE --}}
    <div class="row g-4 mb-4">
      <div class="col-md-6 col-lg-3">
        <div class="card-glass hover-lift p-4 text-white position-relative" role="button" tabindex="0" onclick="location.href='{{ route('admin.users') }}'">
          <i class="bi bi-people-fill fs-1 opacity-50"></i>
          <div class="opacity-75 mt-2">Total Users</div>
          <h2 class="fw-bold">{{ $stats['total_users'] ?? 0 }}</h2>
          <small class="text-success"><i class="bi bi-graph-up-arrow"></i> {{ $userStats['growth'] ?? '+0%' }}</small>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card-glass hover-lift p-4 text-white position-relative" role="button" tabindex="0" onclick="location.href='{{ route('admin.products.list') }}'">
          <i class="bi bi-box-seam fs-1 opacity-50"></i>
          <div class="opacity-75 mt-2">Total Products</div>
          <h2 class="fw-bold">{{ $stats['total_products'] ?? 0 }}</h2>
          <small class="text-info"><i class="bi bi-plus-circle"></i> {{ ($stats['new_today'] ?? 0) }} new today</small>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card-glass hover-lift p-4 text-white position-relative" role="button" tabindex="0" onclick="location.href='{{ route('admin.revenue') }}'">
          <i class="bi bi-currency-rupee fs-1 opacity-50"></i>
          <div class="opacity-75 mt-2">Today's Revenue</div>
          <h2 class="fw-bold">₹{{ number_format($stats['today_revenue'] ?? 0) }}</h2>
          <small class="text-success"><i class="bi bi-graph-up-arrow"></i> {{ $revenue['growth'] ?? '+0%' }}</small>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card-glass hover-lift p-4 text-white position-relative" role="button" tabindex="0" onclick="location.href='{{ route('admin.orders') }}'">
          <i class="bi bi-receipt fs-1 opacity-50"></i>
          <div class="opacity-75 mt-2">Pending Orders</div>
          <h2 class="fw-bold">{{ $adminExtras['pending_orders'] ?? 0 }}</h2>
          <small class="{{ ($adminExtras['pending_orders'] ?? 0) > 0 ? 'text-warning' : 'text-success' }}">
            {{ ($adminExtras['pending_orders'] ?? 0) > 0 ? 'Action needed' : 'All clear' }}
          </small>
        </div>
      </div>
    </div>

    {{-- ===== User Statistics (Seller-friendly) ===== --}}
    @php
      // Safe fallbacks
      $totalUsers = (int)($stats['total_users'] ?? 0);
      $buyers     = (int)($userStats['buyers'] ?? 0);
      $newToday   = (int)($userStats['new_today'] ?? 0);
      $admins     = (int)($userStats['admins'] ?? 0);
      $sellers    = (int)($userStats['sellers'] ?? 1); // single seller app

      // Derived
      $returning  = max(0, $buyers - $newToday);
      $buyersPct  = $totalUsers > 0 ? round(($buyers / $totalUsers) * 100) : 0;
      $adminsPct  = $totalUsers > 0 ? round(($admins / $totalUsers) * 100) : 0;
      $sellersPct = $totalUsers > 0 ? round(($sellers / $totalUsers) * 100) : 0;

      // Optional extras (if you later provide them from controller)
      $active30   = (int)($userStats['active_30d'] ?? 0);          // active buyers in last 30d
      $activePct  = $buyers > 0 ? round(($active30 / $buyers)*100) : 0;
    @endphp

    <div class="card-glass p-4 mb-4 text-white">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-people me-2"></i>User Statistics</h4>
        <span class="small text-white-50">Clear snapshot of your customers</span>
      </div>

      <div class="row g-4 align-items-center">
        {{-- Left: Rings --}}
        <div class="col-lg-6">
          <div class="d-flex flex-wrap gap-4">
            {{-- Buyers share of all users --}}
            <div class="text-center">
              <div class="ring" style="--pct: {{ $buyersPct }}%;">
                <div>
                  <div class="val">{{ $buyersPct }}%</div>
                  <div class="lbl">Buyers of Users</div>
                </div>
              </div>
              <div class="mt-2 small text-white-50">{{ $buyers }} buyers / {{ $totalUsers }} users</div>
            </div>

            {{-- Active last 30 days (optional) --}}
            <div class="text-center">
              <div class="ring" style="--pct: {{ $activePct }}%; background:
                radial-gradient(closest-side, rgba(15,17,21,.92) 78%, transparent 79% 100%),
                conic-gradient(#10b981 {{ $activePct }}%, rgba(255,255,255,.12) 0);">
                <div>
                  <div class="val">{{ $activePct }}%</div>
                  <div class="lbl">Active (30d)</div>
                </div>
              </div>
              <div class="mt-2 small text-white-50">{{ $active30 }} active / {{ $buyers }} buyers</div>
            </div>

            {{-- Admin+Seller mix --}}
            <div class="text-center">
              <div class="ring" style="--pct: {{ min(100, $adminsPct+$sellersPct) }}%; background:
                radial-gradient(closest-side, rgba(15,17,21,.92) 78%, transparent 79% 100%),
                conic-gradient(#f59e0b {{ min(100, $adminsPct+$sellersPct) }}%, rgba(255,255,255,.12) 0);">
                <div>
                  <div class="val">{{ min(100, $adminsPct+$sellersPct) }}%</div>
                  <div class="lbl">Admin + Seller</div>
                </div>
              </div>
              <div class="mt-2 small text-white-50">{{ $admins }} admins • {{ $sellers }} seller(s)</div>
            </div>
          </div>
        </div>

        {{-- Right: seller-first numbers --}}
        <div class="col-lg-6">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="p-3 stat-pill">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-white-50">Total Buyers</span>
                  <i class="bi bi-bag-check text-info"></i>
                </div>
                <div class="h3 fw-bold mb-1">{{ $buyers }}</div>
                <div class="progress" aria-label="Buyers share">
                  <div class="progress-bar bg-info" style="width: {{ $buyersPct }}%"></div>
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="p-3 stat-pill">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-white-50">New Today</span>
                  <i class="bi bi-plus-circle text-primary"></i>
                </div>
                <div class="h3 fw-bold mb-1">{{ $newToday }}</div>
                <small class="text-white-50">New customers registered today</small>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="p-3 stat-pill">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-white-50">Returning Buyers</span>
                  <i class="bi bi-arrow-repeat text-success"></i>
                </div>
                <div class="h3 fw-bold mb-1">{{ $returning }}</div>
                <small class="text-white-50">{{ $buyers>0 ? round(($returning/$buyers)*100) : 0 }}% of buyers</small>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="p-3 stat-pill">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-white-50">Admins & Sellers</span>
                  <i class="bi bi-person-gear text-warning"></i>
                </div>
                <div class="h3 fw-bold mb-1">{{ $admins }} • {{ $sellers }}</div>
                <small class="text-white-50">Admins • Sellers</small>
              </div>
            </div>
          </div>
        </div>

      </div> {{-- /row --}}
    </div> {{-- /card-glass --}}
    {{-- ===== End User Statistics ===== --}}

    {{-- CONTROL CENTER (these can navigate) --}}
    <div class="text-center mb-5">
      <h2 class="section-title mb-4">Control Center</h2>
      <div class="d-flex flex-wrap justify-content-center gap-3 gap-md-4">
        <a href="{{ route('admin.users') }}" class="btn btn-cyan btn-3d text-white px-4 px-md-5 py-4 rounded-pill">
          <i class="bi bi-people-fill fs-3"></i><br>Users List
        </a>
        <a href="{{ route('admin.products.list') }}" class="btn btn-indigo btn-3d text-white px-4 px-md-5 py-4 rounded-pill">
          <i class="bi bi-grid-3x3-gap-fill fs-3"></i><br>Manage Products
        </a>
        <a href="{{ route('admin.revenue') }}" class="btn btn-warning btn-3d text-dark px-4 px-md-5 py-4 rounded-pill">
          <i class="bi bi-cash-coin fs-3"></i><br>Revenue Details
        </a>

        <a href="{{ route('admin.discounts.global.edit') }}" class="btn btn-light btn-3d text-dark px-4 px-md-5 py-4 rounded-pill">
          <i class="bi bi-tags fs-3"></i><br>Storewide Discount
        </a>
      </div>
      <div class="text-white-50 mt-3 small">
        Tip: Per-product discount can be set in <em>Manage Products → Add/Edit</em>.
      </div>
    </div>
  </div>
  @endif

  {{-- SELLER PANEL placeholder (optional) --}}
  @if(method_exists(auth()->user(),'isSeller') && auth()->user()->isSeller())
    {!! '' !!}
  @endif

  {{-- FOOTER --}}
  <div class="text-center text-white-50 pb-4">
    <p class="mb-0 fs-6">
      <i class="bi bi-clock-history"></i>
      Updated: {{ now()->format('d M Y • h:i A') }} • MyStore Admin v2.2
    </p>
    @if(session('success')) <p class="text-success mt-2">{{ session('success') }}</p> @endif
    @if($errors->any()) <p class="text-warning mt-2">{{ $errors->first() }}</p> @endif
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ===== Dashboard-only Search =====
   - Filters items only inside the dashboard
   - Shows a small results dropdown under navbar
*/
(function(){
  const form   = document.getElementById('dashSearchForm');
  const input  = document.getElementById('dashSearchInput');
  const panel  = document.getElementById('dashSearchPanel');
  const list   = document.getElementById('dashResults');
  if(!form || !input || !panel || !list) return;

  // Dashboard search items (REVIEWS REMOVED)
  const items = [
    {label:'Total Users',        icon:'people-fill',      link:'#',                                        tags:['users','kpi','count','admin']},
    {label:'Total Products',     icon:'box-seam',         link:'#',                                        tags:['products','kpi','inventory']},
    {label:'Pending Orders',     icon:'receipt',          link:'#',                                        tags:['orders','pending','kpi']},
    {label:"Today's Revenue",    icon:'currency-rupee',   link:'#',                                        tags:['revenue','sales','money','kpi']},

    {label:'Manage Products',    icon:'grid-3x3-gap-fill', link:'{{ route('admin.products.list') }}',     tags:['manage','crud','products']},
    {label:'Users List',         icon:'people',            link:'{{ route('admin.users') }}',               tags:['users','list','admin']},

    {label:'Storewide Discount', icon:'tags',              link:'{{ route('admin.discounts.global.edit') }}',tags:['discount','offer','sale']},
    {label:'Revenue Details',    icon:'cash-coin',         link:'{{ route('admin.revenue') }}',             tags:['revenue','details']},
    {label:'Admin Dashboard',    icon:'shield-lock-fill',  link:'#',                                        tags:['dashboard','home']},
  ];

  function render(results){
    list.innerHTML = '';
    if(results.length === 0){
      list.innerHTML = '<div class="list-group-item text-white-50">No dashboard items found.</div>';
      return;
    }

    results.forEach(r=>{
      const a = document.createElement('a');
      a.href = r.link === '#' ? 'javascript:void(0)' : r.link;
      a.className = 'list-group-item list-group-item-action d-flex align-items-center gap-3 text-white search-item';
      a.innerHTML = `
        <i class="bi bi-${r.icon} fs-5 text-info"></i>
        <div class="flex-grow-1">
          <div class="fw-semibold">${r.label}</div>
          <small class="text-white-50">${r.tags.join(' · ')}</small>
        </div>
        <i class="bi bi-${r.link==='#'?'dot':'arrow-right-short'} fs-4 text-white-50"></i>
      `;
      list.appendChild(a);
    });
  }

  function filter(q){
    q = q.trim().toLowerCase();
    if(!q) return [];
    return items.filter(it =>
      it.label.toLowerCase().includes(q) ||
      it.tags.some(t => t.includes(q))
    );
  }

  input.addEventListener('input', (e)=>{
    const q = e.target.value;
    const results = filter(q);
    if(q){
      panel.classList.remove('d-none');
      render(results);
    } else {
      panel.classList.add('d-none');
    }
  });

  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    const q = input.value;
    const results = filter(q);
    panel.classList.remove('d-none');
    render(results);
  });

  // Hide panel on outside click or Esc
  document.addEventListener('click',(e)=>{
    if(!panel.contains(e.target) && !form.contains(e.target)){
      panel.classList.add('d-none');
    }
  });
  document.addEventListener('keydown',(e)=>{
    if(e.key === 'Escape'){
      panel.classList.add('d-none');
    }
  });
})();
</script>

</body>
</html>
