@extends('layouts.ga')
@section('title', 'Biaya ATK')
@section('page-title', 'Ringkasan Biaya ATK')

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card text-center py-3">
            <div class="text-success fs-4"><i class="fas fa-arrow-down"></i></div>
            <div class="fw-bold fs-5">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Total Nilai Barang Masuk</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card text-center py-3">
            <div class="text-danger fs-4"><i class="fas fa-arrow-up"></i></div>
            <div class="fw-bold fs-5">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Total Nilai Barang Keluar</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card text-center py-3">
            <div class="text-primary fs-4"><i class="fas fa-receipt"></i></div>
            <div class="fw-bold fs-5">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Grand Total Keseluruhan</div>
        </div>
    </div>
</div>

{{-- Ringkasan per Kategori --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-layer-group me-2"></i>Ringkasan Nilai per Kategori
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="text-end">Nilai Masuk (Rp)</th>
                    <th class="text-end">Nilai Keluar (Rp)</th>
                    <th class="text-end">Total Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perKategori as $kategori => $rows)
                @php
                    $nilaiMasuk  = $rows->where('status_barang', 'Masuk')->sum('total_nilai');
                    $nilaiKeluar = $rows->where('status_barang', '!=', 'Masuk')->sum('total_nilai');
                @endphp
                <tr>
                    <td><span class="badge bg-secondary">{{ $kategori }}</span></td>
                    <td class="text-end text-success fw-semibold">{{ number_format($nilaiMasuk, 0, ',', '.') }}</td>
                    <td class="text-end text-danger fw-semibold">{{ number_format($nilaiKeluar, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($nilaiMasuk + $nilaiKeluar, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data ATK.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Detail per Barang --}}
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-box me-2"></i>Detail Nilai per Barang</span>
        <a href="{{ route('ga.atk.index') }}" class="btn btn-sm btn-outline-light">
            <i class="fas fa-external-link-alt me-1"></i>Kelola ATK
        </a>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama barang, kode, kategori..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $k)
                    <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status_barang" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Masuk"  {{ request('status_barang') == 'Masuk'  ? 'selected' : '' }}>Masuk</option>
                    <option value="Keluar" {{ request('status_barang') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('ga.biaya.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Kategori</th>
                    <th>Nama Barang</th>
                    <th>Status</th>
                    <th class="text-end">Harga Satuan (Rp)</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Total Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($atk as $i => $item)
                <tr>
                    <td>{{ $atk->firstItem() + $i }}</td>
                    <td><code>{{ $item->kode }}</code></td>
                    <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>
                        <span class="badge {{ $item->status_barang == 'Masuk' ? 'bg-success' : 'bg-danger' }}">
                            {{ $item->status_barang }}
                        </span>
                    </td>
                    <td class="text-end">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-end">{{ $item->jumlah }}</td>
                    <td class="text-end fw-semibold">{{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $atk->links() }}</div>
</div>

@endsection
