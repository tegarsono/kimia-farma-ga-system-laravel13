<?php

namespace App\Http\Controllers\AcMonitoring;

use App\Http\Controllers\Controller;
use App\Traits\SimplifiesDbErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class MonitoringController extends Controller
{
    use SimplifiesDbErrors;
    public function index(Request $request)
    {
        $query = DB::table('tb_monitoring');

        if ($request->filled('jenis_barang')) {
            $query->where('jenis_barang', $request->jenis_barang);
        }
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('kode_ga', 'like', $s)
                    ->orWhere('lokasi', 'like', $s)
                    ->orWhere('nama_barang', 'like', $s);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $totalItems = $query->count();
        $monitoring = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $itemTypeList = DB::table('tb_monitoring')->distinct()->pluck('jenis_barang');

        // Statistics
        $statsNormal = DB::table('tb_monitoring')->where('status', 'Normal')->count();
        $statsNeedsService = DB::table('tb_monitoring')->where('status', 'Wajib Service')->count();

        // Items that need service (not maintained for more than 3 months)
        $thresholdDate = now()->subMonths(3)->toDateString();
        $needService = DB::table('tb_monitoring')
            ->where('tgl_perawatan_terakhir', '<', $thresholdDate)
            ->count();

        return view('ac_monitoring.index', [
            'monitoring' => $monitoring,
            'totalItems' => $totalItems,
            'itemTypeList' => $itemTypeList,
            'statsNormal' => $statsNormal,
            'statsWajibService' => $statsNeedsService,
            'needService' => $needService,
            'jenisBarangList' => $itemTypeList,
        ]);





    }

    public function create()
    {
        $jenisBarangList = DB::table('tb_monitoring')->distinct()->pluck('jenis_barang');
        return view('ac_monitoring.create', [
            'jenisBarangList' => $jenisBarangList,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'kode_ga' => 'nullable|string|max:50',
            'lokasi' => 'required|string|max:100',
            'nama_barang' => 'required|string',
            'jenis_barang' => 'required|string',
            'tgl_perawatan_terakhir' => 'required|date',
            'status' => 'required|in:Normal,Wajib Service',
            'keterangan' => 'nullable|string',
        ]);

        $this->validateStatusAgainstDate($request);

        DB::table('tb_monitoring')->insert($request->only([
            'kode_ga',
            'lokasi',
            'nama_barang',
            'jenis_barang',
            'tgl_perawatan_terakhir',
            'status',
            'keterangan',
        ]));

        return redirect()->route('ac.index')->with('success', 'Data monitoring berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('tb_monitoring')->where('id', $id)->first();
        if (!$item)
            abort(404);

        $jenisBarangList = DB::table('tb_monitoring')->distinct()->pluck('jenis_barang');
        return view('ac_monitoring.edit', [
            'item' => $item,
            'jenisBarangList' => $jenisBarangList,
        ]);

    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'kode_ga' => 'nullable|string|max:50',
            'lokasi' => 'required|string|max:100',
            'nama_barang' => 'required|string',
            'jenis_barang' => 'required|string',
            'tgl_perawatan_terakhir' => 'required|date',
            'status' => 'required|in:Normal,Wajib Service',
            'keterangan' => 'nullable|string',
        ]);

        $this->validateStatusAgainstDate($request);

        DB::table('tb_monitoring')->where('id', $id)->update($request->only([
            'kode_ga',
            'lokasi',
            'nama_barang',
            'jenis_barang',
            'tgl_perawatan_terakhir',
            'status',
            'keterangan',
        ]));

        return redirect()->route('ac.index')->with('success', 'Data monitoring berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('tb_monitoring')->where('id', $id)->delete();
        return redirect()->route('ac.index')->with('success', 'Data monitoring berhasil dihapus.');
    }

    /**
     * If status is set to "Normal", make sure the last maintenance date
     * is not more than 3 months ago. If more than 3 months have passed,
     * the "Normal" status is invalid — the item should be "Wajib Service"
     * (Needs Service) instead.
     */
    private function validateStatusAgainstDate(Request $request): void
    {
        if ($request->status !== 'Normal') {
            return;
        }

        $date = \Carbon\Carbon::parse($request->tgl_perawatan_terakhir);
        $threshold = now()->subMonths(3);

        if ($date->lt($threshold)) {
            $diff = $date->diffForHumans(now(), true);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => [
                    "Status tidak bisa diset \"Normal\" karena tanggal perawatan terakhir sudah {$diff} yang lalu (lebih dari 3 bulan). "
                    . "Perbarui tanggal perawatan terakhir ke tanggal servis yang sesungguhnya, atau pilih status \"Wajib Service\".",
                ],
            ]);
        }
    }

    // Halaman notifikasi (router menggunakan method `notifikasi`)
    public function notifikasi(Request $request)
    {
        $notifikasi = $this->getNotificationsQuery()->paginate(20);
        return view('ac_monitoring.notifikasi', [
            'notifikasi' => $notifikasi,
        ]);
    }



    private function getNotificationsQuery()
    {
        $thresholdDate = now()->subMonths(3)->toDateString();

        return DB::table('tb_monitoring')
            ->where('status', 'Wajib Service')
            ->orderBy('tgl_perawatan_terakhir');
    }


    public function bulkMarkServiced(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:tb_monitoring,id',
        ], [
            'selected_ids.required' => 'Pilih minimal satu item terlebih dahulu.',
            'selected_ids.min' => 'Pilih minimal satu item terlebih dahulu.',
        ]);

        $selectedIds = $validated['selected_ids'];

        DB::table('tb_monitoring')
            ->whereIn('id', $selectedIds)
            ->update([
                'status' => 'Normal',
                'tgl_perawatan_terakhir' => now()->toDateString(),
            ]);

        $count = count($selectedIds);
        return redirect()->route('ac.notifikasi')
            ->with('success', "{$count} item berhasil ditandai sudah diservice. Status diubah ke Normal dan tanggal perawatan diperbarui ke hari ini.");
    }


    public function generatePdf(Request $request)
    {
        $data = DB::table('tb_monitoring')->orderBy('jenis_barang')->orderBy('lokasi')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ac_monitoring.pdf_all', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('monitoring_ga_' . date('Ymd_His') . '.pdf');
    }

    public function generatePdfSelected(Request $request)
    {
        $request->validate(['selected_ids' => 'required|array']);

        $data = DB::table('tb_monitoring')
            ->whereIn('id', $request->selected_ids)
            ->orderBy('jenis_barang')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ac_monitoring.pdf_selected', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('monitoring_selected_' . date('Ymd_His') . '.pdf');
    }

    public function recapPdf(Request $request)
    {
        $month = $request->input('bulan', date('m'));
        $year = $request->input('tahun', date('Y'));

        $data = DB::table('tb_monitoring')
            ->whereRaw('MONTH(tgl_perawatan_terakhir) = ?', [$month])
            ->whereRaw('YEAR(tgl_perawatan_terakhir) = ?', [$year])
            ->orderBy('jenis_barang')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ac_monitoring.pdf_rekap', compact('data', 'month', 'year'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("recap_monitoring_{$month}_{$year}.pdf");
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('AC Monitoring');

        // Headers per template: No, Kode GA, Nama Barang, Jenis Barang, Lokasi,
        //                       Tgl Perawatan Terakhir, Status, Keterangan
        $headers = [
            'A' => 'No',
            'B' => 'Kode GA',
            'C' => 'Nama Barang',
            'D' => 'Jenis Barang',
            'E' => 'Lokasi',
            'F' => 'Tgl Perawatan Terakhir',
            'G' => 'Status',
            'H' => 'Keterangan',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        // Header style: blue #0056B3, white bold font, thin border
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0056B3'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Status dropdown in column G (G2:G995 as in the original template)
        $dv = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dv->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(true)
            ->setShowDropDown(true)
            ->setFormula1('"Normal,Wajib Service"')
            ->setPromptTitle('Status')
            ->setPrompt('Pilih status: Normal atau Wajib Service')
            ->setErrorTitle('Input tidak valid')
            ->setError('Pilih salah satu: Normal atau Wajib Service');
        $sheet->setDataValidation('G2:G995', $dv);

        // Auto-size all columns
        foreach (array_keys($headers) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'template excel ac monitoring.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function exportExcel(Request $request)
    {
        $query = DB::table('tb_monitoring');
        if ($request->filled('jenis_barang'))
            $query->where('jenis_barang', $request->jenis_barang);
        if ($request->filled('status'))
            $query->where('status', $request->status);
        $data = $query->orderBy('jenis_barang')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Kimia Farma AC Monitoring')
            ->setTitle('AC Monitoring - ' . date('Y-m-d'))
            ->setDescription('Export data tb_monitoring: ' . $data->count() . ' records');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('AC Monitoring');

        $colMap = [
            'A' => 'No',
            'B' => 'Kode GA',
            'C' => 'Nama Barang',
            'D' => 'Jenis Barang',
            'E' => 'Lokasi',
            'F' => 'Tgl Perawatan Terakhir',
            'G' => 'Status',
            'H' => 'Keterangan',
        ];
        $lastCol = 'H';

        foreach ($colMap as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0056b3']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

        $rowNum = 2;
        foreach ($data as $i => $row) {
            $sheet->setCellValue('A' . $rowNum, $i + 1);
            $sheet->setCellValue('B' . $rowNum, $row->kode_ga ?? '');
            $sheet->setCellValue('C' . $rowNum, $row->nama_barang ?? '');
            $sheet->setCellValue('D' . $rowNum, $row->jenis_barang ?? '');
            $sheet->setCellValue('E' . $rowNum, $row->lokasi ?? '');
            $sheet->setCellValue('F' . $rowNum, $row->tgl_perawatan_terakhir ?? '');
            $sheet->setCellValue('G' . $rowNum, $row->status ?? '');
            $sheet->setCellValue('H' . $rowNum, $row->keterangan ?? '');
            $rowNum++;
        }

        if ($data->count() > 0) {
            $sheet->getStyle('A2:' . $lastCol . ($rowNum - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
        }

        // Status dropdown data validation (column G)
        $dvStatus = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dvStatus->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(true)->setShowDropDown(true)
            ->setFormula1('"Normal,Wajib Service"')
            ->setPromptTitle('Status AC')->setPrompt('Pilih status: Normal atau Wajib Service')
            ->setErrorTitle('Input tidak valid')->setError('Pilih salah satu: Normal atau Wajib Service');
        $sheet->setDataValidation('G2:G1000', $dvStatus);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            'ac_monitoring_' . date('Y-m-d_H-i') . '.xlsx',
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
            $fields = ['kode_ga', 'lokasi', 'nama_barang', 'jenis_barang', 'tgl_perawatan_terakhir', 'status', 'keterangan'];
            $colMap = [];
            foreach ($fields as $field) {
                $idx = array_search(str_replace('_', ' ', $field), $headerRow);
                if ($idx === false)
                    $idx = array_search($field, $headerRow);
                if ($idx !== false)
                    $colMap[$field] = $idx;
            }

            // ── PASS 1: Validate all rows ──────────────────────────────────
            $errors = [];
            $rowsData = [];
            foreach (array_slice($rows, 1) as $rowNum => $row) {
                if (empty(array_filter($row)))
                    continue;
                $data = [];
                foreach ($colMap as $field => $idx) {
                    $data[$field] = isset($row[$idx]) && $row[$idx] !== '' ? trim((string) $row[$idx]) : null;
                }
                if (empty($data['lokasi']) || empty($data['nama_barang'])) {
                    $errors[] = "Baris " . ($rowNum + 2) . ": lokasi dan nama_barang wajib diisi.";
                    continue;
                }
                // Date conversion — supports Excel serial dates, DD/MM/YYYY, DD-MM-YYYY, etc.
                if (isset($colMap['tgl_perawatan_terakhir'])) {
                    $data['tgl_perawatan_terakhir'] = self::parseExcelDate($data['tgl_perawatan_terakhir'] ?? null);
                }
                $rowsData[] = $data;
            }

            // If there are any validation errors, reject the whole import
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

            // ── PASS 2: Insert everything in a single transaction ─────────────────────
            DB::beginTransaction();
            try {
                foreach ($rowsData as $data) {
                    DB::table('tb_monitoring')->insert($data);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'title' => 'Import Gagal',
                    'errors' => ["Import Gagal — pastikan kolom 'status' hanya bernilai 'Normal' atau 'Wajib Service'."],
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