<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class AttendanceController extends BaseController
{
    protected $studentModel;
    protected $attendanceModel;
    protected $attendanceLogModel;
    protected $timeSettingModel;
    protected $absenceStatusModel;
    protected $attendanceStatusModel;
    protected $timeVar;

    public function __construct()
    {
        $this->studentModel = new \App\Models\StudentModel();
        $this->attendanceModel = new \App\Models\AttendanceModel();
        $this->attendanceLogModel = new \App\Models\AttendanceLogTapModel();
        $this->timeSettingModel = new \App\Models\TimeSettingModel();
        $this->absenceStatusModel = new \App\Models\AbsenceStatusModel();
        $this->attendanceStatusModel = new \App\Models\AttendanceStatusModel();

        $this->timeVar = [
            'time_start' => '06.00.00',
            'time_end' => '18.00.00',
            'time_limit' => '12.00.00',
            'free_day' => ['Minggu'],
        ];
    }

    public function index()
    {
        $data = [
            'title' => 'Absensi RFID',
            'activePage' => 'attendance',
        ];
        $data['attendance'] = $this->attendanceModel->getAttendance();

        return view('attendance/index', $data);
    }

    public function report()
    {
        return view('report/attendance', $this->getAttendanceReportData());
    }

    public function downloadReportPdf(): ResponseInterface
    {
        $reportData = $this->getAttendanceReportData();
        if ($reportData['filterError'] !== null) {
            return $this->response->setStatusCode(400)->setBody($reportData['filterError']);
        }

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml(view('report/attendance-pdf', $reportData), 'UTF-8');

        try {
            $dompdf->render();
        } catch (\Dompdf\Exception $exception) {
            log_message('error', 'Dompdf failed to render attendance report: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response->setStatusCode(500)->setBody(
                'PDF laporan absensi gagal dibuat. Periksa log aplikasi untuk detail teknis.'
            );
        }

        $filenameParts = ['laporan-absensi'];
        if ($reportData['studentName'] !== '') {
            $safeStudentName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $reportData['studentName']);
            if ($safeStudentName !== null && trim($safeStudentName, '-') !== '') {
                $filenameParts[] = trim($safeStudentName, '-');
            }
        }
        if ($reportData['dateFrom'] !== '') {
            $filenameParts[] = $reportData['dateFrom'];
        }
        if ($reportData['dateTo'] !== '') {
            $filenameParts[] = $reportData['dateTo'];
        }

        return $this->response
            ->download(implode('-', $filenameParts) . '.pdf', $dompdf->output(), true)
            ->setContentType('application/pdf');
    }

    private function getAttendanceReportData(): array
    {
        $dateFrom = trim((string) $this->request->getGet('from'));
        $dateTo = trim((string) $this->request->getGet('to'));
        $studentName = trim((string) $this->request->getGet('nama'));
        $filterError = null;

        if (($dateFrom !== '' && ! $this->isValidReportDate($dateFrom))
            || ($dateTo !== '' && ! $this->isValidReportDate($dateTo))) {
            $filterError = 'Format tanggal tidak valid.';
        } elseif ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
            $filterError = 'Tanggal mulai tidak boleh melewati tanggal akhir.';
        }

        $attendance = $filterError === null
            ? $this->attendanceModel->getAttendanceReport(
                $dateFrom !== '' ? $dateFrom : null,
                $dateTo !== '' ? $dateTo : null,
                $studentName !== '' ? $studentName : null
            )
            : [];

        return [
            'title' => 'Laporan Absensi',
            'activePage' => 'attendance-report',
            'attendance' => $attendance,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'studentName' => $studentName,
            'filterError' => $filterError,
        ];
    }

    private function isValidReportDate(string $date): bool
    {
        $parsedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsedDate !== false && $parsedDate->format('Y-m-d') === $date;
    }

    public function getAttendance()
    {
        $attendance = $this->attendanceModel->getAttendance(['tanggal' => date('Y-m-d')]);
        return $this->response->setStatusCode(200)->setJSON($attendance);
    }

    public function attendance()
    {
        $data['timeVar'] = $this->timeVar;
        return view('attendance', $data);
    }

    public function show()
    {
        $response['attendanceHistory'] = $this->attendanceModel->getAttendance(['tanggal' => date('Y-m-d')]);

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    public function tap()
    {
        $cardCode = $this->request->getJSON()->card_code;

        // Validasi kode kartu
        if (empty($cardCode)) {
            return $this->response->setStatusCode(400, 'Card code is required')->setJSON(['error' => 'Card code is required']);
        }

        // Mendapatkan waktu saat ini
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $currentTime = date('H.i.s');
        $tapType = null;

        try {
            // Cek apakah card code sudah ada di tabel siswa by condition
            $studentData = $this->studentModel->getStudent(['rfid_tag' => $cardCode]);

            if (!$studentData) {
                // Data siswa tidak ditemukan
                return $this->response->setStatusCode(404, 'Student not found')->setJSON(['error' => 'Student not found']);
            }

            // ambil data absensi hari ini
            $attendanceData = $this->attendanceModel->where('siswa_id', $studentData['siswa_id'])->where('tanggal', $currentDate)->first();
            $timeSettingData = $this->timeSettingModel->where('kelas_id', $studentData['kelas_id'])->first();
            $attendanceId = (int) ($attendanceData['absensi_id'] ?? 0);

            //cek apakah ini jam masuk atau jam pulang
            if ($currentTime >= $this->timeVar['time_start'] && $currentTime <= $this->timeVar['time_limit']) {
                // cek apakah sudah absen masuk
                if ($attendanceData) {
                    return $this->response->setStatusCode(400, 'Attendance already recorded')->setJSON(['error' => 'Attendance already recorded']);
                } else {
                    // Belum absen hari ini, catat absensi masuk
                    $this->attendanceLogModel->insert([
                        'log_id' => uniqid(),
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_tap' => $currentDateTime,
                        'jenis_tap' => 'in',
                        'rfid_tag' => $cardCode
                    ]);

                    if (! $this->attendanceModel->insert([
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_masuk' => $timeSettingData['jam_masuk'],
                        'waktu_pulang' => $timeSettingData['jam_pulang'],
                        'waktu_masuk_aktual' => date('H:i:s'),
                        'status_kehadiran_id' => 1,
                        'status_absensi_masuk_id' => ($currentTime <= $timeSettingData['jam_masuk']) ? 1 : 2,
                        'status_absensi_pulang_id' => 5,
                    ])) {
                        throw new \RuntimeException('Absensi masuk gagal disimpan.');
                    }
                    $attendanceId = (int) $this->attendanceModel->getInsertID();
                    $tapType = 'in';
                }
            } else if ($currentTime > $this->timeVar['time_limit'] && $currentTime <= $this->timeVar['time_end']) {
                //cek apakah sudah absen masuk dan belum absen pulang
                if ($attendanceData) {
                    if (!empty($attendanceData['waktu_pulang_aktual'])) {
                        return $this->response->setStatusCode(400, 'Attendance already recorded')->setJSON(['error' => 'Attendance already recorded']);
                    } else {
                        // Catat absensi pulang
                        $this->attendanceLogModel->insert([
                            'log_id' => uniqid(),
                            'siswa_id' => $studentData['siswa_id'],
                            'tanggal' => $currentDate,
                            'waktu_tap' => $currentDateTime,
                            'jenis_tap' => 'out',
                            'rfid_tag' => $cardCode
                        ]);

                        if (! $this->attendanceModel->update($attendanceData['absensi_id'], [
                            'waktu_pulang_aktual' => date('H:i:s'),
                            'status_absensi_pulang_id' => ($currentTime >= $timeSettingData['jam_pulang']) ? 1 : 3,
                        ])) {
                            throw new \RuntimeException('Absensi pulang gagal disimpan.');
                        }
                        $tapType = 'out';
                    }
                } else {
                    // Belum absen hari ini, catat absensi keluar
                    $this->attendanceLogModel->insert([
                        'log_id' => uniqid(),
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_tap' => $currentDateTime,
                        'jenis_tap' => 'out',
                        'rfid_tag' => $cardCode
                    ]);

                    if (! $this->attendanceModel->insert([
                        'siswa_id' => $studentData['siswa_id'],
                        'tanggal' => $currentDate,
                        'waktu_masuk' => $timeSettingData['jam_masuk'],
                        'waktu_pulang' => $timeSettingData['jam_pulang'],
                        'waktu_pulang_aktual' => date('H:i:s'),
                        'status_kehadiran_id' => 1,
                        'status_absensi_masuk_id' => 4,
                        'status_absensi_pulang_id' => ($currentTime >= $timeSettingData['jam_pulang']) ? 1 : 3,
                    ])) {
                        throw new \RuntimeException('Absensi pulang gagal disimpan.');
                    }
                    $attendanceId = (int) $this->attendanceModel->getInsertID();
                    $tapType = 'out';
                }
            } else {
                return $this->response->setStatusCode(400, 'Attendance not allowed')->setJSON(['error' => 'Attendance not allowed']);
            }

            $whatsAppStatus = 'Notifikasi WhatsApp tidak dikirim karena nomor orang tua belum diisi.';
            if (trim((string) ($studentData['no_whatsapp'] ?? '')) !== '') {
                $notifier = new \App\Libraries\WhatsAppAttendanceNotifier();
                if (! $notifier->isConfigured()) {
                    $missingConfiguration = implode(', ', $notifier->missingConfiguration());
                    log_message('warning', 'RFID attendance for student {id} was saved, but WhatsApp configuration is incomplete.', [
                        'id' => $studentData['siswa_id'],
                    ]);
                    $whatsAppStatus = 'Notifikasi WhatsApp tidak dikirim karena konfigurasi belum lengkap: '
                        . $missingConfiguration . '.';
                } elseif ($notifier->sendRfidAttendance(
                    (string) $studentData['no_whatsapp'],
                    (string) ($studentData['nama_orang_tua'] ?? ''),
                    (string) $studentData['nama_siswa'],
                    (string) $tapType,
                    (string) ($studentData['kelas'] ?? ''),
                    date('d/m/Y', strtotime($currentDateTime)),
                    date('H:i:s', strtotime($currentDateTime))
                )) {
                    $whatsAppStatus = 'Permintaan notifikasi WhatsApp diterima Fonnte untuk diantrekan.';
                    $tapLabel = $tapType === 'in' ? 'Masuk WA' : 'Pulang WA';
                    $currentKeterangan = trim((string) ($attendanceData['keterangan'] ?? ''));
                    $statusKeterangan = $tapLabel . ': Sent Done';
                    if (! str_contains($currentKeterangan, $statusKeterangan)) {
                        $updatedKeterangan = $currentKeterangan === ''
                            ? $statusKeterangan
                            : $currentKeterangan . ' | ' . $statusKeterangan;

                        if ($attendanceId < 1 || ! $this->attendanceModel->update($attendanceId, [
                            'keterangan' => $updatedKeterangan,
                        ])) {
                            log_message('error', 'RFID WhatsApp was accepted for attendance {id}, but its sent status could not be saved.', [
                                'id' => $attendanceId,
                            ]);
                            $whatsAppStatus .= ' Status Sent Done gagal disimpan pada keterangan absensi.';
                        }
                    }
                } else {
                    $whatsAppStatus = 'Absensi tercatat, tetapi permintaan notifikasi WhatsApp gagal. Periksa nomor orang tua dan log aplikasi.';
                }
            }

            $data = [
                'message' => 'Absensi berhasil dicatat',
                'student' => $studentData,
                'whatsapp' => $whatsAppStatus,
            ];
            return $this->response->setStatusCode(200)->setJSON($data);
        } catch (\Exception $e) {
            // Tangani kesalahan yang tidak terduga
            // return $this->response->setStatusCode(500, 'An error occurred')->setJSON(['error' => $e->getMessage()]);
            return $this->response->setStatusCode(500, 'An error occurred')->setJSON(['error' => $e->getMessage()]);
        }
    }
}
