@extends('layouts.ga')
@section('title','Barang Keluar ATK')
@section('page-title','Barang Keluar ATK')

@section('content')
<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-arrow-right me-2"></i>Barang Keluar</h2>
            <p class="text-muted mb-0">Transfer barang dari stok masuk ke unit tertentu</p>
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
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Formulir Barang Keluar</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('ga.atk.barangKeluarStore') }}">
                @csrf
                <div class="row g-3">
                    {{-- Pilih Barang --}}
                    <div class="col-md-8">
                        <label for="id_barang" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_barang" name="id_barang" required onchange="updateStokInfo()">
                            <option value="" disabled selected>Pilih Barang dari Stok Masuk</option>
                            @foreach($dataMasuk as $barang)
                            <option value="{{ $barang->id }}"
                                data-stok="{{ $barang->jumlah }}"
                                data-satuan="{{ $barang->satuan }}"
                                {{ old('id_barang') == $barang->id ? 'selected' : '' }}>
                                {{ $barang->nama_barang }} - Stok: {{ $barang->jumlah }} {{ $barang->satuan }}
                                ({{ $barang->kategori }}){{ $barang->keterangan ? ' - ' . $barang->keterangan : '' }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih barang yang akan ditransfer ke barang keluar.</div>
                    </div>

                    {{-- Jumlah --}}
                    <div class="col-md-4">
                        <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah"
                            min="1" placeholder="0" value="{{ old('jumlah') }}" required>
                        <div class="form-text">Jumlah tidak boleh melebihi stok tersedia.</div>
                    </div>

                    {{-- Info Stok --}}
                    <div class="col-12">
                        <div class="alert alert-info mb-0" id="stok_info" style="display: none;">
                            <i class="fas fa-info-circle me-1"></i>
                            Stok tersedia: <strong id="stok_tersedia">0</strong> <span id="satuan_stok"></span>
                        </div>
                    </div>

                    {{-- Unit Tujuan --}}
                    <div class="col-md-12">
                        <label for="keterangan" class="form-label">Unit Tujuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan"
                            placeholder="Contoh: Aset Management, Keuangan, HRD, dll."
                            value="{{ old('keterangan') }}" required>
                        <div class="form-text">Masukkan nama unit atau departemen tujuan transfer.</div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-end pt-4 mt-3 border-top">
                    <a href="{{ route('ga.atk.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-1"></i> Simpan Barang Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStokInfo() {
    const select = document.getElementById('id_barang');
    const selectedOption = select.options[select.selectedIndex];
    const stokInfo = document.getElementById('stok_info');
    const stokTersedia = document.getElementById('stok_tersedia');
    const satuanStok = document.getElementById('satuan_stok');
    const jumlahInput = document.getElementById('jumlah');

    if (selectedOption.value) {
        const stok = selectedOption.getAttribute('data-stok');
        const satuan = selectedOption.getAttribute('data-satuan');
        stokTersedia.textContent = stok;
        satuanStok.textContent = satuan;
        stokInfo.style.display = 'block';
        jumlahInput.max = stok;
    } else {
        stokInfo.style.display = 'none';
        jumlahInput.removeAttribute('max');
    }
}

document.querySelector('form').addEventListener('submit', function (e) {
    const select = document.getElementById('id_barang');
    const selectedOption = select.options[select.selectedIndex];
    const jumlah = parseInt(document.getElementById('jumlah').value);

    if (selectedOption.value) {
        const stok = parseInt(selectedOption.getAttribute('data-stok'));
        if (jumlah > stok) {
            e.preventDefault();
            alert('Jumlah yang diminta (' + jumlah + ') melebihi stok tersedia (' + stok + ')');
        }
    }
});

// Trigger jika ada old value
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('id_barang').value) updateStokInfo();
});
</script>
@endpush
