<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\GA\HomeController;
use App\Http\Controllers\GA\KendaraanController;
use App\Http\Controllers\GA\TanahBangunanController;
use App\Http\Controllers\GA\AtkController;
use App\Http\Controllers\GA\BiayaController;
use App\Http\Controllers\GA\DIRController;
use App\Http\Controllers\Driver\DashboardDriverController;
use App\Http\Controllers\Driver\JadwalController;
use App\Http\Controllers\Driver\MobilController;
use App\Http\Controllers\Driver\SupirController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/lupa-password', [PasswordResetController::class, 'showForm'])->name('password.request');
    Route::post('/lupa-password/kirim-otp', [PasswordResetController::class, 'sendOtp'])->name('password.sendOtp');
    Route::get('/lupa-password/verifikasi', [PasswordResetController::class, 'showVerify'])->name('password.verify');
    Route::post('/lupa-password/verifikasi', [PasswordResetController::class, 'verifyOtp']);
    Route::get('/lupa-password/reset', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/lupa-password/reset', [PasswordResetController::class, 'resetPassword']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth.session')->group(function () {

    // ─── Admin User Permissions (Spatie) ─────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Halaman admin: pengaturan permission user
        Route::get('/user-permissions', [\App\Http\Controllers\Admin\UserPermissionController::class, 'index'])->name('index');
        Route::post('/user-permissions', [\App\Http\Controllers\Admin\UserPermissionController::class, 'update'])->name('update');

        // CRUD User + permission
        Route::get('/users', [\App\Http\Controllers\Admin\UserCrudController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserCrudController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserCrudController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserCrudController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserCrudController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserCrudController::class, 'destroy'])->name('users.destroy');
    });

    // ─── Beranda / Hub ───────────────────────────────────────────────────────
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    /*
    |----------------------------------------------------------------------
    | General Affair Module  (/ga/*)
    |----------------------------------------------------------------------
    */
Route::prefix('ga')->name('ga.')->middleware('permission_or_role:visit.ga.home')->group(function () {

        // Permintaan permission untuk halaman GA home ditangani oleh middleware grup di atas.


        // Dashboard / Home
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Kendaraan Aset
        Route::prefix('kendaraan')->name('kendaraan.')->middleware('permission_or_role:visit.ga.kendaraan.index')->group(function () {
            Route::get('/', [KendaraanController::class, 'index'])->name('index');
            Route::get('/tambah', [KendaraanController::class, 'create'])->name('create');
            Route::post('/tambah', [KendaraanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [KendaraanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [KendaraanController::class, 'update'])->name('update');
            Route::delete('/{id}', [KendaraanController::class, 'destroy'])->name('destroy');
            Route::get('/export-excel', [KendaraanController::class, 'exportExcel'])->name('exportExcel');
            Route::get('/download-template', [KendaraanController::class, 'downloadTemplate'])->name('downloadTemplate');
            Route::post('/import', [KendaraanController::class, 'import'])->name('import');
        });

        // Tanah & Bangunan Aset
        Route::prefix('tanah-bangunan')->name('tanah_bangunan.')->middleware('permission_or_role:visit.ga.tanah_bangunan.index')->group(function () {
            Route::get('/', [TanahBangunanController::class, 'index'])->name('index');
            Route::get('/tambah', [TanahBangunanController::class, 'create'])->name('create');
            Route::post('/tambah', [TanahBangunanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TanahBangunanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TanahBangunanController::class, 'update'])->name('update');
            Route::delete('/{id}', [TanahBangunanController::class, 'destroy'])->name('destroy');
            Route::get('/export-excel', [TanahBangunanController::class, 'exportExcel'])->name('exportExcel');
            Route::get('/download-template', [TanahBangunanController::class, 'downloadTemplate'])->name('downloadTemplate');
            Route::post('/import', [TanahBangunanController::class, 'import'])->name('import');
        });

        // ATK (Alat Tulis Kantor)
        Route::prefix('atk')->name('atk.')->middleware('permission_or_role:visit.ga.atk.index')->group(function () {
            Route::get('/', [AtkController::class, 'index'])->name('index');
            Route::get('/tambah', [AtkController::class, 'create'])->name('create');
            Route::post('/tambah', [AtkController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AtkController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AtkController::class, 'update'])->name('update');
            Route::delete('/{id}', [AtkController::class, 'destroy'])->name('destroy');

            // Override permission
            Route::get('/barang-keluar', [AtkController::class, 'barangKeluarForm'])->middleware('permission_or_role:visit.ga.atk.barang_keluar')->name('barangKeluarForm');
            Route::post('/barang-keluar', [AtkController::class, 'barangKeluarStore'])->middleware('permission_or_role:visit.ga.atk.barang_keluar')->name('barangKeluarStore');

            Route::get('/{id}/riwayat-item', [AtkController::class, 'riwayatItem'])->name('riwayatItem');
            Route::get('/riwayat', [AtkController::class, 'riwayat'])->middleware('permission_or_role:visit.ga.atk.riwayat')->name('riwayat');

            Route::delete('/transaksi/{id}', [AtkController::class, 'deleteTransaksi'])->name('deleteTransaksi');
            Route::get('/export-excel', [AtkController::class, 'exportExcel'])->name('exportExcel');
            Route::get('/export-pdf-stok', [AtkController::class, 'exportPdfStok'])->name('exportPdfStok');
            Route::get('/download-template', [AtkController::class, 'downloadTemplate'])->name('downloadTemplate');
            Route::post('/import', [AtkController::class, 'import'])->name('import');
        });

        // Biaya Umum
        Route::prefix('biaya')->name('biaya.')->middleware('permission_or_role:visit.ga.biaya.index')->group(function () {
            Route::get('/', [BiayaController::class, 'index'])->name('index');
        });

        // DIR
        Route::prefix('dir')->name('dir.')->middleware('permission_or_role:visit.ga.dir.index')->group(function () {
            Route::get('/', [DIRController::class, 'index'])->name('index');
            Route::get('/tambah', [DIRController::class, 'create'])->name('create');
            Route::post('/tambah', [DIRController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DIRController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DIRController::class, 'update'])->name('update');
            Route::delete('/{id}', [DIRController::class, 'destroy'])->name('destroy');

            Route::get('/download-template', [DIRController::class, 'downloadTemplate'])->name('downloadTemplate');
            Route::post('/import', [DIRController::class, 'import'])->name('import');
            Route::get('/export-excel', [DIRController::class, 'exportExcel'])->name('exportExcel');

            // QR scan -> download PDF
            Route::get('/qr-pdf/{id}', [DIRController::class, 'qrPdf'])->name('qrPdf');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Driver Monitoring Module  (/driver/*)
    |----------------------------------------------------------------------
    */
    Route::prefix('driver')->name('driver.')->middleware('permission_or_role:visit.driver.home')->group(function () {
        Route::get('/', [DashboardDriverController::class, 'index'])->name('home');

        // Jadwal
        Route::prefix('jadwal')->name('jadwal.')->middleware('permission_or_role:visit.driver.jadwal.index')->group(function () {
            Route::get('/', [JadwalController::class, 'index'])->name('index');
            Route::get('/tambah', [JadwalController::class, 'create'])->name('create');
            Route::post('/tambah', [JadwalController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [JadwalController::class, 'edit'])->name('edit');
            Route::put('/{id}', [JadwalController::class, 'update'])->name('update');
            Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/selesai', [JadwalController::class, 'selesai'])->name('selesai');
            Route::get('/riwayat', [JadwalController::class, 'riwayat'])->name('riwayat');
            Route::get('/riwayat/pdf', [JadwalController::class, 'riwayatPdf'])->name('riwayatPdf');
        });

        // Mobil
        Route::prefix('mobil')->name('mobil.')->middleware('permission_or_role:visit.driver.mobil.index')->group(function () {
            Route::get('/', [MobilController::class, 'index'])->name('index');
            Route::get('/tambah', [MobilController::class, 'create'])->name('create');
            Route::post('/tambah', [MobilController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MobilController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MobilController::class, 'update'])->name('update');
            Route::delete('/{id}', [MobilController::class, 'destroy'])->name('destroy');
        });

        // Supir
        Route::prefix('supir')->name('supir.')->middleware('permission_or_role:visit.driver.supir.index')->group(function () {
            Route::get('/', [SupirController::class, 'index'])->name('index');
            Route::get('/tambah', [SupirController::class, 'create'])->name('create');
            Route::post('/tambah', [SupirController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [SupirController::class, 'edit'])->name('edit');
            Route::put('/{id}', [SupirController::class, 'update'])->name('update');
            Route::delete('/{id}', [SupirController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |----------------------------------------------------------------------
    | AC Monitoring Module  (/ac-monitoring/*)
    |----------------------------------------------------------------------
    */
    Route::prefix('ac-monitoring')->name('ac.')->middleware('permission_or_role:visit.ac.monitoring.index')->group(function () {
        Route::get('/', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'index'])->name('index');
        Route::get('/tambah', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'create'])->name('create');
        Route::post('/tambah', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'destroy'])->name('destroy');

        Route::get('/notifikasi', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'notifikasi'])->middleware('permission_or_role:visit.ac.monitoring.notifikasi')->name('notifikasi');
        Route::post('/notifikasi/bulk-service', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'bulkMarkServiced'])->name('notifikasi.bulkService');

        Route::get('/export-excel', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/generate-pdf', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'generatePdf'])->name('generatePdf');
        Route::post('/generate-pdf-selected', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'generatePdfSelected'])->name('generatePdfSelected');
        Route::get('/rekap-pdf', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'rekapPdf'])->name('rekapPdf');

        Route::get('/download-template', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'downloadTemplate'])->name('downloadTemplate');
        Route::post('/import', [\App\Http\Controllers\AcMonitoring\MonitoringController::class, 'import'])->name('import');
    });

    // ─── Profil ──────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-account', [ProfileController::class, 'updateAccount'])->name('profile.updateAccount');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // ─── Settings (Admin Only) ────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
        Route::post('/upload', [SettingsController::class, 'upload'])->name('upload');
        Route::get('/image/{key}', [SettingsController::class, 'getImage'])->name('image')->withoutMiddleware('role:admin');
    });
});

