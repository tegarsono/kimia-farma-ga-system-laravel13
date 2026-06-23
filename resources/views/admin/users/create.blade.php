@extends('layouts.app')
@push('styles')
<style>
/* Jangan sembunyikan main wrapper; cukup rapikan spacing */
.page-content { padding: 0 !important; }
</style>
@endpush
@section('title', 'User')
@section('page-title', 'Tambah User')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-user-plus me-2"></i>Tambah User
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            required
                            autocomplete="new-password"
                            oninput="checkPasswordRules()"
                        >
                        <div id="password-rules" class="mt-2 small" style="color:#6c757d">
                            <div><i class="fas fa-circle"></i> Minimal 8 karakter</div>
                            <div><i class="fas fa-circle"></i> Harus ada huruf kapital (A-Z)</div>
                            <div><i class="fas fa-circle"></i> Harus ada angka (0-9)</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Permissions</label>
                    @php
                        $selectedPermissions = [];
                    @endphp
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="border rounded p-2" style="max-height:320px; overflow:auto;">
                                @include('admin.users._form_permissions')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-save me-1"></i>Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                </div>
            </form>

            @push('scripts')
                <script>
                    function checkPasswordRules() {
                        const pw = document.getElementById('password')?.value || '';
                        const rulesEl = document.getElementById('password-rules');
                        if (!rulesEl) return;

                        const minLen = pw.length >= 8;
                        const hasUpper = /[A-Z]/.test(pw);
                        const hasDigit = /\d/.test(pw);

                        const items = rulesEl.querySelectorAll('div');
                        if (items.length >= 3) {
                            items[0].style.color = minLen ? '#198754' : '#dc3545';
                            items[1].style.color = hasUpper ? '#198754' : '#dc3545';
                            items[2].style.color = hasDigit ? '#198754' : '#dc3545';
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        checkPasswordRules();
                    });
                </script>
            @endpush
        </div>
    </div>
@endsection

