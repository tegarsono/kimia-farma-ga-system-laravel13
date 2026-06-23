@extends('layouts.ac')
@section('title', 'Tambah Data Monitoring')
@section('page-title', 'Tambah Data Monitoring Maintenance')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                Form Data Monitoring (Tambah)
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('ac.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            Kode GA

                            <input type="text" name="kode_ga" class="form-control" value="{{ old('kode_ga') }}"
                                        placeholder="contoh: GA-001">
                        </div>
                        <div class="col-md-8">
                            Lokasi

                            <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}"
                                    placeholder="contoh: Director Room Lt.2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}"
                                placeholder="contoh: AC Split 1PK Daikin" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Barang <span class="text-danger">*</span></label>
                            <input type="text" name="jenis_barang" class="form-control" list="jenisList"
                                value="{{ old('jenis_barang') }}" required>
                            <datalist id="jenisList">
                                <option value="AC">
                                <option value="Genset">
                                <option value="Lift">
                                <option value="Pompa Air">
                                <option value="Panel Listrik">
                                <option value="CCTV">
                                <option value="Komputer">
                                <option value="Printer">
                                    @foreach($jenisBarangList as $j)
                                    <option value="{{ $j }}">@endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Perawatan Terakhir <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tgl_perawatan_terakhir" id="tgl_perawatan_terakhir"
                                class="form-control" value="{{ old('tgl_perawatan_terakhir', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Normal" {{ old('status') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Wajib Service" {{ old('status') == 'Wajib Service' ? 'selected' : '' }}>
                                    Wajib
                                    Service
                            </select>
                            {{-- Show warning if status is Normal but the last date is > 3 months ago --}}
                            <div id="statusWarning" class="alert alert-warning py-2 px-3 mt-2 mb-0 d-none" role="alert">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Tanggal perawatan terakhir lebih dari 3 bulan yang lalu.
                                Status <strong>Normal</strong> hanya valid jika servis benar-benar telah dilakukan.
                                Pastikan tanggal di atas adalah tanggal servis yang sesungguhnya.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="keterangan" class="form-control" rows="3"
                                placeholder="Catatan tentang kondisi, riwayat, atau informasi tambahan...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                        <a href="{{ route('ac.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const statusEl = document.getElementById('status');
            const tglEl = document.getElementById('tgl_perawatan_terakhir');
            const warning = document.getElementById('statusWarning');

            function isOlderThan3Months(dateStr) {
                if (!dateStr) return false;
                const tgl = new Date(dateStr);
                const batas = new Date();
                batas.setMonth(batas.getMonth() - 3);
                return tgl < batas;
            }

            function checkWarning() {
                const showWarning = statusEl.value === 'Normal' && isOlderThan3Months(tglEl.value);
                warning.classList.toggle('d-none', !showWarning);
            }

            statusEl.addEventListener('change', checkWarning);
            tglEl.addEventListener('change', checkWarning);

            // Cek saat halaman pertama kali dimuat
            checkWarning();
        })();
    </script>
@endpush