<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Perjalanan Driver</title>
    <style>
        body{font-family:DejaVu Sans,sans-serif;font-size:8.5pt;color:#1a202c}
        h2{text-align:center;color:#0070c0;margin-bottom:3px;font-size:13pt}
        p.meta{text-align:center;color:#718096;font-size:7.5pt;margin-bottom:10px}
        table{width:100%;border-collapse:collapse}
        th{background:#0070c0;color:#fff;padding:5px 6px;text-align:left;font-size:7.5pt}
        td{padding:4px 6px;border-bottom:1px solid #edf2f7;font-size:7.5pt}
        tr:nth-child(even) td{background:#f8fafc}
    </style>
</head>
<body>
<h2>Laporan Riwayat Perjalanan Driver</h2>
<p class="meta">Kimia Farma Apotek &mdash; Dicetak: {{ now()->format('d/m/Y H:i') }} &mdash; Total: {{ $riwayat->count() }} perjalanan</p>
<table>
    <thead>
        <tr><th>#</th><th>Tanggal</th><th>Waktu</th><th>Supir</th><th>Armada</th><th>Penumpang</th><th>Tujuan</th><th>Keperluan</th></tr>
    </thead>
    <tbody>
        @foreach($riwayat as $i => $r)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($r->tanggal_tugas)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($r->jam_selesai)->format('H:i') }}</td>
            <td>{{ $r->nama_supir }}</td>
            <td>{{ $r->merk }} ({{ $r->plat_nomor }})</td>
            <td>{{ $r->penumpang }}</td>
            <td>{{ $r->tujuan }}</td>
            <td>{{ $r->keperluan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
