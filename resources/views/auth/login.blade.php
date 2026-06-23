<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GA Management KFA</title>
    <link rel="icon" href="{{ asset('img/kf.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Background Image dengan Overlay --}}
    <div class="absolute inset-0 z-0">
        @php
            try {
                $bgLogin = \Illuminate\Support\Facades\DB::table('image_settings')
                    ->where('image_key', 'bg_login')->first();
                if ($bgLogin && $bgLogin->image_value) {
                    $bgUrl = $bgLogin->image_type === 'url'
                        ? $bgLogin->image_value
                        : asset('storage/' . $bgLogin->image_value);
                } else {
                    $bgUrl = 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1920';
                }
            } catch (\Exception $e) {
                $bgUrl = 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1920';
            }
        @endphp
        <img src="{{ $bgUrl }}" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 to-blue-900/60"></div>
    </div>

    {{-- Login Container --}}
    <div class="relative z-10 w-full max-w-md animate-fadeIn">
        <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden border border-white/20">

            {{-- Logo & Title Section --}}
            <div class="p-8 text-center pb-0">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-2xl shadow-lg mb-6 transform -rotate-6">
                    <i class="fas fa-building text-white text-4xl rotate-6"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-800">Welcome</h2>
                <p class="text-gray-500 mt-2">General Affair Dashboard KFA</p>
            </div>

            {{-- Alert Messages --}}
            @if(session('error') || $errors->any())
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-2xl mx-8 mt-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') ?? $errors->first() }}
                </div>
            @endif
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-2xl mx-8 mt-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Section --}}
            <form action="{{ route('login') }}" method="POST" class="p-8 space-y-5">
                @csrf

                {{-- Username / Email --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600 ml-1">Username / Email</label>
                    <div class="relative group">
                        <i
                            class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                        <input type="text" name="username_email" value="{{ old('username_email') }}" required
                            autocomplete="username"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                            placeholder="Enter your username or email" </div>
                    </div>

                    {{-- Password --}}
                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="text-sm font-semibold text-gray-600">Password</label>
                            <a href="{{ route('password.request') }}"
                                class="text-xs text-indigo-600 hover:underline font-medium">
                                Forgot Password?
                            </a>
                        </div>
                        <div class="relative group">
                            <i
                                class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="password" name="password" id="passwordField" required
                                autocomplete="current-password"
                                class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i id="passwordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center ml-1">
                        <input type="checkbox" id="remember" name="remember"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">
                            Remember me on this device
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 transform transition active:scale-[0.98] hover:shadow-xl">
                        Login Now
                    </button>

            </form>

            {{-- Footer Section --}}
            <div class="bg-gray-50/50 p-6 text-center border-t border-gray-100">
                <p class="text-sm text-gray-500 italic">
                    <i class="fas fa-shield-alt mr-1"></i> Sistem Keamanan Terenkripsi KFA
                </p>
            </div>
        </div>

        {{-- Copyright --}}
        <p class="text-center text-white/70 text-sm mt-8">
            &copy; {{ date('Y') }} Kimia Farma Apotek. All rights reserved.
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('passwordField');
            const passwordIcon = document.getElementById('passwordIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>