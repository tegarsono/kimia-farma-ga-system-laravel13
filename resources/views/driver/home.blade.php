@extends('layouts.driver')
@section('title', 'Dashboard Driver')
@section('page-title', 'Dashboard Driver Operasional')

@push('styles')
    <style>
        .status-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.4rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
            border-left: 4px solid
        }

        .status-card.aktif {
            border-color: #00a859
        }

        .status-card.idle {
            border-color: #0070c0
        }

        .status-card.offline {
            border-color: #718096
        }

        .status-val {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1
        }

        .status-lbl {
            font-size: .78rem;
            color: #718096;
            margin-top: .2rem
        }

        .jadwal-row {
            background: #fff;
            border-radius: 10px;
            padding: .85rem 1rem;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
            margin-bottom: .6rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center
        }

        .jadwal-time {
            font-size: .82rem;
            color: #0070c0;
            font-weight: 700;
            white-space: nowrap
        }

        .jadwal-info {
            flex: 1;
            min-width: 0
        }

        .jadwal-supir {
            font-weight: 600;
            font-size: .88rem
        }

        .jadwal-detail {
            font-size: .78rem;
            color: #718096;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }
    </style>
@endpush

@section('content')
    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="status-card aktif">
                <div class="status-val text-success">{{ $supirAktif }}</div>
                <div class="status-lbl"><i class="fas fa-circle text-success me-1"></i>Driver Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="status-card idle">
                <div class="status-val text-primary">{{ $supirIdle }}</div>
                <div class="status-lbl"><i class="fas fa-circle text-primary me-1"></i>Driver Idle</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="status-card offline">
                <div class="status-val text-secondary">{{ $supirOffline }}</div>
                <div class="status-lbl"><i class="fas fa-circle text-secondary me-1"></i>Driver Offline</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="status-card" style="border-color:#d97706">
                <div class="status-val" style="color:#d97706">{{ $totalMobil }}</div>
                <div class="status-lbl"><i class="fas fa-truck me-1" style="color:#d97706"></i>Total Armada</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Schedule for Today --}}
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar-day me-2 text-primary"></i>Jadwal Hari Ini —
                        {{ \Carbon\Carbon::parse($tanggalHariIni)->translatedFormat('dddd, j F Y') }}</span>
                    <a href="{{ route('driver.jadwal.create') }}" class="btn btn-sm btn-light text-primary fw-semibold"><i
                            class="fas fa-plus me-1"></i>Buat Jadwal</a>
                </div>
                <div class="card-body">
                    @forelse($jadwalHariIni as $j)
                        <div class="jadwal-row">
                            <div class="jadwal-time">
                                {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} –
                                {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                            </div>
                            <div class="jadwal-info">
                                <div class="jadwal-supir">{{ $j->nama_supir }}</div>
                                <div class="jadwal-detail">
                                    <i class="fas fa-car me-1"></i>{{ $j->merk }} ({{ $j->plat_nomor }})
                                    &nbsp;|&nbsp;<i class="fas fa-map-marker-alt me-1"></i>{{ $j->tujuan }}
                                </div>
                                <div class="jadwal-detail"><i class="fas fa-user me-1"></i>{{ $j->penumpang }}</div>
                            </div>
                            <div>
                                <form method="POST" action="{{ route('driver.jadwal.selesai', $j->id_jadwal) }}"
                                    class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success py-0 px-2" title="Tandai Selesai"
                                        onclick="return confirm('Tandai jadwal ini sebagai selesai?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fs-3 d-block mb-2 opacity-50"></i>
                            Tidak ada jadwal untuk hari ini.
                            <br><a href="{{ route('driver.jadwal.create') }}" class="btn btn-sm btn-outline-primary mt-2">Buat
                                Jadwal</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-bolt me-2 text-warning"></i>Aksi Cepat</div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('driver.jadwal.index') }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-calendar-alt me-2"></i>Kelola Jadwal
                    </a>
                    <a href="{{ route('driver.jadwal.riwayat') }}" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-history me-2"></i>Riwayat Perjalanan
                    </a>
                    <a href="{{ route('driver.mobil.index') }}" class="btn btn-outline-warning text-start">
                        <i class="fas fa-truck me-2"></i>Data Armada Kendaraan
                    </a>
                    <a href="{{ route('driver.supir.index') }}" class="btn btn-outline-info text-start">
                        <i class="fas fa-id-card me-2"></i>Data Supir
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection