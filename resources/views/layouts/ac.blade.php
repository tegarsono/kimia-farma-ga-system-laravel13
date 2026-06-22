<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Monitoring Maintenance GA') | KFA</title>

    <link rel="icon" type="image/png" href="{{ asset('img/kf.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --kf-blue: #0056b3;
            --kf-blue-dark: #003d82;
            --kf-light: #f8f9fa;
            --kf-accent: #00a859;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        /* ── Navbar ── */
        .navbar {
            background: #ffffff !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
            border-bottom: 3px solid var(--kf-blue);
            min-height: 64px;
            padding-top: 0;
            padding-bottom: 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--kf-blue) !important;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            text-decoration: none;
        }

        .navbar-brand .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .navbar-brand .brand-icon img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .navbar-brand .brand-title {
            font-size: .95rem;
            font-weight: 700;
            color: var(--kf-blue);
            line-height: 1.2;
        }

        .navbar-brand .brand-sub {
            font-size: .7rem;
            font-weight: 400;
            color: #6c757d;
        }

        .navbar-nav .nav-link {
            font-size: .875rem;
            font-weight: 500;
            color: #555 !important;
            padding: 8px 12px !important;
            border-radius: 6px;
            transition: background-color .2s, color .2s;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .navbar-nav .nav-link:hover {
            color: var(--kf-blue) !important;
            background: rgba(0, 86, 179, .07);
        }

        .navbar-nav .nav-link.active {
            font-weight: 600;
            color: var(--kf-blue) !important;
            background: rgba(0, 86, 179, .1);
        }

        .nav-divider {
            width: 1px;
            height: 28px;
            background: #dee2e6;
            margin: 0 6px;
            align-self: center;
        }

        .btn-hub {
            font-size: .82rem;
            font-weight: 600;
            color: var(--kf-blue) !important;
            background: rgba(0, 86, 179, .08);
            border: 1px solid rgba(0, 86, 179, .25);
            border-radius: 6px;
            padding: 6px 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color .2s, border-color .2s;
            white-space: nowrap;
        }

        .btn-hub:hover {
            background: var(--kf-blue);
            color: #fff !important;
            border-color: var(--kf-blue);
        }

        .navbar-user-area {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-user-profile {
            font-size: .82rem;
            font-weight: 500;
            color: #444 !important;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color .2s;
            white-space: nowrap;
            max-width: 160px;
            overflow: hidden;
        }

        .btn-user-profile span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-user-profile:hover {
            background: #e9ecef;
            color: #333 !important;
        }

        .btn-logout {
            font-size: .82rem;
            font-weight: 500;
            color: #dc3545 !important;
            background: #fff5f5;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 6px 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color .2s, color .2s;
            white-space: nowrap;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: #fff !important;
            border-color: #dc3545;
        }

        .notif-badge {
            font-size: .65rem;
            padding: 2px 5px;
            vertical-align: middle;
        }

        /* ── Content ── */
        .main-content {
            padding-top: 40px;
            padding-bottom: 40px;
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
            box-shadow: 0 0 0 .2rem rgba(0, 86, 179, .18);
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
            color: var(--kf-blue-dark);
            background-color: #e0f0ff;
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
            box-shadow: 0 8px 32px rgba(0, 86, 179, .18);
        }

        /* ── Alert ── */
        #alert-container {
            position: fixed;
            top: 80px;
            right: 16px;
            z-index: 9999;
            min-width: 300px;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding: 12px 0 8px;
                border-top: 1px solid #e9ecef;
                margin-top: 8px;
            }

            .navbar-nav .nav-link {
                padding: 9px 10px !important;
                border-radius: 6px;
            }

            .nav-divider {
                display: none;
            }

            .navbar-user-area {
                flex-wrap: wrap;
                padding-top: 8px;
                border-top: 1px solid #e9ecef;
                margin-top: 4px;
            }

            .btn-hub,
            .btn-user-profile,
            .btn-logout {
                flex: 1 1 auto;
                justify-content: center;
            }
        }

        @media (max-width: 767.98px) {
            .main-content {
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- ════ NAVBAR AC MONITORING (sama persis dengan kimiafarma/ac_monitoring/includes/header.php) ════ --}}
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">

            {{-- Brand --}}
            <a class="navbar-brand" href="{{ route('ac.index') }}">
                <div class="brand-icon">
                    <img src="{{ $logoAcHeader }}" alt="Logo KFA">
                </div>
                <div>
                    <div class="brand-title">KIMIA FARMA APOTEK</div>
                    <div class="brand-sub">GA Aset Monitoring Sistem</div>
                </div>
            </a>

            {{-- Mobile toggler --}}
            <button class="navbar-toggler border-0 ms-auto" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                {{-- Main nav links --}}
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ac.index') && !request()->routeIs('ac.create') && !request()->routeIs('ac.edit') ? 'active' : '' }}"
                            href="{{ route('ac.index') }}">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ac.create') ? 'active' : '' }}"
                            href="{{ route('ac.create') }}">
                            <i class="bi bi-plus-circle"></i> Tambah Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ac.notifikasi') ? 'active' : '' }}"
                            href="{{ route('ac.notifikasi') }}">
                            <i class="bi bi-bell"></i> Notifikasi
                        </a>
                    </li>
                </ul>

                {{-- Right-side actions --}}
                <div class="navbar-user-area ms-lg-3 mt-2 mt-lg-0">
                    <a href="{{ route('home') }}" class="btn-hub">
                        <i class="fas fa-arrow-left"></i>
                        <span>Hub Utama</span>
                    </a>
                    <div class="nav-divider d-none d-lg-block"></div>
                    <a href="{{ route('profile.index') }}" class="btn-user-profile" title="Profil Saya">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ session('full_name', session('username', 'Profil')) }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ════ CONTENT ════ --}}
    <div class="container main-content">

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
    <footer class="bg-white border-top mt-5 py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-muted small">&copy; {{ date('Y') }} <strong>Kimia Farma Apotek</strong>. All
                        Rights Reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="badge bg-light text-dark border p-2">
                        <i class="bi bi-clock-history me-1 text-primary"></i> Sistem Monitoring Rutin
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('#alert-container .alert').forEach(a => {
                try { new bootstrap.Alert(a).close(); } catch (e) { }
            });
        }, 4000);

        // Pencarian tabel
        function searchTable() {
            let input = document.getElementById('searchInput');
            if (!input) return;
            let filter = input.value.toUpperCase().trim();
            let table = document.querySelector('#dataTable') || document.querySelector('table');
            if (!table) return;
            let tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                let tds = tr[i].getElementsByTagName('td');
                let rowText = '';
                for (let j = 0; j < tds.length; j++) {
                    rowText += (tds[j].textContent || tds[j].innerText) + ' ';
                }
                tr[i].style.display = rowText.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    </script>
    @stack('scripts')
    {{-- ════ IMPORT EXCEL AJAX HANDLER ════ --}}
    <script>
        (function () {
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form[action*="/import"]').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const submitBtn = form.querySelector('[type="submit"]');
                        const originalHtml = submitBtn ? submitBtn.innerHTML : '';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Importing...';
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
                                        const msgs = data.errors
                                            ? Object.values(data.errors).flat()
                                            : ['Validation failed. Please ensure the uploaded file is correct.'];
                                        throw { title: 'Validation Failed', errors: msgs };
                                    });
                                }
                                return res.json();
                            })
                            .then(function (data) {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalHtml;
                                }

                                const modal = form.closest('.modal');
                                if (modal) {
                                    const bsModal = bootstrap.Modal.getInstance(modal);
                                    if (bsModal) bsModal.hide();
                                }

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.title || 'Import Successful',
                                        html: '<p><strong>' + (data.success_count || 0) + '</strong> records imported successfully.</p>',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#0056b3',
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
                                        listHtml += '<p class="mt-2 mb-0"><strong>...and ' + rest + ' more errors</strong></p>';
                                    }

                                    const successInfo = (data.success_count > 0)
                                        ? '<p class="mb-2 text-success"><strong>' + data.success_count + '</strong> rows imported successfully before errors were found.</p>'
                                        : '';

                                    Swal.fire({
                                        icon: 'error',
                                        title: data.title || 'Import Failed',
                                        html: successInfo +
                                            '<p class="mb-2">There are <strong>' + errors.length + '</strong> problematic rows:</p>' +
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

                                const title = (err && err.title) ? err.title : 'An Error Occurred';
                                const errors = (err && err.errors) ? err.errors : ['An unexpected error occurred. Please try again.'];

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