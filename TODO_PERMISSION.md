# TODO_PERMISSION.md

## Tujuan

Mengunci akses halaman module GA/Driver/AC sesuai permission yang di-set oleh role admin untuk role user.

## Langkah yang dilakukan

- [x] Membuat middleware `PermissionMiddleware` dan `PermissionOrRoleMiddleware`.

## Langkah berikutnya

- [ ] Update `app/Http/Kernel.php` untuk register middleware (jika diperlukan).
- [ ] Tambahkan middleware `permission`/`permission_or_role` ke route-route GA/Driver/AC di `routes/web.php`.
- [ ] Update menu/URL di sidebar agar tidak menampilkan link yang tidak diizinkan (opsional, tapi disarankan).
- [ ] Jalankan smoke test:
    - Login user tanpa permission seharusnya dapat 403/redirect.
    - User dengan permission tertentu bisa membuka halaman.
    - Admin tetap bisa akses semua.
