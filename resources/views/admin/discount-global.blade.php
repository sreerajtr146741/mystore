{{-- resources/views/admin/discounts/global.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Storewide Discount • Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body { background:#0b0c10; color:#e9ecef; }
    .card { background:#15171b; border-color:#1f2228; }
    .form-control, .form-select { background:#0f1115; color:#e9ecef; border-color:#2a2e36; }
    .form-control:focus, .form-select:focus { background:#0f1115; color:#fff; }
    label.form-label, .form-check-label { color:#cdd3da; }
    hr { border-color:#2a2e36; }
    .alert-success { color:#0f5132; background:#d1e7dd; border-color:#badbcc; }
    .alert-danger  { color:#842029; background:#f8d7da; border-color:#f5c2c7; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">MyStore Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
      </a>
      <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
        <button class="btn btn-warning btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
      </form>
    </div>
  </div>
</nav>

<main class="container py-4">

  <h2 class="mb-3">Storewide Discount</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Please fix the following:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  @php
    $active = $active ?? ($data['active'] ?? 0);
    $type   = $type   ?? ($data['type']   ?? 'percent');
    $value  = $value  ?? ($data['value']  ?? 0);
    $starts = $starts ?? ($data['starts_at'] ?? ($data['starts'] ?? null));
    $ends   = $ends   ?? ($data['ends_at']   ?? ($data['ends']   ?? null));
  @endphp

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.discounts.global.update') }}" class="row g-3">
        @csrf

        <div class="col-md-2 form-check mt-4">
          <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
                 {{ old('active', $active) ? 'checked' : '' }}>
          <label class="form-check-label" for="active">Active</label>
        </div>

        <div class="col-md-3">
          <label class="form-label">Type</label>
          @php $t = old('type', $type); @endphp
          <select name="type" class="form-select" required>
            <option value="percent" {{ $t==='percent' ? 'selected' : '' }}>Percent (%)</option>
            <option value="flat"    {{ $t==='flat'    ? 'selected' : '' }}>Flat (₹)</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Value</label>
          <input name="value" type="number" min="0" step="0.01" class="form-control"
                 value="{{ old('value', $value) }}" required>
        </div>

        <div class="col-md-2">
          <label class="form-label">Starts</label>
          <input name="starts" type="datetime-local" class="form-control"
                 value="{{ old('starts', $starts ? \Carbon\Carbon::parse($starts)->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="col-md-2">
          <label class="form-label">Ends</label>
          <input name="ends" type="datetime-local" class="form-control"
                 value="{{ old('ends', $ends ? \Carbon\Carbon::parse($ends)->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="col-12">
          <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button>
          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
        </div>
      </form>

      <hr class="my-4">
      <p class="text-muted small mb-0">
        Rule: product-specific discount overrides global discount when active and within its time window.
      </p>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
