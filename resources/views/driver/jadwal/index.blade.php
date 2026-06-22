@extends('layouts.driver')
@section('title','Jadwal Driver')
@section('page-title','Jadwal Driver Operasional')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Driver</span>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('driver.jadwal.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i class="fas fa-plus me-1"></i>Buat Jadwal</a>
            <a href="{{ route('driver.jadwal.riwayat') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-history me-1"></i>Riwayat</a>
        </div>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1">Filter Tanggal</label>
                <input type="date" name="tanggal" class="form-control form-control-sm"
                    value="{{ request('tanggal', date('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-sm btn-primary"><i class="fas fa-search me-1"></i>Tampilkan</button>
                <a href="{{ route('driver.jadwal.index') }}" class="btn btn-sm btn-outline-secondary">Hari Ini</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Waktu</th><th>Supir</th><th>Armada</th><th>Penumpang</th><th>Tujuan</th><th>Keperluan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($jadwal as $i => $j)
                <tr>
                    <td>{{ $jadwal->firstItem() + $i }}</td>
                    <td>
                        <span class="badge bg-primary">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</span>
                        <span class="text-muted">–</span>
                        <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</span>
                    </td>
                    <td><strong>{{ $j->nama_supir }}</strong></td>
                    <td>{{ $j->merk }}<br><small class="text-muted">{{ $j->plat_nomor }}</small></td>
                    <td>{{ $j->penumpang }}</td>
                    <td>{{ $j->tujuan }}</td>
                    <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $j->keperluan }}">
                        {{ $j->keperluan }}
                    </td>
                    <td>
                        <a href="{{ route('driver.jadwal.edit', $j->id_jadwal) }}" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('driver.jadwal.selesai', $j->id_jadwal) }}" class="d-inline"
                            onsubmit="return confirm('Tandai selesai?')">@csrf
                            <button class="btn btn-sm btn-outline-success py-0 px-2" title="Selesai"><i class="fas fa-check"></i></button>
                        </form>
                        <form method="POST" action="{{ route('driver.jadwal.destroy', $j->id_jadwal) }}" class="d-inline"
                            onsubmit="return confirm('Hapus jadwal ini?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-calendar-times me-2"></i>Tidak ada jadwal untuk tanggal ini.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $jadwal->links() }}</div>
</div>
@endsection
