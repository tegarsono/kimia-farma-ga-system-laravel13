@extends('layouts.ga')
@section('title','Riwayat Transaksi ATK')
@section('page-title','Riwayat Transaksi ATK')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-history me-2 text-info"></i>Riwayat Barang Keluar</span>
        <a href="{{ route('ga.atk.index') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua Bulan</option>
                    @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $idx => $bln)
                    <option value="{{ $idx+1 }}" {{ request('bulan')==$idx+1?'selected':'' }}>{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @for($y=date('Y');$y>=2020;$y--)
                    <option value="{{ $y }}" {{ request('tahun')==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('ga.atk.riwayat') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Tanggal</th><th>Kode</th><th>Nama Barang</th><th>Jenis</th><th>Qty Keluar</th><th>Satuan</th><th>Keterangan</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($transaksi as $i => $t)
                <tr>
                    <td>{{ $transaksi->firstItem()+$i }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td><code>{{ $t->kode ?? '-' }}</code></td>
                    <td>{{ $t->nama_barang ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $t->jenis }}</span></td>
                    <td><strong class="text-danger">{{ $t->jumlah }}</strong></td>
                    <td>{{ $t->satuan ?? '-' }}</td>
                    <td>{{ $t->keterangan ?: '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('ga.atk.deleteTransaksi',$t->id) }}" class="d-inline" onsubmit="return confirm('Hapus riwayat ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada riwayat transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $transaksi->links() }}</div>
</div>
@endsection
