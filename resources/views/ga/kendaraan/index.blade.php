@extends('layouts.ga')
@section('title','Aset Kendaraan')
@section('page-title','Manajemen Aset Kendaraan')

@push('styles')
<style>
    /* ── Status badges kendaraan ── */
    .badge-layak      { background-color: #166534; color: #fff; }   /* hijau tua */
    .badge-tidak-layak{ background-color: #991b1b; color: #fff; }   /* merah tua */
    .badge-dilelang   { background-color: #92400e; color: #fff; }   /* coklat/amber tua */
    .badge-hilang     { background-color: #374151; color: #fff; }   /* abu gelap */

    /* ── Tanggal pajak/STNK ── */
    .badge-expired    { background-color: #991b1b; color: #fff; }   /* merah tua */
    .badge-valid      { background-color: #14532d; color: #fff; }   /* hijau tua */

    /* ── Branch badge ── */
    .badge-branch     { background-color: #1e3a5f; color: #e8edf3; font-weight: 500; }

    /* ── Stat cards ── */
    .stat-card-layak       { border-top: 3px solid #16a34a; }
    .stat-card-tidak-layak { border-top: 3px solid #dc2626; }
    .stat-card-dilelang    { border-top: 3px solid #d97706; }
    .stat-card-hilang      { border-top: 3px solid #6b7280; }
</style>
@endpush

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 stat-card-layak">
            <div class="fs-4" style="color:#16a34a"><i class="fas fa-check-circle"></i></div>
            <div class="fw-bold fs-5">{{ $stats['Layak'] ?? 0 }}</div>
            <div class="text-muted" style="font-size:.78rem">Layak</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 stat-card-tidak-layak">
            <div class="fs-4" style="color:#dc2626"><i class="fas fa-times-circle"></i></div>
            <div class="fw-bold fs-5">{{ $stats['Tidak Layak'] ?? 0 }}</div>
            <div class="text-muted" style="font-size:.78rem">Tidak Layak</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 stat-card-dilelang">
            <div class="fs-4" style="color:#d97706"><i class="fas fa-gavel"></i></div>
            <div class="fw-bold fs-5">{{ $stats['Dilelang'] ?? 0 }}</div>
            <div class="text-muted" style="font-size:.78rem">Dilelang</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3 stat-card-hilang">
            <div class="fs-4" style="color:#6b7280"><i class="fas fa-question-circle"></i></div>
            <div class="fw-bold fs-5">{{ $stats['Hilang'] ?? 0 }}</div>
            <div class="text-muted" style="font-size:.78rem">Hilang</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-car me-2"></i>Data Kendaraan ({{ $total }} record)</span>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ga.kendaraan.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i class="fas fa-plus me-1"></i>Tambah</a>
            <a href="{{ route('ga.kendaraan.downloadTemplate') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-download me-1"></i>Template</a>
            <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-upload me-1"></i>Import</button>
            <a href="{{ route('ga.kendaraan.exportExcel', request()->all()) }}" class="btn btn-sm btn-light text-success fw-semibold"><i class="fas fa-file-excel me-1"></i>Export</a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari merk, no polisi..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-2">
                <select name="manager" class="form-select form-select-sm">
                    <option value="">Semua Branch</option>
                    @foreach($branches as $b)<option value="{{ $b }}" {{ request('manager')==$b?'selected':'' }}>{{ $b }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="jenis_kendaraan" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisKendaraanList as $j)<option value="{{ $j }}" {{ request('jenis_kendaraan')==$j?'selected':'' }}>{{ $j }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
                <a href="{{ route('ga.kendaraan.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th><th>No Posisi</th><th>Bisnis Manager</th><th>Jenis</th>
                    <th>Merk/Type</th><th>No Polisi</th><th>Pajak 1Th</th><th>STNK 5Th</th>
                    <th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kendaraan as $i => $k)
                @php
                    $now = now(); $warn1 = $warn5 = false;
                    if($k->masa_berakhir_1th) $warn1 = \Carbon\Carbon::parse($k->masa_berakhir_1th)->diffInDays($now,false) > 0;
                    if($k->masa_berakhir_5th) $warn5 = \Carbon\Carbon::parse($k->masa_berakhir_5th)->diffInDays($now,false) > 0;

                    $statusClass = match($k->status) {
                        'Layak'       => 'badge-layak',
                        'Tidak Layak' => 'badge-tidak-layak',
                        'Dilelang'    => 'badge-dilelang',
                        default       => 'badge-hilang',
                    };
                @endphp
                <tr>
                    <td>{{ $kendaraan->firstItem() + $i }}</td>
                    <td>{{ $k->no_posisi }}</td>
                    <td><span class="badge badge-branch">{{ $k->branch_manager }}</span></td>
                    <td>{{ $k->jenis_kendaraan }}</td>
                    <td>{{ $k->merk }}{{ $k->type ? ' ('.$k->type.')' : '' }}</td>
                    <td>{{ $k->no_polisi ?? '-' }}</td>
                    <td>
                        @if($k->masa_berakhir_1th)
                            <span class="badge {{ $warn1 ? 'badge-expired' : 'badge-valid' }}" title="{{ $warn1 ? 'Sudah kadaluarsa' : 'Masih berlaku' }}">
                                <i class="fas fa-{{ $warn1 ? 'exclamation-triangle' : 'check' }} me-1" style="font-size:.65rem"></i>{{ \Carbon\Carbon::parse($k->masa_berakhir_1th)->format('d/m/Y') }}
                            </span>
                        @else<span class="text-muted">-</span>@endif
                    </td>
                    <td>
                        @if($k->masa_berakhir_5th)
                            <span class="badge {{ $warn5 ? 'badge-expired' : 'badge-valid' }}" title="{{ $warn5 ? 'Sudah kadaluarsa' : 'Masih berlaku' }}">
                                <i class="fas fa-{{ $warn5 ? 'exclamation-triangle' : 'check' }} me-1" style="font-size:.65rem"></i>{{ \Carbon\Carbon::parse($k->masa_berakhir_5th)->format('d/m/Y') }}
                            </span>
                        @else<span class="text-muted">-</span>@endif
                    </td>
                    <td>
                        <span class="badge {{ $statusClass }}">{{ $k->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('ga.kendaraan.edit',$k->id) }}" class="btn btn-xs btn-outline-primary btn-sm py-0 px-2" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('ga.kendaraan.destroy',$k->id) }}" class="d-inline" onsubmit="return confirm('Hapus data kendaraan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data kendaraan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $kendaraan->links() }}</div>
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Import Data Kendaraan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('ga.kendaraan.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="text-muted small mb-3">Upload file Excel (.xlsx/.xls). <a href="{{ route('ga.kendaraan.downloadTemplate') }}">Download template</a> terlebih dahulu.</p>
                <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i>Import</button>
            </div>
        </form>
    </div></div>
</div>
@endsection
