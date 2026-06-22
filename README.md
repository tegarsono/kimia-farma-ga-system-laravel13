# 🏥 Kimia Farma GA — Laravel 11 System

Konversi lengkap dari **PHP Native** ke **Laravel 11** untuk sistem manajemen General Affair Kimia Farma Apotek.

---

## 📋 Fitur Lengkap

### 🔐 Autentikasi
- Login dengan username atau email
- Reset password via OTP ke email (6 digit, berlaku 15 menit)
- Manajemen profil & ganti password
- Middleware session-based (tanpa Laravel Auth bawaan)
- Role: `admin`, `staff`, `manager`

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

## 🚀 Instalasi

### 1. Prasyarat
- PHP 8.2+
- Composer
- MySQL 8.0+ / MariaDB 10.6+
- Node.js (opsional, untuk asset build)

### 2. Clone & Install

```bash
# Masuk ke direktori project
cd kimiafarma-laravel

# Install dependencies
composer install

# Salin file konfigurasi
cp .env.example .env

# Generate app key
php artisan key:generate

# Link storage untuk file upload
php artisan storage:link
```

### 3. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kimiafarma
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Migrasi & Seeder

```bash
# Buat semua tabel
php artisan migrate

# Isi data awal (user admin + image settings + sample data)
php artisan db:seed
```

### 5. Konfigurasi Email (untuk OTP)

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

### 6. Jalankan Server

```bash
php artisan serve
```

Buka: **http://localhost:8000**

---

## 🔑 Akun Default

| Field    | Value                    |
|----------|--------------------------|
| Username | `adminkfa`               |
| Email    | `kimiafarma@gmail.com`   |
| Password | `admin123`               |
| Role     | `admin`                  |

> ⚠️ **Ganti password setelah login pertama!**

---

## 📁 Struktur Direktori

```
kimiafarma-laravel/
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
│   │       └── RoleMiddleware.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/app.php           # Laravel 11 bootstrap
├── config/
│   ├── app.php
│   ├── database.php
│   └── session.php
├── database/
│   ├── migrations/             # Semua tabel dalam 1 file
│   └── seeders/DatabaseSeeder.php
├── resources/views/
│   ├── layouts/app.blade.php   # Layout utama (sidebar + topbar)
│   ├── auth/                   # Login, OTP, Reset password
│   ├── dashboard.blade.php     # Hub utama
│   ├── ga/                     # General Affair views
│   ├── ac_monitoring/          # AC / Monitoring views
│   ├── driver/                 # Driver views
│   ├── profile/                # Profil user
│   └── settings/               # Pengaturan gambar
└── routes/web.php              # Semua route
```

---

## 📦 Dependencies Utama

```json
{
  "laravel/framework": "^11.0",
  "barryvdh/laravel-dompdf": "^2.2",
  "maatwebsite/excel": "^3.1",
  "phpoffice/phpspreadsheet": "^1.29"
}
```

---

## 🗄️ Tabel Database

| Tabel               | Fungsi                           |
|---------------------|----------------------------------|
| `users`             | Data pengguna sistem             |
| `password_resets`   | Token OTP reset password         |
| `image_settings`    | Konfigurasi gambar/logo sistem   |
| `kendaraan_aset`    | Aset kendaraan GA                |
| `tanah_bangunan_aset`| Aset tanah dan bangunan         |
| `atk_katalog`       | Katalog barang ATK               |
| `atk_transaksi`     | Riwayat transaksi ATK keluar     |
| `biaya_umum`        | Pencatatan biaya operasional     |
| `tb_monitoring`     | Monitoring maintenance           |
| `mobil`             | Data armada kendaraan operasional|
| `supir`             | Data supir/driver                |
| `jadwal`            | Jadwal tugas driver aktif        |
| `riwayat_jadwal`    | Riwayat perjalanan selesai       |

---

## 🔄 Perbedaan dari PHP Native

| Aspek           | PHP Native (Lama)              | Laravel 11 (Baru)                    |
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

## 📞 Support

Hubungi tim IT General Affair Kimia Farma Apotek.
