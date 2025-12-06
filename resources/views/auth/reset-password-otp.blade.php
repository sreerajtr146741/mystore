<!DOCTYPE html>
<html>
<head>
    <title>Reset Password • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;}
        .glass{background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);}
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md glass rounded-2xl shadow-xl p-8">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Verify OTP</h2>
        <p class="text-gray-500 text-center mb-6">Enter the 6-digit code sent to<br><strong>{{ $email }}</strong></p>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.verify.otp') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div>
                <input type="text" name="otp" maxlength="6" required autofocus
                    class="w-full text-center text-3xl tracking-wider font-bold py-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                    placeholder="000000" pattern="\d{6}" inputmode="numeric">
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">
                Verify Code
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            Didn't receive the code? 
            <form action="{{ route('password.resend.otp') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="text-green-600 font-bold hover:underline">Resend</button>
            </form>
        </div>
    </div>
</body>
</html>
