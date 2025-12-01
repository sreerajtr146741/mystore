<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{
            --g1:#667eea; --g2:#764ba2;
        }
        body{
            background: radial-gradient(1200px 600px at 10% 10%, rgba(255,255,255,0.08), transparent 60%),
                        radial-gradient(900px 500px at 90% 20%, rgba(255,255,255,0.06), transparent 60%),
                        linear-gradient(135deg, var(--g1) 0%, var(--g2) 100%);
            min-height: 100vh;
        }
        .glass {
            backdrop-filter: blur(14px);
            background: linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.75));
            border: 1px solid rgba(255,255,255,.35);
        }
        .blob {
            position: absolute; border-radius: 9999px; filter: blur(40px); opacity: .35;
            animation: float 16s ease-in-out infinite;
        }
        .blob-1 { width: 24rem; height: 24rem; left: -5rem; top: -5rem; background: #a78bfa; }
        .blob-2 { width: 20rem; height: 20rem; right: -4rem; bottom: -4rem; background: #60a5fa; animation-delay: -6s; }
        @keyframes float {
            0%,100% { transform: translateY(0) translateX(0) scale(1); }
            50%     { transform: translateY(-15px) translateX(10px) scale(1.03); }
        }
        .field:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(99,102,241,.18), 0 10px 30px rgba(99,102,241,.15);
            border-color: rgb(99,102,241);
        }
        .btn-grad {
            background-image: linear-gradient(90deg,#2563eb 0%,#7c3aed 100%);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .btn-grad:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(124,58,237,.35); filter: saturate(1.1); }
        .btn-grad:active { transform: translateY(0); }

        .input-wrap { position: relative; }
        .input-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity:.6; }
        .input-wrap input { padding-left: 42px; }

        /* FIXED: Eye icon size & position */
        .password-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.6;
        }
    </style>
</head>
<body class="relative flex items-center justify-center">

    <!-- Background -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

 
    <div class="w-full max-w-md mx-auto p-1">
        <div class="rounded-2xl p-[2px] bg-gradient-to-br from-white/40 to-white/10 shadow-[0_25px_60px_rgba(0,0,0,0.35)]">
            <div class="glass rounded-2xl p-8 sm:p-10">

                <!-- Lock Icon -->
                <div class="mx-auto w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 grid place-items-center text-white shadow-lg mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                              d="M16 11V7a4 4 0 10-8 0v4M6 11h12v7a2 2 0 01-2 2H8a2 2 0 01-2-2v-7z"/>
                    </svg>
                </div>

                <h2 class="text-3xl font-extrabold text-center text-gray-900 tracking-tight">Welcome Back!</h2>
                <p class="text-center text-gray-500 mt-1 mb-6">Sign in to continue to your dashboard</p>

                <!-- Success -->
                @if(session('success'))
                <div class="flex items-start gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Errors -->
                @if($errors->any())
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    {{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="input-wrap">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 13L2 6.76V18a2 2 0 002 2h16a2 2 0 002-2V6.76L12 13z"/>
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="field w-full px-4 py-3 border rounded-lg bg-white/70 focus:bg-white border-gray-300"
                               placeholder="you@example.com">
                    </div>

                    <!-- Password -->
                    <div class="input-wrap">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 9V7a5 5 0 00-10 0v2H5v11h14V9h-2z"/>
                        </svg>

                        <input type="password" id="password" name="password" required
                               class="field w-full px-4 py-3 border rounded-lg bg-white/70 focus:bg-white border-gray-300"
                               placeholder="••••••••">

                        <!-- Eye icon -->
                        <span class="password-eye" onclick="togglePassword()">👁️</span>
                    </div>

                    <!-- Forgot Password -->
                    <div class="text-right -mt-2">
                        <a href="{{ url('/forgot-password') }}" class="text-blue-600 text-sm font-semibold hover:underline">
                            Forgot Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-grad w-full text-white font-semibold py-3 rounded-lg shadow-md">
                        Login Now
                    </button>
                </form>

                <p class="text-center mt-6 text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">
                        Register here
                    </a>
                </p>

                <p class="mt-6 text-center text-xs text-gray-400">
                    Secured by modern encryption • E-Commerce CRUD
                </p>

            </div>
        </div>
    </div>

    <!-- Password Toggle -->
    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            pass.type = pass.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>
