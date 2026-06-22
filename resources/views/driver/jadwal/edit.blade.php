@extends('layouts.driver')
@section('title','Edit Jadwal Driver')
@section('page-title','Edit Jadwal Driver')

@section('content')
<div class="row justify-content-center"><div class="col-12 col-md-8">
<div class="card">
    <div class="card-header"><i class="fas fa-edit me-2 text-warning"></i>Edit Jadwal</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('driver.jadwal.update', $jadwal->id_jadwal) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Supir <span class="text-danger">*</span></label>
                    <select name="id_supir" class="form-select" required>
                        @foreach($supir as $s)
                        <option value="{{ $s->id_supir }}" {{ old('id_supir',$jadwal->id_supir)==$s->id_supir?'selected':'' }}>
                            {{ $s->nama_supir }} ({{ ucfirst($s->status) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Armada Mobil <span class="text-danger">*</span></label>
                    <select name="id_mobil" class="form-select" required>
                        @foreach($mobil as $m)
                        <option value="{{ $m->id_mobil }}" {{ old('id_mobil',$jadwal->id_mobil)==$m->id_mobil?'selected':'' }}>
                            {{ $m->merk }} — {{ $m->plat_nomor }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Tugas <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_tugas" class="form-control" value="{{ old('tanggal_tugas',$jadwal->tanggal_tugas) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai',$jadwal->jam_mulai) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai',$jadwal->jam_selesai) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Penumpang / Pemohon <span class="text-danger">*</span></label>
                    <input type="text" name="penumpang" class="form-control" value="{{ old('penumpang',$jadwal->penumpang) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Tujuan <span class="text-danger">*</span></label>
                    <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan',$jadwal->tujuan) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Keperluan <span class="text-danger">*</span></label>
                    <textarea name="keperluan" class="form-control" rows="3">{{ old('keperluan',$jadwal->keperluan) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Perbarui</button>
                <a href="{{ route('driver.jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
