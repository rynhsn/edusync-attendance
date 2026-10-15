<?php

namespace App\Libraries;

class SpmbProofPdf
{
    public static function generate(string $registrationCode, array $application): string
    {
        $createdAt = strtotime((string) ($application['created_at'] ?? ''));
        $receivedAt = $createdAt === false ? 'Tidak tersedia' : date('d-m-Y H:i', $createdAt);
        $emailLines = self::wrapText((string) ($application['contact_email'] ?? ''), 78);

        $stream = self::fillRect(0.95, 0.97, 0.99, 0, 0, 595, 842);
        $stream .= self::fillRect(1, 1, 1, 40, 42, 515, 758);
        $stream .= self::strokeRect(0.84, 0.88, 0.92, 40, 42, 515, 758);
        $stream .= self::fillRect(0.10, 0.20, 0.36, 40, 678, 515, 122);
        $stream .= self::fillRect(0.17, 0.68, 0.67, 40, 796, 515, 4);
        $stream .= self::text('SEKOLAHKU  |  SPMB ONLINE', 68, 765, 10, 'F2', 0.76, 0.84, 0.94);
        $stream .= self::text('BUKTI PENDAFTARAN', 68, 735, 21, 'F2', 1, 1, 1);
        $stream .= self::text('Penerimaan peserta didik baru', 68, 711, 10, 'F1', 0.84, 0.89, 0.95);

        $stream .= self::fillRect(0.90, 0.97, 0.94, 68, 632, 459, 34);
        $stream .= self::text('PENDAFTARAN BERHASIL DITERIMA', 82, 644, 10, 'F2', 0.10, 0.42, 0.28);
        $stream .= self::text('Nomor pendaftaran', 68, 602, 9, 'F1', 0.40, 0.45, 0.52);
        $stream .= self::text($registrationCode, 68, 580, 16, 'F2', 0.10, 0.20, 0.36);
        $stream .= self::text('Tanggal diterima', 350, 602, 9, 'F1', 0.40, 0.45, 0.52);
        $stream .= self::text($receivedAt, 350, 582, 11, 'F2', 0.10, 0.20, 0.36);
        $stream .= self::line(0.87, 0.89, 0.92, 68, 558, 527, 558);

        $stream .= self::text('DATA CALON SISWA', 68, 532, 10, 'F2', 0.10, 0.20, 0.36);
        $stream .= self::text('Nama lengkap', 68, 500, 9, 'F1', 0.42, 0.47, 0.54);
        $stream .= self::text((string) ($application['student_name'] ?? ''), 68, 480, 12, 'F2', 0.12, 0.16, 0.22);
        $stream .= self::line(0.91, 0.93, 0.95, 68, 462, 527, 462);

        $stream .= self::text('NIK', 68, 438, 9, 'F1', 0.42, 0.47, 0.54);
        $stream .= self::text((string) ($application['student_nik'] ?? ''), 68, 418, 11, 'F2', 0.12, 0.16, 0.22);
        $stream .= self::text('NISN', 310, 438, 9, 'F1', 0.42, 0.47, 0.54);
        $stream .= self::text((string) ($application['student_nisn'] ?? ''), 310, 418, 11, 'F2', 0.12, 0.16, 0.22);
        $stream .= self::line(0.91, 0.93, 0.95, 68, 400, 527, 400);

        $stream .= self::text('Email', 68, 376, 9, 'F1', 0.42, 0.47, 0.54);
        $emailY = 356;
        foreach ($emailLines as $emailLine) {
            $stream .= self::text($emailLine, 68, $emailY, 10, 'F1', 0.12, 0.16, 0.22);
            $emailY -= 15;
        }

        $footerY = min(270, $emailY - 20);
        $stream .= self::line(0.87, 0.89, 0.92, 68, $footerY, 527, $footerY);
        $stream .= self::text('Pendaftaran telah diterima oleh sistem.', 68, $footerY - 25, 10, 'F2', 0.10, 0.20, 0.36);
        $stream .= self::text('Simpan bukti ini dan catat nomor pendaftaran untuk tindak lanjut.', 68, $footerY - 43, 9, 'F1', 0.36, 0.40, 0.47);
        $stream .= self::text('Berkas asli akan diperiksa oleh panitia SPMB.', 68, $footerY - 59, 9, 'F1', 0.36, 0.40, 0.47);
        $stream .= self::line(0.91, 0.93, 0.95, 68, 72, 527, 72);
        $stream .= self::text('Dokumen ini diterbitkan secara otomatis oleh sistem SPMB Online.', 68, 56, 8, 'F1', 0.51, 0.55, 0.61);

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private static function text(
        string $text,
        float $x,
        float $y,
        int $size,
        string $font,
        float $red,
        float $green,
        float $blue
    ): string {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT', $text);
        if ($encoded === false) {
            $encoded = preg_replace('/[^\x20-\x7E]/', '?', $text) ?? '';
        }
        $encoded = preg_replace('/[\x00-\x1F\x7F]/', ' ', $encoded) ?? '';
        $encoded = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);

        return sprintf(
            "BT /%s %d Tf %.3f %.3f %.3f rg 1 0 0 1 %.2f %.2f Tm (%s) Tj ET\n",
            $font,
            $size,
            $red,
            $green,
            $blue,
            $x,
            $y,
            $encoded
        );
    }

    private static function fillRect(
        float $red,
        float $green,
        float $blue,
        float $x,
        float $y,
        float $width,
        float $height
    ): string {
        return sprintf("%.3f %.3f %.3f rg %.2f %.2f %.2f %.2f re f\n", $red, $green, $blue, $x, $y, $width, $height);
    }

    private static function strokeRect(
        float $red,
        float $green,
        float $blue,
        float $x,
        float $y,
        float $width,
        float $height
    ): string {
        return sprintf("%.3f %.3f %.3f RG 0.8 w %.2f %.2f %.2f %.2f re S\n", $red, $green, $blue, $x, $y, $width, $height);
    }

    private static function line(float $red, float $green, float $blue, float $x1, float $y1, float $x2, float $y2): string
    {
        return sprintf("%.3f %.3f %.3f RG 0.7 w %.2f %.2f m %.2f %.2f l S\n", $red, $green, $blue, $x1, $y1, $x2, $y2);
    }

    private static function wrapText(string $text, int $maxCharacters): array
    {
        $text = trim($text);
        if ($text === '') {
            return ['-'];
        }

        return explode("\n", wordwrap($text, $maxCharacters, "\n", true));
    }
}
