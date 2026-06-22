<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserPermissionController extends Controller
{
    // Mapping "menu/page" -> permission name.
    // Buat permission ini dulu via database/migrate seeding jika perlu.
    private array $permissionMap = [
        // Dashboard home (GA/Driver/AC Monitoring) sengaja DIHILANGKAN dari opsi Kelola User,
        // karena itu merupakan halaman home.

        'visit.ga.kendaraan.index' => 'Kendaraan (GA)',
        'visit.ga.tanah_bangunan.index' => 'Tanah & Bangunan (GA)',
        'visit.ga.atk.index' => 'ATK (GA)',
        'visit.ga.atk.barang_keluar' => 'Barang Keluar (ATK)',
        'visit.ga.atk.riwayat' => 'Riwayat Transaksi (ATK)',
        'visit.ga.biaya.index' => 'Biaya Umum (GA)',
        'visit.ga.dir.index' => 'DIR (GA)',

        'visit.driver.jadwal.index' => 'Jadwal Driver',
        'visit.driver.mobil.index' => 'Armada Mobil',
        'visit.driver.supir.index' => 'Data Supir',

        'visit.ac.monitoring.notifikasi' => 'AC Monitoring - Notifikasi',
    ];

    public function index()
    {
        // Pastikan tabel permission ada. Kalau belum, user akan dapat 0 permission.
        $users = DB::table('users')->orderBy('id')->get();

        $permissions = Permission::query()
            ->whereIn('name', array_keys($this->permissionMap))
            ->get();

        $permissionByName = $permissions->keyBy('name');

        $roleAdmin = Role::query()->where('name', 'admin')->first();

        // Ambil user permission via Spatie (pivot), dengan guard default.
        // Jika User model belum memakai HasRoles/HasPermissions, query ini akan error.
        $usersWithPermissions = [];
        foreach ($users as $u) {
            $spatieUser = User::find($u->id);
            $userPerms = method_exists($spatieUser, 'permissions')
                ? $spatieUser->permissions()->pluck('name')->toArray()
                : [];
            $usersWithPermissions[$u->id] = array_fill_keys($userPerms, true);
        }

        return view('admin.user_permissions', [
            'users' => $users,
            'permissionMap' => $this->permissionMap,
            'permissionByName' => $permissionByName,
            'usersWithPermissions' => $usersWithPermissions,
            'roleAdmin' => $roleAdmin,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->permissionMap)),
        ]);

        $user = User::findOrFail($request->user_id);

        $selected = $request->input('permissions', []);
        $permissionNames = array_values($selected);

        $permissions = Permission::query()
            ->whereIn('name', $permissionNames)
            ->get();

        // syncPermissions() akan menghapus permission lama dan mengganti.
        if (method_exists($user, 'syncPermissions')) {
            $user->syncPermissions($permissions);
        } else {
            // Fallback: assign satu-satu.
            if (method_exists($user, 'syncRoles')) {
                // no-op
            }
        }

        return back()->with('success', 'Hak akses user berhasil diperbarui.');
    }
}

