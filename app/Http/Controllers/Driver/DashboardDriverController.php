<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardDriverController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $totals = $this->getDriverDashboardTotals($today);
        $todaySchedule = $this->getTodaySchedule($today);

        return view('driver.home', [
            // Variabel sesuai kebutuhan blade
            'totalMobil' => $totals['totalCars'],
            'supirAktif' => $totals['activeDrivers'],
            'supirIdle' => $totals['idleDrivers'],
            'supirOffline' => $totals['offlineDrivers'],

            // Jadwal hari ini
            'jadwalHariIni' => $todaySchedule,
            'tanggalHariIni' => $today,
        ]);

    }

    private function getDriverDashboardTotals(string $today): array
    {
        $totalCars = DB::table('mobil')->count();
        $totalDrivers = DB::table('supir')->count();

        $activeDrivers = DB::table('jadwal')
            ->where('tanggal_tugas', $today)
            ->distinct('id_supir')
            ->count('id_supir');

        $offlineDrivers = DB::table('supir')->where('status', 'offline')->count();
        $idleDrivers = max(0, $totalDrivers - $activeDrivers - $offlineDrivers);

        return [
            'totalCars' => $totalCars,
            'totalDrivers' => $totalDrivers,
            'activeDrivers' => $activeDrivers,
            'offlineDrivers' => $offlineDrivers,
            'idleDrivers' => $idleDrivers,
        ];
    }

    private function getTodaySchedule(string $today)
    {
        return DB::table('jadwal as j')
            ->join('supir as s', 'j.id_supir', '=', 's.id_supir')
            ->join('mobil as m', 'j.id_mobil', '=', 'm.id_mobil')
            ->select('j.*', 's.nama_supir', 'm.merk', 'm.plat_nomor')
            ->where('j.tanggal_tugas', $today)
            ->orderBy('j.jam_mulai')
            ->get();
    }
}

