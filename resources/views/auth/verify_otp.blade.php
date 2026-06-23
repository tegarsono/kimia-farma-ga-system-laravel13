<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP | GA Management KFA</title>
    <link rel="icon" href="{{ asset('img/kf.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn  { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
        @keyframes slideIn { from{opacity:0;transform:translateX(20px)}  to{opacity:1;transform:translateX(0)} }
        .animate-fadeIn  { animation: fadeIn  0.6s ease-out forwards; }
        .animate-slideIn { animation: slideIn 0.4s ease-out forwards; }
        .glass-effect { background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); }
        .otp-input {
            width:48px; height:56px; text-align:center; font-size:1.5rem; font-weight:700;
            border:2px solid #e5e7eb; border-radius:12px; background:#f9fafb;
            transition:all 0.2s; outline:none;
        }
        .otp-input:focus { border-color:#6366f1; background:#fff; box-shadow:0 0 0 3px rgba(99,102,241,0.15); }
        .otp-input.filled { border-color:#6366f1; background:#eef2ff; }
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
                    <i class="fas fa-shield-alt text-white text-4xl rotate-6"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-800">Verifikasi OTP</h2>
                <p class="text-gray-500 mt-1 text-sm">Masukkan 6 digit kode yang dikirim ke email Anda</p>

                {{-- Step Indicator --}}
                <div class="flex items-center justify-center gap-3 mt-5">
                    @php $steps = ['Email','Kode OTP','Password Baru']; @endphp
                    @foreach($steps as $idx => $label)
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-2.5 h-2.5 rounded-full {{ $idx < 1 ? 'bg-emerald-400' : ($idx === 1 ? 'bg-indigo-600 scale-125' : 'bg-gray-200') }} transition-all"></div>
                            <span class="text-xs {{ $idx <= 1 ? 'text-indigo-600 font-semibold' : 'text-gray-400' }}">{{ $label }}</span>
                        </div>
                        @if(!$loop->last)
                        <div class="w-10 h-0.5 mb-4 {{ $idx < 1 ? 'bg-emerald-400' : 'bg-gray-200' }}"></div>
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
            @if(session('success'))
            <div class="mx-6 mb-2 p-3 bg-green-50 border border-green-300 text-green-700 rounded-xl text-sm flex items-start gap-2 animate-slideIn">
                <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            {{-- OTP Form --}}
            <div class="p-6 pt-2 animate-slideIn">
                <p class="text-center text-sm text-gray-500 mb-4">
                    Kode OTP dikirim ke<br>
                    <strong class="text-gray-700">{{ session('reset_email') }}</strong>
                </p>

                <form method="POST" action="{{ route('password.verify') }}" id="otpForm" class="space-y-5">
                    @csrf
                    <input type="hidden" name="otp" id="otpHidden">

                    {{-- 6 OTP Boxes --}}
                    <div class="flex justify-center gap-2" id="otpBoxes">
                        @for($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                               class="otp-input" autocomplete="off" data-index="{{ $i }}">
                        @endfor
                    </div>

                    <button type="submit" id="verifyBtn" disabled
                            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 transition active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class="fas fa-shield-alt"></i> Verifikasi Kode OTP
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-sm text-gray-500">
                        Tidak menerima kode?
                        <a href="{{ route('password.request') }}" class="text-indigo-600 font-semibold hover:underline">
                            Kirim Ulang
                        </a>
                    </p>
                </div>
                <div class="text-center mt-2">
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-400 hover:text-indigo-600 hover:underline">
                        <i class="fas fa-redo mr-1"></i>Mulai ulang
                    </a>
                </div>
            </div>

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
        const boxes  = document.querySelectorAll('.otp-input');
        const hidden = document.getElementById('otpHidden');
        const btn    = document.getElementById('verifyBtn');

        boxes.forEach((box, i) => {
            box.addEventListener('input', () => {
                box.value = box.value.replace(/\D/g, '').slice(-1);
                box.classList.toggle('filled', box.value !== '');
                if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
                updateHidden();
            });
            box.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !box.value && i > 0) boxes[i - 1].focus();
            });
            box.addEventListener('paste', e => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                [...paste].slice(0, 6).forEach((ch, j) => {
                    if (boxes[j]) { boxes[j].value = ch; boxes[j].classList.add('filled'); }
                });
                updateHidden();
                if (boxes[Math.min(paste.length, 5)]) boxes[Math.min(paste.length, 5)].focus();
            });
        });

        function updateHidden() {
            const val = [...boxes].map(b => b.value).join('');
            hidden.value = val;
            btn.disabled = val.length < 6;
        }
    </script>
</body>
</html>
