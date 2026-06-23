<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForm()
    {
        return view('auth.lupa_password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak terdaftar di sistem.',
        ]);

        $email   = $request->email;
        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token   = Str::random(64);
        $expires = now()->addMinutes(15);

        DB::table('password_resets')->where('email', $email)->delete();

        DB::table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'otp_code'   => $otp,
            'created_at' => now(),
            'expires_at' => $expires,
            'used'       => 0,
        ]);

        try {
            Mail::send('auth.email_otp', ['otp' => $otp], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode OTP Reset Password - Kimia Farma GA');
            });
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email OTP: ' . $e->getMessage());
        }

        session(['reset_email' => $email, 'reset_token' => $token]);

        return redirect()->route('password.verify')->with('success', 'OTP telah dikirim ke email Anda.');
    }

    public function showVerify()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify_otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $email = session('reset_email');
        $token = session('reset_token');

        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->where('otp_code', $request->otp)
            ->where('used', 0)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return back()->with('error', 'Kode OTP tidak valid atau sudah kadaluarsa.');
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.reset');
    }

    public function showReset()
    {
        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $email = session('reset_email');

        DB::table('users')->where('email', $email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_resets')->where('email', $email)->update(['used' => 1]);

        session()->forget(['reset_email', 'reset_token', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
