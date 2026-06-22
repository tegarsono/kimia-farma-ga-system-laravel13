<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'General Affair') | KFA GA</title>

    <link rel="icon" type="image/png" href="{{ asset('img/kf.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/ga-nosidebar.css') }}">

    {{-- Per-entry styles --}}
    @stack('styles')
</head>

<body>

    {{-- ════ TOPBAR (full width, tanpa sidebar) ════ --}}
    <div class="topbar">
        <a href="{{ route('ga.home') }}" class="btn btn-outline-secondary btn-sm me-1" title="Kembali ke GA">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h5>@yield('page-title', 'Sistem Manajemen GA')</h5>
            <div class="d-flex align-items-center gap-2 me-2">
                <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary btn-sm" title="Profil Saya">
                    <i class="fas fa-user-circle me-1"></i>
                    <span class="d-none d-sm-inline">{{ session('full_name', session('username', 'Profil')) }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        <span class="d-none d-sm-inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ════ CONTENT WRAPPER ════ --}}
    <div class="content-wrapper px-4 pb-4">

        {{-- Alert Container --}}
        <div id="alert-container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        @yield('content')

    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('#alert-container .alert').forEach(a => {
                    try { new bootstrap.Alert(a).close(); } catch (e) { }
                });
            }, 4000);
        });
    </script>
    @stack('scripts')
</body>

</html>