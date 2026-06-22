<?php

namespace App\Http\Controllers\GA;

// NOTE: file ini disinkronkan persis dari kimiafarmalaravel\app/Http/Controllers/GA/AtkController.php


use App\Http\Controllers\Controller;
use App\Traits\SimplifiesDbErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtkController extends Controller
{
    use SimplifiesDbErrors;

    private array $kategoriList = [
        'Alat Tulis',
        'Kertas',
        'Perlengkapan Kantor',
        'Media Penyimpanan',
        'Aksesoris Komputer',
        'Konsumsi',
    ];

    private array $satuanList = [
        'Pcs',
        'Rim',
        'Box',
        'Unit',
        'Lusinan',
        'Roll',
    ];

    public function index(Request $request)
    {
        $query = $this->buildAtkIndexQuery($request);
        $all = $query->orderBy('nama_barang')->get();

        $dataMasuk = $all->where('status_barang', 'Masuk')->values();
        $dataKeluar = $all->where('status_barang', '!=', 'Masuk')->values();

        $stats = $this->getAtkIndexStats($dataMasuk, $dataKeluar);
        $kategori = $this->getAtkKategoriList();

        return view('ga.atk.index', [
            'dataMasuk' => $dataMasuk,
            'dataKeluar' => $dataKeluar,
            'totalNilaiMasuk' => $stats['totalNilaiMasuk'],
            'totalNilaiKeluar' => $stats['totalNilaiKeluar'],
            'kategori' => $kategori,
        ]);
    }

    private function buildAtkIndexQuery(Request $request)
    {
        $query = DB::table('atk_katalog');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nama_barang', 'like', $s)
                    ->orWhere('spesifikasi', 'like', $s);
            });
        }

        return $query;
    }

    private function getAtkIndexStats($dataMasuk, $dataKeluar): array
    {
        $totalNilaiMasuk = $dataMasuk->sum(
            fn($r) => (float) ($r->harga ?? 0) * (int) ($r->jumlah ?? 0)
        );

        $totalNilaiKeluar = $dataKeluar->sum(
            fn($r) => (float) ($r->harga ?? 0) * (int) ($r->jumlah ?? 0)
        );

        return [
            'totalNilaiMasuk' => $totalNilaiMasuk,
            'totalNilaiKeluar' => $totalNilaiKeluar,
        ];
    }

    private function getAtkKategoriList()
    {
        return DB::table('atk_katalog')->distinct()->pluck('kategori');
    }


    public function create()
    {
        return view('ga.atk.create', [
            'kategoriList' => $this->kategoriList,
            'satuanList' => $this->satuanList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'spesifikasi' => 'nullable|string',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kode = $this->generateKode();

        $id = DB::table('atk_katalog')->insertGetId([
            'kode' => $kode,
            'kategori' => $request->kategori,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
            'jumlah' => $request->jumlah,
            'status_barang' => 'Masuk',
            'spesifikasi' => $request->spesifikasi,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('atk_transaksi')->insert([
            'tanggal' => now()->toDateString(),
            'jenis' => $request->kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan ?? '',
            'id_barang' => $id,
            'created_at' => now(),
        ]);

        return redirect()->route('ga.atk.index')->with('success', 'Barang ATK berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $atk = DB::table('atk_katalog')->where('id', $id)->first();
        if (!$atk) {
            abort(404);
        }

        return view('ga.atk.edit', [
            'atk' => $atk,
            'kategoriList' => $this->kategoriList,
            'satuanList' => $this->satuanList,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'spesifikasi' => 'nullable|string',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $atk = DB::table('atk_katalog')->where('id', $id)->first();
        if (!$atk) {
            abort(404);
        }

        DB::table('atk_katalog')->where('id', $id)->update([
            'kategori' => $request->kategori,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
            'jumlah' => $request->jumlah,
            'status_barang' => $atk->status_barang,
            'spesifikasi' => $request->spesifikasi,
            'keterangan' => $request->keterangan,
            'updated_at' => now(),
        ]);

        return redirect()->route('ga.atk.index')->with('success', 'Data ATK berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('atk_transaksi')->where('id_barang', $id)->delete();
        DB::table('atk_katalog')->where('id', $id)->delete();
        return redirect()->route('ga.atk.index')->with('success', 'Data ATK berhasil dihapus.');
    }

    public function barangKeluarForm()
    {
        $dataMasuk = DB::table('atk_katalog')
            ->where('status_barang', 'Masuk')
            ->where('jumlah', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('ga.atk.barang_keluar', compact('dataMasuk'));
    }

    public function barangKeluarStore(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|integer|exists:atk_katalog,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        $barang = DB::table('atk_katalog')->where('id', $request->id_barang)->first();
        if (!$barang) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($barang->jumlah < $request->jumlah) {
            return back()->with('error', 'Jumlah yang diminta (' . $request->jumlah . ') melebihi stok tersedia (' . $barang->jumlah . ').')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $sisaStok = $barang->jumlah - $request->jumlah;

            DB::table('atk_katalog')->where('id', $request->id_barang)->update([
                'jumlah' => $sisaStok,
                'updated_at' => now(),
            ]);

            $barangKeluar = DB::table('atk_katalog')
                ->where('kode', $barang->kode)
                ->where('status_barang', 'Keluar')
                ->first();

            if ($barangKeluar) {
                DB::table('atk_katalog')->where('id', $barangKeluar->id)->update([
                    'jumlah' => $barangKeluar->jumlah + $request->jumlah,
                    'updated_at' => now(),
                ]);
                $idBarangKeluar = $barangKeluar->id;
            } else {
                $idBarangKeluar = DB::table('atk_katalog')->insertGetId([
                    'kode' => $barang->kode,
                    'kategori' => $barang->kategori,
                    'nama_barang' => $barang->nama_barang,
                    'satuan' => $barang->satuan,
                    'harga' => $barang->harga,
                    'spesifikasi' => $barang->spesifikasi,
                    'status_barang' => 'Keluar',
                    'jumlah' => $request->jumlah,
                    'keterangan' => $barang->keterangan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('atk_transaksi')->insert([
                'tanggal' => now()->toDateString(),
                'jenis' => 'keluar',
                'jumlah' => $request->jumlah,
                'keterangan' => 'Transfer ke: ' . $request->keterangan,
                'id_barang' => $idBarangKeluar,
                'created_at' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('ga.atk.barangKeluarForm')
            ->with('success', $request->jumlah . ' ' . $barang->satuan . ' ' . $barang->nama_barang . ' berhasil dikeluarkan.');
    }

    public function riwayatItem(int $id)
    {
        $barang = DB::table('atk_katalog')
            ->where('id', $id)
            ->where('status_barang', 'Keluar')
            ->first();

        if (!$barang) {
            return redirect()->route('ga.atk.index')->with('error', 'Barang Keluar tidak ditemukan.');
        }

        $transaksi = DB::table('atk_transaksi')
            ->where('id_barang', $id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalSum = $transaksi->sum('jumlah');
        $sumMatch = abs($totalSum - (int) $barang->jumlah) < 0.01;

        return view('ga.atk.riwayat_item', compact('barang', 'transaksi', 'totalSum', 'sumMatch'));
    }

    public function riwayat(Request $request)
    {
        $query = DB::table('atk_transaksi as t')
            ->leftJoin('atk_katalog as k', 't.id_barang', '=', 'k.id')
            ->select('t.*', 'k.nama_barang', 'k.satuan', 'k.kode');

        if ($request->filled('bulan')) {
            $query->whereRaw('MONTH(t.tanggal) = ?', [$request->bulan]);
        }
        if ($request->filled('tahun')) {
            $query->whereRaw('YEAR(t.tanggal) = ?', [$request->tahun]);
        }

        $transaksi = $query->orderBy('t.created_at', 'desc')->paginate(20)->withQueryString();

        return view('ga.atk.riwayat', compact('transaksi'));
    }

    public function deleteTransaksi(int $id)
    {
        DB::table('atk_transaksi')->where('id', $id)->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $data = DB::table('atk_katalog')->orderBy('status_barang')->orderBy('nama_barang')->get();

        $dataMasuk = $data->where('status_barang', 'Masuk')->values();
        $dataKeluar = $data->where('status_barang', '!=', 'Masuk')->values();
        $totalMasuk = $dataMasuk->sum(fn($r) => (float) ($r->harga ?? 0) * (int) ($r->jumlah ?? 0));
        $totalKeluar = $dataKeluar->sum(fn($r) => (float) ($r->harga ?? 0) * (int) ($r->jumlah ?? 0));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Kimia Farma ATK System')
            ->setTitle('ATK Export - ' . date('Y-m-d'))
            ->setDescription('Export data ATK Barang Masuk dan Barang Keluar');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data ATK');

        $Fill = \PhpOffice\PhpSpreadsheet\Style\Fill::class;
        $Border = \PhpOffice\PhpSpreadsheet\Style\Border::class;
        $Alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;

        $sheet->setCellValue('A1', 'LAPORAN DATA ALAT TULIS KANTOR (ATK)');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal($Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Tanggal Export: ' . date('d-m-Y H:i'));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal($Alignment::HORIZONTAL_CENTER);

        $colHeaders = ['No', 'Kategori', 'Nama Barang', 'Satuan', 'Harga (Rp)', 'Jumlah', 'Jumlah Harga', 'Spesifikasi', 'Keterangan', 'Status Barang'];
        $currentRow = 4;

        $writeSection = function (string $sectionLabel, string $bgSection, string $bgHeader, $rows, float $total, string $totalLabel) use ($sheet, $colHeaders, &$currentRow, $Fill, $Border, $Alignment) {
            $sheet->setCellValue('A' . $currentRow, $sectionLabel);
            $sheet->mergeCells('A' . $currentRow . ':J' . $currentRow);
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => $Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgSection]],
                'alignment' => ['horizontal' => $Alignment::HORIZONTAL_CENTER, 'vertical' => $Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => $Border::BORDER_THIN]],
            ]);
            $currentRow++;

            $col = 'A';
            foreach ($colHeaders as $h) {
                $sheet->setCellValue($col++ . $currentRow, $h);
            }
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => $Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHeader]],
                'alignment' => ['horizontal' => $Alignment::HORIZONTAL_CENTER, 'vertical' => $Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => $Border::BORDER_THIN]],
            ]);
            $currentRow++;

            $startData = $currentRow;
            if ($rows->count() > 0) {
                $counter = 1;
                foreach ($rows as $r) {
                    $jumlahHarga = (float) ($r->harga ?? 0) * (int) ($r->jumlah ?? 0);
                    $sheet->setCellValue('A' . $currentRow, $counter++);
                    $sheet->setCellValue('B' . $currentRow, $r->kategori ?? '');
                    $sheet->setCellValue('C' . $currentRow, $r->nama_barang ?? '');
                    $sheet->setCellValue('D' . $currentRow, $r->satuan ?? '');
                    $sheet->setCellValue('E' . $currentRow, $r->harga ?? 0);
                    $sheet->setCellValue('F' . $currentRow, $r->jumlah ?? 0);
                    $sheet->setCellValue('G' . $currentRow, $jumlahHarga);
                    $sheet->setCellValue('H' . $currentRow, $r->spesifikasi ?? '-');
                    $sheet->setCellValue('I' . $currentRow, $r->keterangan ?? '-');
                    $sheet->setCellValue('J' . $currentRow, $r->status_barang ?? '');
                    $currentRow++;
                }
            } else {
                $sheet->setCellValue('A' . $currentRow, 'Tidak ada data');
                $sheet->mergeCells('A' . $currentRow . ':J' . $currentRow);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal($Alignment::HORIZONTAL_CENTER);
                $currentRow++;
            }
            $endData = $currentRow - 1;

            if ($startData <= $endData) {
                $sheet->getStyle('A' . $startData . ':J' . $endData)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => $Border::BORDER_THIN]],
                ]);
                $sheet->getStyle('E' . $startData . ':E' . $endData)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('G' . $startData . ':G' . $endData)->getNumberFormat()->setFormatCode('#,##0');
            }

            $sheet->mergeCells('E' . $currentRow . ':F' . $currentRow);
            $sheet->setCellValue('E' . $currentRow, $totalLabel);
            $sheet->setCellValue('G' . $currentRow, $total);
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0d6efd'], 'size' => 12],
                'fill' => ['fillType' => $Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8f9fa']],
                'alignment' => ['horizontal' => $Alignment::HORIZONTAL_RIGHT, 'vertical' => $Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => $Border::BORDER_MEDIUM, 'color' => ['rgb' => '0d6efd']]],
            ]);
            $sheet->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $currentRow += 2;
        };

        $writeSection('BARANG MASUK', '198754', '157347', $dataMasuk, $totalMasuk, 'HARGA TOTAL BARANG MASUK:');
        $writeSection('BARANG KELUAR', 'DC3545', 'B02A37', $dataKeluar, $totalKeluar, 'HARGA TOTAL BARANG KELUAR:');

        $sheet->mergeCells('E' . $currentRow . ':F' . $currentRow);
        $sheet->setCellValue('E' . $currentRow, 'GRAND TOTAL (Masuk + Keluar):');
        $sheet->setCellValue('G' . $currentRow, $totalMasuk + $totalKeluar);
        $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 13],
            'fill' => ['fillType' => $Fill::FILL_SOLID, 'startColor' => ['rgb' => '0d6efd']],
            'alignment' => ['horizontal' => $Alignment::HORIZONTAL_RIGHT, 'vertical' => $Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => $Border::BORDER_MEDIUM]],
        ]);
        $sheet->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A5');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'atk_export_' . date('Y-m-d_H-i') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function exportPdfStok(Request $request)
    {
        $query = DB::table('atk_katalog')->orderBy('kategori')->orderBy('nama_barang');

        if ($request->filled('atk_selected')) {
            $query->whereIn('id', $request->input('atk_selected'));
        }

        $data = $query->get();
        $totalNilai = $data->sum(fn($r) => $r->harga * $r->jumlah);
        $totalItem = $data->count();
        $threshold = (int) $request->input('threshold', 0);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ga.atk.pdf_stok', compact('data', 'totalNilai', 'totalItem', 'threshold'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_stok_atk_' . date('Ymd_His') . '.pdf');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data ATK');

        $headers = [
            'A' => 'No',
            'B' => 'Kategori',
            'C' => 'Nama Barang',
            'D' => 'Satuan',
            'E' => 'Harga (Rp)',
            'F' => 'Jumlah',
            'G' => 'Jumlah Harga',
            'H' => 'Spesifikasi',
            'I' => 'Keterangan',
            'J' => 'Status Barang',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '157347'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        $example = [
            'A2' => '1',
            'B2' => 'Perlengkapan Kantor',
            'C2' => 'Pulpen',
            'D2' => 'Pcs',
            'E2' => '5000',
            'F2' => '10',
            'G2' => '50000',
            'H2' => '',
            'I2' => '',
            'J2' => 'Masuk',
        ];
        foreach ($example as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $colWidths = [
            'A' => 5.71,
            'B' => 23.43,
            'C' => 16.28,
            'D' => 10.43,
            'E' => 15.14,
            'F' => 10.43,
            'G' => 17.56,
            'H' => 17.56,
            'I' => 16.43,
            'J' => 18.71,
        ];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'template excel atk.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(Request $request)
    {
        $request->validate(['excel_file' => 'required|file|mimes:xlsx,xls|max:10240']);

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('excel_file')->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($request->file('excel_file')->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray();

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'title' => 'File Kosong',
                    'errors' => ['File Excel kosong atau tidak ada data.'],
                ]);
            }

            $headerRow = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);

            $fieldMap = [
                'kategori' => ['kategori'],
                'nama_barang' => ['nama barang', 'nama_barang'],
                'satuan' => ['satuan'],
                'harga' => ['harga (rp)', 'harga'],
                'jumlah' => ['jumlah'],
                'status_barang' => ['status barang', 'status_barang'],
                'spesifikasi' => ['spesifikasi'],
                'keterangan' => ['keterangan'],
            ];

            $colMap = [];
            foreach ($fieldMap as $field => $aliases) {
                foreach ($headerRow as $i => $h) {
                    if (in_array($h, $aliases)) {
                        $colMap[$field] = $i;
                        break;
                    }
                }
            }

            $errors = [];
            $rowsData = [];
            $seenKey = [];

            foreach (array_slice($rows, 1) as $rowNum => $row) {
                if (empty(array_filter($row)))
                    continue;

                $data = [];
                foreach ($colMap as $field => $idx) {
                    $data[$field] = isset($row[$idx]) && $row[$idx] !== '' ? trim((string) $row[$idx]) : null;
                }

                $humanRow = $rowNum + 2;

                if (empty($data['kategori'])) {
                    $errors[] = "Baris {$humanRow}: kategori wajib diisi.";
                    continue;
                }
                if (!in_array($data['kategori'], $this->kategoriList, true)) {
                    $errors[] = "Baris {$humanRow}: kategori '{$data['kategori']}' tidak valid.";
                    continue;
                }

                if (empty($data['nama_barang'])) {
                    $errors[] = "Baris {$humanRow}: nama_barang wajib diisi.";
                    continue;
                }

                if (empty($data['satuan'])) {
                    $errors[] = "Baris {$humanRow}: satuan wajib diisi.";
                    continue;
                }
                if (!in_array($data['satuan'], $this->satuanList, true)) {
                    $errors[] = "Baris {$humanRow}: satuan '{$data['satuan']}' tidak valid.";
                    continue;
                }

                $hargaRaw = $data['harga'] ?? null;
                $jumlahRaw = $data['jumlah'] ?? null;

                $harga = $hargaRaw !== null ? (int) preg_replace('/\D/', '', (string) $hargaRaw) : 0;
                $jumlah = $jumlahRaw !== null ? (int) ($jumlahRaw) : 0;

                if ($harga <= 0) {
                    $errors[] = "Baris {$humanRow}: harga wajib bilangan bulat > 0.";
                    continue;
                }
                if ($jumlah <= 0) {
                    $errors[] = "Baris {$humanRow}: jumlah wajib bilangan bulat > 0.";
                    continue;
                }

                $status = $data['status_barang'] ?? null;
                if (empty($status)) {
                    $errors[] = "Baris {$humanRow}: status_barang wajib diisi (Masuk/Keluar).";
                    continue;
                }
                if (!in_array($status, ['Masuk', 'Keluar'], true)) {
                    $errors[] = "Baris {$humanRow}: status_barang '{$status}' tidak valid (Masuk atau Keluar).";
                    continue;
                }

                $spesifikasi = $data['spesifikasi'] ?? null;
                $keterangan = $data['keterangan'] ?? null;

                $key = strtolower(trim($data['kategori'] . '|' . $data['nama_barang'] . '|' . $status));
                if (isset($seenKey[$key])) {
                    $errors[] = "Baris {$humanRow}: data dengan kategori/nama_barang/status yang sama duplikat di dalam file.";
                    continue;
                }
                $seenKey[$key] = true;

                $rowsData[] = [
                    'kategori' => $data['kategori'],
                    'nama_barang' => $data['nama_barang'],
                    'satuan' => $data['satuan'],
                    'harga' => $harga,
                    'jumlah' => $jumlah,
                    'status_barang' => $status,
                    'spesifikasi' => $spesifikasi,
                    'keterangan' => $keterangan,
                ];
            }

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'title' => 'Import Ditolak — Perbaiki Data Terlebih Dahulu',
                    'errors' => $errors,
                ]);
            }

            if (empty($rowsData)) {
                return response()->json([
                    'success' => false,
                    'title' => 'File Kosong',
                    'errors' => ['Tidak ada data yang dapat diimport.'],
                ]);
            }

            DB::beginTransaction();
            try {
                foreach ($rowsData as $data) {
                    DB::table('atk_katalog')->insert([
                        'kode' => $this->generateKode(),
                        'kategori' => $data['kategori'],
                        'nama_barang' => $data['nama_barang'],
                        'satuan' => $data['satuan'],
                        'harga' => $data['harga'],
                        'jumlah' => $data['jumlah'],
                        'status_barang' => $data['status_barang'],
                        'spesifikasi' => $data['spesifikasi'],
                        'keterangan' => $data['keterangan'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'title' => 'Import Gagal',
                    'errors' => [self::simplifyDbError($e->getMessage())],
                ]);
            }

            return response()->json([
                'success' => true,
                'title' => 'Import Berhasil',
                'success_count' => count($rowsData),
                'errors' => [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Gagal Membaca File',
                'errors' => ['Gagal membaca file Excel: ' . $e->getMessage()],
            ]);
        }
    }

    private function generateKode(): string
    {
        $last = DB::table('atk_katalog')->orderBy('id', 'desc')->value('kode');
        if (!$last) {
            return 'ATK-0001';
        }

        preg_match('/(\d+)$/', $last, $m);
        $num = isset($m[1]) ? (int) $m[1] + 1 : 1;
        return 'ATK-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}

