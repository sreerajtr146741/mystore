{{-- resources/views/auth/verify-otp.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Verify Login • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body{background:linear-gradient(135deg,#f3f4f6 0%,#e5e7eb 100%);min-height:100vh;}
        .glass{background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);}
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md glass rounded-2xl shadow-xl p-8 text-center">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Login Verification</h2>
        <p class="text-gray-500 mb-6">Enter the 6-digit code sent to<br><span class="font-medium text-gray-900">{{ $email }}</span></p>

        <form action="{{ route('verify.otp') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Hidden Email Field Required for Validation --}}
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div>
                <input type="text" name="otp" maxlength="6" required autofocus
                    class="w-full text-center text-3xl tracking-[0.5em] font-bold py-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                    placeholder="000000" pattern="\d{6}">
                @error('otp') <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5">
                Verify & Login
            </button>
        </form>

        <div class="mt-6 text-sm text-gray-500">
            <a href="{{ route('login') }}" class="hover:text-gray-800">← Back to Login</a>
        </div>
    </div>
</body>
</html>
