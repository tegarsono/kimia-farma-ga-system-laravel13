@extends('layouts.ga')
@section('title', 'Edit Kendaraan')
@section('page-title', 'Edit Aset Kendaraan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header"><i class="fas fa-edit me-2 text-warning"></i>Edit Data Kendaraan —
                    {{ $kendaraan->no_posisi }}</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ga.kendaraan.update', $kendaraan->id) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">No Posisi <span class="text-danger">*</span></label>
                                <input type="text" name="no_posisi" class="form-control"
                                    value="{{ old('no_posisi', $kendaraan->no_posisi) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Bisnis Manager <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="branch_manager" class="form-control"
                                    list="bm-list-kend-edit"
                                    value="{{ old('branch_manager', $kendaraan->branch_manager) }}"
                                    placeholder="Ketik atau pilih dari daftar..."
                                    autocomplete="off" required>
                                <datalist id="bm-list-kend-edit">
                                    @foreach($branchManagerList as $bm)
                                        <option value="{{ $bm }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Kendaraan <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_kendaraan" class="form-select" required>
                                    @foreach($jenisKendaraanList as $j)
                                        <option value="{{ $j }}" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == $j ? 'selected' : '' }}>{{ $j }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Merk <span class="text-danger">*</span></label>
                                <input type="text" name="merk" class="form-control"
                                    value="{{ old('merk', $kendaraan->merk) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type</label>
                                <input type="text" name="type" class="form-control"
                                    value="{{ old('type', $kendaraan->type) }}">
                            </div>
                            <div class="col-md-3"><label class="form-label fw-semibold">Warna</label><input type="text"
                                    name="warna" class="form-control" value="{{ old('warna', $kendaraan->warna) }}"></div>
                            <div class="col-md-3"><label class="form-label fw-semibold">Tahun Pembuatan</label><input
                                    type="number" name="tahun_pembuatan" class="form-control"
                                    value="{{ old('tahun_pembuatan', $kendaraan->tahun_pembuatan) }}" min="1900"
                                    max="{{ date('Y') }}"></div>
                            <div class="col-md-3"><label class="form-label fw-semibold">No Polisi</label><input type="text"
                                    name="no_polisi" class="form-control"
                                    value="{{ old('no_polisi', $kendaraan->no_polisi) }}"></div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach($statusList as $s)<option value="{{ $s }}" {{ old('status', $kendaraan->status) == $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label fw-semibold">No Mesin</label><input type="text"
                                    name="no_mesin" class="form-control" value="{{ old('no_mesin', $kendaraan->no_mesin) }}">
                            </div>
                            <div class="col-md-4"><label class="form-label fw-semibold">No Rangka</label><input type="text"
                                    name="no_rangka" class="form-control"
                                    value="{{ old('no_rangka', $kendaraan->no_rangka) }}"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">No BPKB</label><input type="text"
                                    name="no_bpkb" class="form-control" value="{{ old('no_bpkb', $kendaraan->no_bpkb) }}">
                            </div>
                            <div class="col-md-4"><label class="form-label fw-semibold">Masa Berakhir Pajak (1
                                    Th)</label><input type="date" name="masa_berakhir_1th" class="form-control"
                                    value="{{ old('masa_berakhir_1th', $kendaraan->masa_berakhir_1th) }}"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">Masa Berakhir STNK (5
                                    Th)</label><input type="date" name="masa_berakhir_5th" class="form-control"
                                    value="{{ old('masa_berakhir_5th', $kendaraan->masa_berakhir_5th) }}"></div>
                            <div class="col-md-2"><label class="form-label fw-semibold">Tahun Perolehan</label><input
                                    type="number" name="tahun_perolehan" class="form-control"
                                    value="{{ old('tahun_perolehan', $kendaraan->tahun_perolehan) }}" min="1900"
                                    max="{{ date('Y') }}"></div>
                            <div class="col-md-2"><label class="form-label fw-semibold">Harga Perolehan</label><input
                                    type="number" name="harga_perolehan" class="form-control"
                                    value="{{ old('harga_perolehan', $kendaraan->harga_perolehan) }}" min="0"></div>
                            <div class="col-12"><label class="form-label fw-semibold">Keterangan</label><textarea
                                    name="keterangan" class="form-control"
                                    rows="3">{{ old('keterangan', $kendaraan->keterangan) }}</textarea></div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Perbarui</button>
                            <a href="{{ route('ga.kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection