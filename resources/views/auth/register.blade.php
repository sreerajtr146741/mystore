<!DOCTYPE html>
<html>
<head><title>Register</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
<div class="bg-white p-8 rounded shadow-md w-96">
    <h2 class="text-2xl mb-6">Register</h2>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Name" required class="w-full p-2 border mb-3">
        <input type="email" name="email" placeholder="Email" required class="w-full p-2 border mb-3">
        <input type="password" name="password" placeholder="Password" required class="w-full p-2 border mb-3">
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required class="w-full p-2 border mb-3">
        <button type="submit" class="bg-blue-600 text-white w-full py-2">Register</button>
    </form>
    <p class="mt-4 text-center">Already have account? <a href="/login" class="text-blue-600">Login</a></p>
</div>
</body>
</html>