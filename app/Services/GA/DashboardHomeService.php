<?php

namespace App\Services\GA;

use Illuminate\Support\Facades\DB;

class DashboardHomeService
{
    public function getDashboardData(): array
    {
        $kendaraanBranch = DB::table('kendaraan_aset')
            ->selectRaw('branch_manager,
                SUM(CASE WHEN jenis_kendaraan = "Mobil" THEN 1 ELSE 0 END) AS total_mobil,
                SUM(CASE WHEN jenis_kendaraan = "Motor" THEN 1 ELSE 0 END) AS total_motor')
            ->groupBy('branch_manager')
            ->orderBy('branch_manager')
            ->get();

        $totalKendaraan = $kendaraanBranch->sum(fn($r) => (int) $r->total_mobil + (int) $r->total_motor);

        $dataKendaraan = [
            'labels' => [],
            'mobil' => [],
            'motor' => [],
            'total' => [],
        ];
        foreach ($kendaraanBranch as $row) {
            $mobil = (int) $row->total_mobil;
            $motor = (int) $row->total_motor;

            $dataKendaraan['labels'][] = $row->branch_manager;
            $dataKendaraan['mobil'][] = $mobil;
            $dataKendaraan['motor'][] = $motor;
            $dataKendaraan['total'][] = $mobil + $motor;
        }

        $bangunanBranch = DB::table('tanah_bangunan_aset')
            ->selectRaw('branch_manager, COUNT(id) as total_aset')
            ->groupBy('branch_manager')
            ->orderByDesc('total_aset')
            ->get();

        $dataBangunan = [
            'labels' => $bangunanBranch->pluck('branch_manager')->toArray(),
            'data' => $bangunanBranch->pluck('total_aset')->map(fn($v) => (int) $v)->toArray(),
        ];

        $atkMonthly = DB::table('atk_katalog')
            ->selectRaw('MONTH(updated_at) as bulan, status_barang, SUM(jumlah) as total_qty')
            ->whereRaw('YEAR(updated_at) = YEAR(CURDATE())')
            ->groupBy('bulan', 'status_barang')
            ->get();

        $dataAtk = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'masuk' => array_fill(0, 12, 0),
            'keluar' => array_fill(0, 12, 0),
        ];
        $hasAtkData = false;

        foreach ($atkMonthly as $row) {
            $hasAtkData = true;
            $idx = (int) $row->bulan - 1;
            $status = strtolower($row->status_barang);

            if (isset($dataAtk[$status])) {
                $dataAtk[$status][$idx] = (int) $row->total_qty;
            }
        }

        $biayaMonthly = DB::table('atk_katalog')
            ->selectRaw('MONTH(updated_at) as bulan, status_barang, SUM(harga * jumlah) as total_harga')
            ->whereRaw('YEAR(updated_at) = YEAR(CURDATE())')
            ->groupBy('bulan', 'status_barang')
            ->get();

        $dataBiaya = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'masuk' => array_fill(0, 12, 0),
            'keluar' => array_fill(0, 12, 0),
        ];
        $hasBiayaData = false;

        foreach ($biayaMonthly as $row) {
            $hasBiayaData = true;
            $idx = (int) $row->bulan - 1;
            $status = strtolower($row->status_barang);

            if (isset($dataBiaya[$status])) {
                $dataBiaya[$status][$idx] = (float) $row->total_harga;
            }
        }

        $allAtk = DB::table('atk_katalog')->get();
        $totalMasuk = 0;
        $totalKeluar = 0;

        foreach ($allAtk as $item) {
            $jumlahHarga = (float) ($item->harga ?? 0) * (int) ($item->jumlah ?? 0);
            if (($item->status_barang ?? '') === 'Masuk') {
                $totalMasuk += $jumlahHarga;
            } else {
                $totalKeluar += $jumlahHarga;
            }
        }

        $dirUnitBisnis = DB::table('dir_aset')
            ->selectRaw('unit_bisnis, COUNT(id) as total')
            ->groupBy('unit_bisnis')
            ->orderByDesc('total')
            ->get();

        $dataDirUnitBisnis = [
            'labels' => $dirUnitBisnis->pluck('unit_bisnis')->toArray(),
            'data' => $dirUnitBisnis->pluck('total')->map(fn($v) => (int) $v)->toArray(),
        ];

        $totalDir = $dirUnitBisnis->sum('total');

        $totalKendaraanAset = DB::table('kendaraan_aset')->count();
        $totalBangunanAset = DB::table('tanah_bangunan_aset')->count();

        $totalAtkItem = DB::table('atk_katalog')->count();
        $totalAtkTransaksi = DB::table('atk_transaksi')->count();

        $atkMasuk = $dataAtk['masuk'];
        $atkKeluar = $dataAtk['keluar'];

        $totalMasukQty = array_sum($atkMasuk);
        $totalKeluarQty = array_sum($atkKeluar);

        return [
            'kendaraanBranch' => $kendaraanBranch,
            'totalKendaraan' => $totalKendaraan,
            'dataKendaraan' => $dataKendaraan,
            'bangunanBranch' => $bangunanBranch,
            'dataBangunan' => $dataBangunan,
            'dataDirUnitBisnis' => $dataDirUnitBisnis,
            'totalDir' => $totalDir,
            'dataAtk' => $dataAtk,
            'hasAtkData' => $hasAtkData,
            'dataBiaya' => $dataBiaya,
            'hasBiayaData' => $hasBiayaData,
            'totalKendaraanAset' => $totalKendaraanAset,
            'totalBangunanAset' => $totalBangunanAset,
            'totalAtkItem' => $totalAtkItem,
            'totalAtkTransaksi' => $totalAtkTransaksi,
            'atkMasuk' => $atkMasuk,
            'atkKeluar' => $atkKeluar,
            'totalMasukQty' => $totalMasukQty,
            'totalKeluarQty' => $totalKeluarQty,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
        ];
    }
}

