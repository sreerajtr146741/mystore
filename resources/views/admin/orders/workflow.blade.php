<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Workflow • Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body{background:#f8f9fa;}
  .nav-tabs .nav-link { color: #495057; }
  .nav-tabs .nav-link.active { font-weight: bold; color: #0d6efd; }
  .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <span class="navbar-text text-white">Order Workflow Pipeline</span>
  </div>
</nav>

<div class="container pb-5">

  @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="placed-tab" data-bs-toggle="tab" data-bs-target="#placed" type="button" role="tab">
        <i class="bi bi-journal-plus me-1"></i> Placed ({{ $placed->count() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="processing-tab" data-bs-toggle="tab" data-bs-target="#processing" type="button" role="tab">
        <i class="bi bi-gear me-1"></i> Processing ({{ $processing->count() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="shipped-tab" data-bs-toggle="tab" data-bs-target="#shipped" type="button" role="tab">
        <i class="bi bi-truck me-1"></i> Shipped ({{ $shipped->count() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab">
        <i class="bi bi-check-circle me-1"></i> Delivered ({{ $delivered->count() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
        <i class="bi bi-x-circle me-1"></i> Cancelled ({{ $cancelled->count() }})
      </button>
    </li>
  </ul>

  <div class="tab-content" id="orderTabsContent">
    
    <!-- PLACED ORDERS -->
    <div class="tab-pane fade show active" id="placed" role="tabpanel">
       @include('admin.orders.partials.table', ['orders' => $placed, 'nextStatus' => 'processing', 'btnLabel' => 'Accept Order', 'btnClass' => 'btn-primary'])
    </div>

    <!-- PROCESSING ORDERS -->
    <div class="tab-pane fade" id="processing" role="tabpanel">
       @include('admin.orders.partials.table', ['orders' => $processing, 'nextStatus' => 'shipped', 'btnLabel' => 'Ship Order', 'btnClass' => 'btn-warning text-dark'])
    </div>

    <!-- SHIPPED ORDERS -->
    <div class="tab-pane fade" id="shipped" role="tabpanel">
       @include('admin.orders.partials.table', ['orders' => $shipped, 'nextStatus' => 'delivered', 'btnLabel' => 'Mark Delivered', 'btnClass' => 'btn-success'])
    </div>

    <!-- DELIVERED ORDERS -->
    <div class="tab-pane fade" id="delivered" role="tabpanel">
      <div class="card p-3">
        @if($delivered->isEmpty())
          <div class="text-center text-muted py-4">No delivered orders.</div>
        @else
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Order ID</th><th>User</th><th>Total</th><th>Date</th><th>Action</th></tr></thead>
              <tbody>
                @foreach($delivered as $order)
                <tr>
                  <td>#{{ $order->id }}</td>
                  <td>{{ $order->user->name ?? 'Guest' }}<br><small class="text-muted">{{ $order->user->email ?? '' }}</small></td>
                  <td>₹{{ number_format($order->total, 2) }}</td>
                  <td>{{ $order->created_at->format('M d, Y') }}</td>
                  <td><span class="badge bg-success">Completed</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

    <!-- CANCELLED ORDERS -->
    <div class="tab-pane fade" id="cancelled" role="tabpanel">
      <!-- Similar read-only table -->
       <div class="card p-3">
        @if($cancelled->isEmpty())
          <div class="text-center text-muted py-4">No cancelled orders.</div>
        @else
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Order ID</th><th>User</th><th>Total</th><th>Date</th><th>Action</th></tr></thead>
              <tbody>
                @foreach($cancelled as $order)
                <tr>
                  <td>#{{ $order->id }}</td>
                  <td>{{ $order->user->name ?? 'Guest' }}<br><small class="text-muted">{{ $order->user->email ?? '' }}</small></td>
                  <td>₹{{ number_format($order->total, 2) }}</td>
                  <td>{{ $order->created_at->format('M d, Y') }}</td>
                  <td><span class="badge bg-danger">Cancelled</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
