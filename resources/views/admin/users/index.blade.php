@extends('layouts.app')
@push('styles')
<style>
/* Jangan sembunyikan main wrapper; cukup rapikan spacing */
.page-content { padding: 0 !important; }
</style>
@endpush
@section('title', 'User')
@section('page-title', 'Kelola User')

@section('content')
    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <span><i class="fas fa-users me-2"></i>Daftar User</span>
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah User
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->full_name ?? '-' }}</td>
                                <td>{{ $user->username ?? '-' }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger" type="submit">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox me-2"></i>Belum ada user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

