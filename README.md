# 🏥 Kimia Farma GA — Laravel 13 System

Konversi lengkap dari **PHP Native** ke **Laravel 13** untuk sistem manajemen General Affair Kimia Farma Apotek.

---

## 📋 Fitur Lengkap

### 🔐 Autentikasi
- Login dengan username atau email
- Reset password via OTP ke email (6 digit, berlaku 15 menit)
- Manajemen profil & ganti password
- Middleware session-based (tanpa Laravel Auth bawaan)
- Role: `admin`, `user`

### 🏢 General Affair (GA)
- **Dashboard** dengan grafik Chart.js (kendaraan per branch, tanah & bangunan, tren ATK)
- **Aset Kendaraan** — CRUD, filter, export Excel, import Excel (template tersedia)
- **Aset Tanah & Bangunan** — CRUD, filter, export Excel, import Excel
- **Sentralisasi ATK** — CRUD, barang keluar, riwayat transaksi, export Excel + PDF stok
- **Biaya Umum** — CRUD, filter per bulan/tahun

### ❄️ AC / Monitoring Maintenance
- **Dashboard Monitoring** dengan statistik Normal / Wajib Service
- Notifikasi otomatis untuk item yang >3 bulan tidak dirawat
- Export Excel, generate PDF (semua / terpilih / rekap bulanan)
- Import data via Excel

### 🚗 Driver Operasional
- **Dashboard** jadwal hari ini, status supir real-time
- **Jadwal** — buat, edit, tandai selesai (pindah ke riwayat), konflik jadwal terdeteksi
- **Riwayat** perjalanan dengan export PDF
- **Data Armada Mobil** — CRUD
- **Data Supir** — CRUD, status aktif/idle/offline

### ⚙️ Pengaturan (Admin)
- Kelola semua gambar sistem (logo, background, favicon)
- Dua mode: URL eksternal atau upload file

---

## 🚀 Instalasi & Menjalankan Proyek

### 1. Prasyarat

Pastikan sistem Anda telah terinstall:

- **PHP** 8.2 atau lebih tinggi
- **Composer** (package manager PHP)
- **MySQL** 8.0+ / MariaDB 10.6+
- **Node.js** (untuk asset build dengan Vite)
- **Git** (opsional, untuk clone repository)

### 2. Clone & Install Dependencies

```bash
# Masuk ke direktori project
cd kimiafarmalaravel13

# Install dependencies PHP (Composer)
composer install

# Install dependencies Node.js (untuk frontend assets)
npm install
```

### 3. Konfigurasi Environment

```bash
# Salin file konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Buat symlink storage untuk file upload
php artisan storage:link
```

### 3.1. Publish Spatie Permission (Role & Permission)

Package `spatie/laravel-permission` memerlukan publish konfigurasi dan migration:

```bash
# Publish konfigurasi dan migration spatie permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 4. Konfigurasi Database

Edit file `.env` sesuai dengan konfigurasi database lokal Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kimiafarma
DB_USERNAME=root
DB_PASSWORD=your_password
```

Import database yang tersedia:

```bash
# Import file SQL yang sudah disediakan
mysql -u root -p db_kimiafarma < db_kimiafarma.sql
```

> Atau jika menggunakan phpMyAdmin, import file `db_kimiafarma.sql` melalui UI.

### 5. Konfigurasi Email (untuk OTP Reset Password)

Edit `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="KFA GA System"
```

> 💡 **Catatan:** Untuk Gmail, gunakan [App Password](https://support.google.com/accounts/answer/185833) jika 2FA aktif.

### 6. Jalankan Aplikasi

#### Opsi 1: Menggunakan Composer Script (Recommended)

Cara termudah — jalankan semua service sekaligus (server, queue, dan Vite) dengan satu perintah:

```bash
composer run dev
```

Perintah ini akan menjalankan secara bersamaan:
- **Laravel server** di `http://127.0.0.1:8000`
- **Queue listener** untuk proses background
- **Vite dev server** untuk compile asset frontend (Tailwind CSS, dll.)

#### Opsi 2: Manual (Multi Terminal)

```bash
# Terminal 1 — Jalankan server Laravel
php artisan serve

# Terminal 2 — Jalankan queue listener (untuk proses background seperti email OTP)
php artisan queue:listen --tries=1

# Terminal 3 — Compile asset frontend dengan Vite
npm run dev
```

Buka browser dan akses: **http://127.0.0.1:8000**

### 7. Build Asset untuk Production

```bash
npm run build
```

### 8. Perintah Composer yang Berguna

```bash
# Setup lengkap (install, migrate, build)
composer run setup

# Jalankan test
composer run test

# Clear semua cache
php artisan optimize:clear
```

---

## 🔑 Akun Default

| Field    | Value                    |
|----------|--------------------------|
| Username | `adminkfa`               |
| Email    | `kimiafarma@gmail.com`   |
| Password | `admin123`               |
| Role     | `admin`                  |

**User tambahan di database:**

| Username | Email                    | Password (hash) | Role  |
|----------|--------------------------|-----------------|-------|
| `rendi12`| `asdnasndsa@gmail.com`   | *(hash bcrypt)* | `user` |

> ⚠️ **Ganti password setelah login pertama!**

---

## 📁 Struktur Direktori

```
kimiafarmalaravel13/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           # Login, Profil, Reset Password
│   │   │   ├── GA/             # General Affair module
│   │   │   ├── AcMonitoring/   # Monitoring Maintenance
│   │   │   ├── Driver/         # Driver Operasional
│   │   │   ├── DashboardController.php
│   │   │   └── SettingsController.php
│   │   └── Middleware/
│   │       ├── AuthSessionMiddleware.php
│   │       └── PermissionOrRoleMiddleware.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/app.php           # Laravel 13 bootstrap
├── config/
│   ├── app.php
│   ├── database.php
│   └── session.php
├── database/
│   ├── migrations/             # Semua tabel dalam 1 file
│   └── seeders/DatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php   # Layout utama (sidebar + topbar)
│   │   ├── auth/                   # Login, OTP, Reset password
│   │   ├── dashboard.blade.php     # Hub utama
│   │   ├── ga/                     # General Affair views
│   │   ├── ac_monitoring/          # AC / Monitoring views
│   │   ├── driver/                 # Driver views
│   │   ├── profile/                # Profil user
│   │   └── settings/               # Pengaturan gambar
│   └── css/ / js/                  # Asset frontend
├── routes/
│   └── web.php                    # Semua route
├── composer.json
├── package.json
└── db_kimiafarma.sql              # File import database
```

---

## 📦 Dependencies Utama

### PHP (Composer) — Production

| Package | Version | Fungsi |
|---------|---------|--------|
| `laravel/framework` | ^13.0 | Core framework Laravel |
| `barryvdh/laravel-dompdf` | ^3.1 | Generate PDF |
| `phpoffice/phpspreadsheet` | ^5.8 | Import/Export Excel |
| `spatie/laravel-permission` | ^6.25 | Manajemen role & permission (menggunakan tabel `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) |
| `laravel/tinker` | ^3.0 | REPL interaktif untuk debugging |

### PHP (Composer) — Development

| Package | Version | Fungsi |
|---------|---------|--------|
| `laravel/boost` | ^2.2 | Laravel Boost MCP tools |
| `laravel/pint` | ^1.24 | Code formatter (PHP CS Fixer) |
| `laravel/pail` | ^1.2.2 | Log viewer |
| `phpunit/phpunit` | ^12.0 | Testing framework |
| `barryvdh/laravel-ide-helper` | ^3.7 | IDE helper untuk autocomplete |
| `fakerphp/faker` | ^1.23 | Generate fake data untuk testing |
| `laravel/sail` | ^1.41 | Docker development environment |
| `mockery/mockery` | ^1.6 | Mocking library untuk testing |
| `nunomaduro/collision` | ^8.6 | Error handler untuk console |

### Node.js (NPM)

| Package | Version | Fungsi |
|---------|---------|--------|
| `vite` | ^7.0.7 | Build tool & dev server |
| `laravel-vite-plugin` | ^2.0.0 | Integrasi Laravel + Vite |
| `tailwindcss` | ^4.0.0 | Utility-first CSS framework |
| `@tailwindcss/vite` | ^4.0.0 | Plugin Tailwind untuk Vite |
| `axios` | ^1.11.0 | HTTP client untuk AJAX requests |
| `concurrently` | ^9.0.1 | Jalankan multiple commands sekaligus |

---

## 🗄️ Tabel Database

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna sistem (role: `admin`/`user`) |
| `password_resets` | Token OTP reset password |
| `image_settings` | Konfigurasi gambar/logo sistem |
| `kendaraan_aset` | Aset kendaraan GA |
| `tanah_bangunan_aset` | Aset tanah dan bangunan |
| `atk_katalog` | Katalog barang ATK |
| `atk_transaksi` | Riwayat transaksi ATK keluar |
| `biaya_umum` | Pencatatan biaya operasional |
| `tb_monitoring` | Monitoring maintenance |
| `mobil` | Data armada kendaraan operasional |
| `supir` | Data supir/driver |
| `jadwal` | Jadwal tugas driver aktif |
| `riwayat_jadwal` | Riwayat perjalanan selesai |
| `permissions` | Daftar permission (spatie) — 14 permissions |
| `roles` | Daftar role (spatie) — role `admin` |
| `model_has_permissions` | Relasi permission-user (spatie) |
| `model_has_roles` | Relasi role-user (spatie) |
| `role_has_permissions` | Relasi role-permission (spatie) |

---

## 🔄 Perbedaan dari PHP Native

| Aspek           | PHP Native (Lama)              | Laravel 13 (Baru)                    |
|-----------------|-------------------------------|--------------------------------------|
| Routing         | File PHP langsung             | `routes/web.php` terpusat            |
| Auth            | `$_SESSION` manual            | Middleware + session Laravel         |
| DB Query        | MySQLi/PDO manual             | Laravel Query Builder (`DB::table`)  |
| Template        | PHP + HTML campur             | Blade Template Engine                |
| Validasi        | Manual `if-else`              | Laravel `$request->validate()`       |
| CSRF            | Token manual                  | `@csrf` directive otomatis           |
| Pagination      | Manual                        | Laravel Pagination bawaan            |
| Upload file     | `move_uploaded_file`          | `Storage::putFile` / `storeAs`       |
| Error handling  | `die()` / `exit()`            | Laravel Exception Handler            |
| PDF             | dompdf raw                    | `barryvdh/laravel-dompdf` facade     |
| Excel           | PHPExcel (deprecated)         | PhpSpreadsheet via helper controller |

---

## 🛠️ Perintah Artisan yang Berguna

```bash
# Jalankan server lokal
php artisan serve

# Clear semua cache (route, config, view, dll.)
php artisan optimize:clear

# Jalankan migration
php artisan migrate

# Jalankan seeder
php artisan db:seed

# Buat controller baru
php artisan make:controller NamaController

# Buat model baru
php artisan make:model NamaModel

# Buat middleware baru
php artisan make:middleware NamaMiddleware

# Buat seeder baru
php artisan make:seeder NamaSeeder

# Lihat semua route
php artisan route:list

# Lihat route dengan filter
php artisan route:list --path=api
php artisan route:list --method=GET

# Jalankan test
composer run test

# Format kode PHP dengan Pint
vendor/bin/pint
```

### Composer Scripts

```bash
# Setup lengkap (install dependency, migrate, build asset)
composer run setup

# Jalankan development server + queue + vite
composer run dev

# Jalankan test suite
composer run test
```

---

## 📞 Support

Hubungi tim IT General Affair Kimia Farma Apotek.