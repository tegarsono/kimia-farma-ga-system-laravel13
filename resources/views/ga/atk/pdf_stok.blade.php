<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok ATK</title>
    <style>
        body{font-family:DejaVu Sans,sans-serif;font-size:9pt;color:#1a202c}
        h2{text-align:center;color:#0070c0;margin-bottom:4px}
        p.meta{text-align:center;color:#718096;font-size:8pt;margin-bottom:12px}
        table{width:100%;border-collapse:collapse}
        th{background:#0070c0;color:#fff;padding:6px 8px;text-align:left;font-size:8pt}
        td{padding:5px 8px;border-bottom:1px solid #edf2f7;font-size:8pt}
        tr:nth-child(even) td{background:#f8fafc}
        .right{text-align:right}
        .total-row td{background:#ebf4ff;font-weight:bold}
    </style>
</head>
<body>
<h2>Laporan Stok ATK</h2>
<p class="meta">Kimia Farma Apotek &mdash; Dicetak: {{ now()->format('d/m/Y H:i') }} &mdash; Total: {{ $totalItem }} Item</p>
<table>
    <thead>
        <tr><th>#</th><th>Kode</th><th>Kategori</th><th>Nama Barang</th><th>Satuan</th><th class="right">Harga</th><th class="right">Stok</th><th class="right">Nilai</th><th>Status</th></tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->kode }}</td>
            <td>{{ $row->kategori }}</td>
            <td>{{ $row->nama_barang }}</td>
            <td>{{ $row->satuan }}</td>
            <td class="right">Rp {{ number_format($row->harga,0,',','.') }}</td>
            <td class="right">{{ $row->jumlah }}</td>
            <td class="right">Rp {{ number_format($row->harga*$row->jumlah,0,',','.') }}</td>
            <td>{{ $row->status_barang }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="7" class="right"><strong>Total Nilai Stok:</strong></td>
            <td class="right"><strong>Rp {{ number_format($totalNilai,0,',','.') }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>
</body>
</html>
