@extends('layouts.ac')
@section('title','Notification Maintenance')
@section('page-title','Notification Items Need Attention')

@section('content')
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-exclamation-triangle fs-5"></i>
<div>Item berikut memiliki status <strong>Wajib Service</strong> atau belum dilakukan servis selama lebih dari <strong>3 bulan</strong>.</div>
</div>

{{-- Bulk Action Bar --}}
<div id="bulkActionBar" class="card mb-3 border-primary d-none">
    <div class="card-body py-2 px-3 d-flex align-items-center gap-3 flex-wrap">
        <span class="fw-semibold text-primary">
            <i class="fas fa-check-square me-1"></i>
<span id="selectedCount">0</span> items selected
        </span>
        <div class="d-flex gap-2 ms-auto flex-wrap">
            {{-- Tandai Sudah Diservice --}}
            <form id="formBulkService" method="POST" action="{{ route('ac.notifikasi.bulkService') }}">
                @csrf
                <div id="hiddenServiceIds"></div>
                <button type="submit" class="btn btn-success btn-sm"
                        onclick="return confirmBulkService()">
<i class="fas fa-tools me-1"></i>Tandai sebagai Sudah Diservis
                </button>
            </form>
            {{-- Export PDF Selected --}}
            <form id="formBulkPdf" method="POST" action="{{ route('ac.generatePdfSelected') }}">
                @csrf
                <div id="hiddenPdfIds"></div>
                <button type="submit" class="btn btn-danger btn-sm">
<i class="fas fa-file-pdf me-1"></i>Ekspor PDF
                </button>
            </form>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
Batalkan Pemilihan
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex gap-2 align-items-center flex-wrap">
        <i class="fas fa-bell text-warning"></i>
<span>Daftar Item yang Perlu Perhatian ({{ $notifikasi->total() }} item)</span>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-light" onclick="selectAll()">
Pilih Semua
            </button>
            <a href="{{ route('ac.index') }}" class="btn btn-sm btn-outline-light">
Back
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:42px">
                        <input type="checkbox" id="checkAll" class="form-check-input"
                               title="Pilih semua di halaman ini" onchange="toggleAll(this)">
                    </th>
                    <th>#</th>
                    <th>Kode GA</th>
                    <th>Lokasi</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Perawatan Terakhir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifikasi as $i => $item)
                @php
                    $isOld    = \Carbon\Carbon::parse($item->tgl_perawatan_terakhir)->lt(now()->subMonths(3));
                    $diffText = \Carbon\Carbon::parse($item->tgl_perawatan_terakhir)->diffForHumans();
                @endphp
                <tr class="{{ $item->status === 'Wajib Service' ? 'table-warning' : 'table-danger' }}"
                    id="row-{{ $item->id }}">
                    <td>
                        <input type="checkbox" class="form-check-input row-check"
                               value="{{ $item->id }}" onchange="onRowCheck()">
                    </td>
                    <td>{{ $notifikasi->firstItem() + $i }}</td>
                    <td><code>{{ $item->kode_ga ?? '-' }}</code></td>
                    <td>{{ $item->lokasi }}</td>
                    <td><strong>{{ $item->nama_barang }}</strong></td>
                    <td><span class="badge bg-info text-dark">{{ $item->jenis_barang }}</span></td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tgl_perawatan_terakhir)->format('d/m/Y') }}
                        <br><small class="text-danger">{{ $diffText }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $item->status === 'Normal' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('ac.edit', $item->id) }}" class="btn btn-sm btn-primary py-0 px-2">
                            <i class="fas fa-wrench me-1"></i>Update
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-check-circle text-success fs-4 d-block mb-2"></i>
                        Semua item dalam kondisi baik!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $notifikasi->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    // ── State ──────────────────────────────────────────────────────────────────
    // Simpan ID yang dipilih lintas halaman (opsional, saat ini per-halaman)
    let selectedIds = new Set();

    // ── Helpers ────────────────────────────────────────────────────────────────
    function renderHiddenInputs(containerId, ids) {
        const container = document.getElementById(containerId);
        container.innerHTML = ids.map(id =>
            `<input type="hidden" name="selected_ids[]" value="${id}">`
        ).join('');
    }

    function syncHiddenInputs() {
        const ids = [...selectedIds];

        renderHiddenInputs('hiddenServiceIds', ids);
        renderHiddenInputs('hiddenPdfIds', ids);
    }

    function updateBulkBar() {
        const bar = document.getElementById('bulkActionBar');
        const count = document.getElementById('selectedCount');

        count.textContent = selectedIds.size;
        bar.classList.toggle('d-none', selectedIds.size === 0);

        syncHiddenInputs();
    }


    // ── Event Handlers ─────────────────────────────────────────────────────────
    function onRowCheck() {
        document.querySelectorAll('.row-check').forEach(cb => {
            if (cb.checked) selectedIds.add(cb.value);
            else            selectedIds.delete(cb.value);
        });
        // Sinkronkan checkAll
        const all  = document.querySelectorAll('.row-check');
        const chk  = document.querySelectorAll('.row-check:checked');
        document.getElementById('checkAll').checked       = all.length > 0 && chk.length === all.length;
        document.getElementById('checkAll').indeterminate = chk.length > 0 && chk.length < all.length;
        updateBulkBar();
    }

    function toggleAll(master) {
        document.querySelectorAll('.row-check').forEach(cb => {
            cb.checked = master.checked;
            if (master.checked) selectedIds.add(cb.value);
            else                selectedIds.delete(cb.value);
        });
        updateBulkBar();
    }

    function selectAll() {
        document.querySelectorAll('.row-check').forEach(cb => {
            cb.checked = true;
            selectedIds.add(cb.value);
        });
        document.getElementById('checkAll').checked       = true;
        document.getElementById('checkAll').indeterminate = false;
        updateBulkBar();
    }

    function clearSelection() {
        selectedIds.clear();
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
        document.getElementById('checkAll').checked       = false;
        document.getElementById('checkAll').indeterminate = false;
        updateBulkBar();
    }

    function confirmBulkService() {
        if (selectedIds.size === 0) {
            alert('Pilih minimal satu item terlebih dahulu.');
            return false;
        }
        return confirm(
            `Tandai ${selectedIds.size} item sebagai sudah diservice?\n\n` +
            `Status akan diubah ke "Normal" dan tanggal perawatan diperbarui ke hari ini.`
        );
    }

    // Highlight baris yang dipilih
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-check')) {
            const row = e.target.closest('tr');
            if (e.target.checked) row.classList.add('table-active');
            else                  row.classList.remove('table-active');
        }
    });
</script>
@endpush
