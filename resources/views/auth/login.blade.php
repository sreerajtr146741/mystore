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
        
        /* Removed custom positioning rules in favor of Tailwind classes */
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

                <!-- General Error (e.g., from Dashboard crash) -->
                @if(session('error'))
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
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
                    <div class="relative">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-[calc(50%+14px)] -translate-y-1/2 text-gray-500 pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 13L2 6.76V18a2 2 0 002 2h16a2 2 0 002-2V6.76L12 13z"/>
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="field w-full px-4 py-3 border rounded-lg bg-white/70 focus:bg-white border-gray-300 pl-12" 
                               placeholder="you@example.com">
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-[calc(50%+14px)] -translate-y-1/2 text-gray-500 pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 9V7a5 5 0 00-10 0v2H5v11h14V9h-2z"/>
                        </svg>

                        <input type="password" id="password" name="password" required
                               class="field w-full px-4 py-3 border rounded-lg bg-white/70 focus:bg-white border-gray-300 pl-12 pr-12"
                               placeholder="••••••••">

                        <!-- Eye icon -->
                        <div class="absolute right-3 top-[calc(50%+14px)] -translate-y-1/2 cursor-pointer text-gray-500 hover:text-indigo-600 z-10 flex items-center justify-center" onclick="togglePassword()">
                            <!-- Eye Icon (Visible by default state of 'password' type is confusing, usually eye means 'show me') -->
                            <!-- When type is password (dots), show Eye (Click to verify) -->
                            <svg id="eye-show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon (Hidden by default) -->
                            <svg id="eye-hide" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </div>
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
            const showIcon = document.getElementById('eye-show');
            const hideIcon = document.getElementById('eye-hide');
            
            if (pass.type === 'password') {
                pass.type = 'text';
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            } else {
                pass.type = 'password';
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
