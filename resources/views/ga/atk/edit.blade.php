@extends('layouts.ga')
@section('title','Edit Item ATK')
@section('page-title','Edit Item ATK')

@section('content')
<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-edit me-2"></i>Edit Item ATK</h2>
            <p class="text-muted mb-0">Perbarui detail: <span class="fw-bold text-primary">{{ $atk->nama_barang }}</span></p>
        </div>
        <a href="{{ route('ga.atk.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Form --}}
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Formulir Pembaruan Data</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('ga.atk.update', $atk->id) }}">
                @csrf @method('PUT')
                {{-- Status (hidden, pertahankan nilai lama) --}}
                <input type="hidden" name="status_barang" value="{{ $atk->status_barang }}">

                <div class="row g-3">
                    {{-- Kategori --}}
                    <div class="col-md-6">
                        <label for="kategori" class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="" disabled>Pilih Kategori</option>
                            @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ old('kategori', $atk->kategori) == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nama Barang --}}
                    <div class="col-md-6">
                        <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                            placeholder="Contoh: Kertas HVS A4 80gsm"
                            value="{{ old('nama_barang', $atk->nama_barang) }}" required>
                    </div>

                    {{-- Satuan --}}
                    <div class="col-md-4">
                        <label for="satuan" class="form-label">Satuan Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="satuan" name="satuan" required>
                            <option value="" disabled>Pilih Satuan</option>
                            @foreach($satuanList as $s)
                            <option value="{{ $s }}" {{ old('satuan', $atk->satuan) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Harga --}}
                    <div class="col-md-4">
                        <label for="harga" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="harga" name="harga"
                                min="1" placeholder="0" value="{{ old('harga', $atk->harga) }}" required>
                        </div>
                    </div>

                    {{-- Jumlah --}}
                    <div class="col-md-4">
                        <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah"
                            min="1" placeholder="0" value="{{ old('jumlah', $atk->jumlah) }}" required>
                    </div>

                    {{-- Spesifikasi --}}
                    <div class="col-md-6">
                        <label for="spesifikasi" class="form-label">Spesifikasi / Deskripsi</label>
                        <textarea class="form-control" id="spesifikasi" name="spesifikasi" rows="3"
                            placeholder="Contoh: Ukuran A4, 80 gram, 500 lembar/rim">{{ old('spesifikasi', $atk->spesifikasi) }}</textarea>
                    </div>

                    {{-- Keterangan --}}
                    <div class="col-md-6">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                            placeholder="Masukkan keterangan tambahan jika ada">{{ old('keterangan', $atk->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-end pt-4 mt-3 border-top">
                    <a href="{{ route('ga.atk.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-sync me-1"></i> Perbarui Data ATK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
