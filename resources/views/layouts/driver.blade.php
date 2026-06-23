<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driver Monitoring') | KFA</title>

    <link rel="icon" type="image/png" href="{{ asset('img/kf.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --kf-blue: #00529b;
            --kf-red: #e31e24;
            --kf-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }

        /* ── Navbar ── */
        .navbar-kf {
            background-color: white;
            border-bottom: 3px solid var(--kf-blue);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--kf-blue) !important;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            gap: 0;
            padding: 0;
            text-decoration: none;
        }

        .brand-logo {
            height: 38px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }

        .brand-divider {
            width: 1px;
            height: 32px;
            background-color: #d0d0d0;
            margin: 0 12px;
            flex-shrink: 0;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.2;
        }

        .brand-text .brand-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--kf-blue);
            letter-spacing: .8px;
            white-space: nowrap;
        }

        .brand-text .brand-subtitle {
            font-size: .68rem;
            font-weight: 400;
            color: #888;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .nav-link {
            color: #555 !important;
            font-weight: 500;
            padding: .5rem 1.2rem !important;
            transition: all .3s;
        }

        .nav-link:hover {
            color: var(--kf-blue) !important;
        }

        .nav-link.active {
            color: var(--kf-blue) !important;
            border-bottom: 2px solid var(--kf-red);
        }

        .back-to-hub-btn {
            margin: 0 15px;
            background-color: #ffc107;
            color: #000080;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: background-color .2s, transform .2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .2);
        }

        .back-to-hub-btn:hover {
            background-color: #ffda6a;
            color: #000080;
            transform: translateY(-1px);
        }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .05);
        }

        .card-header {
            background-color: var(--kf-blue);
            color: #fff;
            border-bottom: none;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: .85rem 1.25rem;
        }

        .card-header.bg-white {
            background-color: #fff !important;
            color: #333;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header.bg-light {
            background-color: #f8f9fa !important;
            color: #333;
            border-bottom: 1px solid #dee2e6;
        }

        /* ── Table ── */
        .table thead th {
            background-color: var(--kf-blue);
            color: #fff;
            border-bottom: none;
            font-weight: 600;
            font-size: .875rem;
            padding: .85rem 1rem;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        /* ── Buttons ── */
        .btn-primary {
            background-color: var(--kf-blue);
            border-color: var(--kf-blue);
        }

        .btn-primary:hover {
            background-color: #003d75;
            border-color: #003d75;
        }

        .btn-outline-primary {
            color: var(--kf-blue);
            border-color: var(--kf-blue);
        }

        .btn-outline-primary:hover {
            background-color: var(--kf-blue);
            border-color: var(--kf-blue);
        }

        /* ── Forms ── */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--kf-blue);
            box-shadow: 0 0 0 .2rem rgba(0, 82, 155, .18);
        }

        /* ── Pagination ── */
        .page-item.active .page-link {
            background-color: var(--kf-blue);
            border-color: var(--kf-blue);
        }

        .page-link {
            color: var(--kf-blue);
        }

        .page-link:hover {
            color: #003d75;
        }

        /* ── Modal ── */
        .modal-header {
            background-color: var(--kf-blue);
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
            box-shadow: 0 8px 32px rgba(0, 82, 155, .18);
        }

        /* ── Status badges ── */
        .badge-status {
            padding: .5em 1em;
            border-radius: 20px;
            font-weight: 500;
            font-size: .85rem;
        }

        .status-aktif {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-idle {
            background-color: #fff3cd;
            color: #664d03;
        }

        .status-offline {
            background-color: #f8d7da;
            color: #842029;
        }

        /* ── Alert ── */
        #alert-container {
            position: fixed;
            top: 90px;
            right: 16px;
            z-index: 9999;
            min-width: 300px;
        }

        /* ── Responsive ── */
        @media (max-width: 767.98px) {
            .navbar-kf {
                padding-top: .6rem;
                padding-bottom: .6rem;
            }

            .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .btn {
                font-size: .82rem;
            }

            .card-body {
                padding: 1rem;
            }

            h2,
            h3 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 575.98px) {
            .brand-text .brand-title {
                font-size: .85rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Top blue bar --}}
    <div style="background-color: var(--kf-blue); height: 5px; width: 100%;"></div>

    {{-- ════ DRIVER NAVBAR ════ --}}
    <nav class="navbar navbar-expand-lg navbar-kf sticky-top mb-4">
        <div class="container">

            {{-- Brand --}}
            <a class="navbar-brand" href="{{ route('driver.home') }}">
                <img src="{{ asset('img/kf.png') }}" alt="Logo Kimia Farma Apotek" class="brand-logo">
                <div class="brand-divider d-none d-md-block"></div>
                <div class="brand-text d-none d-md-flex">
                    <span class="brand-title">KIMIA FARMA APOTEK</span>
                    <span class="brand-subtitle">Driver Monitoring System</span>
                </div>
            </a>

            {{-- Mobile: Hub + Toggler --}}
            <div class="d-flex align-items-center gap-2 ms-auto me-2 d-lg-none">
                <a href="{{ route('home') }}" class="back-to-hub-btn py-1 px-3" style="font-size:.8rem;">
                    <i class="fas fa-arrow-circle-left me-1"></i> Hub
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    {{-- Hub Utama (desktop) --}}
                    <li class="nav-item d-none d-lg-block">
                        <a href="{{ route('home') }}" class="back-to-hub-btn py-2 px-3" style="font-size:.8rem;">
                            <i class="fas fa-arrow-circle-left me-1"></i> Hub Utama
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.home') ? 'active' : '' }}"
                            href="{{ route('driver.home') }}">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.mobil.*') ? 'active' : '' }}"
                            href="{{ route('driver.mobil.index') }}">
                            Data Mobil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.supir.*') ? 'active' : '' }}"
                            href="{{ route('driver.supir.index') }}">
                            Data Supir
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.jadwal.*') ? 'active' : '' }}"
                            href="{{ route('driver.jadwal.index') }}">
                            Jadwal Tugas
                        </a>
                    </li>

                    {{-- Divider desktop --}}
                    <li class="nav-item d-none d-lg-block">
                        <span class="nav-link px-2 text-muted" style="cursor:default;">|</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                            href="{{ route('profile.index') }}" title="Profil Saya">
                            <i
                                class="fas fa-user-circle me-1"></i>{{ session('full_name', session('username', 'Profil')) }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link text-danger border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- ════ CONTENT ════ --}}
    <div class="container pb-5">

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

    {{-- Footer --}}
    <footer class="mt-5 pt-4 pb-2 bg-white border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <img src="{{ asset('img/kf.png') }}" alt="Logo KF" height="25" class="opacity-75 mb-2">
                    <p class="text-muted small">&copy; {{ date('Y') }} PT Kimia Farma Apotek</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="text-muted small">
                        Versi 1.0.0 <br>
                        <span class="badge bg-primary">Distribusi Driver</span>
                    </p>
                </div>
            </div>
            <hr class="my-2" style="opacity:.1;">
            <div class="text-center">
                <small style="font-size:10px;color:#ccc;">Sistem ini dikembangkan untuk kebutuhan operasional armada
                    pengiriman farmasi.</small>
            </div>
        </div>
        <div style="background-color: var(--kf-blue); height: 5px; width: 100%; margin-top: 10px;"></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('#alert-container .alert').forEach(a => {
                try { new bootstrap.Alert(a).close(); } catch (e) { }
            });
        }, 4000);

        // Konfirmasi hapus universal (SweetAlert)
        function deleteData(url, dataName) {
            Swal.fire({
                title: 'Delete Data?',
                html: '<strong>' + dataName + '</strong> will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) window.location.href = url;
            });
        }

        function confirmTaskCompletion(url, name) {
            Swal.fire({
                title: 'Complete Task?',
                html: 'Task <strong>' + name + '</strong> will be marked as completed.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Complete!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) window.location.href = url;
            });
        }


        // Table search
        function filterTabel(inputId, tableId) {
            var input = document.getElementById(inputId);
            var filter = input.value.toUpperCase();
            var table = document.getElementById(tableId);
            var tr = table.getElementsByTagName('tr');
            for (var i = 1; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName('td');
                var found = false;
                for (var j = 0; j < td.length; j++) {
                    if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) found = true;
                }
                tr[i].style.display = found ? '' : 'none';
            }
        }

        // Validasi form Bootstrap
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.forEach.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>