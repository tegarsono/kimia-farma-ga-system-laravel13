@extends('layouts.ga')
@section('title', 'Tambah Aset Tanah & Bangunan')
@section('page-title', 'Tambah Aset Tanah & Bangunan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2 text-primary"></i>Form Tambah Tanah &amp;
                    Bangunan</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>@endif
                    <form method="POST" action="{{ route('ga.tanah_bangunan.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label fw-semibold">Kode SAP</label><input type="text"
                                    name="kode_sap" class="form-control" value="{{ old('kode_sap') }}"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">No Asset Tanah <span
                                        class="text-danger">*</span></label><input type="text" name="no_asset_tanah"
                                    class="form-control" value="{{ old('no_asset_tanah') }}" required></div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bisnis Manager <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="branch_manager" class="form-control"
                                    list="bm-list-tb-create"
                                    value="{{ old('branch_manager') }}"
                                    placeholder="Ketik atau pilih dari daftar..."
                                    autocomplete="off" required>
                                <datalist id="bm-list-tb-create">
                                    @foreach($branchManagerList as $bm)
                                        <option value="{{ $bm }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-4"><label class="form-label fw-semibold">Digunakan Sebagai</label><input
                                    type="text" name="digunakan_sebagai" class="form-control"
                                    value="{{ old('digunakan_sebagai') }}"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">Penggunaan</label><input type="text"
                                    name="penggunaan" class="form-control" value="{{ old('penggunaan') }}"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">No Posisi Gedung</label><input
                                    type="text" name="no_posisi_gedung" class="form-control"
                                    value="{{ old('no_posisi_gedung') }}"></div>
                            <div class="col-12"><label class="form-label fw-semibold">Alamat <span
                                        class="text-danger">*</span></label><textarea name="alamat" class="form-control"
                                    rows="2" required>{{ old('alamat') }}</textarea></div>
                            <div class="col-md-3"><label class="form-label fw-semibold">Luas Tanah (m²) <span
                                        class="text-danger">*</span></label><input type="number" name="luas_tanah"
                                    class="form-control" value="{{ old('luas_tanah') }}" min="0" step="0.01" required></div>
                            <div class="col-md-3"><label class="form-label fw-semibold">Luas Bangunan (m²)</label><input
                                    type="number" name="luas_bangunan" class="form-control"
                                    value="{{ old('luas_bangunan') }}" min="0" step="0.01"></div>
                            <div class="col-md-3"><label class="form-label fw-semibold">Tahun Perolehan</label><input
                                    type="number" name="tahun_perolehan" class="form-control"
                                    value="{{ old('tahun_perolehan') }}" min="1900" max="{{ date('Y') }}"></div>
                            <div class="col-md-3"><label class="form-label fw-semibold">Masa Berlaku
                                    Sertifikat</label><input type="date" name="masa_berlaku" class="form-control"
                                    value="{{ old('masa_berlaku') }}"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">No Sertifikat Baru <span
                                        class="text-danger">*</span></label><input type="text" name="nomor_sertifikat_baru"
                                    class="form-control" value="{{ old('nomor_sertifikat_baru') }}" required></div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    @foreach($statusList as $s)<option value="{{ $s }}" {{ old('status') == $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-12"><label class="form-label fw-semibold">Keterangan</label><textarea
                                    name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                            <a href="{{ route('ga.tanah_bangunan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection