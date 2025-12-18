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
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <span class="navbar-text text-white">Order Management</span>

  </div>
</nav>

<div class="container pb-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold text-gray-800">Your Orders</h3>
        <form action="{{ route('admin.orders') }}" method="GET" class="d-flex gap-2" role="search">
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            <input class="form-control" type="search" name="search" placeholder="Search Order ID or Email" value="{{ request('search') }}" aria-label="Search">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4 px-2 border-bottom">
        <li class="nav-item">
            <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'all']) }}">
                All <span class="badge-count">{{ $counts['all'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'placed' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'placed']) }}">
                New <span class="badge-count">{{ $counts['placed'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'processing' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'processing']) }}">
                Processing <span class="badge-count">{{ $counts['processing'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'shipped' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'shipped']) }}">
                Shipped <span class="badge-count">{{ $counts['shipped'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'delivered' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'delivered']) }}">
                Delivered <span class="badge-count">{{ $counts['delivered'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'cancelled' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'cancelled']) }}">
                Cancelled <span class="badge-count">{{ $counts['cancelled'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'return_requested' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'return_requested']) }}">
                Requests <span class="badge-count">{{ $counts['return_requested'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'returned' ? 'active' : '' }}" href="{{ route('admin.orders', ['status' => 'returned']) }}">
                Returned <span class="badge-count">{{ $counts['returned'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Order List -->
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Order Details</th>
                        <th>Dates</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="order-rows">
                    @include('admin.orders.partials.row', ['orders' => $orders])
                </tbody>
            </table>
        </div>
        @if($orders->hasMorePages())
            <div id="loading-spinner" class="text-center py-4 d-none">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div id="sentinel" style="height:20px;"></div>
            <div id="pagination-data" data-next-url="{{ $orders->nextPageUrl() }}" style="display:none;"></div>
        @endif
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
