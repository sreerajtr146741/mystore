<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Orders • MyStore</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body{background:#f3f4f6;font-family:'Segoe UI',system-ui}
  .card-shadow{border:0;border-radius:12px;box-shadow:0 4px 18px rgba(0,0,0,.08)}
  .header{background:#0f172a;color:#fff;padding:16px 20px}
</style>
</head>
<body>
<header class="header d-flex justify-content-between align-items-center">
  <h4 class="mb-0"><i class="bi bi-receipt me-2"></i>Orders</h4>
  <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left-circle me-1"></i> Back</a>
</header>

<div class="container py-4">

  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card card-shadow p-3">
        <div class="text-muted small">Pending Orders</div>
        <div class="h4 mb-0">{{ $pendingCount }}</div>
      </div>
    </div>
  </div>

  <div class="card card-shadow p-3 mb-3">
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-md-3">
        <label class="form-label small">Status</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          @foreach(['pending','paid','shipped','cancelled','refunded'] as $s)
            <option value="{{ $s }}" {{ $status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Apply</button>
      </div>
    </form>
  </div>

  <div class="card card-shadow p-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Total (₹)</th>
            <th>Status</th>
            <th>Placed</th>
          </tr>
        </thead>
        <tbody>
        @if($hasOrders && method_exists($orders,'links'))
          @foreach($orders as $o)
          <tr>
            <td>#{{ $o->id }}</td>
            <td>{{ $o->user_id }}</td>
            <td>₹ {{ number_format($o->total ?? 0, 2) }}</td>
            <td>
              <span class="badge
                {{ ($o->status==='pending')?'bg-warning text-dark':
                   (($o->status==='paid')?'bg-success':
                   (($o->status==='shipped')?'bg-info':
                   (($o->status==='cancelled')?'bg-secondary':'bg-dark'))) }}">
                {{ ucfirst($o->status ?? 'n/a') }}
              </span>
            </td>
            <td>{{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
          @endforeach
        @else
          @foreach($orders as $o)
          <tr>
            <td>#{{ $o->id }}</td>
            <td>{{ $o->user_id }}</td>
            <td>₹ {{ number_format($o->total ?? 0, 2) }}</td>
            <td><span class="badge {{ $o->status==='pending'?'bg-warning text-dark':'bg-success' }}">{{ ucfirst($o->status) }}</span></td>
            <td>{{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
          @endforeach
        @endif
        </tbody>
      </table>
    </div>

    @if($hasOrders && method_exists($orders,'links'))
      <div class="mt-2">{{ $orders->withQueryString()->links() }}</div>
    @endif
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
