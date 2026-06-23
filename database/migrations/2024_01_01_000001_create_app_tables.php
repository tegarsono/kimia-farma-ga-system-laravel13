<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── image_settings ───────────────────────────────────────────────────
        Schema::create('image_settings', function (Blueprint $table) {
            $table->id();
            $table->string('image_key', 100)->unique();
            $table->text('image_value');
            $table->enum('image_type', ['url', 'upload'])->default('url');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->datetime('updated_at')->nullable();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // ─── kendaraan_aset ───────────────────────────────────────────────────
        Schema::create('kendaraan_aset', function (Blueprint $table) {
            $table->id();
            $table->string('no_posisi', 50);
            $table->string('branch_manager', 100);
            $table->string('jenis_kendaraan', 50);
            $table->string('merk', 50);
            $table->string('type', 50)->nullable();
            $table->string('warna', 30)->nullable();
            $table->year('tahun_pembuatan')->nullable();
            $table->string('no_mesin', 100)->nullable()->unique();
            $table->string('no_rangka', 100)->nullable()->unique();
            $table->string('no_bpkb', 100)->nullable()->unique();
            $table->string('no_polisi', 20)->nullable();
            $table->date('masa_berakhir_1th')->nullable();
            $table->date('masa_berakhir_5th')->nullable();
            $table->string('status', 30);
            $table->year('tahun_perolehan')->nullable();
            $table->bigInteger('harga_perolehan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── tanah_bangunan_aset ──────────────────────────────────────────────
        Schema::create('tanah_bangunan_aset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sap', 50)->nullable()->unique();
            $table->string('no_asset_tanah', 100)->unique();
            $table->string('branch_manager', 100);
            $table->string('digunakan_sebagai', 100)->nullable();
            $table->string('penggunaan', 100)->nullable();
            $table->string('no_posisi_gedung', 50)->nullable();
            $table->text('alamat');
            $table->decimal('luas_tanah', 10, 2);
            $table->decimal('luas_bangunan', 10, 2)->nullable();
            $table->year('tahun_perolehan')->nullable();
            $table->string('nomor_sertifikat_baru', 100)->unique();
            $table->date('masa_berlaku')->nullable();
            $table->string('status', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── atk_katalog ──────────────────────────────────────────────────────
        Schema::create('atk_katalog', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 100);
            $table->string('kategori', 100);
            $table->string('nama_barang', 255);
            $table->string('satuan', 50);
            $table->integer('harga');
            $table->text('spesifikasi')->nullable();
            $table->enum('status_barang', ['Masuk', 'Keluar']);
            $table->integer('jumlah');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        // ─── atk_transaksi ────────────────────────────────────────────────────
        Schema::create('atk_transaksi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jenis', 50);
            $table->integer('jumlah');
            $table->longText('keterangan');
            $table->unsignedBigInteger('id_barang')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('id_barang');
        });

        // ─── biaya_umum ───────────────────────────────────────────────────────
        Schema::create('biaya_umum', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kategori', 100);
            $table->string('deskripsi', 255);
            $table->bigInteger('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── tb_monitoring ────────────────────────────────────────────────────
        Schema::create('tb_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ga', 50)->nullable();
            $table->string('lokasi', 100);
            $table->date('tgl_perawatan_terakhir');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Normal', 'Wajib Service']);
            $table->text('nama_barang');
            $table->text('jenis_barang');
        });

        // ─── mobil ────────────────────────────────────────────────────────────
        Schema::create('mobil', function (Blueprint $table) {
            $table->increments('id_mobil');
            $table->string('merk', 100);
            $table->string('plat_nomor', 20)->unique();
            $table->string('tipe_mobil', 50);
        });

        // ─── supir ────────────────────────────────────────────────────────────
        Schema::create('supir', function (Blueprint $table) {
            $table->increments('id_supir');
            $table->string('nama_supir', 100);
            $table->enum('status', ['aktif', 'idle', 'offline'])->default('offline');
            $table->string('nip', 50);
        });

        // ─── jadwal ───────────────────────────────────────────────────────────
        Schema::create('jadwal', function (Blueprint $table) {
            $table->increments('id_jadwal');
            $table->unsignedInteger('id_supir');
            $table->unsignedInteger('id_mobil');
            $table->date('tanggal_tugas');
            $table->timestamp('created_at')->useCurrent();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('penumpang', 200);
            $table->string('tujuan', 200);
            $table->longText('keperluan');

            $table->foreign('id_supir')->references('id_supir')->on('supir')->onDelete('cascade');
            $table->foreign('id_mobil')->references('id_mobil')->on('mobil')->onDelete('cascade');
        });

        // ─── riwayat_jadwal ───────────────────────────────────────────────────
        Schema::create('riwayat_jadwal', function (Blueprint $table) {
            $table->integer('id_jadwal')->primary();
            $table->unsignedInteger('id_supir');
            $table->unsignedInteger('id_mobil');
            $table->date('tanggal_tugas');
            $table->timestamp('created_at')->useCurrent();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('penumpang', 200);
            $table->string('tujuan', 200);
            $table->longText('keperluan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jadwal');
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('supir');
        Schema::dropIfExists('mobil');
        Schema::dropIfExists('tb_monitoring');
        Schema::dropIfExists('biaya_umum');
        Schema::dropIfExists('atk_transaksi');
        Schema::dropIfExists('atk_katalog');
        Schema::dropIfExists('tanah_bangunan_aset');
        Schema::dropIfExists('kendaraan_aset');
        Schema::dropIfExists('image_settings');
    }
};
