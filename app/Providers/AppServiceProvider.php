<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gunakan Bootstrap untuk pagination
        Paginator::useBootstrap();

        // Share foto profil default ke semua view layout
        View::composer(['layouts.ga', 'layouts.driver', 'layouts.ac'], function ($view) {
            try {
                $setting = DB::table('image_settings')
                    ->where('image_key', 'foto_profil_default')
                    ->first();

                if ($setting && $setting->image_value) {
                    $fotoProfil = $setting->image_type === 'url'
                        ? $setting->image_value
                        : asset('storage/' . $setting->image_value);
                } else {
                    $fotoProfil = asset('img/kf.png');
                }
            } catch (\Exception $e) {
                $fotoProfil = asset('img/kf.png');
            }

            $view->with('sidebarFotoProfil', $fotoProfil);
        });

        // Share logo AC monitoring header ke layout ac
        View::composer('layouts.ac', function ($view) {
            try {
                $setting = DB::table('image_settings')
                    ->where('image_key', 'logo_acmonitoring_header')
                    ->first();

                if ($setting && $setting->image_value) {
                    $logoAc = $setting->image_type === 'url'
                        ? $setting->image_value
                        : asset('storage/' . $setting->image_value);
                } else {
                    $logoAc = asset('img/kf.png');
                }
            } catch (\Exception $e) {
                $logoAc = asset('img/kf.png');
            }

            $view->with('logoAcHeader', $logoAc);
        });
    }
}
