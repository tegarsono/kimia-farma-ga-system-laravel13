@extends('layouts.driver')
@section('title','Tambah Supir')
@section('page-title','Tambah Data Supir')

@section('content')
<div class="row justify-content-center"><div class="col-12 col-md-6">
<div class="card">
    <div class="card-header"><i class="fas fa-plus-circle me-2 text-primary"></i>Form Tambah Supir</div>
    <div class="card-body">
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('driver.supir.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Supir <span class="text-danger">*</span></label>
                <input type="text" name="nama_supir" class="form-control" value="{{ old('nama_supir') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="idle"   {{ old('status')=='idle'?'selected':'' }}>Idle (Siap bertugas)</option>
                    <option value="aktif"  {{ old('status')=='aktif'?'selected':'' }}>Aktif (Sedang bertugas)</option>
                    <option value="offline"{{ old('status')=='offline'?'selected':'' }}>Offline (Tidak tersedia)</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                <a href="{{ route('driver.supir.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
