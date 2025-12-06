<!DOCTYPE html>
<html>
<head>
    <title>New Password • MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;}
        .glass{background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);}
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md glass rounded-2xl shadow-xl p-8">
        <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Set New Password</h2>
        <p class="text-gray-500 text-center mb-6">Choose a strong password for your account</p>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none"
                    placeholder="••••••••" minlength="6">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none"
                    placeholder="••••••••" minlength="6">
            </div>

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">
                Reset Password
            </button>
        </form>
    </div>
</body>
</html>
