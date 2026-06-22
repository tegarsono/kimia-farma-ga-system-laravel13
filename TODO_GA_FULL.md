# TODO GA Full (kimialaravel13)

## Port controller & view dari kimiafarmalaravel

- [ ]   1. Port controller:
    - [ ] GA/AtkController.php
    - [ ] GA/BiayaController.php
    - [ ] GA/DIRController.php
    - [ ] GA/KendaraanController.php
    - [ ] GA/TanahBangunanController.php
- [ ]   2. Buat/port view:
    - [ ] resources/views/ga/atk/\* (index/create/edit/barang_keluar/riwayat + pdf jika dipakai)
    - [ ] resources/views/ga/biaya/index.blade.php
    - [ ] resources/views/ga/dir/\*
    - [ ] resources/views/ga/kendaraan/\*
    - [ ] resources/views/ga/tanah_bangunan/\*
- [ ]   3. Update routes/web.php:
    - [ ] prefix `ga` dengan subroute `kendaraan`, `tanah-bangunan`, `atk`, `biaya`, `dir`
    - [ ] pastikan route names `ga.*` sama seperti referensi
- [ ]   4. Update sidebar layout `resources/views/layouts/app.blade.php`:
    - [ ] Pastikan menu GA link ke route name yang sesuai (`ga.atk.index`, dll)

## Testing

- [ ]   5. Jalankan Laravel dan test:
    - [ ] /ga (dashboard)
    - [ ] /ga/kendaraan, /ga/tanah-bangunan, /ga/atk, /ga/biaya, /ga/dir
    - [ ] tambah/edit/hapus
    - [ ] export/import jika ada
