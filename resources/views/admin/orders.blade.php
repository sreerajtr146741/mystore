<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Management • Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @include('partials.premium-styles')
    <style>
        .nav-tabs .nav-link { color: #94a3b8; font-weight: 500; border: none; border-bottom: 2px solid transparent; padding: 0.75rem 1rem; }
        .nav-tabs .nav-link:hover { color: #fff; border-color: rgba(255,255,255,0.1); }
        .nav-tabs .nav-link.active { color: #fff; border-bottom-color: #fff; font-weight: 600; background: none; }
        .table-card { box-shadow: none; border: none; background: transparent; }
        .badge-count { font-size: 0.75em; padding: 2px 6px; border-radius: 10px; margin-left: 5px; background: rgba(255,255,255,0.1); color: #fff; }
        .nav-link.active .badge-count { background: #fff; color: #000; }

        /* Custom Dropdown Polish for Glass theme */
        .dropdown-menu { background: #1e293b !important; border: 1px solid rgba(255,255,255,0.2) !important; box-shadow: 0 15px 35px rgba(0,0,0,0.6) !important; padding: 0.5rem; }
        .dropdown-item { color: #e2e8f0 !important; transition: all 0.2s ease; border-radius: 6px; margin: 2px 0; }
        .dropdown-item:hover { background: #0ea5e9 !important; color: #fff !important; font-weight: 500; }
        .dropdown-divider { border-top: 1px solid rgba(255,255,255,0.1); margin: 0.5rem 0; }
        .dropdown-header { color: #94a3b8 !important; font-size: 0.75rem !important; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 0.5rem 1rem; }
        
        .nav-scroller { scrollbar-width: none; -ms-overflow-style: none; }
        .nav-scroller::-webkit-scrollbar { display: none; }
        .hover-bg-glass:hover { background: rgba(255,255,255,0.05) !important; }
    </style>
</head>
<body>

@php
    $palette = ['#0ea5e9','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444','#14b8a6','#84cc16'];
    $color = $palette[(crc32(auth()->user()->name) % count($palette))];
    $initials = collect(explode(' ', auth()->user()->name))->map(fn($p)=>mb_substr($p,0,1))->take(2)->implode('');
@endphp

<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-white border-opacity-10 py-3 sticky-top" style="background: rgba(15, 23, 42, 0.8) !important; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('admin.dashboard') }}">
      <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info border border-info border-opacity-25" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-speedometer2"></i>
      </div>
      <span class="text-white tracking-tight">Admin<span class="text-info">Portal</span></span>
    </a>

    <div class="d-flex align-items-center gap-2 ms-auto">
        <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="bi bi-shop me-1"></i>View Store
        </a>

        <div class="dropdown">
            <button class="btn btn-glass d-flex align-items-center gap-2 shadow-sm" type="button" data-bs-toggle="dropdown">
                <span class="avatar shadow-sm border border-white border-opacity-25" style="background: {{ $color }}; width: 32px; height: 32px; font-size: 0.8rem;">{{ $initials }}</span>
                <span class="text-white small fw-semibold d-none d-md-inline">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                <li><a href="{{ route('profile.edit') }}" class="dropdown-item py-2 d-flex align-items-center gap-2"><i class="bi bi-person-circle fs-5 text-info"></i> My Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2"><i class="bi bi-box-arrow-right fs-5"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
  </div>
</nav>

<div class="page-wrap container position-relative mt-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-lg border-0 border-start border-success border-4 mb-4" role="alert" style="background: rgba(25, 135, 84, 0.1); color: #2ecc71;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="section-title mb-1 fw-bold text-white"><i class="bi bi-receipt-cutoff me-2 text-info"></i>Order Management</h2>
            <p class="text-white-50 small mb-0">Monitor and process store transactions</p>
        </div>
        
        <form action="{{ route('admin.orders') }}" method="GET" class="w-100" style="max-width: 400px;">
            <div class="input-group">
                <span class="input-group-text bg-white bg-opacity-5 border-white border-opacity-10 text-white-50"><i class="bi bi-search"></i></span>
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <input class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white shadow-none" type="search" name="search" placeholder="Order ID or Email..." value="{{ request('search') }}">
                <button class="btn btn-info px-4 fw-bold" type="submit">Filter</button>
            </div>
        </form>
    </div>

    <!-- Tabs (Glass Navigation) -->
    <div class="card-glass mb-4 overflow-hidden">
        <div class="nav-scroller">
            <ul class="nav nav-pills p-2 gap-2 flex-nowrap" style="background: rgba(255,255,255,0.03);">
                @php
                    $tabs = [
                        'all' => ['label' => 'All Orders', 'icon' => 'grid'],
                        'placed' => ['label' => 'New', 'icon' => 'plus-circle'],
                        'processing' => ['label' => 'Processing', 'icon' => 'gear-wide-connected'],
                        'shipped' => ['label' => 'Shipped', 'icon' => 'truck'],
                        'delivered' => ['label' => 'Delivered', 'icon' => 'check-circle'],
                        'cancelled' => ['label' => 'Cancelled', 'icon' => 'x-circle'],
                        'return_requested' => ['label' => 'Requests', 'icon' => 'arrow-return-left'],
                        'returned' => ['label' => 'Returned', 'icon' => 'box-arrow-left'],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                <li class="nav-item">
                    <a class="nav-link rounded-3 py-2 px-3 d-flex align-items-center gap-2 {{ $status == $key ? 'bg-info text-white fw-bold active' : 'text-white-50 hover-bg-glass' }}" 
                       href="{{ route('admin.orders', ['status' => $key]) }}">
                        <i class="bi bi-{{ $tab['icon'] }}"></i>
                        <span>{{ $tab['label'] }}</span>
                        <span class="badge {{ $status == $key ? 'bg-white text-info' : 'bg-white bg-opacity-10 text-white' }} rounded-pill" style="font-size: 0.7rem;">{{ $counts[$key] }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Order List (Premium Table) -->
    <div class="card-glass border-0 overflow-hidden shadow-2xl">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-white border-0">
                <thead style="background: rgba(255,255,255,0.05);">
                    <tr class="border-0">
                        <th class="ps-4 py-3 text-white-50 small text-uppercase fw-bold tracking-widest border-0">Order ID & Customer</th>
                        <th class="py-3 text-white-50 small text-uppercase fw-bold tracking-widest border-0">Dates</th>
                        <th class="py-3 text-white-50 small text-uppercase fw-bold tracking-widest border-0">Status</th>
                        <th class="py-3 text-white-50 small text-uppercase fw-bold tracking-widest border-0 text-center">Total Amount</th>
                        <th class="text-end pe-4 py-3 text-white-50 small text-uppercase fw-bold tracking-widest border-0">Actions</th>
                    </tr>
                </thead>
                <tbody id="order-rows" class="border-0">
                    @include('admin.orders.partials.row', ['orders' => $orders])
                </tbody>
            </table>
        </div>
        
        @if($orders->isEmpty())
            <div class="text-center py-5 text-white-50 bg-white bg-opacity-5">
                <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                <p class="mb-0">No active orders found in this category.</p>
            </div>
        @endif

        @if($orders->hasMorePages())
            <div id="loading-spinner" class="text-center py-4 d-none">
                <div class="spinner-border text-info" role="status"></div>
            </div>
            <div id="sentinel" style="height:2px;"></div>
            <div id="pagination-data" data-next-url="{{ $orders->nextPageUrl() }}" style="display:none;"></div>
        @endif
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Infinite Scroll
    let nextUrl = document.getElementById('pagination-data')?.dataset.nextUrl;
    const sentinel = document.getElementById('sentinel');
    const spinner = document.getElementById('loading-spinner');
    const container = document.getElementById('order-rows');
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
                    attachFormListeners(); // Re-attach for new rows
                } else {
                    observer.disconnect();
                    sentinel.remove();
                }
            })
            .catch(()=> { spinner.classList.add('d-none'); isLoading = false; });
        }
    }

    // 2. Status Confirmations
    function attachFormListeners() {
        // Warning messages map (copied from original)
        const warningMessages = {
            'placed': "Reset status to Placed?",
            'processing': "Mark as Processing? (Packing)",
            'shipped': "Mark as Shipped? (On the way)",
            'delivered': "Mark as Delivered? (Completed)",
            'cancelled': "Cancel this order? This cannot be undone clearly."
        };

        // We use delegation for newly added forms too, but the original script used direct attachment.
        // Let's use delegation on the container for robustness.
    }
    
    // Delegation for Status Forms (handles both initial and dynamic rows)
    document.getElementById('order-rows').addEventListener('submit', function(e) {
        if (e.target.classList.contains('status-update-form')) {
            e.preventDefault();
            const form = e.target;
            const input = form.querySelector('input[name="status"]');
            const newStatus = input ? input.value : 'unknown';
            
            const warningMessages = {
                'placed': "Reset status to Placed?",
                'processing': "Mark as Processing? (Packing)",
                'shipped': "Mark as Shipped? (On the way)",
                'delivered': "Mark as Delivered? (Completed)",
                'cancelled': "Cancel this order? This cannot be undone clearly.",
                'return_requested': "Manually set to Return Requested?",
                'returned': "Mark as Returned? (Refund logic not automated here)"
            };
            
            const msg = warningMessages[newStatus] || `Update status to ${newStatus}?`;

            if (confirm(msg)) {
                form.submit();
            }
        }
    });
});
</script>
</body>
</html>
