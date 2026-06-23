# 🏥 Kimia Farma GA — Laravel 13 System

Aplikasi **General Affair (GA)** untuk Kimia Farma Apotek dengan stack **Laravel** (PHP) dan **Vite** (asset front-end). Sistem mencakup autentikasi, pengelolaan aset, monitoring AC, operasional driver, serta pengaturan gambar sistem.

---

## 📋 Daftar Fitur

### 🔐 Autentikasi
- Login menggunakan **username atau email**
- Reset password menggunakan **OTP via email** (6 digit, berlaku ±15 menit)
- Manajemen profil & ganti password
- Middleware session-based (tanpa Laravel Auth bawaan)
- Role: `admin`, `staff`, `manager`

### 🏢 General Affair (GA)
- Dashboard dengan grafik (Chart.js)
  - Tren/rekap aset dan transaksi (sesuai modul)
- **Aset Kendaraan** — CRUD, filter, export Excel, import Excel (template tersedia)
- **Aset Tanah & Bangunan** — CRUD, filter, export Excel, import Excel
- **Sentralisasi ATK** — CRUD, barang keluar, riwayat transaksi, export Excel + PDF stok
- **Biaya Umum** — CRUD, filter per bulan/tahun

### ❄️ AC / Monitoring Maintenance
- Dashboard monitoring status (Normal / Wajib Service)
- Notifikasi untuk item yang sudah >3 bulan tidak dirawat
- Export Excel dan generate PDF
  - PDF semua / terpilih / rekap bulanan
- Import data via Excel

### 🚗 Driver Operasional
- Dashboard jadwal hari ini & status supir
- **Jadwal** — buat, edit, tandai selesai (pindah ke riwayat), deteksi konflik jadwal
- **Riwayat** perjalanan + export PDF
- CRUD data **mobil**
- CRUD data **supir** (aktif/idle/offline)

### ⚙️ Pengaturan (Admin)
- Kelola gambar sistem (logo, background, favicon)
- Mode konfigurasi: URL eksternal atau upload file

---

## 🧰 Prasyarat
- PHP **8.2+**
- Composer
- MySQL **8.0+** / MariaDB **10.6+**
- Node.js (untuk build front-end asset via Vite)

---

## 🚀 Cara Menjalankan Project (Windows + XAMPP)

> Folder project berada di: `c:/xampp/htdocs/kimiafarmalaravel13`

### 1) Install dependencies
Buka **CMD/Terminal** lalu jalankan:

```bat
cd c:/xampp/htdocs/kimiafarmalaravel13
composer install
npm install
```

### 2) Siapkan file `.env` dan generate key
Jika file `.env` belum ada:

```bat
copy .env.example .env
php artisan key:generate
```

### 3) Konfigurasi database
Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kimiafarma
DB_USERNAME=root
DB_PASSWORD=PASSWORD_KAMU
```

Pastikan database **`db_kimiafarma`** sudah dibuat.

### 4) Import database (jika diperlukan)
Repo menyediakan file: `db_kimiafarma.sql`
- Import via **phpMyAdmin**
- Tujuannya ke database `db_kimiafarma`

### 5) Link storage (wajib untuk file upload)
```bat
php artisan storage:link
```

### 6) Migrasi & seeding
Jika ingin membuat struktur tabel otomatis:

```bat
php artisan migrate --force
php artisan db:seed
```

> Jika database sudah di-import dari `db_kimiafarma.sql`, migrasi bisa tetap dijalankan sesuai kebutuhan.

### 7) Build asset front-end
```bat
npm run build
```

### 8) Jalankan server
```bat
php artisan serve
```

Buka di browser:
- Biasanya: **http://127.0.0.1:8000**

---

## 🔑 Akun Default

Jika seed sudah dijalankan, contoh akun default:
- Username: `adminkfa`
- Email: `kimiafarma@gmail.com`
- Password: `admin123`
- Role: `admin`

> Disarankan ganti password setelah login.

---

## 🗂️ Struktur Direktori (Gambaran)

- `app/Http/Controllers/` : seluruh controller per modul (Auth, GA, AC Monitoring, Driver, Admin)
- `app/Http/Middleware/` : middleware session & permission/role
- `resources/views/` : blade template per modul
- `routes/web.php` : routing web
- `database/migrations/` : skema tabel
- `database/seeders/` : data awal
- `resources/js/` dan `resources/css/` : asset untuk Vite

---

## 📦 Dependencies (Ringkas)
- `laravel/framework`
- `barryvdh/laravel-dompdf` untuk generate PDF
- `maatwebsite/excel` / PhpSpreadsheet untuk import/export Excel
- `spatie/laravel-permission` untuk permission & role

---

## 🧪 Troubleshooting Umum

### 1) Error tidak bisa konek database
- Pastikan `.env` benar (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- Pastikan database `db_kimiafarma` ada di MySQL

### 2) File upload tidak tampil
- Jalankan: `php artisan storage:link`

### 3) Error build asset / tidak tampil styling
- Jalankan: `npm install` lalu `npm run build`

### 4) Permission `storage/` / `bootstrap/cache/`
- Pada umumnya di Windows tidak terlalu rumit, tapi jika error muncul, pastikan folder tersebut bisa ditulis oleh user Windows.

---

## 📌 Catatan
Project ini berjalan sebagai aplikasi web full stack: backend Laravel + front-end asset yang dibangun menggunakan Vite.

---

## 📞 Support
Hubungi tim IT General Affair Kimia Farma Apotek untuk bantuan instalasi, konfigurasi SMTP OTP, dan akses akun.

