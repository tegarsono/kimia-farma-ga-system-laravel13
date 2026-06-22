<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kimia Farma Apotek Digital Hub</title>
    <link rel="icon" href="{{ asset('img/kf.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7f9;
            color: #1a202c
        }

        header {
            background: #fff;
            box-shadow: 0 1px 8px rgba(0, 0, 0, .08);
            position: sticky;
            top: 0;
            z-index: 100
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: .9rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .75rem
        }

        .brand img {
            height: 40px;
            width: auto
        }

        .brand h1 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1a202c
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap
        }

        .header-actions span {
            font-size: .83rem;
            color: #718096
        }

        .btn-hdr {
            padding: .45rem .9rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: .2s
        }

        .btn-gray {
            background: #edf2f7;
            color: #4a5568
        }

        .btn-gray:hover {
            background: #e2e8f0
        }

        .btn-red {
            background: #fed7d7;
            color: #c53030
        }

        .btn-red:hover {
            background: #feb2b2
        }

        .btn-indigo {
            background: #ebf4ff;
            color: #2b6cb0
        }

        .btn-indigo:hover {
            background: #bee3f8
        }

        form.logout {
            display: inline
        }

        /* Hero */
        .hero {
            position: relative;
            padding: 5rem 1.5rem;
            overflow: hidden;
            text-align: center
        }

        .hero-bg {
            position: absolute;
            inset: 0
        }

        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .6)
        }

        .hero-content {
            position: relative;
            z-index: 1;
            color: #fff;
            max-width: 700px;
            margin: 0 auto
        }

        .hero-content h2 {
            font-size: 2.4rem;
            font-weight: 800;
            margin-bottom: .75rem;
            line-height: 1.25
        }

        .hero-content p {
            font-size: 1.05rem;
            opacity: .9
        }

        /* Cards */
        .section {
            padding: 4rem 1.5rem
        }

        .section-inner {
            max-width: 1200px;
            margin: 0 auto
        }

        .section h3 {
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: .5rem
        }

        .section p.sec-sub {
            text-align: center;
            color: #718096;
            margin-bottom: 2.5rem
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem
        }

        .app-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
            border-top: 4px solid;
            text-decoration: none;
            color: inherit;
            transition: transform .25s, box-shadow .25s;
            display: block
        }

        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .12)
        }

        .app-card.green {
            border-color: #00a859
        }

        .app-card.blue {
            border-color: #0070c0
        }

        .app-card.yellow {
            border-color: #d97706
        }

        .card-icon {
            font-size: 2.8rem;
            margin-bottom: 1rem
        }

        .app-card.green .card-icon {
            color: #00a859
        }

        .app-card.blue .card-icon {
            color: #0070c0
        }

        .app-card.yellow .card-icon {
            color: #d97706
        }

        .app-card h4 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: .6rem;
            color: #1a202c
        }

        .app-card p {
            font-size: .84rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 1rem
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .85rem;
            font-weight: 700
        }

        .app-card.green .card-link {
            color: #00a859
        }

        .app-card.blue .card-link {
            color: #0070c0
        }

        .app-card.yellow .card-link {
            color: #d97706
        }

        footer {
            background: #1a202c;
            color: rgba(255, 255, 255, .65);
            text-align: center;
            padding: 1.5rem;
            font-size: .82rem
        }

        @media(max-width:640px) {
            .hero-content h2 {
                font-size: 1.6rem
            }

            .header-actions span {
                display: none
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-inner">
            <div class="brand">
                <img src="{{ $logoMain }}" alt="KFA">
                <h1>KFA General Affair</h1>
            </div>
            <div class="header-actions">
                <span>Selamat datang, <strong>{{ session('full_name', session('username')) }}</strong></span>
                @if(session('role') === 'admin')
                    <a href="{{ route('admin.index') }}" class="btn-hdr btn-indigo"><i
                            class="fas fa-user-shield me-1"></i>Akses</a>

                    <a href="{{ route('admin.users.index') }}" class="btn-hdr btn-indigo"><i
                            class="fas fa-user-plus me-1"></i>Kelola User</a>

                    <a href="{{ route('settings.index') }}" class="btn-hdr btn-indigo"><i
                            class="fas fa-images me-1"></i>Gambar</a>
                @endif
                <a href="{{ route('profile.index') }}" class="btn-hdr btn-gray"><i
                        class="fas fa-user-circle me-1"></i>Profil</a>

                <form class="logout" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-hdr btn-red"><i
                            class="fas fa-sign-out-alt me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-bg"><img src="{{ $bgHero }}" alt="Hero"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2>Akses Dashboard General Affair KFA</h2>
            <p>Akses cepat dan aman ke semua sistem manajemen operasional dan layanan General Affair.</p>
        </div>
    </section>

    <section class="section">
        <div class="section-inner">
            <h3>Pilih Sistem Anda</h3>
            <p class="sec-sub">Tiga pilar utama untuk efisiensi dan layanan prima.</p>
            <div class="cards-grid">

                <a href="{{ route('ga.home') }}" class="app-card green">
                    <div class="card-icon"><i class="fas fa-warehouse"></i></div>
                    <h4>Dashboard General Affair Manajemen Aset</h4>
                    <p>Kelola aset kendaraan, tanah &amp; bangunan, sentralisasi ATK, dan biaya umum secara real-time.
                        Memastikan ketersediaan dan akurasi data.</p>
                    <span class="card-link">Masuk ke Dashboard Manajemen Aset <i
                            class="fas fa-arrow-right fa-xs"></i></span>
                </a>

                <a href="{{ route('ac.index') }}" class="app-card blue">
                    <div class="card-icon"><i class="fas fa-check-square"></i></div>
                    <h4>Dashboard Monitoring Maintenance GA</h4>
                    <p>Platform untuk monitoring aset maintenance (AC, Genset, dll), status perawatan, notifikasi jadwal
                        servis, dan riwayat perawatan yang terintegrasi.</p>
                    <span class="card-link">Masuk ke Dashboard Maintenance <i
                            class="fas fa-arrow-right fa-xs"></i></span>
                </a>

                <a href="{{ route('driver.home') }}" class="app-card yellow">
                    <div class="card-icon"><i class="fas fa-car"></i></div>
                    <h4>Dashboard Monitoring Driver Operasional KFA</h4>
                    <p>Akses pemantauan aktivitas dan penugasan driver operasional secara real-time — jadwal tugas,
                        status perjalanan, data supir &amp; armada kendaraan.</p>
                    <span class="card-link">Masuk ke Dashboard Monitoring <i
                            class="fas fa-arrow-right fa-xs"></i></span>
                </a>

            </div>
        </div>
    </section>

    <footer>&copy; {{ date('Y') }} Kimia Farma Apotek. All Rights Reserved. &mdash; Inovasi Digital untuk Pelayanan
        Terbaik.</footer>
</body>

</html>