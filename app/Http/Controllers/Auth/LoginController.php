<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username_email' => 'required|string',
            'password'       => 'required|string',
        ], [
            'username_email.required' => 'Username atau email wajib diisi.',
            'password.required'       => 'Password wajib diisi.',
        ]);

        $usernameEmail = trim($request->username_email);
        $password      = $request->password;

        $field = filter_var($usernameEmail, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = DB::table('users')
            ->where($field, $usernameEmail)
            ->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Username/Email tidak ditemukan.');
        }

        if (!Hash::check($password, $user->password)) {
            return back()->withInput()->with('error', 'Password salah.');
        }

        if ($user->status !== 'active') {
            return back()->withInput()->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        DB::table('users')->where('id', $user->id)->update(['last_login' => now()]);

        session([
            'user_id'   => $user->id,
            'username'  => $user->username,
            'email'     => $user->email,
            'full_name' => $user->full_name,
            'role'      => $user->role,
        ]);

        return redirect()->route('home')->with('success', 'Selamat datang, ' . ($user->full_name ?: $user->username) . '!');
    }

    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
