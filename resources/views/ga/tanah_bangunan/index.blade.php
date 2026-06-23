@extends('layouts.ga')
@section('title','Aset Tanah & Bangunan')
@section('page-title','Manajemen Aset Tanah & Bangunan')

@push('styles')
<style>
    /* ── Status badges tanah & bangunan ── */
    .badge-aktif-shm  { background-color: #14532d; color: #fff; }   /* hijau tua */
    .badge-aktif-shgb { background-color: #1e3a5f; color: #fff; }   /* biru tua */
    .badge-nonaktif   { background-color: #7f1d1d; color: #fff; }   /* merah tua */
    .badge-sengketa   { background-color: #78350f; color: #fff; }   /* coklat tua */
    .badge-default-tb { background-color: #374151; color: #fff; }   /* abu gelap */

    /* ── Masa berlaku sertifikat ── */
    .badge-expired    { background-color: #991b1b; color: #fff; }
    .badge-valid      { background-color: #14532d; color: #fff; }

    /* ── Branch badge ── */
    .badge-branch     { background-color: #1e3a5f; color: #e8edf3; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-building me-2"></i>Data Tanah &amp; Bangunan ({{ $total }} record)</span>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ga.tanah_bangunan.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i class="fas fa-plus me-1"></i>Tambah</a>
            <a href="{{ route('ga.tanah_bangunan.downloadTemplate') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-download me-1"></i>Template</a>
            <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-upload me-1"></i>Import</button>
            <a href="{{ route('ga.tanah_bangunan.exportExcel', request()->all()) }}" class="btn btn-sm btn-light text-success fw-semibold"><i class="fas fa-file-excel me-1"></i>Export</a>
        </div>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no asset, alamat..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="manager" class="form-select form-select-sm">
                    <option value="">Semua Branch</option>
                    @foreach($branches as $b)<option value="{{ $b }}" {{ request('manager')==$b?'selected':'' }}>{{ $b }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>@endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('ga.tanah_bangunan.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>No Asset</th><th>Kode SAP</th><th>Branch</th><th>Alamat</th><th>Luas Tanah</th><th>No Sertifikat</th><th>Masa Berlaku</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($tanahBangunan as $i => $a)
                @php
                    $isExpired = $a->masa_berlaku ? \Carbon\Carbon::parse($a->masa_berlaku)->isPast() : false;

                    $statusLower = strtolower($a->status ?? '');
                    $statusClass = 'badge-default-tb';
                    if (str_contains($statusLower, 'aktif') && str_contains($statusLower, 'shm')) {
                        $statusClass = 'badge-aktif-shm';
                    } elseif (str_contains($statusLower, 'aktif') && str_contains($statusLower, 'shgb')) {
                        $statusClass = 'badge-aktif-shgb';
                    } elseif (str_contains($statusLower, 'aktif')) {
                        $statusClass = 'badge-aktif-shm';
                    } elseif (str_contains($statusLower, 'sengketa')) {
                        $statusClass = 'badge-sengketa';
                    } elseif (str_contains($statusLower, 'non') || str_contains($statusLower, 'jual') || str_contains($statusLower, 'tidak')) {
                        $statusClass = 'badge-nonaktif';
                    }
                @endphp
                <tr>
                    <td>{{ $tanahBangunan->firstItem() + $i }}</td>
                    <td><strong>{{ $a->no_asset_tanah }}</strong></td>
                    <td>{{ $a->kode_sap ?? '-' }}</td>
                    <td><span class="badge badge-branch">{{ $a->branch_manager }}</span></td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $a->alamat }}">{{ $a->alamat }}</td>
                    <td>{{ number_format($a->luas_tanah, 0, ',', '.') }} m²</td>
                    <td>{{ $a->nomor_sertifikat_baru }}</td>
                    <td>
                        @if($a->masa_berlaku)
                            <span class="badge {{ $isExpired ? 'badge-expired' : 'badge-valid' }}" title="{{ $isExpired ? 'Sudah kadaluarsa' : 'Masih berlaku' }}">
                                <i class="fas fa-{{ $isExpired ? 'exclamation-triangle' : 'check' }} me-1" style="font-size:.65rem"></i>{{ \Carbon\Carbon::parse($a->masa_berlaku)->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-muted small">Tidak terbatas</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $statusClass }}">{{ $a->status }}</span></td>
                    <td>
                        <a href="{{ route('ga.tanah_bangunan.edit', $a->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('ga.tanah_bangunan.destroy', $a->id) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data aset.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $tanahBangunan->links() }}</div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Import Data Tanah &amp; Bangunan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('ga.tanah_bangunan.import') }}" enctype="multipart/form-data">@csrf
            <div class="modal-body">
                <p class="small text-muted mb-2"><a href="{{ route('ga.tanah_bangunan.downloadTemplate') }}">Download template</a> terlebih dahulu.</p>
                <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div></div>
</div>
@endsection
