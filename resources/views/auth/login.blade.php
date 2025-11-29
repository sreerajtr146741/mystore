<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-xl shadow-2xl w-full max-w-md">

        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Welcome Back!</h2>

        <!-- Success Message After Registration -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-blue-500 transition"
                       placeholder="you@example.com">
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-blue-500 transition"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition duration-300">
                Login Now
            </button>
        </form>

        <p class="text-center mt-6 text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
                Register here
            </a>
        </p>
    </div>
</body>
</html>