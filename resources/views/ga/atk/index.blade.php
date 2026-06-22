@extends('layouts.ga')
@section('title','Sentralisasi ATK')
@section('page-title','Sentralisasi ATK — Katalog Barang')

@section('content')
<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <div>
            <h2 class="mb-1"><i class="fas fa-box-open me-2"></i>Katalog Alat Tulis Kantor (ATK)</h2>
            <p class="text-muted mb-0">Daftar inventaris dan detail harga barang ATK</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ga.atk.create') }}" class="btn btn-primary" title="Tambah Item Baru">
                <i class="fas fa-plus me-1"></i> Tambah ATK
            </a>
            <a href="{{ route('ga.atk.barangKeluarForm') }}" class="btn btn-danger" title="Tambah Barang Keluar">
                <i class="fas fa-arrow-right me-1"></i> Barang Keluar
            </a>
        </div>
    </div>

    {{-- Filter & Actions Section --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-2 px-3 bg-light border-bottom d-flex align-items-center gap-2">
            <i class="fas fa-sliders-h text-secondary"></i>
            <span class="fw-semibold text-secondary small">Filter & Pengaturan</span>
        </div>
        <div class="card-body py-3 px-3">
            <form method="GET" action="{{ route('ga.atk.index') }}">
                <div class="row g-3 align-items-end">
                    {{-- Batas Minimum Stok --}}
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>Batas Minimum Stok
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="max-width: 220px;">
                                <input type="number" id="thresholdInput" class="form-control" placeholder="Contoh: 10"
                                    min="0" title="Barang dengan jumlah di bawah angka ini akan diberi peringatan">
                                <button class="btn btn-warning" type="button" id="applyThreshold" title="Terapkan">
                                    <i class="fas fa-check me-1"></i>Set
                                </button>
                            </div>
                            <div class="form-text mb-0" id="thresholdStatus"></div>
                        </div>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-tag me-1"></i>Kategori
                        </label>
                        <select class="form-select form-select-sm" name="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach($kategori as $k)
                            <option value="{{ $k }}" {{ request('kategori')==$k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pencarian --}}
                    <div class="col-12 col-md-5">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-search me-1"></i>Pencarian
                        </label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Cari nama barang atau spesifikasi..."
                            value="{{ request('search') }}">
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="col-12 col-md-4 d-flex gap-2 align-items-end">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-filter me-1"></i>Terapkan
                        </button>
                        @if(request('kategori') || request('search'))
                        <a href="{{ route('ga.atk.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset semua filter">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>

            <hr class="my-3">

            {{-- Excel Actions --}}
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="small fw-semibold text-muted me-1"><i class="fas fa-file-excel me-1"></i>Excel:</span>
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal"
                    title="Import data dari Excel">
                    <i class="fas fa-file-import me-1"></i>Import
                </button>
                <a href="{{ route('ga.atk.exportExcel') }}" class="btn btn-success btn-sm" title="Export data ke Excel">
                    <i class="fas fa-file-export me-1"></i>Export
                </a>
                <a href="{{ route('ga.atk.downloadTemplate') }}" class="btn btn-outline-success btn-sm" title="Download template Excel untuk import">
                    <i class="fas fa-file-excel me-1"></i>Template Excel
                </a>
            </div>

            <hr class="my-3">

            {{-- PDF Stok Minimum Actions --}}
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="small fw-semibold text-muted me-1"><i class="fas fa-file-pdf me-1 text-danger"></i>PDF Stok:</span>
                <button type="button" class="btn btn-danger btn-sm" id="btnGeneratePdfStok"
                    title="Generate PDF untuk item yang dipilih (stok perlu ditambah)" disabled>
                    <i class="fas fa-file-pdf me-1"></i>Generate PDF Stok Minimum
                    <span class="badge bg-light text-danger ms-1" id="selectedCount">0</span>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSelectAllLow"
                    title="Pilih semua item yang stoknya di bawah batas minimum">
                    <i class="fas fa-exclamation-triangle me-1 text-warning"></i>Pilih Semua Stok Rendah
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearSelection"
                    title="Batalkan semua pilihan">
                    <i class="fas fa-times me-1"></i>Batal Pilih
                </button>
                <span class="small text-muted ms-1">
                    <i class="fas fa-info-circle me-1"></i>Centang item di tabel lalu klik Generate PDF
                </span>
            </div>
        </div>
    </div>

    {{-- Tabel Barang Masuk --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Barang Masuk</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="bg-success text-white">
                        <tr>
                            <th class="text-center" style="width:40px">
                                <input type="checkbox" class="form-check-input" id="selectAllMasuk" title="Pilih semua barang masuk">
                            </th>
                            <th class="text-center" style="width:50px">No</th>
                            <th style="width:120px">Kategori</th>
                            <th>Nama Barang</th>
                            <th class="text-center" style="width:80px">Satuan</th>
                            <th class="text-end" style="width:120px">Harga (Rp)</th>
                            <th class="text-center" style="width:80px">Jumlah</th>
                            <th class="text-end" style="width:130px">Jumlah Harga</th>
                            <th>Spesifikasi</th>
                            <th style="width:150px">Keterangan</th>
                            <th class="text-center" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataMasuk as $i => $item)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input atk-checkbox"
                                    value="{{ $item->id }}"
                                    data-jumlah="{{ (int)$item->jumlah }}"
                                    data-nama="{{ $item->nama_barang }}">
                            </td>
                            <th class="text-center">{{ $i + 1 }}</th>
                            <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                            <td class="fw-semibold">{{ $item->nama_barang }}</td>
                            <td class="text-center">{{ $item->satuan }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-success stok-badge" data-jumlah="{{ (int)$item->jumlah }}">{{ $item->jumlah }}</span>
                            </td>
                            <td class="text-end fw-bold text-primary">{{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                            <td><small>{{ $item->spesifikasi ?? '-' }}</small></td>
                            <td><small>{{ $item->keterangan ?? '-' }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('ga.atk.edit', $item->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('ga.atk.destroy', $item->id) }}" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox me-2"></i> Tidak ada barang masuk.
                            </td>
                        </tr>
                        @endforelse
                        @if($dataMasuk->count() > 0)
                        <tr hidden class="table-light fw-bold border-top border-primary">
                            <td colspan="7" class="text-end">Harga Total:</td>
                            <td class="text-end text-primary fs-5">Rp {{ number_format($totalNilaiMasuk, 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tabel Barang Keluar --}}
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white py-3">
            <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Barang Keluar</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th class="text-center" style="width:50px">No</th>
                            <th style="width:120px">Kategori</th>
                            <th>Nama Barang</th>
                            <th class="text-center" style="width:80px">Satuan</th>
                            <th class="text-end" style="width:120px">Harga (Rp)</th>
                            <th class="text-center" style="width:80px">Jumlah</th>
                            <th class="text-end" style="width:130px">Jumlah Harga</th>
                            <th>Spesifikasi</th>
                            <th style="width:150px">Keterangan</th>
                            <th class="text-center" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataKeluar as $i => $item)
                        <tr>
                            <th class="text-center">{{ $i + 1 }}</th>
                            <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                            <td class="fw-semibold">{{ $item->nama_barang }}</td>
                            <td class="text-center">{{ $item->satuan }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-danger stok-badge" data-jumlah="{{ (int)$item->jumlah }}">{{ $item->jumlah }}</span>
                            </td>
                            <td class="text-end fw-bold text-primary">{{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                            <td><small>{{ $item->spesifikasi ?? '-' }}</small></td>
                            <td><small>{{ $item->keterangan ?? '-' }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('ga.atk.riwayatItem', $item->id) }}"
                                    class="btn btn-sm btn-warning text-white" title="Kelola Transfer">
                                    <i class="fas fa-edit me-1"></i>
                                </a>
                                <form method="POST" action="{{ route('ga.atk.destroy', $item->id) }}" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox me-2"></i> Tidak ada barang keluar.
                            </td>
                        </tr>
                        @endforelse
                        @if($dataKeluar->count() > 0)
                        <tr hidden class="table-light fw-bold border-top border-primary">
                            <td colspan="6" class="text-end">Harga Total:</td>
                            <td class="text-end text-primary fs-5">Rp {{ number_format($totalNilaiKeluar, 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Hidden form untuk Generate PDF Stok Minimum --}}
<form id="formGeneratePdfStok" action="{{ route('ga.atk.exportPdfStok') }}" method="GET" target="_blank">
    <div id="hiddenCheckboxContainer"></div>
    <input type="hidden" name="threshold" id="hiddenThreshold" value="0">
</form>

{{-- Import Excel Modal --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Alat Tulis Kantor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ga.atk.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Upload Excel file sesuai format template.
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Pilih File Excel (.xlsx)</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx" required>
                        <div class="form-text">Max 10MB. <a href="{{ route('ga.atk.downloadTemplate') }}">Download template</a> terlebih dahulu.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const thresholdInput = document.getElementById('thresholdInput');
    const applyBtn       = document.getElementById('applyThreshold');
    const statusEl       = document.getElementById('thresholdStatus');

    let currentThreshold = null;

    function renderThreshold(threshold) {
        currentThreshold = threshold;
        let warningCount = 0;

        document.querySelectorAll('.stok-badge').forEach(function (badge) {
            const jumlah = parseInt(badge.getAttribute('data-jumlah'), 10);
            const existingWarning = badge.parentElement.querySelector('.stok-warning-icon');
            if (existingWarning) existingWarning.remove();

            if (threshold !== null && threshold > 0 && jumlah < threshold) {
                warningCount++;
                badge.classList.remove('bg-success', 'bg-danger', 'bg-secondary');
                badge.classList.add('bg-danger', 'stok-rendah');
                badge.style.animation = 'pulse-warning 1.2s infinite';

                const icon = document.createElement('span');
                icon.className = 'stok-warning-icon ms-1 text-danger';
                icon.title = 'Stok di bawah batas minimum (' + threshold + ')';
                icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                badge.parentElement.appendChild(icon);
                badge.closest('tr').classList.add('table-danger');
            } else {
                badge.classList.remove('stok-rendah');
                badge.style.animation = '';
                const isMasuk = badge.closest('table').querySelector('thead').classList.contains('bg-success');
                badge.classList.remove('bg-success', 'bg-danger', 'bg-secondary');
                badge.classList.add(isMasuk ? 'bg-success' : 'bg-danger');
                badge.closest('tr').classList.remove('table-danger');
            }
        });

        if (statusEl) {
            if (threshold === null || threshold === 0) {
                statusEl.innerHTML = '<span class="text-muted">Tidak ada batas minimum.</span>';
            } else if (warningCount > 0) {
                statusEl.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>' + warningCount + ' barang di bawah ' + threshold + '</span>';
            } else {
                statusEl.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Semua stok aman (&ge;' + threshold + ')</span>';
            }
        }
    }

    function applyThreshold() {
        const val = thresholdInput.value.trim();
        const threshold = val === '' ? 0 : parseInt(val, 10);
        renderThreshold(threshold > 0 ? threshold : null);
        // Simpan ke localStorage
        localStorage.setItem('atk_threshold', threshold);
    }

    // Muat dari localStorage
    const saved = localStorage.getItem('atk_threshold');
    if (saved && parseInt(saved) > 0) {
        thresholdInput.value = saved;
        renderThreshold(parseInt(saved));
    }

    applyBtn.addEventListener('click', applyThreshold);
    thresholdInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); applyThreshold(); }
    });
})();
</script>

<script>
(function () {
    const btnGenerate     = document.getElementById('btnGeneratePdfStok');
    const btnSelectLow    = document.getElementById('btnSelectAllLow');
    const btnClear        = document.getElementById('btnClearSelection');
    const countBadge      = document.getElementById('selectedCount');
    const hiddenContainer = document.getElementById('hiddenCheckboxContainer');
    const hiddenThreshold = document.getElementById('hiddenThreshold');
    const form            = document.getElementById('formGeneratePdfStok');
    const selectAllMasuk  = document.getElementById('selectAllMasuk');

    function getChecked() {
        return document.querySelectorAll('.atk-checkbox:checked');
    }

    function updateUI() {
        const count = getChecked().length;
        countBadge.textContent = count;
        btnGenerate.disabled = count === 0;
        syncSelectAll(selectAllMasuk);
    }

    function syncSelectAll(el) {
        if (!el) return;
        const table = el.closest('table');
        if (!table) return;
        const boxes = table.querySelectorAll('.atk-checkbox');
        if (boxes.length === 0) return;
        const allChecked  = Array.from(boxes).every(b => b.checked);
        const someChecked = Array.from(boxes).some(b => b.checked);
        el.checked = allChecked;
        el.indeterminate = !allChecked && someChecked;
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('atk-checkbox')) updateUI();
        if (e.target === selectAllMasuk) {
            const table = selectAllMasuk.closest('table');
            table.querySelectorAll('.atk-checkbox').forEach(cb => cb.checked = selectAllMasuk.checked);
            updateUI();
        }
    });

    btnSelectLow && btnSelectLow.addEventListener('click', function () {
        const saved = localStorage.getItem('atk_threshold');
        const threshold = saved ? parseInt(saved) : 0;
        if (!threshold || threshold <= 0) {
            alert('Silakan set Batas Minimum Stok terlebih dahulu, lalu klik tombol Set.');
            return;
        }
        document.querySelectorAll('.atk-checkbox').forEach(function (cb) {
            const jumlah = parseInt(cb.getAttribute('data-jumlah'), 10);
            cb.checked = jumlah < threshold;
        });
        updateUI();
    });

    btnClear && btnClear.addEventListener('click', function () {
        document.querySelectorAll('.atk-checkbox').forEach(cb => cb.checked = false);
        if (selectAllMasuk) selectAllMasuk.checked = false;
        updateUI();
    });

    btnGenerate && btnGenerate.addEventListener('click', function () {
        const checked = getChecked();
        if (checked.length === 0) return;
        const threshold = localStorage.getItem('atk_threshold') || 0;

        hiddenContainer.innerHTML = '';
        checked.forEach(function (cb) {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'atk_selected[]';
            input.value = cb.value;
            hiddenContainer.appendChild(input);
        });
        hiddenThreshold.value = threshold;
        form.submit();
    });

    updateUI();
})();
</script>

<style>
@keyframes pulse-warning {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.75; transform: scale(1.1); }
}
.stok-rendah { font-weight: bold; }
</style>
@endpush
