<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Monitoring Bulanan</title>
    <style>
        body{font-family:DejaVu Sans,sans-serif;font-size:8.5pt;color:#1a202c}
        h2{text-align:center;color:#0070c0;margin-bottom:3px;font-size:13pt}
        p.meta{text-align:center;color:#718096;font-size:7.5pt;margin-bottom:10px}
        table{width:100%;border-collapse:collapse}
        th{background:#0070c0;color:#fff;padding:5px 6px;text-align:left;font-size:7.5pt}
        td{padding:4px 6px;border-bottom:1px solid #edf2f7;font-size:7.5pt}
        tr:nth-child(even) td{background:#f8fafc}
        .badge-normal{background:#d1fae5;color:#065f46;padding:2px 6px;border-radius:4px}
        .badge-service{background:#fef3c7;color:#92400e;padding:2px 6px;border-radius:4px}
        .badge-old{background:#fee2e2;color:#991b1b;padding:2px 6px;border-radius:4px}
    </style>
</head>
<body>
<h2>Laporan Monitoring Maintenance GA</h2>
<p class="meta">Kimia Farma Apotek &mdash; Dicetak: {{ now()->format('d/m/Y H:i') }} &mdash; Total: {{ $data->count() }} Item</p>
<table>
    <thead>
        <tr><th>#</th><th>Kode GA</th><th>Lokasi</th><th>Nama Barang</th><th>Jenis</th><th>Tgl Perawatan</th><th>Status</th><th>Keterangan</th></tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        @php $isOld = \Carbon\Carbon::parse($row->tgl_perawatan_terakhir)->lt(now()->subMonths(3)); @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->kode_ga ?? '-' }}</td>
            <td>{{ $row->lokasi }}</td>
            <td>{{ $row->nama_barang }}</td>
            <td>{{ $row->jenis_barang }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tgl_perawatan_terakhir)->format('d/m/Y') }}</td>
            <td>
                @if($row->status === 'Normal' && !$isOld)
                    <span class="badge-normal">Normal</span>
                @elseif($isOld)
                    <span class="badge-old">Perlu Cek</span>
                @else
                    <span class="badge-service">Wajib Service</span>
                @endif
            </td>
            <td>{{ $row->keterangan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
