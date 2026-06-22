<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', session('user_id'))->first();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('profile.index', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        $userId = session('user_id');

        $request->validate([
            'username'  => 'required|alpha_num|max:50|unique:users,username,' . $userId,
            'email'     => 'required|email|max:100|unique:users,email,' . $userId,
            'full_name' => 'required|string|max:100',
        ], [
            'username.required'  => 'Username wajib diisi.',
            'username.alpha_num' => 'Username hanya boleh berisi huruf dan angka.',
            'username.unique'    => 'Username sudah digunakan.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
        ]);

        DB::table('users')->where('id', $userId)->update([
            'username'  => $request->username,
            'email'     => $request->email,
            'full_name' => $request->full_name,
        ]);

        session([
            'username'  => $request->username,
            'email'     => $request->email,
            'full_name' => $request->full_name,
        ]);

        return back()->with('success', 'Informasi akun berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $userId = session('user_id');

        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required'     => 'Password baru wajib diisi.',
            'new_password.min'          => 'Password baru minimal 8 karakter.',
            'new_password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        $user = DB::table('users')->where('id', $userId)->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error_password', 'Password lama tidak sesuai.');
        }

        DB::table('users')->where('id', $userId)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
