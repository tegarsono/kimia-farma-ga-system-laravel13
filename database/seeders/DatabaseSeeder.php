<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Users ────────────────────────────────────────────────────────────
        DB::table('users')->insertOrIgnore([
            'id'         => 1,
            'username'   => 'adminkfa',
            'email'      => 'kimiafarma@gmail.com',
            'password'   => Hash::make('admin123'),
            'full_name'  => 'Administrator GA',
            'role'       => 'admin',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─── Image Settings ───────────────────────────────────────────────────
        $imageSettings = [
            ['image_key' => 'logo_main',               'image_value' => 'img/kf.png', 'image_type' => 'upload'],
            ['image_key' => 'bg_login',                'image_value' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1920', 'image_type' => 'url'],
            ['image_key' => 'bg_lupa_password',        'image_value' => 'https://www.kimiafarmaapotek.co.id/wp-content/uploads/2021/10/CN-KFA-04-scaled.jpg', 'image_type' => 'url'],
            ['image_key' => 'logo_profile_topbar',     'image_value' => 'img/kf.png', 'image_type' => 'upload'],
            ['image_key' => 'foto_profil_default',     'image_value' => 'img/kf.png', 'image_type' => 'upload'],
            ['image_key' => 'logo_driver_navbar',      'image_value' => 'img/kf.png', 'image_type' => 'upload'],
            ['image_key' => 'logo_driver_footer',      'image_value' => 'img/kf.png', 'image_type' => 'upload'],
            ['image_key' => 'logo_acmonitoring_header','image_value' => 'img/kf.png', 'image_type' => 'upload'],
            ['image_key' => 'logo_tab',                'image_value' => 'img/kf.png', 'image_type' => 'upload'],
        ];

        foreach ($imageSettings as $s) {
            DB::table('image_settings')->insertOrIgnore(array_merge($s, [
                'updated_by' => 1,
                'updated_at' => now(),
            ]));
        }

        // ─── Mobil ────────────────────────────────────────────────────────────
        DB::table('mobil')->insertOrIgnore([
            ['id_mobil' => 1, 'merk' => 'Toyota Dyna Box', 'plat_nomor' => 'B 9012 KFA', 'tipe_mobil' => 'Mobil Box Pendingin'],
            ['id_mobil' => 2, 'merk' => 'Isuzu Elf',       'plat_nomor' => 'B 4432 KFB', 'tipe_mobil' => 'Truck Engkel'],
        ]);

        // ─── Supir ────────────────────────────────────────────────────────────
        DB::table('supir')->insertOrIgnore([
            ['id_supir' => 1, 'nama_supir' => 'Ahmad Subarjo', 'status' => 'idle', 'nip' => '1001'],
            ['id_supir' => 2, 'nama_supir' => 'Siti Aminah',   'status' => 'idle', 'nip' => '1002'],
        ]);
    }
}
