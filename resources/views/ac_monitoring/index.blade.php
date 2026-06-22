@extends('layouts.ac')
@section('title', 'AC Monitoring Maintenance GA')
@section('page-title', 'AC Monitoring Maintenance GA')

@section('content')
    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center py-3">
                <div class="text-primary fs-4"><i class="fas fa-list-alt"></i></div>
                <div class="fw-bold fs-5">{{ $totalItems }}</div>
                <div class="text-muted" style="font-size:.78rem">Total Item</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3">
                <div class="text-success fs-4"><i class="fas fa-check-circle"></i></div>
                <div class="fw-bold fs-5">{{ $statsNormal }}</div>
                <div class="text-muted" style="font-size:.78rem">Normal</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3">
                <div class="text-warning fs-4"><i class="fas fa-tools"></i></div>
                <div class="fw-bold fs-5">{{ $statsWajibService }}</div>
                <div class="text-muted" style="font-size:.78rem">Wajib Service</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3">
                <div class="text-danger fs-4"><i class="fas fa-bell"></i></div>
                <div class="fw-bold fs-5">{{ $needService }}</div>
                <div class="text-muted" style="font-size:.78rem">Notifikasi</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <span><i class="fas fa-snowflake me-2 text-info"></i>AC Monitoring Maintenance Data
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('ac.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i
                            class="fas fa-plus me-1"></i>Add</a>
                    <a href="{{ route('ac.notifikasi') }}" class="btn btn-sm btn-warning text-dark fw-semibold"><i
                            class="fas fa-bell me-1"></i>Notifikasi</a>
                    <a href="{{ route('ac.downloadTemplate') }}" class="btn btn-sm btn-outline-light"><i
                            class="fas fa-download me-1"></i>Template</a>
                    <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#importModal"><i
                            class="fas fa-upload me-1"></i>Import</button>
                    <a href="{{ route('ac.exportExcel') }}" class="btn btn-sm btn-light text-success fw-semibold"><i
                            class="fas fa-file-excel me-1"></i>Excel</a>
                    <a href="{{ route('ac.generatePdf') }}" class="btn btn-sm btn-light text-danger fw-semibold"><i
                            class="fas fa-file-pdf me-1"></i>Generate PDF</a>
                </div>
        </div>

                <div class="card-body pb-0">
                    <form method="GET" id="filterForm" class="row g-2 mb-3">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari kode GA, lokasi, nama barang..." </div>
                    <div class="col-6 col-md-3">
                        <select name="jenis_barang" class="form-select form-select-sm">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisBarangList as $j)
                                <option value="{{ $j }}" {{ request('jenis_barang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Normal" {{ request('status') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Wajib Service" {{ request('status') == 'Wajib Service' ? 'selected' : '' }}>Wajib Service
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('ac.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="mainTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll" class="form-check-input"></th>
                        <th>#</th>
                        <th>Kode GA</th>
                        <th>Lokasi</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Tgl. Perawatan Terakhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monitoring as $i => $item)
                        @php
                            $isOld = \Carbon\Carbon::parse($item->tgl_perawatan_terakhir)->lt(now()->subMonths(3));
                            $rowClass = $item->status === 'Wajib Service' ? 'table-warning' : ($isOld ? 'table-danger' : '');
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td><input type="checkbox" class="form-check-input row-check" value="{{ $item->id }}"></td>
                            <td>{{ $monitoring->firstItem() + $i }}</td>
                            <td><code>{{ $item->kode_ga ?? '-' }}</code></td>
                            <td>{{ $item->lokasi }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td><span class="badge bg-info text-dark">{{ $item->jenis_barang }}</span></td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tgl_perawatan_terakhir)->format('d/m/Y') }}
                                @if($isOld)
                                    <span
                                        class="badge bg-danger ms-1">{{ \Carbon\Carbon::parse($item->tgl_perawatan_terakhir)->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $item->status === 'Normal' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('ac.edit', $item->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2"><i
                                        class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('ac.destroy', $item->id) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data monitoring.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body pt-2 d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div>
                <button class="btn btn-sm btn-danger" id="btnPdfSelected" disabled>
                    <i class="fas fa-file-pdf me-1"></i>PDF Item Terpilih
                </button>
            </div>
            {{ $monitoring->links() }}
        </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Monitoring</h5><button class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('ac.import') }}" enctype="multipart/form-data">@csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-2"><a href="{{ route('ac.downloadTemplate') }}">Download template</a>
                            first.</p>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PDF Selected Form --}}
    <form id="pdfSelectedForm" method="POST" action="{{ route('ac.generatePdfSelected') }}" target="_blank">
        @csrf
        <div id="selectedIdsContainer"></div>
    </form>
@endsection

@push('scripts')
    <script>
        // Select all checkbox
        document.getElementById('checkAll').addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
            updateBtn();
        });
        document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateBtn));

        function updateBtn() {
            const checked = document.querySelectorAll('.row-check:checked').length;
            document.getElementById('btnPdfSelected').disabled = checked === 0;
        }

        document.getElementById('btnPdfSelected').addEventListener('click', function () {
            const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
            const container = document.getElementById('selectedIdsContainer');
            container.innerHTML = '';
            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'selected_ids[]'; inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('pdfSelectedForm').submit();
        });
    </script>
@endpush