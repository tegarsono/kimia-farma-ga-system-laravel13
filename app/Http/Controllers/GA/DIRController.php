<?php

namespace App\Http\Controllers\GA;

use App\Http\Controllers\Controller;
use App\Traits\ExcelDropdownHelper;
use App\Traits\SimplifiesDbErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Abort;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DIRController extends Controller
{
    use SimplifiesDbErrors, ExcelDropdownHelper;

    private array $unitBisnisList = [
        'Unit Bisnis Kantor Pusat',
        'Unit Bisnis Jember',
        'Unit Bisnis Surabaya',
        'Unit Bisnis Madura',
        'Unit Bisnis Aceh',
        'Unit Bisnis Ambon',
        'Unit Bisnis Balikpapan',
        'Unit Bisnis Bandung',
        'Unit Bisnis Bangka Belitung',
        'Unit Bisnis Banjarmasin',
        'Unit Bisnis Batam',
        'Unit Bisnis Bekasi',
        'Unit Bisnis Bogor',
        'Unit Bisnis Cilegon',
        'Unit Bisnis Jayapura',
        'Unit Bisnis Cirebon',
        'Unit Bisnis Denpasar',
        'Unit Bisnis Depok',
        'Unit Bisnis Gorontalo',
        'Unit Bisnis Gresik',
        'Unit Bisnis Jambi',
        'Unit Bisnis Jaya 1',
        'Unit Bisnis Jaya 2',
        'Unit Bisnis Karawang',
        'Unit Bisnis Kendari',
        'Unit Bisnis Kupang',
        'Unit Bisnis Lampung',
        'Unit Bisnis Makassar',
        'Unit Bisnis Malang',
        'Unit Bisnis Manado',
        'Unit Bisnis Mataram',
        'Unit Bisnis Medan',
        'Unit Bisnis Nusa Dua',
        'Unit Bisnis Palangkaraya',
        'Unit Bisnis Padang',
        'Unit Bisnis Palembang',
        'Unit Bisnis Palu',
        'Unit Bisnis Pekalongan',
        'Unit Bisnis Pekanbaru',
        'Unit Bisnis Pontianak',
        'Unit Bisnis Purwokerto',
        'Unit Bisnis Samarinda',
        'Unit Bisnis Semarang',
        'Unit Bisnis Sidoarjo',
        'Unit Bisnis Sukabumi',
        'Unit Bisnis Surakarta',
        'Unit Bisnis Tangerang',
        'Unit Bisnis Sorong',
        'Unit Bisnis Tanjung Pinang',
        'Unit Bisnis Tasikmalaya',
        'Unit Bisnis Ternate',
        'Unit Bisnis Yogyakarta',
    ];

    public function qrPdf(string|int $id)
    {
        $item = DB::table('dir_aset')->where('id', $id)->first();
        if (!$item) {
            Abort::abort(404, 'Data DIR tidak ditemukan.');
        }

        $qrData = route('ga.dir.qrPdf', $item->id);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($qrData);

        // DomPDF sering gagal memuat gambar dari URL eksternal.
        // Ambil gambar QR terlebih dahulu, lalu embed via base64.
        $qrBinary = @file_get_contents($qrUrl);
        $qrBase64 = $qrBinary !== false && $qrBinary !== null ? base64_encode($qrBinary) : null;

        if ($qrBase64 === null || $qrBase64 === '') {
            // Jika QR gagal terambil, tetap tampilkan PDF tanpa crash.
            // (Dengan QR base64 null, view akan fallback ke URL eksternal.)
        }


        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ga.dir.qr_pdf', [
            'item' => $item,
            'qrBase64' => $qrBase64,
            'qrUrl' => $qrUrl,
        ])->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $pdfFileName = 'DIR_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) ($item->id_aset ?? $item->id)) . '.pdf';

        return $pdf->download($pdfFileName);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DIR');

        $headers = [
            'A' => 'COST CENTER',
            'B' => 'PROFIT CENTER',
            'C' => 'UNIT BISNIS',
            'D' => 'GOLONGAN ASET',
            'E' => 'KATEGORI ASET',
            'F' => 'DESKRIPSI ASET',
            'G' => 'LOKASI / PEMAKAI',
            'H' => 'KODE ASET',
            'I' => 'KETERANGAN',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565c0']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $example = [
            'A2' => 'CC-001',
            'B2' => 'PC-001',
            'C2' => 'Unit Bisnis Surabaya',
            'D2' => 'Gol-001',
            'E2' => 'Kat-001',
            'F2' => 'Deskripsi contoh',
            'G2' => 'Lokasi / pemakai contoh',
            'H2' => '1.001',
            'I2' => 'Keterangan contoh',
        ];
        foreach ($example as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $this->addDropdownFromHiddenSheet(
            $spreadsheet,
            '_UB_List',
            $this->unitBisnisList,
            'UnitBisnisList',
            'C2:C1000',
            'Unit Bisnis',
            'Pilih Unit Bisnis dari daftar',
            'Input tidak valid',
            'Nilai tidak ada dalam daftar Unit Bisnis.'
        );

        $sheet->freezePane('A2');
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return Response::streamDownload(
            fn() => $writer->save('php://output'),
            'template_dir.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function exportExcel(Request $request)
    {
        $query = DB::table('dir_aset');

        $search = (string) ($request->search ?? '');
        if ($search !== '') {
            $s = '%' . $search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('kode_aset', 'like', $s)
                    ->orWhere('deskripsi_aset', 'like', $s)
                    ->orWhere('lokasi_pemakai', 'like', $s)
                    ->orWhere('unit_bisnis', 'like', $s)
                    ->orWhere('kategori_aset', 'like', $s)
                    ->orWhere('golongan_aset', 'like', $s)
                    ->orWhere('profit_center', 'like', $s)
                    ->orWhere('cost_center', 'like', $s)
                    ->orWhere('id_aset', 'like', $s)
                    ->orWhere('keterangan', 'like', $s);
            });
        }

        $kode_aset = (string) ($request->kode_aset ?? '');
        if ($kode_aset !== '') {
            $query->where('kode_aset', $kode_aset);
        }

        $unit_bisnis = (string) ($request->unit_bisnis ?? '');
        if ($unit_bisnis !== '') {
            $query->where('unit_bisnis', $unit_bisnis);
        }

        $kategori_aset = (string) ($request->kategori_aset ?? '');
        if ($kategori_aset !== '') {
            $query->where('kategori_aset', $kategori_aset);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DIR');

        $colMap = [
            'A' => 'NO',
            'B' => 'COST CENTER',
            'C' => 'PROFIT CENTER',
            'D' => 'UNIT BISNIS',
            'E' => 'GOLONGAN ASET',
            'F' => 'KATEGORI ASET',
            'G' => 'DESKRIPSI ASET',
            'H' => 'LOKASI / PEMAKAI',
            'I' => 'KODE ASET',
            'J' => 'ID ASET',
            'K' => 'KETERANGAN',
            'L' => 'CREATED AT',
            'M' => 'UPDATED AT',
        ];
        $lastCol = 'M';

        foreach ($colMap as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565c0']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $rowNum = 2;
        foreach ($data as $i => $row) {
            $sheet->setCellValue('A' . $rowNum, $i + 1);
            $sheet->setCellValue('B' . $rowNum, $row->cost_center ?? '');
            $sheet->setCellValue('C' . $rowNum, $row->profit_center ?? '');
            $sheet->setCellValue('D' . $rowNum, $row->unit_bisnis ?? '');
            $sheet->setCellValue('E' . $rowNum, $row->golongan_aset ?? '');
            $sheet->setCellValue('F' . $rowNum, $row->kategori_aset ?? '');
            $sheet->setCellValue('G' . $rowNum, $row->deskripsi_aset ?? '');
            $sheet->setCellValue('H' . $rowNum, $row->lokasi_pemakai ?? '');
            $sheet->setCellValue('I' . $rowNum, $row->kode_aset ?? '');
            $sheet->setCellValue('J' . $rowNum, $row->id_aset ?? '');
            $sheet->setCellValue('K' . $rowNum, $row->keterangan ?? '');
            $sheet->setCellValue('L' . $rowNum, $row->created_at ?? '');
            $sheet->setCellValue('M' . $rowNum, $row->updated_at ?? '');
            $rowNum++;
        }

        $this->addDropdownFromHiddenSheet(
            $spreadsheet,
            '_UB_List',
            $this->unitBisnisList,
            'UnitBisnisList',
            'D2:D1000',
            'Unit Bisnis',
            'Pilih Unit Bisnis dari daftar',
            'Input tidak valid',
            'Nilai tidak ada dalam daftar Unit Bisnis.'
        );

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        return Response::streamDownload(
            fn() => $writer->save('php://output'),
            'dir_export_' . date('Y-m-d_H-i') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(Request $request)
    {
        $request->validate(['excel_file' => 'required|file|mimes:xlsx,xls|max:10240']);

        // Hindari pemanggilan $request->file() supaya intelephense tidak error P1013.
        $excelFile = $request->excel_file ?? null;
        if (!$excelFile) {
            return Response::json([
                'success' => false,
                'title' => 'File Kosong',
                'errors' => ['File Excel kosong atau tidak ada data.'],
            ]);
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($excelFile->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray();

            if (count($rows) < 2) {
                return Response::json([
                    'success' => false,
                    'title' => 'File Kosong',
                    'errors' => ['File Excel kosong atau tidak ada data.'],
                ]);
            }

            $headerRow = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);

            $fieldMap = [
                'cost_center' => ['cost center', 'cost_center'],
                'profit_center' => ['profit center', 'profit_center'],
                'unit_bisnis' => ['unit bisnis', 'unit_bisnis'],
                'golongan_aset' => ['golongan aset', 'golongan_aset'],
                'kategori_aset' => ['kategori aset', 'kategori_aset'],
                'deskripsi_aset' => ['deskripsi aset', 'deskripsi_aset'],
                'lokasi_pemakai' => ['lokasi / pemakai', 'lokasi_pemakai', 'lokasi pemakai'],
                'kode_aset' => ['kode aset', 'kode_aset'],
                'keterangan' => ['keterangan'],
            ];

            $colMap = [];
            foreach ($fieldMap as $field => $aliases) {
                foreach ($headerRow as $i => $h) {
                    if (in_array($h, $aliases, true)) {
                        $colMap[$field] = $i;
                        break;
                    }
                }
            }

            $errors = [];
            $rowsData = [];

            foreach (array_slice($rows, 1) as $rowNum => $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = [];
                foreach ($colMap as $field => $idx) {
                    $data[$field] = isset($row[$idx]) && $row[$idx] !== '' ? trim((string) $row[$idx]) : null;
                }

                $humanRow = $rowNum + 2;

                foreach (['cost_center', 'profit_center', 'unit_bisnis', 'golongan_aset', 'kategori_aset', 'deskripsi_aset', 'lokasi_pemakai', 'kode_aset'] as $f) {
                    if (empty($data[$f])) {
                        $errors[] = "Baris {$humanRow}: {$f} wajib diisi.";
                    }
                }

                if (!empty($errors)) {
                    continue;
                }

                $rowsData[] = $data;
            }

            if (!empty($errors)) {
                return Response::json([
                    'success' => false,
                    'title' => 'Import Ditolak — Perbaiki Data Terlebih Dahulu',
                    'errors' => $errors,
                ]);
            }

            if (empty($rowsData)) {
                return Response::json([
                    'success' => false,
                    'title' => 'File Kosong',
                    'errors' => ['Tidak ada data yang dapat diimport.'],
                ]);
            }

            // Ambil MAX id_aset sekali di awal agar tidak menghasilkan duplikat saat DB cepat/terputus.
            // Jika gagal connect, fallback generate standar (fail-safe).
            $row = null;
            try {
                $row = DB::table('dir_aset')
                    ->selectRaw('MAX(id_aset) as max_id_aset')
                    ->value('max_id_aset');
            } catch (\Throwable) {
                $row = null;
            }

            $nextIds = [];
            $generated = [];

            $startNomor = 1;
            $startKodeAsap = 1;
            $startProfitCenter = '3000';

            if (!$row || !preg_match('/^1\.(\d{3})\.(\d{4})\.(\d{3})$/', (string) $row, $m)) {
                $kodeAsap = (int) $startKodeAsap;
                $profitCenter = (int) $startProfitCenter;
                $nomorUrut = (int) $startNomor;
            } else {
                $kodeAsap = (int) $m[1];
                $profitCenter = (int) $m[2];
                $nomorUrut = (int) $m[3];
            }

            foreach ($rowsData as $i => $dataRow) {
                $nomorUrut++;
                $kodeAsap++;
                $idAsap = sprintf('1.%03d.%04d.%03d', $kodeAsap, $profitCenter, $nomorUrut);

                // Guard duplikat internal (dari generator kami sendiri)
                while (isset($generated[$idAsap])) {
                    $nomorUrut++;
                    $kodeAsap++;
                    $idAsap = sprintf('1.%03d.%04d.%03d', $kodeAsap, $profitCenter, $nomorUrut);
                }

                $generated[$idAsap] = true;
                $nextIds[$i] = $idAsap;
            }

            $existingDup = DB::table('dir_aset')
                ->whereIn('id_aset', array_values($nextIds))
                ->pluck('id_aset')
                ->toArray();

            if (!empty($existingDup)) {
                // Jika ternyata sudah ada (race condition), generate ulang secara deterministik untuk baris yang bentrok.
                // (fallback sederhana: jalankan loop generate sampai aman)
                foreach ($rowsData as $idx => $dataRow) {
                    $currentId = $nextIds[$idx];
                    $guard = 0;
                    while (in_array($currentId, $existingDup, true) && $guard < 1000) {
                        $guard++;
                        $kodeAsap++;
                        $nomorUrut++;
                        $currentId = sprintf('1.%03d.%04d.%03d', $kodeAsap, $profitCenter, $nomorUrut);
                    }
                    $nextIds[$idx] = $currentId;
                }
            }

            DB::beginTransaction();
            try {
                foreach ($rowsData as $idx => $dataRow) {
                    $validated = $dataRow;
                    $validated['id_aset'] = $nextIds[$idx];

                    DB::table('dir_aset')->insert(array_merge($validated, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                return Response::json([
                    'success' => false,
                    'title' => 'Import Gagal',
                    'errors' => [self::simplifyDbError($e->getMessage())],
                ]);
            }

            // pastikan respons tidak selalu dianggap gagal saat ada "0 baris bermasalah"
            return Response::json([
                'success' => true,
                'title' => 'Import Berhasil',
                'success_count' => count($rowsData),
                'errors' => [],
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'title' => 'Gagal Membaca File',
                'errors' => ['Gagal membaca file Excel: ' . $e->getMessage()],
            ]);
        }
    }

    private function generateIdAsetNext(): string
    {
        // Jika DB belum siap / gagal connect, jangan sampai import langsung crash.
        // Tetap generate id awal, sehingga proses upload file tetap berjalan.
        try {
            $row = DB::table('dir_aset')
                ->selectRaw('MAX(id_aset) as max_id_aset')
                ->value('max_id_aset');
        } catch (\Throwable) {
            $row = null;
        }

        $startNomor = 1;
        $startKodeAsap = 1;
        $startProfitCenter = '3000';

        if (!$row) {
            return sprintf('1.%03d.%s.%03d', $startKodeAsap, $startProfitCenter, $startNomor);
        }

        if (!preg_match('/^1\.(\d{3})\.(\d{4})\.(\d{3})$/', (string) $row, $m)) {
            return sprintf('1.%03d.%s.%03d', $startKodeAsap, $startProfitCenter, $startNomor);
        }

        $kodeAsap = (int) $m[1];
        $profitCenter = (int) $m[2];
        $nomorUrut = (int) $m[3];

        $nextNomor = $nomorUrut + 1;
        $nextKodeAsap = $kodeAsap + 1;

        return sprintf('1.%03d.%04d.%03d', $nextKodeAsap, $profitCenter, $nextNomor);
    }

    public function index(Request $request)
    {
        try {
            $query = $this->buildDirIndexQuery($request);

            $total = $query->count();
            $items = $query
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();
        } catch (\Throwable $e) {
            // Jika DB belum available (Connection refused), tampilkan halaman kosong agar UI tidak 500.
            $total = 0;
            // paginate hanya ada di Query Builder, bukan Collection
            $items = collect();
        }

        return View::make('ga.dir.index', [
            'items' => $items,
            'total' => $total,
            'request' => $request,
        ]);
    }

    private function buildDirIndexQuery(Request $request)
    {
        $query = DB::table('dir_aset');

        $search = (string) ($request->search ?? '');
        if ($search !== '') {
            $s = '%' . $search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('kode_aset', 'like', $s)
                    ->orWhere('deskripsi_aset', 'like', $s)
                    ->orWhere('pemilik', 'like', $s)
                    ->orWhere('lokasi_pemakai', 'like', $s)
                    ->orWhere('unit_bisnis', 'like', $s)
                    ->orWhere('kategori_aset', 'like', $s)
                    ->orWhere('golongan_aset', 'like', $s)
                    ->orWhere('profit_center', 'like', $s)
                    ->orWhere('cost_center', 'like', $s)
                    ->orWhere('id_aset', 'like', $s)
                    ->orWhere('keterangan', 'like', $s);
            });
        }

        $kode_aset = (string) ($request->kode_aset ?? '');
        if ($kode_aset !== '') {
            $query->where('kode_aset', $kode_aset);
        }

        $unit_bisnis = (string) ($request->unit_bisnis ?? '');
        if ($unit_bisnis !== '') {
            $query->where('unit_bisnis', $unit_bisnis);
        }

        $kategori_aset = (string) ($request->kategori_aset ?? '');
        if ($kategori_aset !== '') {
            $query->where('kategori_aset', $kategori_aset);
        }

        $tahun = (string) ($request->tahun ?? '');
        if ($tahun !== '') {
            $query->where('tahun', $tahun);
        }

        return $query;
    }


    public function create()
    {
        return View::make('ga.dir.create', [
            'unitBisnisList' => $this->unitBisnisList,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_center' => 'required|string|max:100',
            'profit_center' => 'required|string|max:100',
            'unit_bisnis' => 'required|string|max:100',
            'golongan_aset' => 'required|string|max:100',
            'kategori_aset' => 'required|string|max:100',
            'deskripsi_aset' => 'required|string|max:255',
            'lokasi_pemakai' => 'required|string|max:255',
            'kode_aset' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['id_aset'] = $this->generateIdAsetNext();

        DB::table('dir_aset')->insert(array_merge($validated, [
            'created_at' => Now::now(),
            'updated_at' => Now::now(),
        ]));

        return Redirect::route('ga.dir.index')->with('success', 'Data DIR berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('dir_aset')->where('id', $id)->first();
        if (!$item) {
            Abort::abort(404, 'Data DIR tidak ditemukan.');
        }

        return View::make('ga.dir.edit', [
            'item' => $item,
            'unitBisnisList' => $this->unitBisnisList,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('dir_aset')->where('id', $id)->first();
        if (!$item) {
            Abort::abort(404, 'Data DIR tidak ditemukan.');
        }

        $validated = $request->validate([
            'cost_center' => 'required|string|max:100',
            'profit_center' => 'required|string|max:100',
            'unit_bisnis' => 'required|string|max:100',
            'golongan_aset' => 'required|string|max:100',
            'kategori_aset' => 'required|string|max:100',
            'deskripsi_aset' => 'required|string|max:255',
            'lokasi_pemakai' => 'required|string|max:255',
            'kode_aset' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        DB::table('dir_aset')->where('id', $id)->update(array_merge($validated, [
            'id_aset' => $item->id_aset,
            'updated_at' => Now::now(),
        ]));

        return Redirect::route('ga.dir.index')->with('success', 'Data DIR berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $deleted = DB::table('dir_aset')->where('id', $id)->delete();
        if (!$deleted) {
            return Redirect::back()->with('error', 'Data DIR tidak ditemukan.');
        }

        return Redirect::route('ga.dir.index')->with('success', 'Data DIR berhasil dihapus.');
    }
}
