<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobilController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('mobil');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('merk', 'like', $s)->orWhere('plat_nomor', 'like', $s)->orWhere('tipe_mobil', 'like', $s);
            });
        }

        $mobil = $query->orderBy('id_mobil')->paginate(15)->withQueryString();
        return view('driver.mobil.index', [
            'mobil' => $mobil,
        ]);

    }

    public function create()
    {
        return view('driver.mobil.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'merk'      => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:20|unique:mobil,plat_nomor',
            'tipe_mobil' => 'required|string|max:50',
        ]);

        DB::table('mobil')->insert([
            'merk'       => $request->merk,
            'plat_nomor' => strtoupper($request->plat_nomor),
            'tipe_mobil' => $request->tipe_mobil,
        ]);

        return redirect()->route('driver.mobil.index')->with('success', 'Data mobil berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $mobil = DB::table('mobil')->where('id_mobil', $id)->first();
        if (!$mobil) abort(404);
        return view('driver.mobil.edit', ['mobil' => $mobil]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'merk'       => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:20|unique:mobil,plat_nomor,' . $id . ',id_mobil',
            'tipe_mobil' => 'required|string|max:50',
        ]);

        DB::table('mobil')->where('id_mobil', $id)->update([
            'merk'       => $request->merk,
            'plat_nomor' => strtoupper($request->plat_nomor),
            'tipe_mobil' => $request->tipe_mobil,
        ]);

        return redirect()->route('driver.mobil.index')->with('success', 'Data mobil berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        // Check whether there is still an active schedule for this car
        if (DB::table('jadwal')->where('id_mobil', $id)->exists()) {
            return back()->with('error', 'Tidak dapat menghapus: mobil ini masih memiliki jadwal aktif.');
        }

        DB::table('mobil')->where('id_mobil', $id)->delete();
        return redirect()->route('driver.mobil.index')->with('success', 'Data mobil berhasil dihapus.');
    }
}