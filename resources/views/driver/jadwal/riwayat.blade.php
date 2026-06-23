@extends('layouts.driver')
@section('title','Riwayat Perjalanan')
@section('page-title','Riwayat Perjalanan Driver')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-history me-2 text-info"></i>Riwayat Perjalanan Selesai</span>
        <div class="d-flex gap-2">
            <a href="{{ route('driver.jadwal.riwayatPdf', request()->all()) }}" class="btn btn-sm btn-light text-danger fw-semibold" target="_blank">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </a>
            <a href="{{ route('driver.jadwal.index') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua Bulan</option>
                    @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $idx => $b)
                    <option value="{{ $idx+1 }}" {{ request('bulan')==$idx+1?'selected':'' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @for($y=date('Y');$y>=2020;$y--)<option value="{{ $y }}" {{ request('tahun')==$y?'selected':'' }}>{{ $y }}</option>@endfor
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('driver.jadwal.riwayat') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Tanggal</th><th>Waktu</th><th>Supir</th><th>Armada</th><th>Penumpang</th><th>Tujuan</th><th>Keperluan</th></tr>
            </thead>
            <tbody>
                @forelse($riwayat as $i => $r)
                <tr>
                    <td>{{ $riwayat->firstItem() + $i }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->tanggal_tugas)->format('d/m/Y') }}</td>
                    <td>
                        <small>{{ \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') }} –
                        {{ \Carbon\Carbon::parse($r->jam_selesai)->format('H:i') }}</small>
                    </td>
                    <td><strong>{{ $r->nama_supir }}</strong></td>
                    <td>{{ $r->merk }}<br><small class="text-muted">{{ $r->plat_nomor }}</small></td>
                    <td>{{ $r->penumpang }}</td>
                    <td>{{ $r->tujuan }}</td>
                    <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $r->keperluan }}">{{ $r->keperluan }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada riwayat perjalanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $riwayat->links() }}</div>
</div>
@endsection
