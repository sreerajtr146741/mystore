{{-- resources/views/payment/verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Payment OTP • MyStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --g1:#667eea; --g2:#764ba2; }
        body {
            background:
                radial-gradient(1000px 500px at 10% 10%, rgba(255,255,255,.08), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(255,255,255,.06), transparent 60%),
                linear-gradient(135deg, var(--g1) 0%, var(--g2) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .glass {
            backdrop-filter: blur(16px);
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
            border: 1px solid rgba(255,255,255,.4);
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
        }
        .otp-input {
            width: 70px; height: 70px;
            font-size: 2.2rem; font-weight: bold;
            text-align: center; letter-spacing: 8px;
            border: 2px solid rgba(102,126,234,.3);
            border-radius: 16px;
            transition: all .2s;
        }
        .otp-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 5px rgba(102,126,234,.25), 0 15px 35px rgba(102,126,234,.2);
            background: #fff;
        }
        .btn-grad {
            background: linear-gradient(90deg, #10b981, #059669);
            transition: all .2s ease;
        }
        .btn-grad:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(16,185,129,.5);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        <div class="glass rounded-3xl p-10 text-center">
            <!-- Icon -->
            <div class="mx-auto w-20 h-20 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 grid place-items-center text-white shadow-2xl mb-6">
                <i class="bi bi-shield-lock-fill text-4xl"></i>
            </div>

            <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Verify Payment</h1>
            <p class="text-gray-600 text-lg mb-8">
                We just sent a <strong>6-digit OTP</strong> to your email<br>
                <span class="text-indigo-600 font-bold">{{ $email ?? auth()->user()->email }}</span>
            </p>

            <!-- OTP Form -->
            <form action="{{ route('verify.payment.otp') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? auth()->user()->email }}">

                <div>
                    <input type="text" name="otp" maxlength="6" required autofocus autocomplete="off"
                           class="otp-input w-full tracking-widest"
                           placeholder="000000"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)">
                    
                    @error('otp')
                        <p class="text-red-500 text-sm mt-3 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-grad text-white w-full py-5 rounded-2xl font-bold text-xl shadow-lg">
                    Verify & Complete Payment
                </button>
            </form>

            <div class="mt-8 text-gray-600">
                <p class="text-sm">
                    Didn’t receive the code?
                    <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();"
                       class="text-indigo-600 font-bold hover:underline">
                        Resend OTP
                    </a>
                </p>

                <!-- Hidden Resend Form -->
                <form id="resend-form" action="{{ route('pay.now') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

            <div class="mt-6">
                <a href="{{ route('checkout.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                    Back to Checkout
                </a>
            </div>
        </div>
    </div>

    <!-- Auto-focus & paste support -->
    <script>
        const input = document.querySelector('input[name="otp"]');
        input.focus();

        // Allow pasting full OTP
        input.addEventListener('paste', function(e) {
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            paste = paste.replace(/\D/g, '').slice(0,6);
            if (paste.length === 6) {
                input.value = paste;
                setTimeout(() => input.form.submit(), 300);
            }
        });
    </script>
</body>
</html>