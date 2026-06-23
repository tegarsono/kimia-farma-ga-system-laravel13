<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    private array $imageKeys = [
        'logo_main'                => 'Logo Utama',
        'bg_login'                 => 'Background Login',
        'bg_lupa_password'         => 'Background Lupa Password',
        'logo_profile_topbar'      => 'Logo Profil Topbar',
        'foto_profil_default'      => 'Foto Profil Default',
        'logo_driver_navbar'       => 'Logo Driver Navbar',
        'logo_driver_footer'       => 'Logo Driver Footer',
        'logo_acmonitoring_header' => 'Logo AC Monitoring Header',
        'logo_tab'                 => 'Logo Tab Browser',
    ];

    public function index()
    {
        $settings = DB::table('image_settings')->get()->keyBy('image_key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'image_key'  => 'required|string',
            'image_type' => 'required|in:url,upload',
            'image_url'  => 'required_if:image_type,url|nullable|url',
        ]);

        $key   = $request->image_key;
        $type  = $request->image_type;
        $value = $type === 'url' ? $request->image_url : null;

        DB::table('image_settings')->updateOrInsert(
            ['image_key' => $key],
            [
                'image_type'  => $type,
                'image_value' => $value ?? '',
                'updated_by'  => session('user_id'),
                'updated_at'  => now(),
            ]
        );

        return back()->with('success', 'Pengaturan gambar berhasil disimpan.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image_key'  => 'required|string',
            'image_file' => 'required|file|image|max:2048',
        ]);

        $key  = $request->image_key;
        $file = $request->file('image_file');
        $ext  = $file->getClientOriginalExtension();
        $name = $key . '_' . time() . '.' . $ext;

        $path = $file->storeAs('settings', $name, 'public');

        DB::table('image_settings')->updateOrInsert(
            ['image_key' => $key],
            [
                'image_type'  => 'upload',
                'image_value' => $path,
                'updated_by'  => session('user_id'),
                'updated_at'  => now(),
            ]
        );

        return back()->with('success', 'Gambar berhasil diunggah.');
    }

    public function getImage(string $key)
    {
        $setting = DB::table('image_settings')->where('image_key', $key)->first();
        if (!$setting) {
            return response()->json(['url' => asset('img/kf.png')]);
        }

        $url = $setting->image_type === 'url'
            ? $setting->image_value
            : asset('storage/' . $setting->image_value);

        return response()->json(['url' => $url]);
    }
}
