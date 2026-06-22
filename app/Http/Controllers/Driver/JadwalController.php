<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('jadwal as j')
            ->join('supir as s', 'j.id_supir', '=', 's.id_supir')
            ->join('mobil as m', 'j.id_mobil', '=', 'm.id_mobil')
            ->select('j.*', 's.nama_supir', 'm.merk', 'm.plat_nomor');

        if ($request->filled('tanggal')) {
            $query->where('j.tanggal_tugas', $request->tanggal);
        } else {
            $query->where('j.tanggal_tugas', now()->toDateString());
        }

        $jadwal = $query->orderBy('j.jam_mulai')->paginate(15)->withQueryString();

        return view('driver.jadwal.index', [
            'jadwal' => $jadwal,
        ]);

    }

    public function create()
    {
        $drivers = DB::table('supir')->where('status', '!=', 'offline')->get();
        $cars    = DB::table('mobil')->get();
        return view('driver.jadwal.create', compact('drivers', 'cars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_supir'     => 'required|integer|exists:supir,id_supir',
            'id_mobil'     => 'required|integer|exists:mobil,id_mobil',
            'tanggal_tugas' => 'required|date',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required|after:jam_mulai',
            'penumpang'    => 'required|string|max:200',
            'tujuan'       => 'required|string|max:200',
            'keperluan'    => 'required|string',
        ], [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Check for driver schedule conflict
        $hasConflict = DB::table('jadwal')
            ->where('id_supir', $request->id_supir)
            ->where('tanggal_tugas', $request->tanggal_tugas)
            ->where(function ($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai]);
            })
            ->exists();

        if ($hasConflict) {
            return back()->withInput()->with('error', 'Supir ini sudah memiliki jadwal di waktu yang bersamaan.');
        }

        DB::table('jadwal')->insert([
            'id_supir'      => $request->id_supir,
            'id_mobil'      => $request->id_mobil,
            'tanggal_tugas' => $request->tanggal_tugas,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'penumpang'     => $request->penumpang,
            'tujuan'        => $request->tujuan,
            'keperluan'     => $request->keperluan,
            'created_at'    => now(),
        ]);

        // Update driver status to active
        DB::table('supir')->where('id_supir', $request->id_supir)->update(['status' => 'aktif']);

        return redirect()->route('driver.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $schedule = DB::table('jadwal')->where('id_jadwal', $id)->first();
        if (!$schedule) abort(404);

        $drivers = DB::table('supir')->get();
        $cars    = DB::table('mobil')->get();
        return view('driver.jadwal.edit', compact('schedule', 'drivers', 'cars'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'id_supir'     => 'required|integer|exists:supir,id_supir',
            'id_mobil'     => 'required|integer|exists:mobil,id_mobil',
            'tanggal_tugas' => 'required|date',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
            'penumpang'    => 'required|string|max:200',
            'tujuan'       => 'required|string|max:200',
            'keperluan'    => 'required|string',
        ]);

        DB::table('jadwal')->where('id_jadwal', $id)->update([
            'id_supir'      => $request->id_supir,
            'id_mobil'      => $request->id_mobil,
            'tanggal_tugas' => $request->tanggal_tugas,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'penumpang'     => $request->penumpang,
            'tujuan'        => $request->tujuan,
            'keperluan'     => $request->keperluan,
        ]);

        return redirect()->route('driver.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $schedule = DB::table('jadwal')->where('id_jadwal', $id)->first();
        if ($schedule) {
            DB::table('jadwal')->where('id_jadwal', $id)->delete();
        }
        return redirect()->route('driver.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function complete(Request $request, int $id)
    {
        $schedule = DB::table('jadwal')->where('id_jadwal', $id)->first();
        if (!$schedule) abort(404);

        // Move to history
        DB::table('riwayat_jadwal')->insert((array)$schedule);
        DB::table('jadwal')->where('id_jadwal', $id)->delete();

        // Update driver status to idle if there's no other active schedule today
        $hasRemainingSchedule = DB::table('jadwal')
            ->where('id_supir', $schedule->id_supir)
            ->where('tanggal_tugas', now()->toDateString())
            ->exists();

        if (!$hasRemainingSchedule) {
            DB::table('supir')->where('id_supir', $schedule->id_supir)->update(['status' => 'idle']);
        }

        return redirect()->route('driver.jadwal.index')->with('success', 'Jadwal selesai dan dipindahkan ke riwayat.');
    }

    public function history(Request $request)
    {
        $query = DB::table('riwayat_jadwal as r')
            ->join('supir as s', 'r.id_supir', '=', 's.id_supir')
            ->join('mobil as m', 'r.id_mobil', '=', 'm.id_mobil')
            ->select('r.*', 's.nama_supir', 'm.merk', 'm.plat_nomor');

        if ($request->filled('bulan')) {
            $query->whereRaw('MONTH(r.tanggal_tugas) = ?', [$request->bulan]);
        }
        if ($request->filled('tahun')) {
            $query->whereRaw('YEAR(r.tanggal_tugas) = ?', [$request->tahun]);
        }

        $history = $query->orderBy('r.tanggal_tugas', 'desc')->paginate(20)->withQueryString();

        return view('driver.jadwal.riwayat', compact('history'));
    }

    public function historyPdf(Request $request)
    {
        $query = DB::table('riwayat_jadwal as r')
            ->join('supir as s', 'r.id_supir', '=', 's.id_supir')
            ->join('mobil as m', 'r.id_mobil', '=', 'm.id_mobil')
            ->select('r.*', 's.nama_supir', 'm.merk', 'm.plat_nomor');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereRaw('MONTH(r.tanggal_tugas) = ?', [$request->bulan])
                ->whereRaw('YEAR(r.tanggal_tugas) = ?', [$request->tahun]);
        }

        $history = $query->orderBy('r.tanggal_tugas')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('driver.jadwal.riwayat_pdf', compact('history'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('riwayat_jadwal_' . date('Ymd_His') . '.pdf');
    }
}