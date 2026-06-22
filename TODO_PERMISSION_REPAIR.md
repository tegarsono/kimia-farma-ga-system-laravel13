# TODO_PERMISSION_REPAIR.md

## Masalah

`routes/web.php` sudah diedit parsial sehingga strukturnya rusak (group/middleware dibuka-tutup tidak konsisten). Ini menyebabkan syntax error.

## Tujuan repair

- Balik `routes/web.php` ke struktur yang valid
- Terapkan permission middleware dengan cara rapi per module/subroute

## Langkah repair (direncanakan)

1. Replace `routes/web.php` dengan versi utuh yang benar (berbasis versi awal + penambahan permission):
    - GA group pakai middleware `visit.ga.home`
    - sub-group:
        - kendaraan -> `visit.ga.kendaraan.index`
        - tanah-bangunan -> `visit.ga.tanah_bangunan.index`
        - atk -> `visit.ga.atk.index`, plus barang-keluar (`visit.ga.atk.barang_keluar`) & riwayat (`visit.ga.atk.riwayat`)
        - biaya -> `visit.ga.biaya.index`
        - dir -> `visit.ga.dir.index`
    - driver group pakai `visit.driver.home`
        - jadwal -> `visit.driver.jadwal.index` (termasuk riwayat/pdf)
        - mobil -> `visit.driver.mobil.index`
        - supir -> `visit.driver.supir.index`
    - ac-monitoring group pakai `visit.ac.monitoring.index`
        - notifikasi -> `visit.ac.monitoring.notifikasi`
2. Pastikan semua kurung `group(function(){ ... });` benar.
3. Cek syntax dengan `php -l routes/web.php`.

## Status

- [ ] Replace routes/web.php
- [ ] php -l routes/web.php
