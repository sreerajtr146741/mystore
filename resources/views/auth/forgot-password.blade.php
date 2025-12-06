<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;}
        .glass{background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);}
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md glass rounded-2xl shadow-xl p-8">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Forgot Password?</h2>
        <p class="text-gray-500 text-center mb-6">Enter your email to receive a reset code</p>

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                    placeholder="your@email.com">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">
                Send Reset Code
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">← Back to Login</a>
        </div>
    </div>
</body>
</html>
