<?php

namespace App\Http\Controllers\GA;

use App\Http\Controllers\Controller;
use App\Traits\SimplifiesDbErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TanahBangunanController extends Controller
{
    use SimplifiesDbErrors, \App\Traits\ExcelDropdownHelper;
    private array $statusList = ['Aktif/SHM', 'Aktif/SHGB', 'Non-Aktif/Dijual', 'Sengketa'];

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
        $landBuildingAssets = $query
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('ga.tanah_bangunan.index', [
            // Blade memakai variabel $tanahBangunan
            'tanahBangunan' => $landBuildingAssets,
            'total' => $total,
            'branches' => $this->getBranches(),
            'stats' => $this->getStats(),
            'request' => $request,
            'statusList' => $this->statusList,
        ]);

    }

    private function buildIndexQuery(Request $request)
    {
        $query = DB::table('tanah_bangunan_aset');

        if ($request->filled('manager')) {
            $query->where('branch_manager', $request->manager);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('no_asset_tanah', 'like', $s)
                    ->orWhere('branch_manager', 'like', $s)
                    ->orWhere('alamat', 'like', $s)
                    ->orWhere('kode_sap', 'like', $s)
                    ->orWhere('nomor_sertifikat_baru', 'like', $s);
            });
        }

        return $query;
    }

    private function getBranches()
    {
        return DB::table('tanah_bangunan_aset')->distinct()->pluck('branch_manager');
    }

    private function getStats()
    {
        return DB::table('tanah_bangunan_aset')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }


    public function create()
    {
        return view('ga.tanah_bangunan.create', [
            'statusList' => $this->statusList,
            'branchManagerList' => $this->branchManagerList,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_sap' => 'nullable|string|max:50|unique:tanah_bangunan_aset,kode_sap',
            'no_asset_tanah' => 'required|string|max:100|unique:tanah_bangunan_aset,no_asset_tanah',
            'branch_manager' => 'required|in:' . implode(',', $this->branchManagerList),
            'digunakan_sebagai' => 'nullable|string|max:100',
            'penggunaan' => 'nullable|string|max:100',
            'no_posisi_gedung' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'luas_tanah' => 'required|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'tahun_perolehan' => 'nullable|digits:4|integer',
            'nomor_sertifikat_baru' => 'required|string|max:100|unique:tanah_bangunan_aset,nomor_sertifikat_baru',
            'masa_berlaku' => 'nullable|date',
            'status' => 'required|in:' . implode(',', $this->statusList),
            'keterangan' => 'nullable|string',
        ]);

        DB::table('tanah_bangunan_aset')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('ga.tanah_bangunan.index')->with('success', 'Data aset tanah/bangunan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $aset = DB::table('tanah_bangunan_aset')->where('id', $id)->first();
        if (!$aset)
            abort(404);

        return view('ga.tanah_bangunan.edit', compact('aset') + [
            'statusList' => $this->statusList,
            'branchManagerList' => $this->branchManagerList,
        ]);

    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'kode_sap' => 'nullable|string|max:50|unique:tanah_bangunan_aset,kode_sap,' . $id,
            'no_asset_tanah' => 'required|string|max:100|unique:tanah_bangunan_aset,no_asset_tanah,' . $id,
            'branch_manager' => 'required|in:' . implode(',', $this->branchManagerList),
            'digunakan_sebagai' => 'nullable|string|max:100',
            'penggunaan' => 'nullable|string|max:100',
            'no_posisi_gedung' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'luas_tanah' => 'required|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'tahun_perolehan' => 'nullable|digits:4|integer',
            'nomor_sertifikat_baru' => 'required|string|max:100|unique:tanah_bangunan_aset,nomor_sertifikat_baru,' . $id,
            'masa_berlaku' => 'nullable|date',
            'status' => 'required|in:' . implode(',', $this->statusList),
            'keterangan' => 'nullable|string',
        ]);

        DB::table('tanah_bangunan_aset')->where('id', $id)->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

        return redirect()->route('ga.tanah_bangunan.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('tanah_bangunan_aset')->where('id', $id)->delete();
        return redirect()->route('ga.tanah_bangunan.index')->with('success', 'Data aset berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $query = DB::table('tanah_bangunan_aset');
        if ($request->filled('manager'))
            $query->where('branch_manager', $request->manager);
        if ($request->filled('status'))
            $query->where('status', $request->status);
        $data = $query->orderBy('branch_manager')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Kimia Farma Asset System')
            ->setTitle('Tanah & Bangunan - ' . date('Y-m-d'))
            ->setDescription('Export data tanah & bangunan: ' . $data->count() . ' records');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tanah_Bangunan');

        $colMap = [
            'A' => 'No',
            'B' => 'Kode SAP',
            'C' => 'No Asset Tanah',
            'D' => 'Bisnis Manager',
            'E' => 'Digunakan Sebagai',
            'F' => 'Penggunaan',
            'G' => 'No Posisi Gedung',
            'H' => 'Alamat',
            'I' => 'Luas Tanah (m²)',
            'J' => 'Luas Bangunan (m²)',
            'K' => 'Tahun Perolehan',
            'L' => 'Nomor Sertifikat Baru',
            'M' => 'Masa Berlaku',
            'N' => 'Status',
            'O' => 'Keterangan',
            'P' => 'Created At',
            'Q' => 'Updated At',
        ];
        $lastCol = 'Q';

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
            $sheet->setCellValue('B' . $rowNum, $row->kode_sap ?? '');
            $sheet->setCellValue('C' . $rowNum, $row->no_asset_tanah ?? '');
            $sheet->setCellValue('D' . $rowNum, $row->branch_manager ?? '');
            $sheet->setCellValue('E' . $rowNum, $row->digunakan_sebagai ?? '');
            $sheet->setCellValue('F' . $rowNum, $row->penggunaan ?? '');
            $sheet->setCellValue('G' . $rowNum, $row->no_posisi_gedung ?? '');
            $sheet->setCellValue('H' . $rowNum, $row->alamat ?? '');
            $sheet->setCellValue('I' . $rowNum, $row->luas_tanah ?? 0);
            $sheet->setCellValue('J' . $rowNum, $row->luas_bangunan ?? 0);
            $sheet->setCellValue('K' . $rowNum, $row->tahun_perolehan ?? '');
            $sheet->setCellValue('L' . $rowNum, $row->nomor_sertifikat_baru ?? '');
            $sheet->setCellValue('M' . $rowNum, $row->masa_berlaku ?? '');
            $sheet->setCellValue('N' . $rowNum, $row->status ?? '');
            $sheet->setCellValue('O' . $rowNum, $row->keterangan ?? '');
            $sheet->setCellValue('P' . $rowNum, $row->created_at ?? '');
            $sheet->setCellValue('Q' . $rowNum, $row->updated_at ?? '');
            $rowNum++;
        }

        if ($data->count() > 0) {
            $sheet->getStyle('A2:' . $lastCol . ($rowNum - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
        }

        // Format angka luas
        $sheet->getStyle('I2:J' . ($rowNum - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Data validation Status (N)
        $dvStatus = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvStatus->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setAllowBlank(true)->setShowDropDown(true)
            ->setFormula1('"Aktif/SHM,Aktif/SHGB,Non-Aktif/Dijual,Sengketa"')
            ->setPromptTitle('Status Aset')->setPrompt('Pilih status dari daftar')
            ->setErrorTitle('Input Error')->setError('Value is not in list.');
        $sheet->setDataValidation('N2:N1000', $dvStatus);

        // Data validation Bisnis Manager (D) — dari hidden sheet karena list panjang
        $this->addDropdownFromHiddenSheet(
            $spreadsheet,
            '_BM_List',
            $this->branchManagerList,
            'BisnisMgrList',
            'D2:D1000',
            'Bisnis Manager',
            'Pilih Bisnis Manager dari daftar',
            'Input tidak valid',
            'Nilai tidak ada dalam daftar Bisnis Manager.'
        );

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'tanah_bangunan_export_' . date('Y-m-d_H-i') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tanah_Bangunan');

        // Header sesuai template: No, Kode SAP, No Asset Tanah, Bisnis Manager,
        //                         Digunakan Sebagai, Penggunaan, No Posisi Gedung, Alamat,
        //                         Luas Tanah (m²), Luas Bangunan (m²), Tahun Perolehan,
        //                         Nomor Sertifikat Baru, Masa Berlaku, Status, Keterangan
        $headers = [
            'A' => 'No',
            'B' => 'Kode SAP',
            'C' => 'No Asset Tanah',
            'D' => 'Bisnis Manager',
            'E' => 'Digunakan Sebagai',
            'F' => 'Penggunaan',
            'G' => 'No Posisi Gedung',
            'H' => 'Alamat',
            'I' => 'Luas Tanah (m²)',
            'J' => 'Luas Bangunan (m²)',
            'K' => 'Tahun Perolehan',
            'L' => 'Nomor Sertifikat Baru',
            'M' => 'Masa Berlaku',
            'N' => 'Status',
            'O' => 'Keterangan',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        // Style header: biru #4472C4, font putih bold, border tipis
        $sheet->getStyle('A1:O1')->applyFromArray([
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

        // Dropdown Status (N2) sesuai template asli
        $dvStatus = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvStatus->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowDropDown(true)
            ->setFormula1('"Aktif/SHM,Aktif/SHGB,Non-Aktif/Dijual,Sengketa"')
            ->setPromptTitle('Status Aset')
            ->setPrompt('Pilih status dari daftar')
            ->setErrorTitle('Input tidak valid')
            ->setError('Pilih salah satu: Aktif/SHM, Aktif/SHGB, Non-Aktif/Dijual, atau Sengketa');
        $sheet->setDataValidation('N2:N1000', $dvStatus);

        // Dropdown Bisnis Manager (D2) — dari hidden sheet karena list panjang
        $this->addDropdownFromHiddenSheet(
            $spreadsheet,
            '_BM_List',
            $this->branchManagerList,
            'BisnisMgrList',
            'D2:D1000',
            'Bisnis Manager',
            'Pilih Bisnis Manager dari daftar',
            'Input tidak valid',
            'Nilai tidak ada dalam daftar Bisnis Manager.'
        );

        // Lebar kolom sesuai template asli (fixed width)
        $colWidths = [
            'A' => 5.71,
            'B' => 12.85,
            'C' => 19.85,
            'D' => 19.85,
            'E' => 28.14,
            'F' => 28.14,
            'G' => 22.28,
            'H' => 63.55,
            'I' => 20.99,
            'J' => 24.56,
            'K' => 20.99,
            'L' => 28.14,
            'M' => 17.56,
            'N' => 11.71,
            'O' => 58.85,
        ];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Freeze baris header
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'template excel tanah bangunan.xlsx',
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
            $colMap = [];

            $fieldMap = [
                'kode_sap' => ['kode sap', 'kode_sap'],
                'no_asset_tanah' => ['no asset tanah', 'no_asset_tanah'],
                'branch_manager' => ['bisnis manager', 'branch manager', 'branch_manager'],
                'digunakan_sebagai' => ['digunakan sebagai', 'digunakan_sebagai'],
                'penggunaan' => ['penggunaan'],
                'no_posisi_gedung' => ['no posisi gedung', 'no_posisi_gedung'],
                'alamat' => ['alamat'],
                'luas_tanah' => ['luas tanah (m²)', 'luas tanah (m2)', 'luas tanah', 'luas_tanah'],
                'luas_bangunan' => ['luas bangunan (m²)', 'luas bangunan (m2)', 'luas bangunan', 'luas_bangunan'],
                'tahun_perolehan' => ['tahun perolehan', 'tahun_perolehan'],
                'nomor_sertifikat_baru' => ['nomor sertifikat baru', 'nomor_sertifikat_baru'],
                'masa_berlaku' => ['masa berlaku', 'masa_berlaku'],
                'status' => ['status'],
                'keterangan' => ['keterangan'],
            ];

            foreach ($fieldMap as $field => $aliases) {
                foreach ($headerRow as $i => $h) {
                    if (in_array($h, $aliases)) {
                        $colMap[$field] = $i;
                        break;
                    }
                }
            }

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
                if (empty($data['no_asset_tanah']) || empty($data['branch_manager'])) {
                    $errors[] = "Baris " . ($rowNum + 2) . ": No Asset Tanah & Bisnis Manager wajib diisi.";
                    continue;
                }
                // Konversi kolom tanggal
                if (isset($colMap['masa_berlaku'])) {
                    $data['masa_berlaku'] = self::parseExcelDate($data['masa_berlaku'] ?? null);
                }
                $rowsData[] = $data;
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
                    DB::table('tanah_bangunan_aset')->insert(array_merge($data, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
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
}
