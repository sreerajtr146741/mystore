<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background:#0b0c10; color:#e9ecef; min-height:100vh; }
        .stat-card { background:#1f2937; border:none; border-radius:12px; transition:transform 0.2s; color:#e9ecef; }
        .stat-card:hover { transform:translateY(-4px); }
        .stat-card h3, .stat-card h6 { color:#fff; }
        .stat-card .text-muted { color:#9ca3af !important; }
        .stat-card small { color:#d1d5db; }
        .stat-icon { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; }
        .table { color:#e9ecef; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-shop me-1"></i>View Store
            </a>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button class="btn btn-warning btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Users</div>
                        <h3 class="mb-0">{{ number_format($stats['total_users']) }}</h3>
                        <small class="text-success"><i class="bi bi-arrow-up"></i> Active: {{ $stats['active_users'] }}</small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-25 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Products</div>
                        <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
                        <small class="text-info">In Catalog</small>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-25 text-info">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Orders</div>
                        <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                        <small class="text-warning">All Time</small>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-25 text-warning">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Revenue</div>
                        <h3 class="mb-0">₹{{ number_format($stats['total_revenue'], 2) }}</h3>
                        <small class="text-success"><i class="bi bi-graph-up"></i> Lifetime</small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-25 text-success">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h6 class="text-muted mb-3">User Breakdown</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-circle-fill text-primary me-2" style="font-size:8px"></i>Buyers</span>
                    <strong>{{ $stats['buyers'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-circle-fill text-info me-2" style="font-size:8px"></i>Sellers</span>
                    <strong>{{ $stats['sellers'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-circle-fill text-warning me-2" style="font-size:8px"></i>Suspended</span>
                    <strong>{{ $stats['suspended_users'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-circle-fill text-danger me-2" style="font-size:8px"></i>Blocked</span>
                    <strong>{{ $stats['blocked_users'] }}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card stat-card p-3">
                <h6 class="text-muted mb-3">Revenue Trend (Last 6 Months)</h6>
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Orders -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h6 class="text-muted mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.manage') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box me-2"></i>Manage Products
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-people me-2"></i>Manage Users
                    </a>
                    <a href="{{ route('admin.orders') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-receipt me-2"></i>View All Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card stat-card p-3">
                <h6 class="text-muted mb-3">Recent Orders</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($order->total, 2) }}</td>
                                    <td><span class="badge bg-success">{{ ucfirst($order->status) }}</span></td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No orders yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(array_column($monthlyRevenue, 'month')),
        datasets: [{
            label: 'Revenue (₹)',
            data: @json(array_column($monthlyRevenue, 'revenue')),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#9ca3af' },
                grid: { color: '#374151' }
            },
            x: {
                ticks: { color: '#9ca3af' },
                grid: { color: '#374151' }
            }
        }
    }
});
</script>
</body>
</html>
