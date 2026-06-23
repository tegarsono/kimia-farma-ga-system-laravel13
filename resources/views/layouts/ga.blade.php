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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ============================================
           COLOR PALETTE - General Affair
           Primary:    #1565c0  (Bright Blue)
           Dark Navy:  #0d1b2a  (Sidebar BG)
           Light BG:   #f0f2f5
           ============================================ */
        :root {
            --ga-primary: #1565c0;
            --ga-primary-hover: #1976d2;
            --ga-primary-light: #e3f0ff;
            --ga-sidebar-bg: #0d1b2a;
            --ga-sidebar-sub: #0a1520;
            --ga-sidebar-hover: #1e3a5f;
            --ga-sidebar-active: #1565c0;
            --ga-page-bg: #f0f2f5;
            --ga-card-bg: #ffffff;
            --ga-text-dark: #1a2744;
            --ga-text-muted: #6b7280;
            --ga-border: #e2e8f0;
            --ga-shadow-card: 0 1px 4px rgba(0, 0, 0, .08), 0 4px 16px rgba(21, 101, 192, .07);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--ga-page-bg);
            color: var(--ga-text-dark);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #0d1b2a;
            color: #e8edf3;
            padding-top: 0;
            box-shadow: 3px 0 12px rgba(0, 0, 0, .25);
            z-index: 1040;
            overflow-x: hidden;
            overflow-y: auto;
            transition: transform 0.3s ease;
            transform: translateX(-260px);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #1e3a5f;
            border-radius: 4px;
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .55);
            z-index: 1035;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            margin-bottom: 8px;
        }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: #1565c0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand-icon i {
            color: #fff;
            font-size: 1rem;
        }

        .sidebar-brand-text {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-brand-sub {
            font-size: .7rem;
            color: #7a9bbf;
            font-weight: 400;
        }

        /* Profile section */
        .profile-section {
            text-align: center;
            padding: 16px 20px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            margin-bottom: 8px;
            cursor: pointer;
            transition: background-color .2s;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .profile-section:hover {
            background-color: #1e3a5f;
        }

        .profile-section img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid #1565c0;
            object-fit: cover;
        }

        .profile-section h6 {
            color: #fff;
            font-size: .9rem;
            font-weight: 600;
            margin: 8px 0 2px;
        }

        .profile-section small {
            color: #7a9bbf;
            font-size: .75rem;
        }

        /* Nav section label */
        .nav-section-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4a6a8a;
            padding: 12px 20px 4px;
        }

        /* Nav links */
        .sidebar .nav-link {
            color: #b0c4d8 !important;
            padding: 10px 20px;
            border-radius: 0;
            transition: background-color .2s, color .2s;
            text-decoration: none;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link i.nav-icon {
            width: 18px;
            text-align: center;
            font-size: .95rem;
            flex-shrink: 0;
        }

        .sidebar .nav-link:hover {
            background-color: #1e3a5f;
            color: #fff !important;
        }

        .sidebar .nav-item.active>.nav-link,
        .sidebar .nav-link.active-link {
            background-color: #1565c0;
            color: #fff !important;
            font-weight: 600;
            border-left: 3px solid #42a5f5;
        }

        /* Dropdown arrow */
        .sidebar .nav-link .fa-angle-left {
            margin-left: auto;
            font-size: .8rem;
            transition: transform .25s ease;
            color: #4a6a8a;
        }

        .sidebar .nav-link.collapsed .fa-angle-left {
            transform: rotate(-90deg);
        }

        .sidebar .nav-link:not(.collapsed) .fa-angle-left {
            transform: rotate(0deg);
        }

        /* Sub-menu */
        .nav-treeview {
            list-style: none;
            padding: 4px 0;
            margin: 0;
            background-color: #0a1520;
        }

        .nav-treeview .nav-item a {
            padding: 8px 20px 8px 48px;
            font-size: .855rem;
            color: #7a9bbf !important;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color .2s, color .2s;
            border-left: 3px solid transparent;
        }

        .nav-treeview .nav-item a::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #4a6a8a;
            flex-shrink: 0;
        }

        .nav-treeview .nav-item a:hover {
            background-color: #1e3a5f;
            color: #fff !important;
            border-left-color: #1565c0;
        }

        .nav-treeview .nav-item a:hover::before {
            background: #42a5f5;
        }

        .nav-treeview .nav-item a.active-link {
            background-color: #1e3a5f;
            color: #fff !important;
            border-left-color: #1565c0;
        }

        /* Back to hub */
        .back-to-hub-btn {
            margin: 8px 16px 4px;
            background-color: rgba(21, 101, 192, .15);
            color: #42a5f5;
            font-weight: 600;
            font-size: .85rem;
            padding: 9px 14px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color .2s, color .2s;
            border: 1px solid rgba(21, 101, 192, .3);
        }

        .back-to-hub-btn:hover {
            background-color: #1565c0;
            color: #fff;
            border-color: #1565c0;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, .07);
            margin-top: auto;
            font-size: .75rem;
            color: #4a6a8a;
            text-align: center;
        }

        /* ============================================
           TOPBAR
           ============================================ */
        .toggle-header {
            height: 60px;
            padding: 0 20px;
            background-color: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1030;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            transition: left .3s ease;
            border-bottom: 1px solid #e2e8f0;
        }

        @media (min-width: 768px) {
            .toggle-header.sidebar-open {
                left: 260px;
            }
        }

        .toggle-header h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #1a2744;
        }

        .sidebar-toggler-btn {
            background-color: #1565c0;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            flex-shrink: 0;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: background-color .2s;
        }

        .sidebar-toggler-btn:hover {
            background-color: #1976d2;
        }

        /* ============================================
           CONTENT WRAPPER
           ============================================ */
        .content-wrapper {
            margin-left: 0;
            transition: margin-left .3s ease;
            padding-top: 80px;
            min-height: 100vh;
            background-color: #f0f2f5;
        }

        @media (min-width: 768px) {
            .content-wrapper.sidebar-open {
                margin-left: 260px;
            }
        }

        /* ============================================
           CARDS
           ============================================ */
        .card {
            border: 1px solid var(--ga-border);
            border-radius: 12px;
            box-shadow: var(--ga-shadow-card);
            background-color: var(--ga-card-bg);
        }

        .card-header {
            background-color: var(--ga-primary);
            color: #fff;
            border-bottom: none;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: .85rem 1.25rem;
        }

        .card-header.bg-white {
            background-color: #fff !important;
            color: var(--ga-text-dark);
            border-bottom: 1px solid var(--ga-border);
        }

        .card-header.bg-light {
            background-color: #f8fafc !important;
            color: var(--ga-text-dark);
            border-bottom: 1px solid var(--ga-border);
        }

        /* ============================================
           TABLE
           ============================================ */
        .table {
            background-color: var(--ga-card-bg);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--ga-primary);
            color: #fff;
            border-bottom: none;
            font-weight: 600;
            font-size: .875rem;
            letter-spacing: .3px;
            padding: .85rem 1rem;
        }

        .table tbody tr:hover {
            background-color: var(--ga-primary-light);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn-primary {
            background-color: var(--ga-primary);
            border-color: var(--ga-primary);
            color: #fff;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--ga-primary-hover);
            border-color: var(--ga-primary-hover);
            color: #fff;
        }

        .btn-warning {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #fff;
        }

        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: #fff;
        }

        .btn-outline-primary {
            color: var(--ga-primary);
            border-color: var(--ga-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--ga-primary);
            border-color: var(--ga-primary);
            color: #fff;
        }

        /* ============================================
           FORMS
           ============================================ */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--ga-primary);
            box-shadow: 0 0 0 .2rem rgba(21, 101, 192, .18);
        }

        .form-label {
            font-weight: 500;
            color: var(--ga-text-dark);
            font-size: .9rem;
        }

        /* ============================================
           PAGINATION
           ============================================ */
        .page-item.active .page-link {
            background-color: var(--ga-primary);
            border-color: var(--ga-primary);
        }

        .page-link {
            color: var(--ga-primary);
            border-color: var(--ga-border);
        }

        .page-link:hover {
            color: var(--ga-primary-hover);
            background-color: var(--ga-primary-light);
            border-color: var(--ga-border);
        }

        /* ============================================
           MODAL
           ============================================ */
        .modal-header {
            background-color: var(--ga-primary);
            color: #fff;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 32px rgba(21, 101, 192, .18);
        }

        /* ============================================
           ALERT TOAST
           ============================================ */
        #alert-container {
            position: fixed;
            top: 70px;
            right: 16px;
            z-index: 9999;
            min-width: 300px;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 767.98px) {
            .content-wrapper.p-4 {
                padding: 1rem !important;
            }

            .card {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .btn {
                font-size: .82rem;
            }

            .modal-dialog {
                margin: .5rem;
            }

            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-link {
                padding: .3rem .6rem;
                font-size: .82rem;
            }
        }

        @media (max-width: 575.98px) {
            .card-header {
                font-size: .9rem;
                padding: .6rem 1rem;
            }

            .badge {
                font-size: .7rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ════ SIDEBAR GA (sama persis dengan kimiafarma/general_affair/templates/navbar.php) ════ --}}
    <div class="sidebar" id="mainSidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div>
                <div class="sidebar-brand-text">General Affair</div>
                <div class="sidebar-brand-sub">Kimia Farma Apotek</div>
            </div>
        </div>

        {{-- Profile Section --}}
        <a href="{{ route('profile.index') }}" class="profile-section" title="Lihat Profil">
            <img src="{{ $sidebarFotoProfil }}" alt="Foto Profil">
            <h6>{{ session('full_name', session('username', 'User')) }}</h6>
            <small>
                @php
                    $roleLabels = ['admin' => 'Administrator', 'staff' => 'Staff GA', 'manager' => 'Manager GA'];
                    echo $roleLabels[session('role', 'staff')] ?? ucfirst(session('role', 'staff'));
                @endphp
            </small>
        </a>

        {{-- Tombol Kembali ke Hub Utama --}}
        <a href="{{ route('home') }}" class="back-to-hub-btn">
            <i class="fas fa-arrow-left"></i> Kembali ke Hub Utama
        </a>

        {{-- Navigation --}}
        <div class="nav-section-label">Menu Utama</div>

        <ul class="nav flex-column">
            <li class="nav-item {{ request()->routeIs('ga.home') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('ga.home') ? 'active-link' : '' }}"
                    href="{{ route('ga.home') }}">
                    <i class="fas fa-home nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Asset Manajemen --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ga.kendaraan.*') || request()->routeIs('ga.tanah_bangunan.*') ? '' : 'collapsed' }}"
                    href="#assetCollapse" data-bs-toggle="collapse"
                    aria-expanded="{{ request()->routeIs('ga.kendaraan.*') || request()->routeIs('ga.tanah_bangunan.*') ? 'true' : 'false' }}"
                    aria-controls="assetCollapse">
                    <i class="fas fa-warehouse nav-icon"></i>
                    <span>Asset Manajemen</span>
                    <i class="fas fa-angle-left"></i>
                </a>
                <div class="collapse {{ request()->routeIs('ga.kendaraan.*') || request()->routeIs('ga.tanah_bangunan.*') ? 'show' : '' }}"
                    id="assetCollapse">
                    <ul class="nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('ga.tanah_bangunan.index') }}"
                                class="{{ request()->routeIs('ga.tanah_bangunan.*') ? 'active-link' : '' }}">
                                Tanah &amp; Bangunan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ga.kendaraan.index') }}"
                                class="{{ request()->routeIs('ga.kendaraan.*') ? 'active-link' : '' }}">
                                Kendaraan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ga.dir.index') }}"
                                class="{{ request()->routeIs('ga.dir.*') ? 'active-link' : '' }}">
                                DIR
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Sentralisasi --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ga.atk.*') ? '' : 'collapsed' }}" href="#sentralisasiCollapse"
                    data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('ga.atk.*') ? 'true' : 'false' }}"
                    aria-controls="sentralisasiCollapse">
                    <i class="fas fa-boxes nav-icon"></i>
                    <span>Sentralisasi</span>
                    <i class="fas fa-angle-left"></i>
                </a>
                <div class="collapse {{ request()->routeIs('ga.atk.*') ? 'show' : '' }}" id="sentralisasiCollapse">
                    <ul class="nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('ga.atk.index') }}"
                                class="{{ request()->routeIs('ga.atk.index') || request()->routeIs('ga.atk.create') || request()->routeIs('ga.atk.edit') ? 'active-link' : '' }}">
                                Alat Tulis Kantor (ATK)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ga.atk.barangKeluarForm') }}"
                                class="{{ request()->routeIs('ga.atk.barangKeluarForm') ? 'active-link' : '' }}">
                                Barang Keluar
                            </a>
                        </li>
                        {{-- Riwayat Transaksi ATK di-hide di sidebar --}}
                        {{-- <li class="nav-item">
                            <a href="{{ route('ga.atk.riwayat') }}"
                                class="{{ request()->routeIs('ga.atk.riwayat') ? 'active-link' : '' }}">
                                Riwayat Transaksi
                            </a>
                        </li> --}}
                    </ul>
                </div>
            </li>

            {{-- Biaya --}}
            <li class="nav-item {{ request()->routeIs('ga.biaya.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('ga.biaya.*') ? 'active-link' : '' }}"
                    href="{{ route('ga.biaya.index') }}">
                    <i class="fas fa-wallet nav-icon"></i>
                    <span>Biaya</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            &copy; {{ date('Y') }} Kimia Farma Apotek
        </div>
    </div>

    {{-- ════ TOPBAR ════ --}}
    <div class="toggle-header" id="mainHeader">
        <button class="sidebar-toggler-btn" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h5 class="mb-0">@yield('page-title', 'Sistem Manajemen GA')</h5>
            <div class="d-flex align-items-center gap-2 me-3">
                <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary btn-sm" title="Profil Saya">
                    <i class="fas fa-user-circle me-1"></i>
                    <span class="d-none d-sm-inline">{{ session('full_name', session('username', 'Profil')) }}</span>
                </a>
                @if(session('role') === 'admin')
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm" title="Pengaturan">
                        <i class="fas fa-images me-1"></i>
                        <span class="d-none d-sm-inline">Gambar</span>
                    </a>
                @endif
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
    <div class="content-wrapper px-4 pb-4" id="contentWrapper">

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
            const sidebar = document.getElementById('mainSidebar');
            const content = document.getElementById('contentWrapper');
            const header = document.getElementById('mainHeader');
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const DESKTOP = () => window.innerWidth >= 768;

            let isOpen = false;

            function applyState() {
                if (isOpen) {
                    sidebar.classList.add('open');
                    if (DESKTOP()) {
                        content.classList.add('sidebar-open');
                        header.classList.add('sidebar-open');
                        overlay.classList.remove('active');
                    } else {
                        content.classList.remove('sidebar-open');
                        header.classList.remove('sidebar-open');
                        overlay.classList.add('active');
                    }
                } else {
                    sidebar.classList.remove('open');
                    content.classList.remove('sidebar-open');
                    header.classList.remove('sidebar-open');
                    overlay.classList.remove('active');
                }
            }

            // State awal: desktop = terbuka, mobile = tertutup
            isOpen = DESKTOP();
            applyState();

            toggleBtn.addEventListener('click', function () {
                isOpen = !isOpen;
                applyState();
            });

            overlay.addEventListener('click', function () {
                isOpen = false;
                applyState();
            });

            window.addEventListener('resize', function () {
                isOpen = DESKTOP();
                applyState();
            });

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('#alert-container .alert').forEach(a => {
                    try { new bootstrap.Alert(a).close(); } catch (e) { }
                });
            }, 4000);
        });
    </script>
    @stack('scripts')
    {{-- ════ IMPORT EXCEL AJAX HANDLER ════ --}}
    <script>
        (function () {
            /**
             * Intercept semua form import Excel di halaman ini.
             * Kirim via AJAX, tampilkan SweetAlert detail jika gagal.
             */
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form[action*="/import"]').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const submitBtn = form.querySelector('[type="submit"]');
                        const originalHtml = submitBtn ? submitBtn.innerHTML : '';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengimport...';
                        }

                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: formData,
                        })
                            .then(function (res) {
                                if (res.status === 422) {
                                    return res.json().then(function (data) {
                                        // Laravel validation error
                                        const msgs = data.errors
                                            ? Object.values(data.errors).flat()
                                            : ['Validasi gagal. Pastikan file yang diupload benar.'];
                                        throw { title: 'Validasi Gagal', errors: msgs };
                                    });
                                }
                                return res.json();
                            })
                            .then(function (data) {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalHtml;
                                }

                                // Tutup modal jika ada
                                const modal = form.closest('.modal');
                                if (modal) {
                                    const bsModal = bootstrap.Modal.getInstance(modal);
                                    if (bsModal) bsModal.hide();
                                }

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.title || 'Import Berhasil',
                                        html: '<p><strong>' + (data.success_count || 0) + '</strong> data berhasil diimport.</p>',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#1565c0',
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else {
                                    const errors = data.errors || [];
                                    const shown = errors.slice(0, 20);
                                    const rest = errors.length - shown.length;

                                    let listHtml = '<ul style="text-align:left;padding-left:1.2rem;margin:0">';
                                    shown.forEach(function (err) {
                                        listHtml += '<li>' + err.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</li>';
                                    });
                                    listHtml += '</ul>';
                                    if (rest > 0) {
                                        listHtml += '<p class="mt-2 mb-0"><strong>... dan ' + rest + ' error lainnya</strong></p>';
                                    }

                                    const successInfo = (data.success_count > 0)
                                        ? '<p class="mb-2 text-success"><strong>' + data.success_count + '</strong> baris berhasil diimport sebelum error ditemukan.</p>'
                                        : '';

                                    Swal.fire({
                                        icon: 'error',
                                        title: data.title || 'Import Gagal',
                                        html: successInfo +
                                            '<p class="mb-2">Terdapat <strong>' + errors.length + '</strong> baris bermasalah:</p>' +
                                            '<div style="max-height:280px;overflow-y:auto;border:1px solid #dee2e6;border-radius:6px;padding:.6rem .8rem;background:#fff8f8;font-size:.875rem">' +
                                            listHtml + '</div>',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#dc3545',
                                        width: '600px',
                                    });
                                }
                            })
                            .catch(function (err) {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalHtml;
                                }

                                const title = (err && err.title) ? err.title : 'Terjadi Kesalahan';
                                const errors = (err && err.errors) ? err.errors : ['Terjadi kesalahan tak terduga. Silakan coba lagi.'];

                                let listHtml = '<ul style="text-align:left;padding-left:1.2rem;margin:0">';
                                errors.forEach(function (e) {
                                    listHtml += '<li>' + String(e).replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</li>';
                                });
                                listHtml += '</ul>';

                                Swal.fire({
                                    icon: 'error',
                                    title: title,
                                    html: '<div style="max-height:280px;overflow-y:auto">' + listHtml + '</div>',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc3545',
                                    width: '560px',
                                });
                            });
                    });
                });
            });
        })();
    </script>
</body>

</html>