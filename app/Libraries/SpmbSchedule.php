<?php

namespace App\Libraries;

use App\Models\SchoolkuWebsiteContentModel;
use DateTimeImmutable;
use DateTimeZone;

class SpmbSchedule
{
    public function status(): array
    {
        $settings = (new SchoolkuWebsiteContentModel())->getSettings([
            'spmb_start_date' => '',
            'spmb_end_date' => '',
        ]);
        $startDate = trim((string) $settings['spmb_start_date']);
        $endDate = trim((string) $settings['spmb_end_date']);

        if ($startDate === '' && $endDate === '') {
            return [
                'is_open' => true,
                'is_configured' => false,
                'start_date' => '',
                'end_date' => '',
                'message' => '',
            ];
        }

        $timezone = new DateTimeZone(config('App')->appTimezone);
        $start = $this->parseDate($startDate, $timezone);
        $end = $this->parseDate($endDate, $timezone);
        if ($start === null || $end === null || $end < $start) {
            return [
                'is_open' => false,
                'is_configured' => true,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'message' => 'Jadwal pendaftaran SPMB belum dikonfigurasi dengan benar. Silakan hubungi pihak sekolah.',
            ];
        }

        $today = new DateTimeImmutable('today', $timezone);
        if ($today < $start) {
            return [
                'is_open' => false,
                'is_configured' => true,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'message' => 'Pendaftaran SPMB Online belum dibuka. Pendaftaran dimulai pada ' . $start->format('d/m/Y') . '.',
            ];
        }
        if ($today > $end) {
            return [
                'is_open' => false,
                'is_configured' => true,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'message' => 'Pendaftaran SPMB sudah ditutup pada ' . $end->format('d/m/Y') . '.',
            ];
        }

        return [
            'is_open' => true,
            'is_configured' => true,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'message' => '',
        ];
    }

    private function parseDate(string $date, DateTimeZone $timezone): ?DateTimeImmutable
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, $timezone);

        return $parsed !== false && $parsed->format('Y-m-d') === $date ? $parsed : null;
    }
}
