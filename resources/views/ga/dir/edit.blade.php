@extends('layouts.ga')
@section('title', 'Edit DIR')
@section('page-title', 'Edit Data DIR')

@section('content')

    <div class="card">
        <div class="card-header"><i class="fas fa-edit me-2 text-warning"></i>Edit DIR</div>
        <div class="card-body">
            <form method="POST" action="{{ route('ga.dir.update', $item->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">COST CENTER <span class="text-danger">*</span></label>
                        <input type="text" name="cost_center" class="form-control"
                            value="{{ old('cost_center', $item->cost_center) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">PROFIT CENTER <span class="text-danger">*</span></label>
                        <input type="text" name="profit_center" class="form-control"
                            value="{{ old('profit_center', $item->profit_center) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">UNIT BISNIS <span class="text-danger">*</span></label>
                        <input type="text" name="unit_bisnis" class="form-control"
                            list="ub-list-edit"
                            value="{{ old('unit_bisnis', $item->unit_bisnis) }}"
                            placeholder="Ketik atau pilih dari daftar..."
                            autocomplete="off" required>
                        <datalist id="ub-list-edit">
                            @foreach($unitBisnisList as $ub)
                                <option value="{{ $ub }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">GOLONGAN ASET <span class="text-danger">*</span></label>
                        <input type="text" name="golongan_aset" class="form-control"
                            value="{{ old('golongan_aset', $item->golongan_aset) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">KATEGORI ASET <span class="text-danger">*</span></label>
                        <input type="text" name="kategori_aset" class="form-control"
                            value="{{ old('kategori_aset', $item->kategori_aset) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">DESKRIPSI ASET <span class="text-danger">*</span></label>
                        <input type="text" name="deskripsi_aset" class="form-control"
                            value="{{ old('deskripsi_aset', $item->deskripsi_aset) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">LOKASI / PEMAKAI <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_pemakai" class="form-control"
                            value="{{ old('lokasi_pemakai', $item->lokasi_pemakai) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">KODE ASET <span class="text-danger">*</span></label>
                        <input type="text" name="kode_aset" class="form-control"
                            value="{{ old('kode_aset', $item->kode_aset) }}" required>
                    </div>
                    <div class="col-md-3">
                        {{-- ID ASET penanda (tidak bisa diubah) --}}
                        <label class="form-label fw-semibold">ID ASET</label>
                        <input type="text" class="form-control" value="{{ $item->id_aset }}" readonly>
                    </div>

                    <div class="col-md-12">

                        <label class="form-label fw-semibold">KETERANGAN</label>
                        <input type="text" name="keterangan" class="form-control"
                            value="{{ old('keterangan', $item->keterangan) }}">
                    </div>

                </div>

                @if(isset($errors) && is_object($errors) && $errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="d-flex justify-content-end pt-4 mt-3 border-top gap-2">
                    <a href="{{ route('ga.dir.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection