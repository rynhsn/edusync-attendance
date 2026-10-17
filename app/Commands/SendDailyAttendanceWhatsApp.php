<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SendDailyAttendanceWhatsApp extends BaseCommand
{
    protected $group = 'Attendance';
    protected $name = 'whatsapp:attendance-daily';
    protected $description = 'Deprecated command retained for older Task Scheduler entries.';
    protected $usage = 'whatsapp:attendance-daily';

    public function run(array $params)
    {
        CLI::write('Rekap WhatsApp harian sudah dinonaktifkan. Notifikasi sekarang dikirim saat absensi dicatat.', 'yellow');

        return EXIT_SUCCESS;
    }
}
