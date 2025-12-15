{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Register • MyStore</title>
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
        .field { 
            transition: box-shadow .2s, border-color .2s, background .2s;
            background: rgba(255,255,255,.75);
            border: 1px solid rgba(0,0,0,.12);
        }
        .field:focus {
            outline: none;
            border-color: rgb(99 102 241);
            box-shadow: 0 0 0 4px rgba(99,102,241,.18), 0 10px 30px rgba(99,102,241,.15);
            background: #fff;
        }
        .input-wrap { position: relative; }
        .input-wrap > svg {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity:.6;
            pointer-events: none; /* Ensure clicks pass through left icon */
        }
        .input-wrap input, .input-wrap textarea {
            padding-left: 48px !important;
            padding-right: 48px !important;
        }
        .eye {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            cursor: pointer; opacity:.6; font-size: 1.15rem;
            z-index: 10;
        }
        .btn-grad {
            background-image: linear-gradient(90deg,#2563eb 0%,#7c3aed 100%);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .btn-grad:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 14px 28px rgba(124,58,237,.4); 
            filter: saturate(1.1); 
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="p-[2px] rounded-2xl bg-gradient-to-br from-white/40 to-white/10 shadow-[0_25px_60px_rgba(0,0,0,.35)]">
            <div class="glass rounded-2xl p-8 sm:p-10">
                <!-- Logo -->
                <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 grid place-items-center text-white shadow-xl mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>

                <h2 class="text-3xl font-extrabold text-center text-gray-900 tracking-tight mb-2">Create Account</h2>
                <p class="text-center text-gray-600 mb-8">Fill in your details & verify with OTP</p>

                <!-- Fixed Form: All required fields -->
                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div class="input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/><path d="M4 20a8 8 0 0116 0H4z" opacity=".6"/>
                            </svg>
                            <input type="text" name="first_name" placeholder="First Name" required
                                   class="field w-full p-3.5 rounded-xl border">
                            @error('first_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/><path d="M4 20a8 8 0 0116 0H4z" opacity=".6"/>
                            </svg>
                            <input type="text" name="last_name" placeholder="Last Name" required
                                   class="field w-full p-3.5 rounded-xl border">
                            @error('last_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="input-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 8l9 6 9-6V18a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/><path d="M21 8v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8l9 6 9-6z" opacity=".6"/>
                        </svg>
                        <input type="email" name="email" placeholder="Email Address" required
                               class="field w-full p-3.5 rounded-xl border">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="input-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.62 10.79a15.2 15.2 0 016.59 6.59l2.2-2.2a1 1 0 011.11-.22 11.11 11.11 0 004.66.94 1 1 0 011 1v3.5a1 1 0 01-1 1C9.89 21.52 3 15.37 3 8a1 1 0 011-1h3.5a1 1 0 011 1c0 1.56.33 3.06.94 4.66a1 1 0 01-.22 1.11l-2.2 2.2z"/>
                        </svg>
                        <input type="text" name="phone" placeholder="Phone Number" required
                               class="field w-full p-3.5 rounded-xl border">
                        @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="input-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 7a2 2 0 100-4 2 2 0 000 4z"/><path d="M12 9c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <input type="password" id="password" name="password" placeholder="Password" required
                               class="field w-full p-3.5 rounded-xl border">
                        <span class="eye" onclick="toggle('password')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="input-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 7a2 2 0 100-4 2 2 0 000 4z"/><path d="M12 9c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required
                               class="field w-full p-3.5 rounded-xl border">
                        <span class="eye" onclick="toggle('password_confirmation')">
                             <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>



                    <button type="submit" class="btn-grad text-white w-full py-4 rounded-xl font-bold text-lg shadow-lg">
                        Register
                    </button>
                </form>

                <p class="mt-8 text-center text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-indigo-600 font-bold hover:underline">Login here</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Show/Hide Password -->
    <script>
        function toggle(id) {
            const el = document.getElementById(id);
            el.type = (el.type === 'password') ? 'text' : 'password';
        }
    </script>
</body>
</html>