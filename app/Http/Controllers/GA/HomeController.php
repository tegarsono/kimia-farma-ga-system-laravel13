<?php

namespace App\Http\Controllers\GA;

use App\Http\Controllers\Controller;
use App\Services\GA\DashboardHomeService;

class HomeController extends Controller
{
    public function index(DashboardHomeService $service)
    {
        $data = $service->getDashboardData();

        return view('ga.home', $data);
    }
}


