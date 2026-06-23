@extends('layouts.ga')
@section('title', 'DIR')
@section('page-title', 'Manajemen DIR')

@push('styles')
    <style>
        .badge-dir {
            background-color: #1e3a5f;
            color: #e8edf3;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <span><i class="fas fa-list-check me-2"></i>Data DIR ({{ $total }} record)</span>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('ga.dir.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah
                </a>
                <a href="{{ route('ga.dir.downloadTemplate') }}" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-download me-1"></i>Template
                </a>
                <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-upload me-1"></i>Import
                </button>
                <a href="{{ route('ga.dir.exportExcel', request()->all()) }}"
                    class="btn btn-sm btn-light text-success fw-semibold">
                    <i class="fas fa-file-excel me-1"></i>Export
                </a>
            </div>
        </div>


        {{-- Filter --}}
        <div class="card-body pb-0">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-12 col-md-8">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari dari kata (kode aset, deskripsi, lokasi, unit bisnis, kategori...)" value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('ga.dir.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>

        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>NO</th>


                        <th>COST CENTER</th>
                        <th>PROFIT CENTER</th>
                        <th>UNIT BISNIS</th>
                        <th>GOLONGAN ASET</th>
                        <th>KATEGORI ASET</th>
                        <th>DESKRIPSI ASET</th>
                        <th>LOKASI / PEMAKAI</th>
                        <th>KODE ASET</th>
                        <th>ID ASET</th>
                        <th>QR Code</th>
                        <th>KETERANGAN</th>
                        <th>Aksi</th>
                    </tr>

                </thead>
                <tbody>
                    @forelse($items as $i => $it)
                        <tr>
                            <td>{{ $items->firstItem() + $i }}</td>
                            <td>{{ $it->cost_center }}</td>

                            <td>{{ $it->profit_center }}</td>
                            <td>{{ $it->unit_bisnis }}</td>
                            <td>{{ $it->golongan_aset }}</td>
                            <td>{{ $it->kategori_aset }}</td>
                            <td>{{ $it->deskripsi_aset }}</td>
                            <td>{{ $it->lokasi_pemakai }}</td>
                            <td>{{ $it->kode_aset }}</td>
                            <td>{{ $it->id_aset }}</td>
                            <td>
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <div class="qr-code" style="display:flex;align-items:center;justify-content:center;">
                                        {{-- URL QR (scan -> download PDF) --}}
                                        <div style="display:none" class="qr-url">{{ route('ga.dir.qrPdf', $it->id) }}</div>
                                        <img alt="QR" class="qr-img" width="90" height="90"
                                            src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('ga.dir.qrPdf', $it->id)) }}" />
                                    </div>

                                    {{-- Print QR Code (download PDF) --}}
                                    <a href="{{ route('ga.dir.qrPdf', $it->id) }}"
                                        class="btn btn-xs btn-outline-success btn-sm py-0 px-2" title="Print QR Code">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                            <td>{{ $it->keterangan }}</td>
                            <td>


                                <a href="{{ route('ga.dir.edit', $it->id) }}"
                                    class="btn btn-xs btn-outline-primary btn-sm py-0 px-2" title="Edit"><i
                                        class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('ga.dir.destroy', $it->id) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus data DIR ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" title="Hapus"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data
                                DIR.</td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body pt-2">
            @if(method_exists($items, 'links'))
                {{ $items->links() }}
            @endif
        </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Data DIR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('ga.dir.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            Upload Excel (.xlsx/.xls) sesuai template.
                            <a href="{{ route('ga.dir.downloadTemplate') }}" class="ms-1">Download template</a>
                        </div>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i>Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection