{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard • MyStore</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@include('partials.premium-styles')

<style>
  /* Standalone Dashboard Helpers (not in premium-styles) */
  .dashboard-card { height: 100%; display: flex; flex-direction: column; }
  .bento-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; }
  .action-icon { width: 48px; height: 48px; border-radius: 12px; display: grid; place-items: center; font-size: 1.5rem; }

  /* Avatar Silhouette */
  .avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: inline-flex; align-items:center; justify-content:center;
    font-weight:700; color:#fff; user-select:none;
    border:2px solid rgba(255,255,255,0.5); overflow: hidden;
  }
  .avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .text-gradient { background:linear-gradient(90deg,#00d4ff,#ff00c8); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
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
        {{-- Dashboard Navbar Link pointing to Store --}}
        <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
          <i class="bi bi-shop me-1"></i>View Store
        </a>

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
              <div class="avatar shadow-sm">
                <img src="{{ $photo }}" alt="avatar">
              </div>
            @else
              <span class="avatar shadow-sm" style="background: {{ $color }}">{{ $initials }}</span>
            @endif
            <span class="text-white small fw-semibold d-none d-md-inline">{{ $name }}</span>
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



  {{-- ADMIN PANEL --}}
  @if(method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
  <div class="mb-5">
    <h2 class="section-title mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Admin Dashboard</h2>

    {{-- KPI CARDS — UNIFORM HEIGHT --}}
    {{-- KPI CARDS — VERTICAL STACK (2 PER ROW) --}}
    <div class="row g-4 mb-4">
      {{-- Total Accounts --}}
      <div class="col-lg-6 col-md-6">
        <div class="card-glass hover-lift p-4 text-white d-flex flex-column justify-content-between" style="height: 160px; cursor: pointer;" onclick="location.href='{{ route('admin.users') }}'">
          <div>
            <div class="d-flex justify-content-between">
                <i class="bi bi-people-fill fs-3 text-info opacity-75"></i>
                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 h-100 align-self-center">Accounts</span>
            </div>
            <div class="opacity-75 small text-uppercase fw-bold mt-2">Managed Base</div>
          </div>
          <div>
            <h2 class="fw-bold mb-0 text-white">{{ $stats['total_users'] ?? 0 }}</h2>
            <div class="small text-info opacity-75 text-truncate"><i class="bi bi-globe me-1"></i>Network Reach</div>
          </div>
        </div>
      </div>

      {{-- Total Inventory --}}
      <div class="col-lg-6 col-md-6">
        <div class="card-glass hover-lift p-4 text-white d-flex flex-column justify-content-between" style="height: 160px; cursor: pointer;" onclick="location.href='{{ route('admin.products.list') }}'">
          <div>
             <div class="d-flex justify-content-between">
                <i class="bi bi-box-seam-fill fs-3 text-primary opacity-75"></i>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 h-100 align-self-center">Inventory</span>
            </div>
            <div class="opacity-75 small text-uppercase fw-bold mt-2">Active Products</div>
          </div>
          <div>
            <h2 class="fw-bold mb-0 text-white">{{ $stats['total_products'] ?? 0 }}</h2>
            <div class="text-primary-emphasis opacity-75 small text-truncate"><i class="bi bi-plus-circle me-1"></i>{{ $stats['new_today'] ?? 0 }} new today</div>
          </div>
        </div>
      </div>

      {{-- Aggregate Revenue --}}
      <div class="col-lg-6 col-md-6">
        <div class="card-glass hover-lift p-4 text-white d-flex flex-column justify-content-between" style="height: 160px; border: 1px solid rgba(245, 158, 11, 0.3) !important;">
          <div>
            <div class="d-flex justify-content-between">
                <i class="bi bi-bank2 fs-3 text-warning opacity-75"></i>
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 h-100 align-self-center">Historical</span>
            </div>
            <div class="opacity-75 small text-uppercase fw-bold mt-2 text-truncate">Gross Revenue</div>
          </div>
          <div>
            <h2 class="fw-bold mb-0 text-warning">₹{{ number_format($stats['total_revenue'] ?? 0) }}</h2>
            <div class="text-white-50 small text-truncate">Lifetime Performance</div>
          </div>
        </div>
      </div>

      {{-- Pending Orders --}}
      <div class="col-lg-6 col-md-6">
        <div class="card-glass hover-lift p-4 text-white d-flex flex-column justify-content-between" style="height: 160px; cursor: pointer;" onclick="location.href='{{ route('admin.orders') }}'">
          <div>
            <div class="d-flex justify-content-between">
                <i class="bi bi-receipt fs-3 text-success opacity-75"></i>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 h-100 align-self-center">Orders</span>
            </div>
            <div class="opacity-75 small text-uppercase fw-bold mt-2">Pending Tasks</div>
          </div>
          <div>
            <h2 class="fw-bold mb-0 text-white">{{ $adminExtras['pending_orders'] ?? 0 }}</h2>
            <div class="small {{ ($adminExtras['pending_orders'] ?? 0) > 0 ? 'text-warning' : 'text-success' }} text-truncate">
              <i class="bi bi-info-circle me-1"></i>{{ ($adminExtras['pending_orders'] ?? 0) > 0 ? 'Requires attention' : 'No items pending' }}
            </div>
          </div>
        </div>
      </div>

      {{-- Support Inbox --}}
      <div class="col-lg-6 col-md-6">
        <div class="card-glass hover-lift p-4 text-white d-flex flex-column justify-content-between" style="height: 160px; cursor: pointer;" onclick="location.href='{{ route('admin.messages.index') }}'">
          <div>
            <div class="d-flex justify-content-between">
                <i class="bi bi-chat-left-text-fill fs-3 text-info opacity-75" style="color: #06b6d4 !important;"></i>
                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 h-100 align-self-center" style="color: #06b6d4 !important;">Messages</span>
            </div>
            <div class="opacity-75 small text-uppercase fw-bold mt-2">User Inquiries</div>
          </div>
          <div>
            <h2 class="fw-bold mb-0 text-white">{{ $adminExtras['pending_messages'] ?? 0 }}</h2>
            <div class="small {{ ($adminExtras['pending_messages'] ?? 0) > 0 ? 'text-info' : 'text-white-50' }} text-truncate" style="{{ ($adminExtras['pending_messages'] ?? 0) > 0 ? 'color: #22d3ee !important;' : '' }}">
              <i class="bi bi-envelope-open me-1"></i>{{ ($adminExtras['pending_messages'] ?? 0) > 0 ? 'New inbox items' : 'No new mail' }}
            </div>
          </div>
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

    <div class="row g-4 mb-5">
      {{-- User Statistics --}}
      <div class="col-lg-6">
        <div class="card-glass p-4 h-100 text-white d-flex flex-column" style="min-height: 520px;">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-info"></i>User Network</h4>
            <span class="badge bg-info bg-opacity-20 text-white border border-info border-opacity-50 px-3 py-2 rounded-pill shadow-sm">Real-time Analytics</span>
          </div>

          <div class="row align-items-center flex-grow-1 gy-4">
            <div class="col-md-6 text-center">
               <div style="height: 260px; position: relative;">
                  <canvas id="userDistChart"></canvas>
                  <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                    <div class="h3 fw-bold mb-0 text-white">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="small text-white-50 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.5px;">Users</div>
                  </div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="d-grid gap-3">
                  <div class="p-4 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10 d-flex align-items-center gap-3 hover-lift border-start border-4 border-info">
                     <i class="bi bi-cart-fill fs-2 text-info"></i>
                     <div class="flex-grow-1 overflow-hidden">
                        <div class="text-white text-uppercase fw-bold tracking-widest mb-1" style="font-size: 0.8rem;">Active Buyers</div>
                        <div class="display-6 fw-bold text-info lh-1 mb-0">{{ $userStats['buyers'] ?? 0 }}</div>
                     </div>
                  </div>
               </div>
            </div>
          </div>

          <div class="mt-4 row g-2">
             <div class="col-4">
                <div class="p-3 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 text-center shadow-sm">
                   <div class="text-danger-emphasis text-uppercase fw-bold mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;">Suspended</div>
                   <div class="h4 mb-0 fw-bold text-white">{{ $stats['suspended_users'] ?? 0 }}</div>
                </div>
             </div>
             <div class="col-4">
                <div class="p-3 rounded-4 bg-secondary bg-opacity-10 border border-secondary border-opacity-25 text-center shadow-sm">
                   <div class="text-secondary-emphasis text-uppercase fw-bold mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;">Deleted</div>
                   <div class="h4 mb-0 fw-bold text-white">{{ $stats['blocked_users'] ?? 0 }}</div>
                </div>
             </div>
             <div class="col-4">
                <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 text-center shadow-sm">
                   <div class="text-success-emphasis text-uppercase fw-bold mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;">Active (30d)</div>
                   <div class="h4 mb-0 fw-bold text-white">{{ $userStats['active_30d'] ?? 0 }}</div>
                </div>
             </div>
          </div>

          <div class="mt-4 p-3 rounded-4 bg-info bg-opacity-5 border border-info border-opacity-10 text-center position-relative overflow-hidden">
             <div class="small text-info text-uppercase tracking-widest fw-bold opacity-75">Aggregate Database Entries</div>
             <div class="h3 fw-bold mb-0 text-white">{{ $stats['total_users'] ?? 0 }}</div>
          </div>
        </div>
      </div>

      {{-- Revenue Analysis --}}
      <div class="col-lg-6">
        <div class="card-glass p-4 h-100 text-white d-flex flex-column" style="min-height: 500px;">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-warning"></i>Revenue History</h4>
            <div class="d-flex gap-2">
                <span class="badge bg-warning bg-opacity-20 text-white border border-warning border-opacity-50 px-3 py-2 rounded-pill shadow-sm">Store Performance</span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">{{ $revenue['growth'] ?? '+0%' }} Growth</span>
            </div>
          </div>
          <div class="flex-grow-1" style="min-height: 300px; position: relative;">
            <canvas id="revenueChart"></canvas>
          </div>
          <div class="mt-4 p-4 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 text-center shadow-lg position-relative overflow-hidden">
             <div class="position-absolute opacity-10" style="bottom: -10px; right: 10px; font-size: 4rem;"><i class="bi bi-cash-coin"></i></div>
             <div class="small text-warning text-uppercase tracking-widest fw-bold mb-2">Historical Gross Revenue</div>
             <div class="display-5 fw-bold mb-0 text-white lh-1">₹{{ number_format($stats['total_revenue'] ?? 0) }}</div>
          </div>
        </div>
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
      Updated: {{ now()->format('d M Y • h:i A') }} • MyStore Admin v2.3 (Latest)
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

// -- User Distribution Chart --
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('userDistChart');
  if(!ctx) return;
  
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Buyers', 'Sellers', 'Admins'],
      datasets: [{
        data: [
          {{ $userStats['buyers'] ?? 0 }}, 
          {{ $userStats['sellers'] ?? 0 }}, 
          {{ $userStats['admins'] ?? 0 }}
        ],
        backgroundColor: ['#0ea5e9', '#6366f1', '#f59e0b'],
        hoverBackgroundColor: ['#38bdf8', '#818cf8', '#fbbf24'],
        borderWidth: 0,
        weight: 1,
        spacing: 5,
        borderRadius: 10,
        hoverOffset: 20
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '82%',
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1e293b',
          padding: 12,
          displayColors: true,
          cornerRadius: 8,
          titleFont: { size: 14, weight: 'bold' },
          bodyFont: { size: 13 }
        }
      }
    }
  });

  // -- Revenue Chart --
  const revCtx = document.getElementById('revenueChart');
  if(revCtx) {
    new Chart(revCtx, {
      type: 'line',
      data: {
        labels: {!! json_encode(collect($monthlyRevenue)->pluck('month')) !!},
        datasets: [{
          label: 'Revenue (₹)',
          data: {!! json_encode(collect($monthlyRevenue)->pluck('revenue')) !!},
          borderColor: '#f59e0b',
          backgroundColor: 'rgba(245, 158, 11, 0.1)',
          fill: true,
          tension: 0.4,
          pointRadius: 6,
          pointHoverRadius: 8,
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: '#1e293b',
            titleColor: '#fff',
            bodyColor: '#cbd5e1',
            borderColor: 'rgba(255,255,255,0.1)',
            borderWidth: 1
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(255,255,255,0.05)' },
            ticks: { color: '#cbd5e1' }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#cbd5e1' }
          }
        }
      }
    });
  }
});
</script>

</body>
</html>
