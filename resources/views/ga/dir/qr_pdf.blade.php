<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>PDF DIR</title>
    <style>
        @page {
            margin: 12mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .sub {
            font-size: 12px;
            margin-top: 2px;
        }

        .box {
            border: 1px solid #e5e7eb;
            padding: 12px;
        }

        .row {
            width: 100%;
            display: table;
        }

        .col {
            display: table-cell;
            vertical-align: top;
        }

        .left {
            width: 72%;
            padding-right: 12px;
        }

        .right {
            width: 28%;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            vertical-align: top;
        }

        .k {
            width: 35%;
            background: #f3f4f6;
            font-weight: 700;
            color: #1f2937;
        }

        .v {
            width: 65%;
            background: #ffffff;
        }

        .grid {
            margin-top: 10px;
            display: table;
            width: 100%;
        }

        .grid-row {
            display: table-row;
        }

        .grid-cell {
            display: table-cell;
            width: 50%;
            padding-right: 8px;
        }

        .grid-cell:nth-child(2) {
            padding-right: 0;
        }

        .small {
            color: #374151;
            font-size: 11.5px;
            line-height: 1.35;
        }

        .label {
            font-weight: 700;
            color: #111827;
        }

        .qr {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .qr img {
            width: 110px;
            height: 110px;
        }

        .qr-foot {
            margin-top: 8px;
            font-size: 11px;
            color: #4b5563;
        }

        .muted {
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">DIR</div>
        <div class="sub muted">Dokumen untuk aset</div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col left">
                <table>
                    <tr>
                        <td class="k">ID ASET</td>
                        <td class="v">{{ $item->id_aset }}</td>
                    </tr>
                    <tr>
                        <td class="k">KODE ASET</td>
                        <td class="v">{{ $item->kode_aset }}</td>
                    </tr>
                    <tr>
                        <td class="k">COST CENTER</td>
                        <td class="v">{{ $item->cost_center }}</td>
                    </tr>
                    <tr>
                        <td class="k">PROFIT CENTER</td>
                        <td class="v">{{ $item->profit_center }}</td>
                    </tr>
                </table>

                <div class="grid">
                    <div class="grid-row">
                        <div class="grid-cell small"><span class="label">Lokasi / Pemakai:</span> {{ $item->lokasi_pemakai }}</div>
                        <div class="grid-cell small"><span class="label">Unit Bisnis:</span> {{ $item->unit_bisnis }}</div>
                    </div>
                    <div style="height:8px"></div>
                    <div class="grid-row">
                        <div class="grid-cell small"><span class="label">Golongan Aset:</span> {{ $item->golongan_aset }}</div>
                        <div class="grid-cell small"><span class="label">Kategori Aset:</span> {{ $item->kategori_aset }}</div>
                    </div>
                    <div style="height:8px"></div>
                    <div class="grid-row">
                        <div class="grid-cell small" style="width:100%;">
                            <span class="label">Deskripsi Aset:</span> {{ $item->deskripsi_aset }}
                        </div>
                    </div>
                    @if(!empty($item->keterangan))
                        <div style="height:8px"></div>
                        <div class="grid-row">
                            <div class="grid-cell small" style="width:100%;">
                                <span class="label">Keterangan:</span> {{ $item->keterangan }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col right">
                <div class="qr">
                    @if(!empty($qrBase64))
                        <img alt="QR" src="data:image/png;base64,{{ $qrBase64 }}" />
                    @else
                        <img alt="QR" src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('ga.dir.qrPdf', $item->id)) }}" />
                    @endif
                </div>
                <div class="qr-foot">
                    <strong>Scan QR</strong> untuk download ulang PDF.
                </div>
                <div class="small muted" style="margin-top:6px;">
                    Link: <span>{{ $item->kode_aset }}</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
