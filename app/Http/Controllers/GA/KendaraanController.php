<?php

namespace App\Http\Controllers\GA;

use App\Http\Controllers\Controller;
use App\Traits\SimplifiesDbErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KendaraanExport;
use App\Imports\KendaraanImport;
use Barryvdh\DomPDF\Facade\Pdf;

class KendaraanController extends Controller
{
    use SimplifiesDbErrors, \App\Traits\ExcelDropdownHelper;
    // Status valid kendaraan
    private array $statusList = ['Layak', 'Tidak Layak', 'Dilelang', 'Hilang'];
    private array $jenisKendaraanList = ['Mobil', 'Motor', 'Truk', 'Minibus', 'Bus'];

    // Dropdown Bisnis Manager
    private array $branchManagerList = [
        'Kantor Pusat',
        'Jember',
        'Surabaya',
        'Madura',
        'Aceh',
        'Ambon',
        'Balikpapan',
        'Bandung',
        'Bangka Belitung',
        'Banjarmasin',
        'Batam',
        'Bekasi',
        'Bogor',
        'Cilegon',
        'Jayapura',
        'Cirebon',
        'Denpasar',
        'Depok',
        'Gorontalo',
        'Gresik',
        'Jambi',
        'Jaya 1',
        'Jaya 2',
        'Karawang',
        'Kendari',
        'Kupang',
        'Lampung',
        'Makassar',
        'Malang',
        'Manado',
        'Mataram',
        'Medan',
        'Nusa Dua',
        'Palangkaraya',
        'Padang',
        'Palembang',
        'Palu',
        'Pekalongan',
        'Pekanbaru',
        'Pontianak',
        'Purwokerto',
        'Samarinda',
        'Semarang',
        'Sidoarjo',
        'Sukabumi',
        'Surakarta',
        'Tangerang',
        'Sorong',
        'Tanjung Pinang',
        'Tasikmalaya',
        'Ternate',
        'Yogyakarta',
    ];


    public function index(Request $request)
    {
        $query = $this->buildIndexQuery($request);

        $total = $query->count();
        $kendaraan = $query
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('ga.kendaraan.index', [
            'kendaraan' => $kendaraan,
            'total' => $total,
            'branches' => $this->getBranches(),
            'stats' => $this->getStats(),
            'request' => $request,
            'jenisKendaraanList' => $this->jenisKendaraanList,
            'statusList' => $this->statusList,
        ]);

    }

    private function buildIndexQuery(Request $request)
    {
        $query = DB::table('kendaraan_aset');

        // Filter
        if ($request->filled('manager')) {
            $query->where('branch_manager', $request->manager);
        }
        if ($request->filled('jenis_kendaraan')) {
            $query->where('jenis_kendaraan', $request->jenis_kendaraan);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('merk', 'like', $s)
                    ->orWhere('type', 'like', $s)
                    ->orWhere('no_posisi', 'like', $s)
                    ->orWhere('branch_manager', 'like', $s)
                    ->orWhere('no_polisi', 'like', $s);
            });
        }

        return $query;
    }

    private function getBranches()
    {
        return DB::table('kendaraan_aset')->distinct()->pluck('branch_manager');
    }

    private function getStats()
    {
        return DB::table('kendaraan_aset')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }


    public function create()
    {
        return view('ga.kendaraan.create', [
            'statusList' => $this->statusList,
            'jenisKendaraanList' => $this->jenisKendaraanList,
            'branchManagerList' => $this->branchManagerList,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_posisi' => 'required|string|max:50',
            'branch_manager' => 'required|in:' . implode(',', $this->branchManagerList),
            'jenis_kendaraan' => 'required|string|max:50',
            'merk' => 'required|string|max:50',
            'type' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:30',
            'tahun_pembuatan' => 'nullable|digits:4|integer',
            'no_mesin' => 'nullable|string|max:100|unique:kendaraan_aset,no_mesin',
            'no_rangka' => 'nullable|string|max:100|unique:kendaraan_aset,no_rangka',
            'no_bpkb' => 'nullable|string|max:100|unique:kendaraan_aset,no_bpkb',
            'no_polisi' => 'nullable|string|max:20',
            'masa_berakhir_1th' => 'nullable|date',
            'masa_berakhir_5th' => 'nullable|date',
            'status' => 'required|in:' . implode(',', $this->statusList),
            'tahun_perolehan' => 'nullable|digits:4|integer',
            'harga_perolehan' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('kendaraan_aset')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('ga.kendaraan.index')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kendaraan = DB::table('kendaraan_aset')->where('id', $id)->first();
        if (!$kendaraan) {
            abort(404, 'Data kendaraan tidak ditemukan.');
        }

        return view('ga.kendaraan.edit', [
            'kendaraan' => $kendaraan,
            'statusList' => $this->statusList,
            'jenisKendaraanList' => $this->jenisKendaraanList,
            'branchManagerList' => $this->branchManagerList,
        ]);

    }

    public function update(Request $request, int $id)
    {
        $kendaraan = DB::table('kendaraan_aset')->where('id', $id)->first();
        if (!$kendaraan) {
            abort(404);
        }

        $validated = $request->validate([
            'no_posisi' => 'required|string|max:50',
            'branch_manager' => 'required|in:' . implode(',', $this->branchManagerList),
            'jenis_kendaraan' => 'required|string|max:50',
            'merk' => 'required|string|max:50',
            'type' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:30',
            'tahun_pembuatan' => 'nullable|digits:4|integer',
            'no_mesin' => 'nullable|string|max:100|unique:kendaraan_aset,no_mesin,' . $id,
            'no_rangka' => 'nullable|string|max:100|unique:kendaraan_aset,no_rangka,' . $id,
            'no_bpkb' => 'nullable|string|max:100|unique:kendaraan_aset,no_bpkb,' . $id,
            'no_polisi' => 'nullable|string|max:20',
            'masa_berakhir_1th' => 'nullable|date',
            'masa_berakhir_5th' => 'nullable|date',
            'status' => 'required|in:' . implode(',', $this->statusList),
            'tahun_perolehan' => 'nullable|digits:4|integer',
            'harga_perolehan' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('kendaraan_aset')->where('id', $id)->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

        return redirect()->route('ga.kendaraan.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $deleted = DB::table('kendaraan_aset')->where('id', $id)->delete();
        if (!$deleted) {
            return back()->with('error', 'Data tidak ditemukan.');
        }
        return redirect()->route('ga.kendaraan.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $query = DB::table('kendaraan_aset');
        if ($request->filled('manager'))
            $query->where('branch_manager', $request->manager);
        if ($request->filled('jenis_kendaraan'))
            $query->where('jenis_kendaraan', $request->jenis_kendaraan);
        if ($request->filled('status'))
            $query->where('status', $request->status);
        $data = $query->orderBy('branch_manager')->orderBy('id')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Kimia Farma Asset System')
            ->setTitle('Kendaraan - ' . date('Y-m-d'))
            ->setDescription('Export data kendaraan: ' . $data->count() . ' records');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kendaraan');

        $colMap = [
            'A' => 'No',
            'B' => 'No Posisi',
            'C' => 'Bisnis Manager',
            'D' => 'Jenis Kendaraan',
            'E' => 'Merk',
            'F' => 'Type',
            'G' => 'Warna',
            'H' => 'Tahun Pembuatan',
            'I' => 'No Mesin',
            'J' => 'No Rangka',
            'K' => 'No BPKB',
            'L' => 'No Polisi',
            'M' => 'Masa Berakhir 1 Th',
            'N' => 'Masa Berakhir 5 Th',
            'O' => 'Status',
            'P' => 'Tahun Perolehan',
            'Q' => 'Harga Perolehan (Rp)',
            'R' => 'Keterangan',
        ];
        $lastCol = 'R';

        foreach ($colMap as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

        $rowNum = 2;
        foreach ($data as $i => $row) {
            $sheet->setCellValue('A' . $rowNum, $i + 1);
            $sheet->setCellValue('B' . $rowNum, $row->no_posisi ?? '');
            $sheet->setCellValue('C' . $rowNum, $row->branch_manager ?? '');
            $sheet->setCellValue('D' . $rowNum, $row->jenis_kendaraan ?? '');
            $sheet->setCellValue('E' . $rowNum, $row->merk ?? '');
            $sheet->setCellValue('F' . $rowNum, $row->type ?? '');
            $sheet->setCellValue('G' . $rowNum, $row->warna ?? '');
            $sheet->setCellValue('H' . $rowNum, $row->tahun_pembuatan ?? '');
            $sheet->setCellValue('I' . $rowNum, $row->no_mesin ?? '');
            $sheet->setCellValue('J' . $rowNum, $row->no_rangka ?? '');
            $sheet->setCellValue('K' . $rowNum, $row->no_bpkb ?? '');
            $sheet->setCellValue('L' . $rowNum, $row->no_polisi ?? '');
            $sheet->setCellValue('M' . $rowNum, $row->masa_berakhir_1th ?? '');
            $sheet->setCellValue('N' . $rowNum, $row->masa_berakhir_5th ?? '');
            $sheet->setCellValue('O' . $rowNum, $row->status ?? '');
            $sheet->setCellValue('P' . $rowNum, $row->tahun_perolehan ?? '');
            $sheet->setCellValue('Q' . $rowNum, $row->harga_perolehan ?? 0);
            $sheet->setCellValue('R' . $rowNum, $row->keterangan ?? '');
            $rowNum++;
        }

        if ($data->count() > 0) {
            $sheet->getStyle('A2:' . $lastCol . ($rowNum - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
        }

        // Format angka harga
        $sheet->getStyle('Q2:Q' . ($rowNum - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Data validation Jenis Kendaraan (D)
        $dvJenis = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvJenis->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(false)->setShowDropDown(true)
            ->setFormula1('"Mobil,Motor"')
            ->setPromptTitle('Jenis Kendaraan')->setPrompt('Pilih jenis kendaraan')
            ->setErrorTitle('Error')->setError('Jenis Kendaraan tidak valid');
        $sheet->setDataValidation('D2:D1000', $dvJenis);

        // Data validation Bisnis Manager (C) — dari hidden sheet
        $this->addDropdownFromHiddenSheet(
            $spreadsheet,
            '_BM_List',
            $this->branchManagerList,
            'BisnisMgrList',
            'C2:C1000',
            'Bisnis Manager',
            'Pilih Bisnis Manager dari daftar',
            'Input tidak valid',
            'Nilai tidak ada dalam daftar Bisnis Manager.'
        );

        // Data validation Status (O)
        $dvStatus = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvStatus->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(false)->setShowDropDown(true)
            ->setFormula1('"Layak,Tidak Layak,Dilelang,Hilang"')
            ->setPromptTitle('Status')->setPrompt('Pilih status kendaraan')
            ->setErrorTitle('Error')->setError('Status tidak valid');
        $sheet->setDataValidation('O2:O1000', $dvStatus);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'kendaraan_export_' . date('Y-m-d_H-i') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kendaraan');

        // Header sesuai template: No, No Posisi, Bisnis Manager, Jenis Kendaraan, Merk, Type,
        //                         Warna, Tahun Pembuatan, No Mesin, No Rangka, No BPKB, No Polisi,
        //                         Masa Berakhir 1 Th, Masa Berakhir 5 Th, Status,
        //                         Tahun Perolehan, Harga Perolehan (Rp), Keterangan
        $headers = [
            'A' => 'No',
            'B' => 'No Posisi',
            'C' => 'Bisnis Manager',
            'D' => 'Jenis Kendaraan',
            'E' => 'Merk',
            'F' => 'Type',
            'G' => 'Warna',
            'H' => 'Tahun Pembuatan',
            'I' => 'No Mesin',
            'J' => 'No Rangka',
            'K' => 'No BPKB',
            'L' => 'No Polisi',
            'M' => 'Masa Berakhir 1 Th',
            'N' => 'Masa Berakhir 5 Th',
            'O' => 'Status',
            'P' => 'Tahun Perolehan',
            'Q' => 'Harga Perolehan (Rp)',
            'R' => 'Keterangan',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        // Style header: biru #4472C4, font putih bold, border tipis
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Dropdown Jenis Kendaraan (D2) sesuai template asli
        $dvJenis = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvJenis->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setFormula1('"Mobil,Motor"')
            ->setPromptTitle('Jenis Kendaraan')
            ->setPrompt('Pilih jenis kendaraan')
            ->setErrorTitle('Input tidak valid')
            ->setError('Pilih salah satu: Mobil atau Motor');
        $sheet->setDataValidation('D2:D1000', $dvJenis);

        // Dropdown Bisnis Manager (C2) — dari hidden sheet karena list panjang
        $this->addDropdownFromHiddenSheet(
            $spreadsheet,
            '_BM_List',
            $this->branchManagerList,
            'BisnisMgrList',
            'C2:C1000',
            'Bisnis Manager',
            'Pilih Bisnis Manager dari daftar',
            'Input tidak valid',
            'Nilai tidak ada dalam daftar Bisnis Manager.'
        );

        // Dropdown Status (O2) sesuai template asli
        $dvStatus = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvStatus->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setFormula1('"Layak,Tidak Layak,Dilelang,Hilang"')
            ->setPromptTitle('Status Kendaraan')
            ->setPrompt('Pilih status kendaraan')
            ->setErrorTitle('Input tidak valid')
            ->setError('Pilih salah satu: Layak, Tidak Layak, Dilelang, atau Hilang');
        $sheet->setDataValidation('O2:O1000', $dvStatus);

        // Lebar kolom sesuai template asli (fixed width)
        $colWidths = [
            'A' => 5.71,
            'B' => 16.43,
            'C' => 21.14,
            'D' => 20.99,
            'E' => 14.00,
            'F' => 17.56,
            'G' => 12.85,
            'H' => 20.99,
            'I' => 14.00,
            'J' => 23.43,
            'K' => 16.43,
            'L' => 14.00,
            'M' => 24.56,
            'N' => 24.56,
            'O' => 14.00,
            'P' => 20.99,
            'Q' => 26.99,
            'R' => 15.14,
        ];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Freeze baris header
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'template excel kendaraan.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'title' => 'File Kosong',
                    'errors' => ['File Excel kosong atau tidak ada data.'],
                ]);
            }

            $headerRow = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);

            $map = [
                'no_posisi' => ['no posisi', 'no_posisi'],
                'branch_manager' => ['bisnis manager', 'branch manager', 'branch_manager', 'cabang'],
                'jenis_kendaraan' => ['jenis kendaraan', 'jenis_kendaraan', 'jenis'],
                'merk' => ['merk', 'merek'],
                'type' => ['type', 'tipe'],
                'warna' => ['warna'],
                'tahun_pembuatan' => ['tahun pembuatan', 'tahun_pembuatan'],
                'no_mesin' => ['no mesin', 'no_mesin'],
                'no_rangka' => ['no rangka', 'no_rangka'],
                'no_bpkb' => ['no bpkb', 'no_bpkb'],
                'no_polisi' => ['no polisi', 'no_polisi', 'plat'],
                'masa_berakhir_1th' => ['masa berakhir 1 th', 'masa_berakhir_1th', 'pajak 1th'],
                'masa_berakhir_5th' => ['masa berakhir 5 th', 'masa_berakhir_5th', 'stnk 5th'],
                'status' => ['status'],
                'tahun_perolehan' => ['tahun perolehan', 'tahun_perolehan'],
                'harga_perolehan' => ['harga perolehan (rp)', 'harga perolehan', 'harga_perolehan', 'harga'],
                'keterangan' => ['keterangan', 'catatan'],
            ];

            $colMap = [];
            foreach ($map as $field => $aliases) {
                foreach ($headerRow as $i => $h) {
                    if (in_array($h, $aliases)) {
                        $colMap[$field] = $i;
                        break;
                    }
                }
            }

            $now = now();

            // ── PASS 1: Validasi semua baris ──────────────────────────────────
            $errors = [];
            $rowsData = [];
            foreach (array_slice($rows, 1) as $rowNum => $row) {
                if (empty(array_filter($row)))
                    continue;

                $data = [];
                foreach ($colMap as $field => $idx) {
                    $data[$field] = isset($row[$idx]) && $row[$idx] !== '' ? trim((string) $row[$idx]) : null;
                }

                if (empty($data['no_posisi']) || empty($data['branch_manager']) || empty($data['merk'])) {
                    $errors[] = "Baris " . ($rowNum + 2) . ": No Posisi, Bisnis Manager, dan Merk wajib diisi.";
                    continue;
                }

                // Konversi kolom tanggal
                foreach (['masa_berakhir_1th', 'masa_berakhir_5th'] as $dateField) {
                    if (isset($colMap[$dateField])) {
                        $data[$dateField] = self::parseExcelDate($data[$dateField] ?? null);
                    }
                }

                $rowsData[] = array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
            }

            // Jika ada error validasi, tolak seluruh import
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

            // ── PASS 2: Insert semua dalam satu transaksi ─────────────────────
            DB::beginTransaction();
            try {
                foreach ($rowsData as $data) {
                    DB::table('kendaraan_aset')->insert($data);
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

    // ─── Helper: generate Excel sederhana ────────────────────────────────────
    private function generateExcel(string $filename, array $headers, array $rows, string $title = ''): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($title ?: 'Data', 0, 31));

        // Header
        foreach ($headers as $i => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Data
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($col . ($rowIndex + 2), $value);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
