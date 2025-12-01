<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root { --g1:#667eea; --g2:#764ba2; }
    body{
      background:
        radial-gradient(1000px 500px at 10% 10%, rgba(255,255,255,.08), transparent 60%),
        radial-gradient(900px 500px at 90% 20%, rgba(255,255,255,.06), transparent 60%),
        linear-gradient(135deg, var(--g1) 0%, var(--g2) 100%);
    }
    .glass {
      backdrop-filter: blur(14px);
      background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.82));
      border: 1px solid rgba(255,255,255,.4);
    }
    .field { transition: box-shadow .2s, border-color .2s, background .2s }
    .field:focus {
      outline: none;
      border-color: rgb(99 102 241);
      box-shadow: 0 0 0 4px rgba(99,102,241,.18), 0 10px 30px rgba(99,102,241,.15);
      background: #fff;
    }
    .input-wrap { position: relative; }
    .input-wrap svg {
      position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity:.6
    }
    .input-wrap input { padding-left: 42px }
    .eye {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      cursor: pointer; opacity:.6; font-size: 1.05rem;
    }
    .btn-grad {
      background-image: linear-gradient(90deg,#2563eb 0%,#7c3aed 100%);
      transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn-grad:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(124,58,237,.35); filter: saturate(1.06); }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-md">
    <div class="p-[2px] rounded-2xl bg-gradient-to-br from-white/40 to-white/10 shadow-[0_25px_60px_rgba(0,0,0,.35)]">
      <div class="glass rounded-2xl p-8 sm:p-10">

        <!-- Badge/Icon -->
        <div class="mx-auto w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 grid place-items-center text-white shadow-lg mb-5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                  d="M16 7a4 4 0 10-8 0v2M6 9h12v8a2 2 0 01-2 2H8a2 2 0 01-2-2V9z"/>
          </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-center text-gray-900 tracking-tight">Create your account</h2>
        <p class="text-center text-gray-500 mt-1 mb-6">It’s quick and easy</p>

        <!-- Your original form (same action/method/fields) -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
          @csrf

          <div class="input-wrap">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/><path d="M4 20a8 8 0 0116 0H4z" opacity=".6"/>
            </svg>
            <input type="text" name="name" placeholder="Name" required
                   class="field w-full p-3 border rounded-lg bg-white/70 border-gray-300">
          </div>

          <div class="input-wrap">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 13L2 6.76V18a2 2 0 002 2h16a2 2 0 002-2V6.76L12 13z"/><path d="M22 6l-10 6L2 6l10-4 10 4z" opacity=".6"/>
            </svg>
            <input type="email" name="email" placeholder="Email" required
                   class="field w-full p-3 border rounded-lg bg-white/70 border-gray-300">
          </div>

          <div class="input-wrap">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17 9V7a5 5 0 00-10 0v2H5v11h14V9h-2z"/>
            </svg>
            <input type="password" id="password" name="password" placeholder="Password" required
                   class="field w-full p-3 border rounded-lg bg-white/70 border-gray-300">
            <span class="eye" onclick="toggle('password')">👁️</span>
          </div>

          <div class="input-wrap">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17 9V7a5 5 0 00-10 0v2H5v11h14V9h-2z"/>
            </svg>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required
                   class="field w-full p-3 border rounded-lg bg-white/70 border-gray-300">
            <span class="eye" onclick="toggle('password_confirmation')">👁️</span>
          </div>

          <button type="submit" class="btn-grad text-white w-full py-3 rounded-lg font-semibold shadow-md">
            Register
          </button>
        </form>

        <p class="mt-5 text-center text-gray-600">
          Already have account?
          <a href="/login" class="text-indigo-600 font-semibold hover:underline">Login</a>
        </p>

      </div>
    </div>
  </div>

  <!-- show/hide password (no backend change) -->
  <script>
    function toggle(id){
      const el = document.getElementById(id);
      el.type = (el.type === 'password') ? 'text' : 'password';
    }
  </script>
</body>
</html>
