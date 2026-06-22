@extends('layouts.ga')
@section('title', 'My Profile')
@section('page-title', 'Account Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

            {{-- Header Profil --}}
            <div class="card mb-4">
                <div class="card-body d-flex align-items-center gap-4 flex-wrap">
                    <div
                        style="width:80px;height:80px;background:linear-gradient(135deg,#0070c0,#00a859);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($user->full_name ?: $user->username, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">{{ $user->full_name ?: $user->username }}</h4>
                        <div class="text-muted small">{{ $user->email }}</div>
                        <span class="badge bg-primary mt-1">{{ ucfirst($user->role) }}</span>
                        <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }} mt-1">
                            {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if($user->last_login)
                        <div class="ms-auto text-end text-muted" style="font-size:.78rem">
                            <div>Login terakhir:</div>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                {{-- Update Account Information --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header"><i class="fas fa-user-edit me-2 text-primary"></i>Account Information</div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success py-2"><i
                                        class="fas fa-check-circle me-1"></i>{{ session('success') }}</div>
                            @endif
                            @if($errors->has('username') || $errors->has('email') || $errors->has('full_name'))
                                <div class="alert alert-danger py-2">
                                    <ul class="mb-0 small">
                                        @foreach(['username', 'email', 'full_name'] as $f)
                                            @if($errors->has($f))
                                            <li>{{ $errors->first($f) }}</li>@endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.updateAccount') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ old('full_name', $user->full_name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                        <input type="text" name="username" class="form-control"
                                            value="{{ old('username', $user->username) }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Ganti Password --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header"><i class="fas fa-lock me-2 text-warning"></i>Ganti Password</div>
                        <div class="card-body">
                            @if(session('error_password'))
                                <div class="alert alert-danger py-2"><i
                                        class="fas fa-exclamation-circle me-1"></i>{{ session('error_password') }}</div>
                            @endif
                            @if($errors->has('current_password') || $errors->has('new_password'))
                                <div class="alert alert-danger py-2">
                                    <ul class="mb-0 small">
                                        @foreach(['current_password', 'new_password'] as $f)
                                            @if($errors->has($f))
                                            <li>{{ $errors->first($f) }}</li>@endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.updatePassword') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Password Lama</label>
                                    <input type="password" name="current_password" class="form-control"
                                        placeholder="Masukkan password saat ini" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Password Baru</label>
                                    <input type="password" name="new_password" class="form-control" id="newPwd"
                                        placeholder="Minimal 8 karakter" required oninput="checkPwdStrength(this.value)">
                                    <div class="progress mt-1" style="height:3px">
                                        <div id="pwdBar" class="progress-bar" style="width:0;transition:.3s"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" class="form-control"
                                        placeholder="Ulangi password baru" required>
                                </div>
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-key me-1"></i>Ganti Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function checkPwdStrength(v) {
            let score = 0;
            if (v.length >= 8) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;
            const bar = document.getElementById('pwdBar');
            const colors = ['#fc8181', '#f6ad55', '#68d391', '#38a169'];
            bar.style.width = (score * 25) + '%';
            bar.style.background = colors[score - 1] || '#e2e8f0';
        }
    </script>
@endpush