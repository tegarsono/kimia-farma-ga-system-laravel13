@extends('layouts.driver')
@section('title','Tambah Mobil')
@section('page-title','Tambah Data Armada Mobil')

@section('content')
<div class="row justify-content-center"><div class="col-12 col-md-6">
<div class="card">
    <div class="card-header"><i class="fas fa-plus-circle me-2 text-primary"></i>Form Tambah Mobil</div>
    <div class="card-body">
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('driver.mobil.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Merk / Nama Mobil <span class="text-danger">*</span></label>
                <input type="text" name="merk" class="form-control" value="{{ old('merk') }}"
                    placeholder="cth: Toyota Innova" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Plat Nomor <span class="text-danger">*</span></label>
                <input type="text" name="plat_nomor" class="form-control" value="{{ old('plat_nomor') }}"
                    placeholder="cth: B 1234 KFA" style="text-transform:uppercase" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tipe Mobil <span class="text-danger">*</span></label>
                <input type="text" name="tipe_mobil" class="form-control" list="tipeList"
                    value="{{ old('tipe_mobil') }}" required>
                <datalist id="tipeList">
                    <option value="MPV"><option value="SUV"><option value="Sedan">
                    <option value="Pickup"><option value="Box"><option value="Minibus">
                </datalist>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                <a href="{{ route('driver.mobil.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
