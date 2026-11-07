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
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
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
        $data['students'] = $this->studentModel->getAllStudent();
        $data['teachers'] = $this->db->table('master_guru')
            ->select('guru_id, guru_nama, guru_nip, rfid_tag, foto')
            ->orderBy('guru_nama', 'ASC')
            ->get()
            ->getResultArray();

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
        $data['students'] = $this->studentModel->getAllStudent();
        $data['teachers'] = $this->db->table('master_guru')
            ->select('guru_id, guru_nama, guru_nip, rfid_tag, foto')
            ->orderBy('guru_nama', 'ASC')
            ->get()
            ->getResultArray();

        return view('attendance', $data);
    }

    public function scan()
    {
        $data = [
            'title' => 'Absen Scan Barcode & QR',
            'activePage' => 'attendance-scan',
            'timeVar' => $this->timeVar,
            'students' => $this->studentModel->getAllStudent(),
            'teachers' => $this->db->table('master_guru')
                ->select('guru_id, guru_nama, guru_nip, rfid_tag, foto')
                ->orderBy('guru_nama', 'ASC')
                ->get()
                ->getResultArray(),
        ];

        return view('attendance/scan', $data);
    }

    public function show()
    {
        $response['attendanceHistory'] = $this->attendanceModel->getAttendance(['tanggal' => date('Y-m-d')]);

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    public function tap()
    {
        $json = null;
        if (method_exists($this->request, 'getJSON')) {
            try {
                $json = $this->request->getJSON();
            } catch (\Throwable $th) {
                $json = null;
            }
        }
        $cardCode = trim((string) (
            ($json->card_code ?? null)
            ?: (method_exists($this->request, 'getPost') ? $this->request->getPost('card_code') : null)
            ?: (method_exists($this->request, 'getVar') ? $this->request->getVar('card_code') : null)
            ?: ''
        ));
        $scanType = strtolower(trim((string) (
            ($json->scan_type ?? null)
            ?: (method_exists($this->request, 'getPost') ? $this->request->getPost('scan_type') : null)
            ?: (method_exists($this->request, 'getVar') ? $this->request->getVar('scan_type') : null)
            ?: 'auto'
        )));
        $customTime = trim((string) (
            ($json->custom_time ?? null)
            ?: (method_exists($this->request, 'getPost') ? $this->request->getPost('custom_time') : null)
            ?: (method_exists($this->request, 'getVar') ? $this->request->getVar('custom_time') : null)
            ?: ''
        ));

        // Validasi kode kartu / barcode
        if ($cardCode === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Kode barcode / RFID wajib diisi']);
        }

        // Waktu aktual saat scan dilakukan
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $currentTime = date('H.i.s');
        $currentTimeColon = (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $customTime))
            ? (strlen($customTime) === 5 ? $customTime . ':00' : $customTime)
            : date('H:i:s');

        $daysMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $todayIndo = $daysMap[date('l')] ?? 'Senin';

        try {
            $studentData = null;
            $teacherData = null;

            // 1. Cek rfid_tag (Siswa atau Guru)
            $studentData = $this->studentModel->getStudent(['rfid_tag' => $cardCode]);
            if (! $studentData) {
                $teacherData = $this->db->table('master_guru')->where('rfid_tag', $cardCode)->get()->getRowArray();
            }

            // 2. Cek NIS / NISN (Siswa) atau NIP (Guru)
            if (! $studentData && ! $teacherData) {
                $studentData = $this->studentModel->getStudent(['nis' => $cardCode]);
                if (! $studentData) {
                    $studentData = $this->studentModel->getStudent(['nisn' => $cardCode]);
                }
            }
            if (! $studentData && ! $teacherData) {
                $teacherData = $this->db->table('master_guru')->where('guru_nip', $cardCode)->get()->getRowArray();
            }

            // 3. Fallback jika barcode menggunakan ID
            if (! $studentData && ! $teacherData && is_numeric($cardCode)) {
                $teacherData = $this->db->table('master_guru')->where('guru_id', (int) $cardCode)->get()->getRowArray();
                if (! $teacherData) {
                    $studentData = $this->studentModel->getStudent(['master_siswa.siswa_id' => (int) $cardCode]);
                }
            }

            if (! $studentData && ! $teacherData) {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Data barcode/RFID tidak terdaftar (' . esc($cardCode) . ')',
                ]);
            }

            $isTeacher = ($teacherData !== null);

            // ==========================================
            // ALUR ABSENSI GURU
            // ==========================================
            if ($isTeacher) {
                $teacherId = (int) $teacherData['guru_id'];
                $attendanceData = $this->attendanceModel->where('guru_id', $teacherId)->where('tanggal', $currentDate)->first();

                // Ambil konfigurasi jam masuk/pulang
                $timeSettingData = $this->timeSettingModel->where('hari', $todayIndo)->first() ?: $this->timeSettingModel->first();
                $jamMasuk = ! empty($timeSettingData['jam_masuk']) ? $timeSettingData['jam_masuk'] : '07:00:00';
                $jamPulang = ! empty($timeSettingData['jam_pulang']) ? $timeSettingData['jam_pulang'] : '14:00:00';

                $isArrivalTap = false;
                $message = '';

                if ($attendanceData === null) {
                    // Belum ada absensi hari ini -> Catat HADIR (Masuk)
                    $this->attendanceLogModel->insert([
                        'log_id' => uniqid(),
                        'siswa_id' => null,
                        'guru_id' => $teacherId,
                        'waktu_tap' => $currentDateTime,
                        'jenis_tap' => 'in',
                        'rfid_tag' => $cardCode,
                    ]);

                    if (! $this->attendanceModel->insert([
                        'guru_id' => $teacherId,
                        'siswa_id' => null,
                        'tanggal' => $currentDate,
                        'waktu_masuk' => $jamMasuk,
                        'waktu_pulang' => $jamPulang,
                        'waktu_masuk_aktual' => $currentTimeColon,
                        'status_kehadiran_id' => 1,
                        'status_absensi_masuk_id' => ($currentTimeColon <= $jamMasuk) ? 1 : 2,
                        'status_absensi_pulang_id' => 5,
                        'keterangan' => 'Hadir (Guru - Scan Barcode/QR)',
                    ])) {
                        throw new \RuntimeException('Absensi masuk guru gagal disimpan.');
                    }

                    $isArrivalTap = true;
                    $message = 'Absensi guru berhasil dicatat (Hadir)';
                } else {
                    // Sudah ada record hari ini
                    if (empty($attendanceData['waktu_pulang_aktual'])) {
                        // Belum tap pulang
                        $shouldTapOut = ($scanType === 'out') || ($scanType === 'auto' && ($currentTimeColon >= $jamPulang || $currentTimeColon > '11:00:00'));

                        if ($shouldTapOut) {
                            $this->attendanceLogModel->insert([
                                'log_id' => uniqid(),
                                'siswa_id' => null,
                                'guru_id' => $teacherId,
                                'waktu_tap' => $currentDateTime,
                                'jenis_tap' => 'out',
                                'rfid_tag' => $cardCode,
                            ]);

                            if (! $this->attendanceModel->update($attendanceData['absensi_id'], [
                                'waktu_pulang_aktual' => $currentTimeColon,
                                'status_absensi_pulang_id' => ($currentTimeColon >= $jamPulang) ? 1 : 3,
                            ])) {
                                throw new \RuntimeException('Absensi pulang guru gagal disimpan.');
                            }

                            $message = 'Absensi pulang guru berhasil dicatat pada jam ' . $currentTimeColon;
                        } else {
                            if ($scanType === 'in') {
                                $this->attendanceModel->update($attendanceData['absensi_id'], [
                                    'waktu_masuk_aktual' => $currentTimeColon,
                                    'status_kehadiran_id' => 1,
                                ]);
                                $message = 'Absensi masuk guru diperbarui pada jam ' . $currentTimeColon . ' (Status: Hadir)';
                                $isArrivalTap = true;
                            } else {
                                $message = 'Absensi masuk guru sudah tercatat hari ini pada jam ' . $attendanceData['waktu_masuk_aktual'] . ' (Status: Hadir)';
                                $isArrivalTap = true;
                            }
                        }
                    } else {
                        $message = 'Absensi guru hari ini sudah lengkap (Masuk: ' . $attendanceData['waktu_masuk_aktual'] . ', Pulang: ' . $attendanceData['waktu_pulang_aktual'] . ')';
                    }
                }

                $teacherPhoto = trim((string) ($teacherData['foto'] ?? ''));
                $photoUrl = $teacherPhoto !== ''
                    ? base_url('uploads/student-photos/' . rawurlencode(basename($teacherPhoto)))
                    : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';

                $data = [
                    'status' => 'success',
                    'message' => $message,
                    'type' => $isArrivalTap ? 'in' : 'out',
                    'role' => 'teacher',
                    'teacher' => $teacherData,
                    'student' => [
                        'nama_siswa' => $teacherData['guru_nama'],
                        'kelas' => 'Guru',
                        'foto' => $teacherData['foto'] ?? '',
                    ],
                    'photo_url' => $photoUrl,
                    'waktu_aktual' => $currentTimeColon,
                    'tanggal' => $currentDate,
                    'status_kehadiran' => 'Hadir',
                    'whatsapp' => 'Tanpa notifikasi WhatsApp (Guru)',
                ];

                return $this->response->setStatusCode(200)->setJSON($data);
            }

            // ==========================================
            // ALUR ABSENSI SISWA
            // ==========================================
            $studentId = (int) $studentData['siswa_id'];
            $attendanceData = $this->attendanceModel->where('siswa_id', $studentId)->where('tanggal', $currentDate)->first();

            // Konfigurasi waktu kelas siswa
            $timeSettingData = $this->timeSettingModel
                ->where('kelas_id', $studentData['kelas_id'])
                ->where('hari', $todayIndo)
                ->first();
            if (! $timeSettingData) {
                $timeSettingData = $this->timeSettingModel->where('kelas_id', $studentData['kelas_id'])->first();
            }
            $jamMasuk = ! empty($timeSettingData['jam_masuk']) ? $timeSettingData['jam_masuk'] : '07:00:00';
            $jamPulang = ! empty($timeSettingData['jam_pulang']) ? $timeSettingData['jam_pulang'] : '14:00:00';

            $isArrivalTap = false;
            $message = '';

            if ($attendanceData === null) {
                // Belum ada absensi hari ini -> Catat HADIR (Masuk)
                $this->attendanceLogModel->insert([
                    'log_id' => uniqid(),
                    'siswa_id' => $studentId,
                    'waktu_tap' => $currentDateTime,
                    'jenis_tap' => 'in',
                    'rfid_tag' => $cardCode,
                ]);

                if (! $this->attendanceModel->insert([
                    'siswa_id' => $studentId,
                    'tanggal' => $currentDate,
                    'waktu_masuk' => $jamMasuk,
                    'waktu_pulang' => $jamPulang,
                    'waktu_masuk_aktual' => $currentTimeColon,
                    'status_kehadiran_id' => 1,
                    'status_absensi_masuk_id' => ($currentTimeColon <= $jamMasuk) ? 1 : 2,
                    'status_absensi_pulang_id' => 5,
                    'keterangan' => 'Hadir (Scan Barcode/QR)',
                ])) {
                    throw new \RuntimeException('Absensi masuk siswa gagal disimpan.');
                }

                $isArrivalTap = true;
                $message = 'Absensi berhasil dicatat (Hadir)';
            } else {
                // Sudah ada data absensi hari ini
                if (empty($attendanceData['waktu_pulang_aktual'])) {
                    // Belum tap pulang
                    $shouldTapOut = ($scanType === 'out') || ($scanType === 'auto' && ($currentTimeColon >= $jamPulang || $currentTimeColon > '11:00:00'));

                    if ($shouldTapOut) {
                        $this->attendanceLogModel->insert([
                            'log_id' => uniqid(),
                            'siswa_id' => $studentId,
                            'waktu_tap' => $currentDateTime,
                            'jenis_tap' => 'out',
                            'rfid_tag' => $cardCode,
                        ]);

                        if (! $this->attendanceModel->update($attendanceData['absensi_id'], [
                            'waktu_pulang_aktual' => $currentTimeColon,
                            'status_absensi_pulang_id' => ($currentTimeColon >= $jamPulang) ? 1 : 3,
                        ])) {
                            throw new \RuntimeException('Absensi pulang siswa gagal disimpan.');
                        }

                        $message = 'Absensi pulang berhasil dicatat pada jam ' . $currentTimeColon;
                    } else {
                        if ($scanType === 'in') {
                            $this->attendanceModel->update($attendanceData['absensi_id'], [
                                'waktu_masuk_aktual' => $currentTimeColon,
                                'status_kehadiran_id' => 1,
                            ]);
                            $message = 'Absensi masuk diperbarui pada jam ' . $currentTimeColon . ' (Status: Hadir)';
                            $isArrivalTap = true;
                        } else {
                            $message = 'Absensi masuk sudah tercatat hari ini pada jam ' . $attendanceData['waktu_masuk_aktual'] . ' (Status: Hadir)';
                            $isArrivalTap = true;
                        }
                    }
                } else {
                    $message = 'Absensi hari ini sudah lengkap (Masuk: ' . $attendanceData['waktu_masuk_aktual'] . ', Pulang: ' . $attendanceData['waktu_pulang_aktual'] . ')';
                }
            }

            // Notifikasi WhatsApp jika tap masuk
            $whatsappStatus = 'Notifikasi WhatsApp tidak dikirim untuk tap pulang.';
            if ($isArrivalTap) {
                $notifier = new \App\Libraries\WhatsAppAttendanceNotifier();
                $parentPhone = trim((string) ($studentData['no_whatsapp'] ?? ''));
                if ($parentPhone === '') {
                    $whatsappStatus = 'Notifikasi kedatangan tidak terkirim: nomor WhatsApp orang tua belum diisi.';
                } elseif (! $notifier->isConfigured()) {
                    $whatsappStatus = 'Notifikasi kedatangan tidak terkirim: konfigurasi WhatsApp belum lengkap.';
                } else {
                    $accepted = $notifier->sendStudentArrivalNotification(
                        $parentPhone,
                        trim((string) ($studentData['nama_orang_tua'] ?? '')),
                        (string) $studentData['nama_siswa'],
                        (string) ($studentData['kelas'] ?? ''),
                        date('d/m/Y', strtotime($currentDate)),
                        date('H:i', strtotime($currentDateTime))
                    );
                    $whatsappStatus = $accepted
                        ? 'Notifikasi kedatangan diterima oleh Fonnte.'
                        : 'Pengiriman notifikasi kedatangan gagal. Periksa log aplikasi dan koneksi Fonnte.';
                }
            }

            $studentPhoto = trim((string) ($studentData['foto'] ?? ''));
            $photoUrl = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';
            if ($studentPhoto !== '') {
                $encoded = rawurlencode(basename($studentPhoto));
                $photoUrl = preg_match('/\A[a-f0-9]{32}(?:-cutout)?\.(?:jpg|png|webp)\z/i', $studentPhoto) === 1
                    ? base_url('uploads/student-photos/' . $encoded)
                    : base_url('assets/img/avatars/' . $encoded);
            }

            $data = [
                'status' => 'success',
                'message' => $message,
                'type' => $isArrivalTap ? 'in' : 'out',
                'role' => 'student',
                'student' => $studentData,
                'photo_url' => $photoUrl,
                'waktu_aktual' => $currentTimeColon,
                'tanggal' => $currentDate,
                'status_kehadiran' => 'Hadir',
                'whatsapp' => $whatsappStatus,
            ];

            return $this->response->setStatusCode(200)->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
