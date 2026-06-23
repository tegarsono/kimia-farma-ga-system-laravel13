<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kimia Farma GA') | KFA GA System</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('img/kf.png') }}">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --kf-primary: #1565c0;
            --kf-primary-hover: #1976d2;
            --kf-primary-light: #e3f0ff;
            --kf-secondary: #00a859;
            --kf-sidebar-bg: #0d1b2a;
            --kf-sidebar-sub: #132337;
            --kf-sidebar-hover: #1e3a5f;
            --kf-sidebar-active: #1565c0;
            --kf-sidebar-width: 260px;
            --kf-page-bg: #f0f2f5;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--kf-page-bg); color: #1a2744; }

        /* ── Sidebar ─────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--kf-sidebar-width);
            background: var(--kf-sidebar-bg);
            color: #e8edf3;
            display: flex; flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
            overflow-x: hidden; overflow-y: auto;
            box-shadow: 3px 0 12px rgba(0,0,0,.25);
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #1e3a5f; border-radius: 4px; }

        .sidebar.collapsed { transform: translateX(-100%); }

        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand img { width: 36px; height: 36px; object-fit: contain; }
        .sidebar-brand-text { font-size: 1rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .sidebar-brand-sub { font-size: .7rem; color: #7a9bbf; font-weight: 400; }

        .sidebar-nav { flex: 1; padding: 8px 0; }
        .nav-section-label {
            font-size: .68rem; font-weight: 700; letter-spacing: 1px;
            color: #4a6a8a; text-transform: uppercase;
            padding: 12px 20px 4px;
        }
        .nav-item-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; color: #b0c4d8;
            text-decoration: none; font-size: .9rem;
            transition: background .2s, color .2s;
            border-radius: 0;
        }
        .nav-item-link:hover { background: var(--kf-sidebar-hover); color: #fff; }
        .nav-item-link.active { background: var(--kf-primary); color: #fff; font-weight: 600; border-left: 3px solid #42a5f5; }
        .nav-item-link i { width: 18px; text-align: center; font-size: .95rem; flex-shrink: 0; }

        .nav-collapse-toggle {
            display: flex; align-items: center; gap: 10px; justify-content: space-between;
            padding: 10px 20px; color: #b0c4d8;
            cursor: pointer; font-size: .9rem;
            transition: background .2s, color .2s;
        }
        .nav-collapse-toggle:hover { background: var(--kf-sidebar-hover); color: #fff; }
        .nav-collapse-toggle .left { display: flex; align-items: center; gap: 10px; }
        .nav-collapse-inner { padding-left: 28px; background: #0a1520; }
        .nav-collapse-inner .nav-item-link { font-size: .855rem; color: #7a9bbf; }
        .nav-collapse-inner .nav-item-link::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%;
            background: #4a6a8a; flex-shrink: 0;
        }
        .nav-collapse-inner .nav-item-link:hover::before { background: #42a5f5; }
        .nav-collapse-inner .nav-item-link.active { border-left: 3px solid var(--kf-primary); }

        .sidebar-profile {
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-profile img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--kf-primary); }
        .sidebar-profile-info { overflow: hidden; }
        .sidebar-profile-name { font-size: .8rem; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-profile-role { font-size: .68rem; color: #7a9ab8; }

        /* ── Overlay ─────────────────────────────────────────── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.55); z-index: 1035;
        }
        .sidebar-overlay.active { display: block; }

        /* ── Main content ────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--kf-sidebar-width);
            min-height: 100vh;
            transition: margin-left .3s ease;
            display: flex; flex-direction: column;
        }
        .main-wrapper.expanded { margin-left: 0; }

        /* ── Topbar ──────────────────────────────────────────── */
        .topbar {
            position: sticky; top: 0; z-index: 1030;
            background: #fff; border-bottom: 3px solid var(--kf-primary);
            padding: 0 20px; height: 60px;
            display: flex; align-items: center; gap: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .topbar-title { font-size: 1rem; font-weight: 600; color: #1a2744; flex: 1; }

        .btn-sidebar-toggle {
            background-color: var(--kf-primary); color: white;
            border: none; font-size: 1rem;
            cursor: pointer; padding: 8px 12px;
            border-radius: 8px; transition: background .2s;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-sidebar-toggle:hover { background-color: var(--kf-primary-hover); }

        /* ── Page content ────────────────────────────────────── */
        .page-content { flex: 1; padding: 24px; }

        /* ── Cards ───────────────────────────────────────────── */
        .card { border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(21,101,192,.07); border-radius: 12px; }
        .card-header { background: var(--kf-primary); color: #fff; border-bottom: none; border-radius: 12px 12px 0 0 !important; padding: 0.85rem 1.25rem; font-weight: 600; }
        .card-header.bg-white { background: #fff !important; color: #1a2744; border-bottom: 1px solid #edf2f7; }
        .card-header.bg-light { background: #f8fafc !important; color: #1a2744; border-bottom: 1px solid #edf2f7; }

        /* ── Status badges ───────────────────────────────────── */
        .badge-layak    { background: #d1fae5; color: #065f46; }
        .badge-tidak    { background: #fee2e2; color: #991b1b; }
        .badge-normal   { background: #d1fae5; color: #065f46; }
        .badge-service  { background: #fef3c7; color: #92400e; }

        /* ── Table ───────────────────────────────────────────── */
        .table thead th {
            background: var(--kf-primary); color: #fff;
            font-size: .875rem; font-weight: 600; letter-spacing: .3px;
            border-bottom: none; padding: .85rem 1rem;
        }
        .table tbody tr:hover { background-color: var(--kf-primary-light); }
        .table td { font-size: .84rem; vertical-align: middle; }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn-primary { background-color: var(--kf-primary); border-color: var(--kf-primary); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--kf-primary-hover); border-color: var(--kf-primary-hover); }
        .btn-outline-primary { color: var(--kf-primary); border-color: var(--kf-primary); }
        .btn-outline-primary:hover { background-color: var(--kf-primary); border-color: var(--kf-primary); }

        /* ── Forms ───────────────────────────────────────────── */
        .form-control:focus, .form-select:focus {
            border-color: var(--kf-primary);
            box-shadow: 0 0 0 .2rem rgba(21,101,192,.18);
        }

        /* ── Pagination ──────────────────────────────────────── */
        .page-item.active .page-link { background-color: var(--kf-primary); border-color: var(--kf-primary); }
        .page-link { color: var(--kf-primary); }
        .page-link:hover { color: var(--kf-primary-hover); background-color: var(--kf-primary-light); }

        /* ── Modal ───────────────────────────────────────────── */
        .modal-header { background-color: var(--kf-primary); color: #fff; border-bottom: none; border-radius: 12px 12px 0 0; }
        .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .modal-content { border-radius: 12px; border: none; box-shadow: 0 8px 32px rgba(21,101,192,.18); }

        /* ── Alert toast ─────────────────────────────────────── */
        #alert-container { position: fixed; top: 16px; right: 16px; z-index: 9999; min-width: 300px; }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ════ SIDEBAR ════ --}}
@php
// Hindari white-screen: pastikan hanya bagian sidebar yang disembunyikan,
// bukan seluruh wrapper utama.
$hideSidebar = request()->routeIs('admin.users.*');
@endphp

@if(!$hideSidebar)
<aside class="sidebar" id="mainSidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <img src="{{ asset('img/kf.png') }}" alt="KFA" id="sidebarLogo">
        <span>KFA General Affair</span>
    </div>

    {{-- Nav --}}
    <nav class="sidebar-nav">
        {{-- ─ Module Hub ─ --}}
        <div class="nav-section-label">Menu Utama</div>
        <a href="{{ route('home') }}" class="nav-item-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Hub Sistem
        </a>

        {{-- ─ General Affair ─ --}}
        <div class="nav-section-label">General Affair</div>
        <a href="{{ route('ga.home') }}" class="nav-item-link {{ request()->routeIs('ga.home') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Dashboard GA
        </a>

        {{-- Aset Management --}}
        <div class="nav-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#collapseAset">
            <div class="left"><i class="fas fa-warehouse"></i> Manajemen Aset</div>
            <i class="fas fa-chevron-down fa-xs"></i>
        </div>
        <div class="collapse {{ request()->routeIs('ga.kendaraan.*') || request()->routeIs('ga.tanah_bangunan.*') ? 'show' : '' }}" id="collapseAset">
            <div class="nav-collapse-inner">
                <a href="{{ route('ga.kendaraan.index') }}" class="nav-item-link {{ request()->routeIs('ga.kendaraan.*') ? 'active' : '' }}">
                    <i class="fas fa-car"></i> Kendaraan
                </a>
                <a href="{{ route('ga.tanah_bangunan.index') }}" class="nav-item-link {{ request()->routeIs('ga.tanah_bangunan.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Tanah & Bangunan
                </a>
            </div>
        </div>

        {{-- Sentralisasi ATK --}}
        <div class="nav-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#collapseAtk">
            <div class="left"><i class="fas fa-boxes"></i> Sentralisasi ATK</div>
            <i class="fas fa-chevron-down fa-xs"></i>
        </div>
        <div class="collapse {{ request()->routeIs('ga.atk.*') ? 'show' : '' }}" id="collapseAtk">
            <div class="nav-collapse-inner">
                <a href="{{ route('ga.atk.index') }}" class="nav-item-link {{ request()->routeIs('ga.atk.index') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Katalog ATK
                </a>
                <a href="{{ route('ga.atk.riwayat') }}" class="nav-item-link {{ request()->routeIs('ga.atk.riwayat') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Transaksi
                </a>
            </div>
        </div>

        <a href="{{ route('ga.biaya.index') }}" class="nav-item-link {{ request()->routeIs('ga.biaya.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> Biaya Umum
        </a>

        {{-- ─ AC Monitoring ─ --}}
        <div class="nav-section-label">Monitoring</div>
        <a href="{{ route('ac.index') }}" class="nav-item-link {{ request()->routeIs('ac.*') ? 'active' : '' }}">
            <i class="fas fa-snowflake"></i> Monitoring Maintenance
        </a>

        {{-- ─ Driver ─ --}}
        <div class="nav-section-label">Driver Operasional</div>
        <a href="{{ route('driver.home') }}" class="nav-item-link {{ request()->routeIs('driver.home') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard Driver
        </a>

        <div class="nav-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#collapseDriver">
            <div class="left"><i class="fas fa-car-side"></i> Kelola Armada</div>
            <i class="fas fa-chevron-down fa-xs"></i>
        </div>
        <div class="collapse {{ request()->routeIs('driver.jadwal.*') || request()->routeIs('driver.mobil.*') || request()->routeIs('driver.supir.*') ? 'show' : '' }}" id="collapseDriver">
            <div class="nav-collapse-inner">
                <a href="{{ route('driver.jadwal.index') }}" class="nav-item-link {{ request()->routeIs('driver.jadwal.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Jadwal
                </a>
                <a href="{{ route('driver.mobil.index') }}" class="nav-item-link {{ request()->routeIs('driver.mobil.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i> Armada Mobil
                </a>
                <a href="{{ route('driver.supir.index') }}" class="nav-item-link {{ request()->routeIs('driver.supir.*') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i> Data Supir
                </a>
            </div>
        </div>

        {{-- ─ Akun ─ --}}
        <div class="nav-section-label">Akun</div>
        @if(session('role') === 'admin')
        <a href="{{ route('settings.index') }}" class="nav-item-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-images"></i> Pengaturan Gambar
        </a>
        @endif
        <a href="{{ route('profile.index') }}" class="nav-item-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item-link w-100 text-start border-0 bg-transparent text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    {{-- Profile Footer --}}
    <div class="sidebar-profile">
        <img src="{{ asset('img/kf.png') }}" alt="Profil">
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">{{ session('full_name', session('username', 'User')) }}</div>
            <div class="sidebar-profile-role">{{ ucfirst(session('role', 'staff')) }}</div>
        </div>
    </div>
</aside>

@endif

{{-- ════ MAIN WRAPPER ════ --}}
<div class="main-wrapper" id="mainWrapper" style="margin-left:0;">

    {{-- Topbar --}}
    <div class="topbar">
        <button class="btn-sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-user-circle me-1"></i>
                <span class="d-none d-sm-inline">{{ session('full_name', session('username')) }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt me-1"></i><span class="d-none d-sm-inline">Logout</span></button>
            </form>
        </div>
    </div>

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

    {{-- Page Content --}}
    <div class="page-content">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="text-center text-muted py-3 border-top bg-white" style="font-size:.75rem;">
        &copy; {{ date('Y') }} Kimia Farma Apotek &mdash; General Affair System. All Rights Reserved.
    </footer>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar  = document.getElementById('mainSidebar');
    const wrapper  = document.getElementById('mainWrapper');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggle   = document.getElementById('sidebarToggle');
    const isMobile = () => window.innerWidth <= 768;

    function openSidebar() {
        sidebar.classList.add('open');
        if (isMobile()) overlay.classList.add('active');
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
    function toggleSidebar() {
        if (isMobile()) {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        } else {
            sidebar.classList.toggle('collapsed');
            wrapper.classList.toggle('expanded');
        }
    }

    toggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Auto-hide alerts after 4s
    setTimeout(() => {
        document.querySelectorAll('#alert-container .alert').forEach(a => {
            new bootstrap.Alert(a).close();
        });
    }, 4000);
</script>
@stack('scripts')
</body>
</html>
