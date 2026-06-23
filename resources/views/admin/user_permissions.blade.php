@extends('layouts.app')

@push('scripts')
    <script>
        // Hilangkan sidebar untuk halaman ini
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('mainSidebar');
            const wrapper = document.getElementById('mainWrapper');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar) sidebar.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            if (wrapper) wrapper.style.marginLeft = '0';
        });
    </script>
@endpush

@section('title', 'User Permission')
@section('page-title', 'Pengaturan Akses User (Spatie)')

@push('styles')
    <style>
        .perm-badge {
            font-size: .78rem;
        }

        .checkbox-col {
            min-width: 320px;
        }

        .table-wrap {
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <span><i class="fas fa-user-shield me-2"></i>Kelola menu/halaman yang bisa dikunjungi user</span>
            <span class="text-white-50" style="font-size:.85rem">Gunakan checkbox untuk memberi permission.</span>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-wrap">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:220px">User</th>
                            <th class="checkbox-col">Permissions (checkbox)</th>
                            <th style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $selectedMap = $usersWithPermissions[$user->id] ?? [];
                            @endphp

                            <tr>
                                <td>
                                    <div style="font-weight:600">
                                        {{ $user->full_name ?? $user->username ?? ('User #' . $user->id) }}
                                    </div>
                                    <div class="text-muted" style="font-size:.85rem">
                                        @{{ $user->username ?? $user->email }}
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column gap-2" style="max-height:260px;overflow:auto; padding-right:8px">
                                        {{-- Form per user (checkbox ada di dalam form agar terkirim JS-free) --}}
                                        <form method="POST" action="{{ route('admin.update') }}" id="perm-form-{{ $user->id }}">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                                            @foreach($permissionMap as $permName => $label)
                                                @php
                                                    $checked = isset($selectedMap[$permName]) && $selectedMap[$permName] === true;
                                                @endphp
                                                <label class="d-flex align-items-center gap-2 mb-0">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permName }}" {{ $checked ? 'checked' : '' }}>
                                                    <span class="perm-badge badge bg-light text-dark border">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </form>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <button type="submit" form="perm-form-{{ $user->id }}" class="btn btn-sm btn-primary">
                                            Simpan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                Jika checkbox tidak tercentang atau tidak tersimpan, kemungkinan tabel permission/role Spatie belum dibuat
                atau
                model <strong>User</strong> belum memakai trait <code>HasRoles</code>/<code>HasPermissions</code>.
            </div>
        </div>
    </div>
@endsection
