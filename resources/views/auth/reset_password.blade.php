<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | GA Management KFA</title>
    <link rel="icon" href="{{ asset('img/kf.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn  { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
        @keyframes slideIn { from{opacity:0;transform:translateX(20px)}  to{opacity:1;transform:translateX(0)} }
        .animate-fadeIn  { animation: fadeIn  0.6s ease-out forwards; }
        .animate-slideIn { animation: slideIn 0.4s ease-out forwards; }
        .glass-effect { background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); }
        .strength-bar { height:4px; border-radius:2px; transition:all 0.3s; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0 z-0">
        @php
            try {
                $bgSetting = \Illuminate\Support\Facades\DB::table('image_settings')
                    ->where('image_key', 'bg_lupa_password')->first();
                if ($bgSetting && $bgSetting->image_value) {
                    $bgUrl = $bgSetting->image_type === 'url'
                        ? $bgSetting->image_value
                        : asset('storage/' . $bgSetting->image_value);
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

    {{-- Card --}}
    <div class="relative z-10 w-full max-w-md animate-fadeIn">
        <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden border border-white/20">

            {{-- Header --}}
            <div class="p-8 pb-4 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-2xl shadow-lg mb-5 transform -rotate-6">
                    <i class="fas fa-lock-open text-white text-4xl rotate-6"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-800">Buat Password Baru</h2>
                <p class="text-gray-500 mt-1 text-sm">
                    Untuk akun <strong class="text-gray-700">{{ session('reset_full_name', session('reset_email', '')) }}</strong>
                </p>

                {{-- Step Indicator --}}
                <div class="flex items-center justify-center gap-3 mt-5">
                    @php $steps = ['Email','Kode OTP','Password Baru']; @endphp
                    @foreach($steps as $idx => $label)
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-2.5 h-2.5 rounded-full {{ $idx < 2 ? 'bg-emerald-400' : 'bg-indigo-600 scale-125' }} transition-all"></div>
                            <span class="text-xs {{ $idx <= 2 ? 'text-indigo-600 font-semibold' : 'text-gray-400' }}">{{ $label }}</span>
                        </div>
                        @if(!$loop->last)
                        <div class="w-10 h-0.5 mb-4 {{ $idx < 2 ? 'bg-emerald-400' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('error') || $errors->any())
            <div class="mx-6 mb-2 p-3 bg-red-50 border border-red-300 text-red-700 rounded-xl text-sm flex items-start gap-2 animate-slideIn">
                <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                <span>{{ session('error') ?? $errors->first() }}</span>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.reset') }}" class="p-6 pt-2 space-y-4 animate-slideIn">
                @csrf

                {{-- Password Baru --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-600 ml-1">Password Baru</label>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                        <input type="password" name="password" id="newPassword" required
                               class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                               placeholder="Min. 8 karakter, huruf &amp; angka"
                               oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePwd('newPassword','eyeNew')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="eyeNew" class="fas fa-eye"></i>
                        </button>
                    </div>
                    {{-- Strength bars --}}
                    <div class="flex gap-1 mt-1 px-1">
                        <div id="bar1" class="strength-bar flex-1 bg-gray-200"></div>
                        <div id="bar2" class="strength-bar flex-1 bg-gray-200"></div>
                        <div id="bar3" class="strength-bar flex-1 bg-gray-200"></div>
                        <div id="bar4" class="strength-bar flex-1 bg-gray-200"></div>
                    </div>
                    <p id="strengthText" class="text-xs ml-1 text-gray-400"></p>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-600 ml-1">Konfirmasi Password</label>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                        <input type="password" name="password_confirmation" id="confirmPassword" required
                               class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                               placeholder="Ulangi password baru"
                               oninput="checkMatch()">
                        <button type="button" onclick="togglePwd('confirmPassword','eyeConfirm')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="eyeConfirm" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p id="matchText" class="text-xs ml-1"></p>
                </div>

                {{-- Requirements --}}
                <ul class="text-xs text-gray-500 space-y-1 ml-1">
                    <li id="req-len" class="flex items-center gap-1.5">
                        <i class="fas fa-circle text-gray-300 text-[8px]"></i> Minimal 8 karakter
                    </li>
                    <li id="req-let" class="flex items-center gap-1.5">
                        <i class="fas fa-circle text-gray-300 text-[8px]"></i> Mengandung huruf
                    </li>
                    <li id="req-num" class="flex items-center gap-1.5">
                        <i class="fas fa-circle text-gray-300 text-[8px]"></i> Mengandung angka
                    </li>
                </ul>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 transition active:scale-[0.98] flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Password Baru
                </button>
            </form>

            {{-- Footer --}}
            <div class="bg-gray-50/50 px-6 py-4 text-center border-t border-gray-100">
                <p class="text-xs text-gray-400 italic">
                    <i class="fas fa-shield-alt mr-1"></i> Sistem Keamanan Terenkripsi KFA
                </p>
            </div>
        </div>
        <p class="text-center text-white/70 text-sm mt-8">
            &copy; {{ date('Y') }} Kimia Farma Apotek. All rights reserved.
        </p>
    </div>

    <script>
        function togglePwd(id, iconId) {
            const f = document.getElementById(id);
            const i = document.getElementById(iconId);
            if (f.type === 'password') {
                f.type = 'text';
                i.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                f.type = 'password';
                i.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function checkStrength(v) {
            let score = 0;
            if (v.length >= 8)          score++;
            if (/[A-Za-z]/.test(v))     score++;
            if (/[0-9]/.test(v))        score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;

            const colors = ['#fc8181','#f6ad55','#68d391','#38a169'];
            const labels = ['Lemah','Cukup','Kuat','Sangat Kuat'];
            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('bar' + i);
                bar.style.background = i <= score ? colors[score - 1] : '#e5e7eb';
            }
            document.getElementById('strengthText').textContent = score > 0 ? labels[score - 1] : '';

            // Requirements
            setReq('req-len', v.length >= 8);
            setReq('req-let', /[A-Za-z]/.test(v));
            setReq('req-num', /[0-9]/.test(v));
        }

        function setReq(id, ok) {
            const el = document.getElementById(id);
            const icon = el.querySelector('i');
            icon.className = ok ? 'fas fa-check-circle text-emerald-500 text-[8px]' : 'fas fa-circle text-gray-300 text-[8px]';
            el.classList.toggle('text-emerald-600', ok);
            el.classList.toggle('text-gray-500', !ok);
        }

        function checkMatch() {
            const p1 = document.getElementById('newPassword').value;
            const p2 = document.getElementById('confirmPassword').value;
            const el = document.getElementById('matchText');
            if (!p2) { el.textContent = ''; return; }
            if (p1 === p2) {
                el.textContent = '✓ Password cocok';
                el.className = 'text-xs ml-1 text-emerald-600';
            } else {
                el.textContent = '✗ Password tidak cocok';
                el.className = 'text-xs ml-1 text-red-500';
            }
        }
    </script>
</body>
</html>
