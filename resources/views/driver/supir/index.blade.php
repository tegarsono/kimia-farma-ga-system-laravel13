@extends('layouts.driver')
@section('title','Data Supir')
@section('page-title','Data Supir Operasional')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="fas fa-id-card me-2 text-info"></i>Data Supir</span>
        <a href="{{ route('driver.supir.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i class="fas fa-plus me-1"></i>Tambah Supir</a>
    </div>
    <div class="card-body pb-0">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama supir, NIP..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif"  {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="idle"   {{ request('status')=='idle'?'selected':'' }}>Idle</option>
                    <option value="offline"{{ request('status')=='offline'?'selected':'' }}>Offline</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('driver.supir.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Nama Supir</th><th>NIP</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($supir as $i => $s)
                @php
                    $sc = match($s->status) {
                        'aktif'   => 'success',
                        'idle'    => 'primary',
                        'offline' => 'secondary',
                        default   => 'secondary'
                    };
                @endphp
                <tr>
                    <td>{{ $supir->firstItem() + $i }}</td>
                    <td><strong>{{ $s->nama_supir }}</strong></td>
                    <td><code>{{ $s->nip }}</code></td>
                    <td>
                        <span class="badge bg-{{ $sc }}">
                            <i class="fas fa-circle me-1" style="font-size:.5rem"></i>{{ ucfirst($s->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('driver.supir.edit', $s->id_supir) }}" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('driver.supir.destroy', $s->id_supir) }}" class="d-inline"
                            onsubmit="return confirm('Hapus data supir ini?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-inbox me-2"></i>Tidak ada data supir.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">{{ $supir->links() }}</div>
</div>
@endsection
