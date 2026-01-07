{{-- resources/views/auth/verify-register-otp.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-md w-full text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Check Your Email</h1>
        <p class="text-gray-600 mb-8">
            We sent a <strong>6-digit OTP</strong> to<br>
            <span class="text-indigo-600 font-bold">{{ $email ?? 'your email' }}</span>
        </p>

        <form action="{{ route('verify.register.otp.post') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="text" name="otp" maxlength="6" required autofocus
                   class="w-full text-center text-3xl font-bold tracking-wider border-2 border-indigo-300 rounded-2xl py-6 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                   placeholder="000000" pattern="\d{6}" inputmode="numeric">

            @error('otp')
                <p class="text-red-500">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-xl py-5 rounded-2xl hover:shadow-xl transition">
                Verify & Complete Registration
            </button>
        </form>

        @if(session('status'))
            <div class="mt-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6">
            <form action="{{ route('register.otp.resend') }}" method="POST" class="inline">
                @csrf
                <p class="text-gray-500">
                    Didn't receive the code? 
                    <button type="submit" class="text-indigo-600 font-bold hover:underline bg-transparent border-0 cursor-pointer">
                        Resend OTP
                    </button>
                </p>
            </form>
        </div>

        <p class="mt-4 text-gray-500">
            Didn’t get it? 
            <a href="{{ route('register') }}" class="text-indigo-600 font-bold">Register again</a>
        </p>
    </div>
</body>
</html>