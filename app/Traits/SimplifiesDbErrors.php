<?php

namespace App\Traits;

/**
 * Menyederhanakan pesan error database menjadi keterangan yang mudah dipahami user.
 * Juga menyediakan helper konversi tanggal dari berbagai format Excel.
 * Digunakan pada semua fitur import Excel.
 */
trait SimplifiesDbErrors
{
    /**
     * Konversi nilai tanggal dari Excel ke format Y-m-d yang diterima MySQL.
     *
     * Mendukung:
     *  - Serial number Excel (misal: 49449)
     *  - d/m/Y  → 03/10/2030
     *  - d-m-Y  → 03-10-2030
     *  - Y/m/d  → 2030/10/03
     *  - Y-m-d  → 2030-10-03  (sudah benar, langsung dikembalikan)
     *  - d/m/y  → 03/10/30
     *
     * Mengembalikan null jika nilai kosong atau tidak bisa diparse.
     */
    protected static function parseExcelDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return null;
        }

        // ── Serial number Excel (angka bulat, misal 49449) ────────────────────
        if (is_numeric($raw) && !str_contains($raw, '-') && !str_contains($raw, '/')) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $raw);
                return $dt->format('Y-m-d');
            } catch (\Throwable) {
                // bukan serial Excel yang valid, lanjut ke parsing string
            }
        }

        // ── Sudah format Y-m-d (MySQL native) ────────────────────────────────
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return $raw;
        }

        // ── Format d/m/Y atau d/m/y (03/10/2030 atau 03/10/30) ───────────────
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{2,4})$#', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $year  = strlen($m[3]) === 2 ? (((int)$m[3] >= 50) ? '19' : '20') . $m[3] : $m[3];
            $candidate = "{$year}-{$month}-{$day}";
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return $candidate;
            }
        }

        // ── Format d-m-Y atau d-m-y (03-10-2030 atau 03-10-30) ──────────────
        if (preg_match('#^(\d{1,2})-(\d{1,2})-(\d{2,4})$#', $raw, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $year  = strlen($m[3]) === 2 ? (((int)$m[3] >= 50) ? '19' : '20') . $m[3] : $m[3];
            $candidate = "{$year}-{$month}-{$day}";
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return $candidate;
            }
        }

        // ── Format Y/m/d (2030/10/03) ─────────────────────────────────────────
        if (preg_match('#^(\d{4})/(\d{1,2})/(\d{1,2})$#', $raw, $m)) {
            $year  = $m[1];
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $day   = str_pad($m[3], 2, '0', STR_PAD_LEFT);
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // ── Fallback: coba PHP strtotime ──────────────────────────────────────
        $ts = strtotime($raw);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    /**
     * Ubah pesan exception database menjadi kalimat singkat yang informatif.
     */
    protected static function simplifyDbError(string $message): string
    {
        // ── Duplicate entry ───────────────────────────────────────────────────
        if (str_contains($message, 'Duplicate entry')) {
            preg_match("/Duplicate entry '([^']+)' for key '([^'.]+\.)?([^']+)'/", $message, $m);
            $value  = $m[1] ?? '';
            $column = $m[3] ?? '';
            $column = preg_replace('/_unique$|_UNIQUE$/i', '', $column);
            $column = $column === 'PRIMARY' ? 'id' : $column;
            if ($value && $column) {
                return "Data duplikat: nilai '{$value}' pada kolom '{$column}' sudah ada di database.";
            }
            return "Data duplikat: nilai yang sama sudah ada di database.";
        }

        // ── Format tanggal tidak valid ────────────────────────────────────────
        if (str_contains($message, 'Incorrect date value') || str_contains($message, 'Invalid datetime format')) {
            preg_match("/Incorrect date value: '([^']+)' for column [`'\"]?[^`'\"]+[`'\"]?\.[`'\"]?[^`'\"]+[`'\"]?\.[`'\"]?([^`'\"]+)[`'\"]?/i", $message, $m);
            $value  = $m[1] ?? '';
            $column = $m[2] ?? '';
            if ($value && $column) {
                return "Format tanggal tidak valid pada kolom '{$column}': nilai '{$value}' bukan format tanggal yang benar (gunakan format YYYY-MM-DD atau DD/MM/YYYY).";
            }
            return "Format tanggal tidak valid. Pastikan kolom tanggal menggunakan format YYYY-MM-DD atau DD/MM/YYYY.";
        }

        // ── Nilai terlalu panjang ─────────────────────────────────────────────
        if (str_contains($message, 'Data too long for column')) {
            preg_match("/Data too long for column '([^']+)'/i", $message, $m);
            $column = $m[1] ?? '';
            if ($column) {
                return "Nilai terlalu panjang untuk kolom '{$column}'. Periksa batas karakter yang diizinkan.";
            }
            return "Salah satu nilai terlalu panjang. Periksa panjang karakter di setiap kolom.";
        }

        // ── Kolom wajib diisi (NULL tidak diizinkan) ──────────────────────────
        if (str_contains($message, "cannot be null") || str_contains($message, "doesn't have a default value")) {
            preg_match("/Column '([^']+)' cannot be null|Field '([^']+)' doesn't have a default/i", $message, $m);
            $column = $m[1] ?: ($m[2] ?? '');
            if ($column) {
                return "Kolom '{$column}' wajib diisi, tidak boleh kosong.";
            }
            return "Ada kolom wajib yang tidak diisi.";
        }

        // ── Nilai di luar batas ───────────────────────────────────────────────
        if (str_contains($message, 'Out of range value for column')) {
            preg_match("/Out of range value for column '([^']+)'/i", $message, $m);
            $column = $m[1] ?? '';
            if ($column) {
                return "Nilai di luar batas yang diizinkan untuk kolom '{$column}'.";
            }
            return "Salah satu nilai melebihi batas angka yang diizinkan.";
        }

        // ── Nilai bukan angka ─────────────────────────────────────────────────
        if (str_contains($message, 'Incorrect integer value') || str_contains($message, 'Incorrect decimal value')) {
            preg_match("/Incorrect (?:integer|decimal) value: '([^']+)' for column [`'\"]?([^`'\"]+)[`'\"]?/i", $message, $m);
            $value  = $m[1] ?? '';
            $column = preg_replace('/^.*\./', '', trim($m[2] ?? '', '`\'"'));
            if ($value && $column) {
                return "Nilai '{$value}' bukan angka yang valid untuk kolom '{$column}'.";
            }
            return "Salah satu kolom angka berisi nilai yang bukan angka.";
        }

        // ── Foreign key constraint ────────────────────────────────────────────
        if (str_contains($message, 'foreign key constraint fails')) {
            return "Referensi data tidak ditemukan. Pastikan nilai yang diisi sudah terdaftar di tabel terkait.";
        }

        // ── Fallback ──────────────────────────────────────────────────────────
        $short = preg_split('/\s*\(Connection:|\s*\(SQL:/i', $message)[0];
        if (preg_match('/SQLSTATE\[[^\]]+\]:\s*(.+)/s', $short, $m)) {
            $short = trim($m[1]);
        }
        return mb_strlen($short) > 150 ? mb_substr($short, 0, 147) . '...' : $short;
    }
}
