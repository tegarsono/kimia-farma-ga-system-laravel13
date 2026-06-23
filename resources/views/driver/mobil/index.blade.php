@extends('layouts.driver')
@section('title','Data Armada Mobil')
@section('page-title','Data Armada Mobil')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-truck me-2 text-warning"></i>Data Armada Mobil</span>
        <a href="{{ route('driver.mobil.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i class="fas fa-plus me-1"></i>Tambah Mobil</a>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari merk, plat nomor..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Cari</button>
                <a href="{{ route('driver.mobil.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Merk / Nama</th><th>Plat Nomor</th><th>Tipe Mobil</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($mobil as $i => $m)
                <tr>
                    <td>{{ $mobil->firstItem() + $i }}</td>
                    <td><strong>{{ $m->merk }}</strong></td>
                    <td><span class="badge bg-dark fs-6 px-3">{{ $m->plat_nomor }}</span></td>
                    <td>{{ $m->tipe_mobil }}</td>
                    <td>
                        <a href="{{ route('driver.mobil.edit', $m->id_mobil) }}" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('driver.mobil.destroy', $m->id_mobil) }}" class="d-inline"
                            onsubmit="return confirm('Hapus data mobil ini?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data armada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $mobil->links() }}</div>
</div>
@endsection
