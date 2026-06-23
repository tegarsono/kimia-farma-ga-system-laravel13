<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function index(DashboardService $service)
    {
        $logoMain = $service->getImageSetting(
            'logo_main',
            'img/kf.png'
        );

        $bgHero = $service->getImageSetting(
            'bg_login',
            'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1920'
        );

        return View::make('dashboard', compact('logoMain', 'bgHero'));
    }
}

