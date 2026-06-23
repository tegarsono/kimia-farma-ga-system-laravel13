@extends('layouts.app')
@push('styles')
<style>
/* Jangan sembunyikan main wrapper; cukup rapikan spacing */
.page-content { padding: 0 !important; }
</style>
@endpush
@section('title', 'User')
@section('page-title', 'Edit User')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-user-edit me-2"></i>Edit User
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->full_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password (opsional)</label>
                        <input
                            type="password"
                            name="password"
                            id="passwordEdit"
                            class="form-control"
                            placeholder="Kosongkan jika tidak diubah"
                            autocomplete="new-password"
                        >
                        <div id="password-rules-edit" class="mt-2 small" style="color:#6c757d">
                            <div><i class="fas fa-circle"></i> Minimal 8 karakter</div>
                            <div><i class="fas fa-circle"></i> Harus ada huruf kapital (A-Z)</div>
                            <div><i class="fas fa-circle"></i> Harus ada angka (0-9)</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Wajib diisi jika mengubah password">
                    </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Permissions</label>
                    @php
                        $selectedPermissions = [];
                        foreach ($userPermissionNames as $pname) {
                            $selectedPermissions[$pname] = true;
                        }
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
        </div>
    </div>
@endsection

