<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class UserCrudController extends Controller
{
    /**
     * Mapping permission "name" yang disediakan admin (konsisten dengan UserPermissionController).
     */
    private array $permissionMap = [
        // Mapping untuk opsi checkbox permission user.
        // Dashboard home (GA/Driver/AC Monitoring) sengaja DIHILANGKAN,
        // karena itu halaman home.

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
        // Tampilkan hanya user dengan role "user" (bukan admin)
        $users = User::query()
            ->where('role', 'user')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $permissions = $this->getPermissions();

        return view('admin.users.create', [
            'permissions' => $permissions,
            'permissionMap' => $this->permissionMap,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Kaidah sederhana keamanan password:
                // - minimal 1 huruf kapital
                // - minimal 1 angka
                // - minimal 1 karakter non-alfanumerik (opsional namun disarankan)
                'regex:/^(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->permissionMap)),
        ]);

        $user = User::create([
            // Sesuaikan dengan kolom tabel: full_name, username, email
            'full_name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        $permissionNames = array_values($request->input('permissions', []));
        $permissions = Permission::query()->whereIn('name', $permissionNames)->get();

        if (method_exists($user, 'syncPermissions')) {
            $user->syncPermissions($permissions);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $permissions = $this->getPermissions();

        $userPermissionNames = method_exists($user, 'permissions')
            ? $user->permissions()->pluck('name')->toArray()
            : [];

        return view('admin.users.edit', [
            'user' => $user,
            'permissions' => $permissions,
            'permissionMap' => $this->permissionMap,
            'userPermissionNames' => $userPermissionNames,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->permissionMap)),
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
        ]);

        $permissionNames = array_values($request->input('permissions', []));
        $permissions = Permission::query()->whereIn('name', $permissionNames)->get();

        if (method_exists($user, 'syncPermissions')) {
            $user->syncPermissions($permissions);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    private function getPermissions()
    {
        $permissionNames = array_keys($this->permissionMap);

        return Permission::query()
            ->whereIn('name', $permissionNames)
            ->orderBy('name')
            ->get();
    }
}

