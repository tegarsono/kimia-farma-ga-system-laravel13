<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        // Jika user belum terautentikasi, laravel biasanya akan redirect, tapi jaga-jaga.
        if (!$user) {
            abort(403, 'Akses ditolak. Silakan login kembali.');
        }

        // Spatie: method hasPermissionTo ada.
        if (method_exists($user, 'hasPermissionTo')) {
            if (!$user->hasPermissionTo($permission)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            }
        }

        return $next($request);
    }
}

