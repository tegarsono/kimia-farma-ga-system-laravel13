<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class PermissionOrRoleMiddleware
{
    /**
     * Izinkan akses jika:
     * - user role-nya admin, ATAU
     * - user memiliki permission sesuai parameter
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Login di project ini menggunakan session manual (user_id, role, dst)
        // sehingga $request->user() bisa null pada beberapa kondisi.
        $user = $request->user();
        if (!$user) {
            $userId = session('user_id');
            if ($userId) {
                $user = User::find($userId);
            }
        }

        if (!$user) {
            abort(403, 'Akses ditolak. Silakan login kembali.');
        }

        // Admin role bypass
        if ((string)($user->role ?? '') === 'admin') {
            return $next($request);
        }

        // Spatie permission check.
        // Hindari pemanggilan hasPermissionTo() karena instalasi kamu belum punya table `cache`.
        // Maka kita cek lewat pivot relationship.
        if (method_exists($user, 'permissions')) {
            try {
                $permissionModel = \Spatie\Permission\Models\Permission::query()
                    ->where('name', $permission)
                    ->first();

                if (!$permissionModel) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
                }

                $has = $user->permissions()
                    ->where('permissions.id', $permissionModel->id)
                    ->exists();

                if (!$has) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
                }

                return $next($request);
            } catch (\Throwable $e) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            }
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
    }
}

