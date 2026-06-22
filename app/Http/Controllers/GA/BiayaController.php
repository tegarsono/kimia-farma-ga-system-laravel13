<?php

namespace App\Http\Controllers\GA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiayaController extends Controller
{
    public function index(Request $request)
    {
        $atk = $this->buildBiayaAtkQuery($request)
            ->orderBy('kategori')
            ->orderBy('nama_barang')
            ->paginate(20)
            ->withQueryString();

        $totals = $this->getBiayaTotals();
        $perKategori = $this->getPerKategoriRingkasan();
        $kategoriList = $this->getKategoriList();

        return view('ga.biaya.index', [
            'atk' => $atk,
            'totalMasuk' => $totals['totalMasuk'],
            'totalKeluar' => $totals['totalKeluar'],
            'grandTotal' => $totals['grandTotal'],
            'perKategori' => $perKategori,
            'kategoriList' => $kategoriList,
        ]);
    }

    private function buildBiayaAtkQuery(Request $request)
    {
        $query = DB::table('atk_katalog');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status_barang')) {
            $query->where('status_barang', $request->status_barang);
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nama_barang', 'like', $s)
                    ->orWhere('kategori', 'like', $s)
                    ->orWhere('kode', 'like', $s);
            });
        }

        return $query;
    }

    private function getBiayaTotals(): array
    {
        $totalMasuk = (float) (DB::table('atk_katalog')
            ->where('status_barang', 'Masuk')
            ->selectRaw('SUM(harga * jumlah) as total')
            ->value('total') ?? 0);

        $totalKeluar = (float) (DB::table('atk_katalog')
            ->where('status_barang', '!=', 'Masuk')
            ->selectRaw('SUM(harga * jumlah) as total')
            ->value('total') ?? 0);

        return [
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'grandTotal' => $totalMasuk + $totalKeluar,
        ];
    }

    private function getPerKategoriRingkasan()
    {
        return DB::table('atk_katalog')
            ->selectRaw('kategori, status_barang, SUM(harga * jumlah) as total_nilai, SUM(jumlah) as total_qty, COUNT(*) as jumlah_item')
            ->groupBy('kategori', 'status_barang')
            ->orderBy('kategori')
            ->get()
            ->groupBy('kategori');
    }

    private function getKategoriList()
    {
        return DB::table('atk_katalog')->distinct()->pluck('kategori');
    }

}
