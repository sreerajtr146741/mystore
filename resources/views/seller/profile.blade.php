<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Profile • Seller • MyStore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:#f8f9fa; color:#212529; }
    .profile-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .profile-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 16px 16px 0 0; }
    .avatar { width: 100px; height: 100px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 48px; color: #667eea; margin: 0 auto 20px; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('seller.dashboard') }}">MyStore <span class="badge bg-white text-primary ms-1">Seller</span></a>
    <div class="d-flex gap-3 align-items-center">
        <a href="{{ route('seller.dashboard') }}" class="btn btn-outline-light btn-sm">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button class="btn btn-light btn-sm fw-bold text-primary">Logout</button>
        </form>
    </div>
  </div>
</nav>

<main class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
          <div class="fw-semibold mb-1">Please fix the following errors:</div>
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="profile-card">
        <div class="profile-header text-center">
          <div class="avatar">
            <i class="bi bi-person-fill"></i>
          </div>
          <h3 class="mb-1">{{ $user->name }}</h3>
          <p class="mb-0 opacity-75">
            <i class="bi bi-envelope me-1"></i>{{ $user->email }}
          </p>
          <span class="badge bg-white text-primary mt-2">{{ ucfirst($user->role) }}</span>
        </div>

        <div class="p-4">
          <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Profile</h5>
          
          <form action="{{ route('seller.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">First Name</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                <div class="form-text">Changing email will require verification</div>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control" rows="3" required>{{ old('address', $user->address) }}</textarea>
              </div>

              <div class="col-12">
                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-key me-2"></i>Change Password (Optional)</h6>
                <div class="alert alert-info">
                  <i class="bi bi-info-circle me-2"></i>Leave blank to keep current password
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="password" class="form-control" minlength="6" placeholder="Enter new password">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="6" placeholder="Confirm new password">
              </div>

              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                  <i class="bi bi-check-circle me-2"></i>Save Changes
                </button>
                <a href="{{ route('seller.dashboard') }}" class="btn btn-light btn-lg px-5 ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card mt-4 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Account Information</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <small class="text-muted d-block">Account Status</small>
              <span class="badge bg-success">{{ ucfirst($user->status) }}</span>
            </div>
            <div class="col-md-6">
              <small class="text-muted d-block">Member Since</small>
              <strong>{{ $user->created_at->format('F d, Y') }}</strong>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
