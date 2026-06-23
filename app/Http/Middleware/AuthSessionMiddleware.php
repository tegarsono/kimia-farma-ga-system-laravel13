<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            if (session()->has('was_logged_in')) {
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            return redirect()->route('login');
        }

        session(['was_logged_in' => true]);

        return $next($request);
    }
}
