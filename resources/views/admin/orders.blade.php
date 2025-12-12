<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Management • Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: #f8f9fa; }
    .nav-tabs .nav-link { color: #4b5563; font-weight: 500; border: none; border-bottom: 2px solid transparent; padding: 0.75rem 1rem; }
    .nav-tabs .nav-link:hover { color: #111827; border-color: #e5e7eb; }
    .nav-tabs .nav-link.active { color: #2563eb; border-bottom-color: #2563eb; font-weight: 600; background: none; }
    .table-card { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06); border: none; }
    .badge-count { font-size: 0.75em; padding: 2px 6px; border-radius: 10px; margin-left: 5px; background: #e5e7eb; color: #374151; }
    .nav-link.active .badge-count { background: #dbeafe; color: #1e40af; }
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
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">#{{ $order->id }}</div>
                                <div class="small text-muted mb-1">{{ $order->user->name ?? 'Guest' }}</div>
                                <div class="small text-muted">{{ $order->items->count() }} Items</div>
                            </td>
                            <td>
                                <div class="small text-muted">Ordered: {{ $order->created_at->format('M d, Y') }}</div>
                                @if($order->status == 'delivered')
                                    <div class="small text-success fw-bold">Delivered: {{ $order->updated_at->format('M d') }}</div>
                                @elseif($order->status == 'shipped')
                                    <div class="small text-primary fw-bold">Shipped: {{ $order->updated_at->format('M d') }}</div>
                                @elseif($order->status == 'processing')
                                    <div class="small text-info fw-bold">Processing: {{ $order->updated_at->format('M d') }}</div>
                                @elseif($order->delivery_date)
                                     <div class="small text-muted">Exp: {{ $order->delivery_date->format('M d') }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = match($order->status) {
                                        'placed' => 'bg-secondary',
                                        'processing' => 'bg-info text-dark',
                                        'shipped' => 'bg-primary',
                                        'out_for_delivery' => 'bg-warning text-dark',
                                        'delivered' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-light text-dark border',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} rounded-pill font-monospace fw-normal px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="fw-bold">₹{{ number_format($order->total, 2) }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Quick Action Button --}}
                                    @if($order->status == 'placed')
                                        <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="processing">
                                            <button class="btn btn-sm btn-outline-primary" title="Accept Order">
                                                Accept
                                            </button>
                                        </form>
                                    @elseif($order->status == 'processing')
                                        <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="shipped">
                                            <button class="btn btn-sm btn-warning text-dark" title="Ship Order">
                                                Ship
                                            </button>
                                        </form>
                                    @elseif($order->status == 'shipped')
                                        <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="d-inline status-update-form">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="delivered">
                                            <button class="btn btn-sm btn-success" title="Mark Delivered">
                                                Deliver
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Manual Status Override (Dropdown) --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><h6 class="dropdown-header">Update Status</h6></li>
                                            @foreach(['placed','processing','shipped','delivered','cancelled'] as $s)
                                                <li>
                                                    <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="status-update-form">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $s }}">
                                                        <button class="dropdown-item {{ $order->status == $s ? 'active' : '' }}">{{ ucfirst($s) }}</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.orders.download', $order->id) }}"><i class="bi bi-download me-2"></i>Invoice</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-3"></i>
                                No orders found in this category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.status-update-form');
    
    // Status Messages Map
    const warningMessages = {
        'placed': "Reset status to Placed?",
        'processing': "Mark as Processing? (Packing)",
        'shipped': "Mark as Shipped? (On the way)",
        'delivered': "Mark as Delivered? (Completed)",
        'cancelled': "Cancel this order? This cannot be undone clearly."
    };

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = this.querySelector('input[name="status"]');
            const newStatus = input ? input.value : 'unknown';
            
            // Only confirm if not the primary quick action (or maybe always confirm?) 
            // User requested confirmation for all status changes before.
            const msg = warningMessages[newStatus] || `Update status to ${newStatus}?`;

            if (confirm(msg)) {
                this.submit();
            }
        });
    });
});
</script>
</body>
</html>
