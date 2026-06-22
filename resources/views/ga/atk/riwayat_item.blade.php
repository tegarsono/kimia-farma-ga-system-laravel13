@extends('layouts.ga')
@section('title','Riwayat Transfer ATK')
@section('page-title','Riwayat Transfer ATK')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-list me-2"></i>Riwayat Transfer: {{ $barang->nama_barang }}</h2>
            <p class="text-muted">
                Total Keluar katalog: <strong>{{ $barang->jumlah }} {{ $barang->satuan }}</strong>
                | ∑Transaksi: <strong>{{ $totalSum }}</strong>
                @if($sumMatch)
                    <span class="badge bg-success">OK</span>
                @else
                    <span class="badge bg-danger">MISMATCH!</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ga.atk.barangKeluarForm') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Tambah Transfer
            </a>
            <a href="{{ route('ga.atk.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(!$sumMatch)
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Inconsistency detected!</strong> Total transaksi ({{ $totalSum }}) != katalog jumlah ({{ $barang->jumlah }}).
    </div>
    @endif

    @if($transaksi->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-info-circle me-1"></i> Belum ada transaksi transfer untuk barang ini.
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan (Unit)</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi as $trans)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($trans->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $trans->keterangan }}</td>
                            <td class="text-center fw-bold">{{ $trans->jumlah }}</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('ga.atk.deleteTransaksi', $trans->id) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus transfer ini? Stok Masuk akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td class="text-center">{{ $totalSum }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
