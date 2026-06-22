@extends('layouts.ga')
@section('title', 'Dashboard General Affair')
@section('page-title', 'Dashboard General Affair')

@push('styles')
    <style>
        .chart-wrapper {
            position: relative;
            min-height: 300px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .no-data-text {
            color: #adb5bd;
            font-style: italic;
            font-size: 1.1rem;
        }
    </style>
@endpush

@section('content')

    <h2 class="mb-4"><i class="fas fa-chart-line"></i> Main Asset Management Dashboard</h2>
    <p class="lead">Selamat datang, <strong>{{ session('full_name', session('username', 'User')) }}</strong>.</p>

    <hr>

    {{-- ── Stat Cards ── --}}
    <div class="row mb-5 g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-car" style="color:#4ca1ff;"></i> Total Kendaraan
                    </h5>
                    <p class="card-text fs-2 mb-0 text-dark">{{ $totalKendaraan }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-building" style="color:#ff6b6b;"></i> Total Bangunan
                    </h5>
                    <p class="card-text fs-2 mb-0 text-dark">{{ array_sum($dataBangunan['data']) }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-folder-open" style="color:#20c997;"></i> Total DIR
                    </h5>
                    <p class="card-text fs-2 mb-0 text-dark">{{ $totalDir }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-arrow-down" style="color:#51cf66;"></i> Total Masuk ATK
                    </h5>
                    <p class="card-text fs-2 mb-0 text-dark">{{ array_sum($dataAtk['masuk']) }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-arrow-up" style="color:#ffa94d;"></i> Total Keluar ATK
                    </h5>
                    <p class="card-text fs-2 mb-0 text-dark">{{ array_sum($dataAtk['keluar']) }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-arrow-down" style="color:#845ef7;"></i> Harga Masuk (YTD)
                    </h5>
                    <p class="card-text fs-4 mb-0 text-dark">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm" style="background-color:#ffffff;border:1px solid #e0e0e0;">
                <div class="card-body">
                    <h5 class="card-title fs-6 text-dark">
                        <i class="fas fa-arrow-up" style="color:#ff6b9d;"></i> Harga Keluar (YTD)
                    </h5>
                    <p class="card-text fs-4 mb-0 text-dark">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="row">

        {{-- Chart 1: Kendaraan per Branch --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color:#1A3263;">
                    <i class="fas fa-car"></i> Jumlah Kendaraan per Bisnis Manager
                </div>
                <div class="card-body chart-wrapper">
                    @if(empty($dataKendaraan['labels']))
                        <span class="no-data-text">Tidak ada data kendaraan</span>
                    @else
                        <canvas id="kendaraanBranchChart"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart 2: Tanah & Bangunan --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color:#547792;">
                    <i class="fas fa-chart-bar"></i> Jumlah Aset Tanah/Bangunan
                </div>
                <div class="card-body chart-wrapper">
                    @if(empty($dataBangunan['labels']))
                        <span class="no-data-text">Tidak ada data aset tanah/bangunan</span>
                    @else
                        <canvas id="bangunanChart"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart 3: DIR per Unit Bisnis --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color:#1565c0;">
                    <i class="fas fa-folder-open"></i> Jumlah Aset DIR per Unit Bisnis
                </div>
                <div class="card-body chart-wrapper">
                    @if(empty($dataDirUnitBisnis['labels']))
                        <span class="no-data-text">Tidak ada data DIR</span>
                    @else
                        <canvas id="dirUnitBisnisChart"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart 4: ATK Qty Bulanan --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color:#6C757D;">
                    <i class="fas fa-chart-bar"></i> Total Qty ATK Bulanan
                </div>
                <div class="card-body chart-wrapper">
                    @if(!$hasAtkData)
                        <span class="no-data-text">Tidak ada data transaksi ATK</span>
                    @else
                        <canvas id="atkChart"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart 4: Biaya ATK Bulanan --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color:#495057;">
                    <i class="fas fa-chart-bar"></i> Total Harga Biaya ATK Bulanan (Rp)
                </div>
                <div class="card-body chart-wrapper">
                    @if(!$hasBiayaData)
                        <span class="no-data-text">Tidak ada data biaya bulanan</span>
                    @else
                        <canvas id="biayaChart"></canvas>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Helper: generate alternating colors per bar
            function alternatingColors(count, colorA, colorB) {
                return Array.from({ length: count }, (_, i) => i % 2 === 0 ? colorA : colorB);
            }

            const COLOR_A = '#4ca1ff';
            const COLOR_B = '#b4d9ff';

            // 1. Chart Kendaraan
            const ctxKendaraan = document.getElementById('kendaraanBranchChart');
            if (ctxKendaraan) {
                const dataKendaraan = {!! json_encode($dataKendaraan) !!};
                const n = dataKendaraan.labels.length;
                new Chart(ctxKendaraan, {
                    type: 'bar',
                    data: {
                        labels: dataKendaraan.labels,
                        datasets: [
                            { label: 'Mobil', data: dataKendaraan.mobil, backgroundColor: alternatingColors(n, COLOR_A, COLOR_B), yAxisID: 'y' },
                            { label: 'Motor', data: dataKendaraan.motor, backgroundColor: alternatingColors(n, COLOR_B, COLOR_A), yAxisID: 'y' },
                            { label: 'Total', data: dataKendaraan.total, type: 'line', borderColor: '#ff6b6b', fill: false }
                        ]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
                });
            }

            // 2. Chart Bangunan
            const ctxBangunan = document.getElementById('bangunanChart');
            if (ctxBangunan) {
                const dataBangunan = {!! json_encode($dataBangunan) !!};
                const n = dataBangunan.labels.length;
                new Chart(ctxBangunan, {
                    type: 'bar',
                    data: {
                        labels: dataBangunan.labels,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: dataBangunan.data,
                            backgroundColor: alternatingColors(n, COLOR_A, COLOR_B),
                            borderWidth: 1
                        }]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
                });
            }

            // 3. Chart DIR per Unit Bisnis
            const ctxDir = document.getElementById('dirUnitBisnisChart');
            if (ctxDir) {
                const dataDir = {!! json_encode($dataDirUnitBisnis) !!};
                const n3 = dataDir.labels.length;
                new Chart(ctxDir, {
                    type: 'bar',
                    data: {
                        labels: dataDir.labels,
                        datasets: [{
                            label: 'Jumlah Aset DIR',
                            data: dataDir.data,
                            backgroundColor: alternatingColors(n3, '#20c997', '#a3e9d6'),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                        return ' ' + ctx.parsed.y + ' aset';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 4. Chart ATK Qty
            const ctxATK = document.getElementById('atkChart');
            if (ctxATK) {
                const dataATK = {!! json_encode($dataAtk) !!};
                const n = dataATK.labels.length;
                new Chart(ctxATK, {
                    type: 'bar',
                    data: {
                        labels: dataATK.labels,
                        datasets: [
                            { label: 'Masuk (Qty)', data: dataATK.masuk, backgroundColor: alternatingColors(n, COLOR_A, COLOR_B) },
                            { label: 'Keluar (Qty)', data: dataATK.keluar, backgroundColor: alternatingColors(n, COLOR_B, COLOR_A) }
                        ]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
                });
            }

            // 4. Chart Biaya Harga
            const ctxBiaya = document.getElementById('biayaChart');
            if (ctxBiaya) {
                const dataBiaya = {!! json_encode($dataBiaya) !!};
                const n = dataBiaya.labels.length;
                new Chart(ctxBiaya, {
                    type: 'bar',
                    data: {
                        labels: dataBiaya.labels,
                        datasets: [
                            { label: 'Masuk (Rp)', data: dataBiaya.masuk, backgroundColor: alternatingColors(n, COLOR_A, COLOR_B) },
                            { label: 'Keluar (Rp)', data: dataBiaya.keluar, backgroundColor: alternatingColors(n, COLOR_B, COLOR_A) }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true } },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                        return ctx.dataset.label + ': Rp ' + Math.round(ctx.parsed.y).toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

        });
    </script>
@endpush