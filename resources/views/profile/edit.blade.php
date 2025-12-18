{{-- resources/views/profile/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Profile • MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .page-wrap { max-width: 1200px; margin-inline: auto; }
    .profile-card { border: none; border-radius: 24px; overflow: hidden; box-shadow: 0 32px 70px rgba(17,24,39,.15); }
    .profile-hero { background: linear-gradient(135deg, #6d28d9 0%, #4c1d95 50%, #3b82f6 100%); color:#fff; padding: 44px 40px; }
    .avatar-xl { width:120px; height:120px; border-radius:50%; overflow:hidden; border:4px solid rgba(255,255,255,.92); box-shadow:0 16px 40px rgba(0,0,0,.35); }
    .glass { background: rgba(255,255,255,.85); backdrop-filter: blur(8px); border: 1px solid rgba(15,23,42,.06); border-radius: 16px; }
    .field { display:flex; flex-direction:column; gap:.5rem; }
    .section-title { font-weight: 900; color:#4c1d95; text-align:center; margin-bottom: 1rem; font-size: 1.55rem; }
    .section-desc { text-align:center; color:#64748b; margin-top:-.35rem; margin-bottom: 1.2rem; font-size: .98rem; }
    .panel-soft { padding: 26px; border-radius: 20px; background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border: 1px solid #e9d5ff; box-shadow: 0 12px 28px rgba(76,29,149,.12); }
    .divider { height:1px; background: linear-gradient(90deg,transparent,#e5e7eb,transparent); margin: 1.75rem 0; }
    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:18px; } @media (max-width: 992px){ .grid-2 { grid-template-columns:1fr; } }
    .btn-save{ background: linear-gradient(135deg, #10b981, #059669); border:none; font-weight:800; padding:.95rem 1.6rem; box-shadow:0 18px 36px rgba(16,185,129,.28); color:#fff; border-radius: 999px;}
    .btn-cancel{ padding:.95rem 1.6rem; font-weight:800; color:#6d28d9; border-radius:999px; border:2px solid transparent; background:linear-gradient(#fff,#fff) padding-box, linear-gradient(135deg,#6d28d9,#4c1d95) border-box; text-decoration:none;}
    .btn-cancel:hover{ color:#fff; background: linear-gradient(135deg,#6d28d9,#4c1d95); }
  </style>
</head>
<body>
@php
  $user = auth()->user()->fresh();
  $fallback = 'https://ui-avatars.com/api/?background=6d28d9&color=fff&name='.urlencode($user->name);
  $photoPath = $user->profile_photo ? asset('storage/'.$user->profile_photo) : $fallback;
  $photoUrl = $photoPath.'?t='.(optional($user->updated_at)->timestamp ?? time());
@endphp

<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('products.index') }}"><i class="bi bi-bag-fill me-2"></i>MyStore</a>
  </div>
</nav>

<div class="page-wrap py-4">

  <div class="row justify-content-center mt-2">
    <div class="col-12 col-lg-10 col-xl-8">
      @if(session('success')) <div class="alert alert-success shadow-sm">{{ session('success') }}</div> @endif
      @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
          <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="card profile-card">
        <div class="profile-hero d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
          <div class="d-flex align-items-center gap-4">
            <div class="avatar-xl"><img id="avatarPreview" src="{{ $photoUrl }}" alt="Profile" class="w-100 h-100 object-fit-cover"></div>
            <div>
              <h2 class="m-0 fw-bold">Edit Profile</h2>
              <div class="opacity-75">Update your personal info & profile photo</div>
            </div>
          </div>
          <div><span class="badge bg-light text-dark fw-semibold px-3 py-2"><i class="bi bi-shield-lock me-1"></i> Secure Area</span></div>
        </div>

        <div class="card-body p-4 p-xl-5">
          <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="grid-2 align-items-center" style="row-gap:20px;">
              <div class="field">
                <label class="fw-bold"><i class="bi bi-image me-1 text-primary"></i> Profile Photo</label>
                <div class="d-flex align-items-center gap-3">
                  <div class="avatar-xl" style="width:96px;height:96px;border-color:#e5e7eb;"><img id="avatarThumb" src="{{ $photoUrl }}" class="w-100 h-100 object-fit-cover"></div>
                  <input type="file" name="profile_photo" class="form-control glass" accept="image/*" id="photoInput">
                </div>
                <small class="text-muted">PNG/JPG up to ~2MB. Use a square image for best results.</small>
              </div>
              <div></div>
            </div>

            <div class="divider"></div>

            <div class="section-title"><i class="bi bi-person-lines-fill me-2"></i> Basic Information</div>
            <div class="section-desc">These details will appear on billing & orders.</div>

            <div class="panel-soft">
              <div class="grid-2">
                <div class="field">
                  <label class="fw-bold"><i class="bi bi-person-circle me-1 text-primary"></i> Full Name</label>
                  <input type="text" name="name" class="form-control glass form-control-lg" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="field">
                  <label class="fw-bold"><i class="bi bi-envelope-at me-1 text-primary"></i> Email Address</label>
                  <input type="email" name="email" class="form-control glass form-control-lg" value="{{ old('email', $user->email) }}" required>
                </div>
              </div>
              <div class="grid-2 mt-3">
                <div class="field">
                  <label class="fw-bold"><i class="bi bi-telephone me-1 text-primary"></i> Phone</label>
                  <input type="text" name="phone" class="form-control glass form-control-lg" value="{{ old('phone', $user->phone ?? '') }}">
                </div>
                <div class="field">
                  <label class="fw-bold"><i class="bi bi-geo-alt-fill me-1 text-primary"></i> Address</label>
                  <textarea name="address" class="form-control glass form-control-lg" rows="4">{{ old('address', $user->address ?? '') }}</textarea>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            {{-- Security section removed as per request --}}

            <div class="d-flex justify-content-center gap-3 mt-4">
              <button type="submit" class="btn-save px-5"><i class="bi bi-save me-1"></i> Save Changes</button>
              <a href="{{ route('products.index') }}" class="btn-cancel px-5">✖ Cancel</a>
            </div>

          </form>
        </div>
      </div>

      <div class="py-3"></div>
    </div>
  </div>
</div>

<script>
function togglePassword(fieldId, iconSpan) {
    const field = document.getElementById(fieldId);
    const icon = iconSpan.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}

(function(){
  const input=document.getElementById('photoInput'), preview=document.getElementById('avatarPreview'), thumb=document.getElementById('avatarThumb');
  if(!input) return;
  input.addEventListener('change', e=>{
    const file=e.target.files && e.target.files[0]; if(!file) return;
    const r=new FileReader(); r.onload=ev=>{ if(preview) preview.src=ev.target.result; if(thumb) thumb.src=ev.target.result; };
    r.readAsDataURL(file);
  });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
