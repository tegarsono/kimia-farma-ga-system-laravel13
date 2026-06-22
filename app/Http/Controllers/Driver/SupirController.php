<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupirController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('supir');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';

            $query->where(function ($queryBuilder) use ($searchTerm) {
                $queryBuilder
                    ->where('nama_supir', 'like', $searchTerm)
                    ->orWhere('nip', 'like', $searchTerm);
            });
        }

        $supir = $query->orderBy('nama_supir')->paginate(15)->withQueryString();

        return view('driver.supir.index', [
            'supir' => $supir,
        ]);

    }

    public function create()
    {
        // Blade yang tersedia: resources/views/driver/supir/create.blade.php
        return view('driver.supir.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_supir' => 'required|string|max:100',
            'nip'        => 'required|string|max:50',
            'status'     => 'required|in:aktif,idle,offline',
        ]);

        DB::table('supir')->insert([
            'nama_supir' => $request->nama_supir,
            'nip'        => $request->nip,
            'status'     => $request->status,
        ]);

        return redirect()
            ->route('driver.supir.index')
            ->with('success', 'Driver data has been added successfully.');
    }

    public function edit(int $driverId)
    {
        $driver = DB::table('supir')
            ->where('id_supir', $driverId)
            ->first();

        if (!$driver) {
            abort(404);
        }

        // view yang tersedia pada project ini: driver/supir/edit.blade.php
        // Kirim variabel yang sesuai dengan blade: $supir
        return view('driver.supir.edit', ['supir' => $driver]);
    }

    public function update(Request $request, int $driverId)
    {
        $request->validate([
            'nama_supir' => 'required|string|max:100',
            'nip'        => 'required|string|max:50',
            'status'     => 'required|in:aktif,idle,offline',
        ]);

        DB::table('supir')
            ->where('id_supir', $driverId)
            ->update([
                'nama_supir' => $request->nama_supir,
                'nip'        => $request->nip,
                'status'     => $request->status,
            ]);

        return redirect()
            ->route('driver.supir.index')
            ->with('success', 'Driver data has been updated successfully.');
    }

    public function destroy(int $driverId)
    {
        if (
            DB::table('jadwal')
                ->where('id_supir', $driverId)
                ->exists()
        ) {
            return back()->with(
                'error',
                'Cannot delete this driver because they still have active schedules.'
            );
        }

        DB::table('supir')
            ->where('id_supir', $driverId)
            ->delete();

        return redirect()
            ->route('driver.supir.index')
            ->with('success', 'Driver data has been deleted successfully.');
    }
}