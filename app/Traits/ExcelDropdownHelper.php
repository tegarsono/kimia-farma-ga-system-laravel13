<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;

trait ExcelDropdownHelper
{
    /**
     * Tambahkan data validation dropdown dari hidden sheet ke kolom tertentu.
     *
     * Karena Excel membatasi formula1 (inline list) max 255 karakter,
     * daftar panjang harus disimpan di sheet tersembunyi lalu direferensikan
     * sebagai named range.
     *
     * @param Spreadsheet $spreadsheet      Objek spreadsheet aktif
     * @param string      $dataSheetTitle   Nama sheet tersembunyi (unik per dropdown)
     * @param array       $items            Daftar nilai dropdown
     * @param string      $namedRange       Nama named range (tanpa spasi)
     * @param string      $targetRange      Range kolom tujuan, mis. "C2:C1000"
     * @param string      $promptTitle      Judul tooltip
     * @param string      $prompt           Isi tooltip
     * @param string      $errorTitle       Judul pesan error
     * @param string      $errorMsg         Isi pesan error
     */
    protected function addDropdownFromHiddenSheet(
        Spreadsheet $spreadsheet,
        string $dataSheetTitle,
        array $items,
        string $namedRange,
        string $targetRange,
        string $promptTitle = 'Pilih nilai',
        string $prompt = 'Pilih dari daftar yang tersedia',
        string $errorTitle = 'Input tidak valid',
        string $errorMsg = 'Nilai tidak ada dalam daftar.'
    ): void {
        // 1. Buat hidden sheet untuk menyimpan daftar nilai
        $dataSheet = $spreadsheet->createSheet();
        $dataSheet->setTitle($dataSheetTitle);
        $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        foreach ($items as $i => $value) {
            $dataSheet->setCellValue('A' . ($i + 1), $value);
        }

        $lastRow = count($items);

        // 2. Daftarkan named range yang menunjuk ke hidden sheet
        $spreadsheet->addNamedRange(new NamedRange(
            $namedRange,
            $dataSheet,
            '$A$1:$A$' . $lastRow
        ));

        // 3. Terapkan data validation pada sheet utama
        $mainSheet = $spreadsheet->getActiveSheet();
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(false)
            ->setShowDropDown(false)   // false = tampilkan panah dropdown di Excel
            ->setShowInputMessage(true)
            ->setShowErrorMessage(true)
            ->setFormula1($namedRange)
            ->setPromptTitle($promptTitle)
            ->setPrompt($prompt)
            ->setErrorTitle($errorTitle)
            ->setError($errorMsg);

        $mainSheet->setDataValidation($targetRange, $dv);
    }
}
