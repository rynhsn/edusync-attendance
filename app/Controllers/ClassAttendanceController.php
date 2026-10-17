<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class ClassAttendanceController extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $date = trim((string) $this->request->getGet('tanggal'));
        $classId = trim((string) $this->request->getGet('kelas_id'));
        if ($date !== '' && ! $this->isValidDate($date)) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Format tanggal tidak valid.');
        }
        if ($classId !== '' && (! ctype_digit($classId) || (int) $classId < 1)) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Pilihan kelas tidak valid.');
        }

        $builder = $this->db->table('absensi_absensi_kelas ak')
            ->select('ak.absensi_kelas_id, ak.tanggal, mk.kelas_nama, mm.mapel_nama, mg.guru_nama, mj.jam_ke, mj.jam_mulai, mj.jam_selesai, mta.tahun_ajaran_nama, mta.semester, COUNT(aks.absensi_kelas_siswa_id) AS jumlah_siswa, SUM(aks.status = "hadir") AS jumlah_hadir, SUM(aks.status = "sakit") AS jumlah_sakit, SUM(aks.status = "izin") AS jumlah_izin, SUM(aks.status = "alpa") AS jumlah_alpa')
            ->join('master_kelas mk', 'mk.kelas_id = ak.kelas_id')
            ->join('master_mapel mm', 'mm.mapel_id = ak.mapel_id')
            ->join('master_guru mg', 'mg.guru_id = ak.guru_id')
            ->join('master_jam_pelajaran mj', 'mj.jam_pelajaran_id = ak.jam_pelajaran_id')
            ->join('master_tahun_ajaran mta', 'mta.tahun_ajaran_id = ak.tahun_ajaran_id')
            ->join('absensi_absensi_kelas_siswa aks', 'aks.absensi_kelas_id = ak.absensi_kelas_id', 'left')
            ->groupBy('ak.absensi_kelas_id');

        if ($date !== '') {
            $builder->where('ak.tanggal', $date);
        }
        if ($classId !== '') {
            $builder->where('ak.kelas_id', (int) $classId);
        }

        $records = $builder->orderBy('ak.tanggal', 'DESC')->orderBy('mk.kelas_nama', 'ASC')->get()->getResultArray();

        return view('panel/class-attendance', [
            'title' => 'Data Absensi Kelas',
            'activePage' => 'class-attendance',
            'records' => $records,
            'date' => $date,
            'classId' => $classId,
            'classes' => $this->db->table('master_kelas')->orderBy('kelas_nama')->get()->getResultArray(),
            'error' => session()->getFlashdata('attendanceError'),
            'message' => session()->getFlashdata('attendanceMessage'),
            'canCreate' => \App\Libraries\ActionAccess::can('class-attendance.create'),
            'canEdit' => \App\Libraries\ActionAccess::can('class-attendance.edit'),
            'canDelete' => \App\Libraries\ActionAccess::can('class-attendance.delete'),
            'canSendWhatsApp' => \App\Libraries\ActionAccess::can('class-attendance.whatsapp'),
        ]);
    }

    public function sendDailyWhatsAppReport(): RedirectResponse
    {
        $date = trim((string) $this->request->getPost('tanggal'));
        $classId = filter_var($this->request->getPost('kelas_id'), FILTER_VALIDATE_INT);
        if ($date === '' || ! $this->isValidDate($date) || $classId === false || $classId < 1) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Pilih tanggal dan kelas yang valid sebelum mengirim rekap WhatsApp.');
        }

        $result = (new \App\Libraries\ClassAttendanceWhatsAppService($this->db))
            ->sendDailyClassReport($classId, $date);
        $redirectUrl = site_url('class-attendance') . '?' . http_build_query([
            'tanggal' => $date,
            'kelas_id' => $classId,
        ]);
        if (isset($result['error'])) {
            return redirect()->to($redirectUrl)->with('attendanceError', $result['error']);
        }
        if (! empty($result['noStudents'])) {
            return redirect()->to($redirectUrl)->with('attendanceError', 'Tidak ada siswa aktif di kelas yang dipilih.');
        }
        if (! empty($result['noRecords'])) {
            return redirect()->to($redirectUrl)->with('attendanceError', 'Belum ada data tap RFID atau absensi kelas pada tanggal tersebut.');
        }

        $summary = $this->request->getLocale() === 'en'
            ? sprintf(
                'Class WhatsApp summary: Fonnte accepted %d guardian messages, %d failed, and %d students have no WhatsApp contact.',
                $result['sent'],
                $result['failed'],
                $result['withoutContact']
            )
            : sprintf(
                'Rekap WhatsApp kelas: %d wali murid diterima Fonnte, %d gagal, %d siswa tanpa kontak WhatsApp.',
                $result['sent'],
                $result['failed'],
                $result['withoutContact']
            );
        if ($result['logErrors'] > 0) {
            $summary .= $this->request->getLocale() === 'en'
                ? sprintf(' Failed to save %d log records; check the application log.', $result['logErrors'])
                : sprintf(' Status log gagal disimpan untuk %d catatan; periksa log aplikasi.', $result['logErrors']);
        }

        return redirect()->to($redirectUrl)
            ->with($result['failed'] > 0 || $result['logErrors'] > 0 ? 'attendanceError' : 'attendanceMessage', $summary);
    }

    public function create()
    {
        return $this->form();
    }

    public function sendAbsenceWhatsApp(int $id): RedirectResponse
    {
        $result = (new \App\Libraries\ClassAttendanceWhatsAppService($this->db))->sendAbsenceNotifications($id);
        if (isset($result['error'])) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', $result['error']);
        }

        if (! empty($result['noAbsences'])) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceMessage', 'Tidak ada notifikasi WhatsApp yang perlu dikirim karena semua siswa hadir.');
        }

        $notificationSummary = sprintf(
            'Notifikasi absensi per mapel: %d orang tua masuk antrean Fonnte, %d gagal, %d sudah terkirim dan dilewati, %d siswa tanpa kontak.',
            $result['sent'],
            $result['failed'],
            $result['skipped'],
            $result['withoutContact']
        );

        return redirect()->to(site_url('class-attendance'))
            ->with($result['failed'] > 0 ? 'attendanceError' : 'attendanceMessage', $notificationSummary);
    }

    public function edit(int $id)
    {
        if ($id < 1) {
            return redirect()->to(site_url('class-attendance'));
        }

        $record = $this->db->table('absensi_absensi_kelas')
            ->where('absensi_kelas_id', $id)
            ->get()
            ->getRowArray();
        if ($record === null) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Data absensi tidak ditemukan.');
        }

        return $this->form($record);
    }

    private function form(?array $record = null)
    {
        $classes = $this->db->table('master_kelas')->orderBy('kelas_tingkat')->orderBy('kelas_nama')->get()->getResultArray();
        $students = $this->db->table('master_siswa')
            ->select('siswa_id, kelas_id, nama_siswa, nis')
            ->where('status', 'aktif')
            ->where('deleted_at', null)
            ->orderBy('nama_siswa')
            ->get()
            ->getResultArray();
        if ($record !== null) {
            $existingStudents = $this->db->table('absensi_absensi_kelas_siswa aks')
                ->select('aks.siswa_id, aks.status, aks.keterangan, ms.nama_siswa, ms.nis')
                ->join('master_siswa ms', 'ms.siswa_id = aks.siswa_id')
                ->where('aks.absensi_kelas_id', $record['absensi_kelas_id'])
                ->orderBy('ms.nama_siswa')
                ->get()
                ->getResultArray();
            $existingByStudent = [];
            foreach ($existingStudents as $student) {
                $existingByStudent[(int) $student['siswa_id']] = $student;
            }

            foreach ($students as &$student) {
                $id = (int) $student['siswa_id'];
                if (isset($existingByStudent[$id]) && (int) $student['kelas_id'] !== (int) $record['kelas_id']) {
                    $student['kelas_id'] = $record['kelas_id'];
                }
                $student['status'] = $existingByStudent[$id]['status'] ?? 'hadir';
                $student['keterangan'] = $existingByStudent[$id]['keterangan'] ?? null;
                unset($existingByStudent[$id]);
            }
            unset($student);

            foreach ($existingByStudent as $student) {
                $students[] = [
                    'siswa_id' => $student['siswa_id'],
                    'kelas_id' => $record['kelas_id'],
                    'nama_siswa' => $student['nama_siswa'],
                    'nis' => $student['nis'],
                    'status' => $student['status'],
                    'keterangan' => $student['keterangan'],
                ];
            }
        }

        $subjectTeacherIds = [];
        foreach ($this->db->table('teacher_mapel')->select('mapel_id, guru_id')->get()->getResultArray() as $assignment) {
            $subjectTeacherIds[(int) $assignment['mapel_id']][] = (int) $assignment['guru_id'];
        }

        return view('panel/class-attendance-form', [
            'title' => $record === null ? 'Catat Absensi Kelas' : 'Edit Absensi Kelas',
            'activePage' => 'class-attendance',
            'classes' => $classes,
            'students' => $students,
            'subjects' => $this->db->table('master_mapel')->orderBy('mapel_nama')->get()->getResultArray(),
            'subjectTeacherIds' => $subjectTeacherIds,
            'teachers' => $this->db->table('master_guru')->orderBy('guru_nama')->get()->getResultArray(),
            'periods' => $this->db->table('master_jam_pelajaran')->orderBy('jam_ke')->get()->getResultArray(),
            'academicYears' => $this->db->table('master_tahun_ajaran')->orderBy('is_active', 'DESC')->orderBy('tahun_ajaran_nama', 'DESC')->orderBy('semester')->get()->getResultArray(),
            'statuses' => ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpa' => 'Alpa'],
            'record' => $record,
            'error' => session()->getFlashdata('attendanceError'),
        ]);
    }

    public function delete(int $id): RedirectResponse
    {
        if ($id < 1 || $this->db->table('absensi_absensi_kelas')
            ->where('absensi_kelas_id', $id)->countAllResults() !== 1) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Data absensi tidak ditemukan.');
        }

        $this->db->transBegin();
        try {
            $detailsDeleted = $this->db->table('absensi_absensi_kelas_siswa')
                ->where('absensi_kelas_id', $id)
                ->delete();
            if (! $detailsDeleted) {
                throw new DatabaseException('Could not delete class attendance student details.');
            }
            $deleted = $this->db->table('absensi_absensi_kelas')
                ->where('absensi_kelas_id', $id)
                ->delete();
            if (! $deleted || $this->db->transStatus() === false) {
                throw new DatabaseException('Could not delete the class attendance session and its details.');
            }
            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            log_message('error', 'Failed to delete class attendance {id}: {message}', [
                'id' => $id,
                'message' => $exception->getMessage(),
            ]);
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Absensi gagal dihapus. Silakan coba kembali.');
        }

        return redirect()->to(site_url('class-attendance'))
            ->with('attendanceMessage', 'Absensi kelas berhasil dihapus.');
    }

    public function show(int $id)
    {
        $data = $this->getSessionReportData($id);
        if ($data === null) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Data absensi tidak ditemukan.');
        }

        return view('panel/class-attendance-detail', [
            'title' => 'Detail Absensi Kelas',
            'activePage' => 'class-attendance',
            ...$data,
        ]);
    }

    public function downloadPdf(int $id): ResponseInterface
    {
        $data = $this->getSessionReportData($id);
        if ($data === null) {
            return $this->response->setStatusCode(404)->setBody('Data absensi kelas tidak ditemukan.');
        }

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml(view('report/class-attendance-pdf', $data), 'UTF-8');

        try {
            $dompdf->render();
        } catch (\Dompdf\Exception $exception) {
            log_message('error', 'Dompdf failed to render class attendance report: {message}', [
                'message' => $exception->getMessage(),
                'attendanceId' => $id,
            ]);

            return $this->response->setStatusCode(500)->setBody(
                'PDF laporan absensi siswa gagal dibuat. Periksa log aplikasi untuk detail teknis.'
            );
        }

        $filenameClass = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $data['session']['kelas_nama']) ?? 'kelas';
        $filenameDate = str_replace('-', '', (string) $data['session']['tanggal']);
        $filename = 'absensi-siswa-' . trim($filenameClass, '-') . '-' . $filenameDate . '.pdf';

        return $this->response
            ->download($filename, $dompdf->output(), true)
            ->setContentType('application/pdf');
    }

    public function monthlyReport()
    {
        return view('report/class-attendance', $this->getMonthlyReportData());
    }

    public function downloadMonthlyReportPdf(): ResponseInterface
    {
        $reportData = $this->getMonthlyReportData();
        if ($reportData['filterError'] !== null) {
            return $this->response->setStatusCode(400)->setBody($reportData['filterError']);
        }
        if ($reportData['selectedClass'] === null || $reportData['month'] === '') {
            return $this->response->setStatusCode(400)->setBody('Pilih kelas dan bulan terlebih dahulu.');
        }

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml(view('report/class-attendance-monthly-pdf', $reportData), 'UTF-8');

        try {
            $dompdf->render();
        } catch (\Dompdf\Exception $exception) {
            log_message('error', 'Dompdf failed to render monthly class attendance report: {message}', [
                'message' => $exception->getMessage(),
                'classId' => $reportData['classId'],
                'month' => $reportData['month'],
            ]);

            return $this->response->setStatusCode(500)->setBody(
                'PDF laporan absensi kelas gagal dibuat. Periksa log aplikasi untuk detail teknis.'
            );
        }

        $safeClassName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $reportData['selectedClass']['kelas_nama']) ?? 'kelas';
        $filename = 'laporan-absensi-kelas-' . trim($safeClassName, '-') . '-' . $reportData['month'] . '.pdf';

        return $this->response
            ->download($filename, $dompdf->output(), true)
            ->setContentType('application/pdf');
    }

    private function getMonthlyReportData(): array
    {
        $classes = $this->db->table('master_kelas')
            ->orderBy('kelas_tingkat')
            ->orderBy('kelas_nama')
            ->get()
            ->getResultArray();
        $rawClassId = trim((string) $this->request->getGet('kelas_id'));
        $classId = $rawClassId === '' ? null : filter_var($rawClassId, FILTER_VALIDATE_INT);
        $month = trim((string) $this->request->getGet('bulan'));
        $filterError = null;
        $selectedClass = null;
        $students = [];
        $records = [];

        if ($classId === false || ($classId !== null && $classId < 1)) {
            $filterError = 'Pilih kelas yang valid.';
        } elseif ($month !== '' && ! $this->isValidMonth($month)) {
            $filterError = 'Format bulan tidak valid.';
        } elseif ($classId !== null) {
            foreach ($classes as $class) {
                if ((int) $class['kelas_id'] === $classId) {
                    $selectedClass = $class;
                    break;
                }
            }
            if ($selectedClass === null) {
                $filterError = 'Kelas tidak ditemukan.';
            }
        }

        if ($filterError === null && $selectedClass !== null && $month !== '') {
            $startDate = $month . '-01';
            $endDate = (new \DateTimeImmutable($startDate))->modify('last day of this month')->format('Y-m-d');
            $records = $this->db->table('absensi_absensi_kelas_siswa aks')
                ->select('aks.absensi_kelas_siswa_id, aks.status, aks.keterangan, ms.siswa_id, ms.nama_siswa, ms.nis, ak.tanggal, mm.mapel_nama, mg.guru_nama, mj.jam_ke, mj.jam_mulai, mj.jam_selesai')
                ->join('absensi_absensi_kelas ak', 'ak.absensi_kelas_id = aks.absensi_kelas_id')
                ->join('master_siswa ms', 'ms.siswa_id = aks.siswa_id')
                ->join('master_mapel mm', 'mm.mapel_id = ak.mapel_id')
                ->join('master_guru mg', 'mg.guru_id = ak.guru_id')
                ->join('master_jam_pelajaran mj', 'mj.jam_pelajaran_id = ak.jam_pelajaran_id')
                ->where('ak.kelas_id', $classId)
                ->where('ak.tanggal >=', $startDate)
                ->where('ak.tanggal <=', $endDate)
                ->orderBy('ms.nama_siswa')
                ->orderBy('ak.tanggal')
                ->orderBy('mj.jam_ke')
                ->get()
                ->getResultArray();

            $summaryByStudent = [];
            foreach ($records as $record) {
                $studentId = (int) $record['siswa_id'];
                if (! isset($summaryByStudent[$studentId])) {
                    $summaryByStudent[$studentId] = [
                        'nama_siswa' => $record['nama_siswa'],
                        'nis' => $record['nis'],
                        'hadir' => 0,
                        'sakit' => 0,
                        'izin' => 0,
                        'alpa' => 0,
                        'total' => 0,
                    ];
                }
                if (isset($summaryByStudent[$studentId][$record['status']])) {
                    $summaryByStudent[$studentId][$record['status']]++;
                    $summaryByStudent[$studentId]['total']++;
                }
            }
            $students = array_values($summaryByStudent);
        }

        return [
            'title' => 'Laporan Absensi Kelas',
            'activePage' => 'class-attendance-report',
            'classes' => $classes,
            'classId' => $classId === false ? null : $classId,
            'selectedClass' => $selectedClass,
            'month' => $month,
            'monthLabel' => $month !== '' && $this->isValidMonth($month)
                ? $this->formatMonthLabel($month)
                : '',
            'students' => $students,
            'records' => $records,
            'filterError' => $filterError,
        ];
    }

    private function isValidMonth(string $month): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m', $month);

        return $parsed !== false && $parsed->format('Y-m') === $month;
    }

    private function formatMonthLabel(string $month): string
    {
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $date = \DateTimeImmutable::createFromFormat('!Y-m', $month);

        return $date === false ? $month : $monthNames[(int) $date->format('n')] . ' ' . $date->format('Y');
    }

    private function getSessionReportData(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }

        $session = $this->db->table('absensi_absensi_kelas ak')
            ->select('ak.absensi_kelas_id, ak.tanggal, mk.kelas_nama, mm.mapel_nama, mg.guru_nama, mj.jam_ke, mj.jam_mulai, mj.jam_selesai, mta.tahun_ajaran_nama, mta.semester')
            ->join('master_kelas mk', 'mk.kelas_id = ak.kelas_id')
            ->join('master_mapel mm', 'mm.mapel_id = ak.mapel_id')
            ->join('master_guru mg', 'mg.guru_id = ak.guru_id')
            ->join('master_jam_pelajaran mj', 'mj.jam_pelajaran_id = ak.jam_pelajaran_id')
            ->join('master_tahun_ajaran mta', 'mta.tahun_ajaran_id = ak.tahun_ajaran_id')
            ->where('ak.absensi_kelas_id', $id)
            ->get()
            ->getRowArray();

        if ($session === null) {
            return null;
        }

        $students = $this->db->table('absensi_absensi_kelas_siswa aks')
            ->select('aks.siswa_id, aks.status, aks.keterangan, ms.nama_siswa, ms.nis')
            ->join('master_siswa ms', 'ms.siswa_id = aks.siswa_id')
            ->where('aks.absensi_kelas_id', $id)
            ->orderBy('ms.nama_siswa')
            ->get()
            ->getResultArray();

        $summary = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
        foreach ($students as $student) {
            if (isset($summary[$student['status']])) {
                $summary[$student['status']]++;
            }
        }

        return [
            'session' => $session,
            'students' => $students,
            'summary' => $summary,
        ];
    }

    public function store(): RedirectResponse
    {
        $classId = filter_var($this->request->getPost('kelas_id'), FILTER_VALIDATE_INT);
        $subjectId = filter_var($this->request->getPost('mapel_id'), FILTER_VALIDATE_INT);
        $teacherId = filter_var($this->request->getPost('guru_id'), FILTER_VALIDATE_INT);
        $periodId = filter_var($this->request->getPost('jam_pelajaran_id'), FILTER_VALIDATE_INT);
        $yearId = filter_var($this->request->getPost('tahun_ajaran_id'), FILTER_VALIDATE_INT);
        $date = trim((string) $this->request->getPost('tanggal'));
        $user = session()->get('authUser');

        if ($classId === false || $subjectId === false || $teacherId === false || $periodId === false || $yearId === false || ! $this->isValidDate($date)) {
            return redirect()->to(site_url('class-attendance/create'))
                ->with('attendanceError', 'Pilih kelas, mapel, guru, jam, tahun ajaran, dan tanggal yang valid.');
        }

        return $this->storeAfterValidation($classId, $subjectId, $teacherId, $periodId, $yearId, $date, $user);
    }

    public function update(int $id): RedirectResponse
    {
            $redirect = redirect()->to(site_url('class-attendance/' . $id . '/edit'));
            $record = $this->db->table('absensi_absensi_kelas')
                ->where('absensi_kelas_id', $id)
                ->get()
                ->getRowArray();
            if ($id < 1 || $record === null) {
                return redirect()->to(site_url('class-attendance'))
                    ->with('attendanceError', 'Data absensi tidak ditemukan.');
            }

            $classId = filter_var($this->request->getPost('kelas_id'), FILTER_VALIDATE_INT);
            $subjectId = filter_var($this->request->getPost('mapel_id'), FILTER_VALIDATE_INT);
            $teacherId = filter_var($this->request->getPost('guru_id'), FILTER_VALIDATE_INT);
            $periodId = filter_var($this->request->getPost('jam_pelajaran_id'), FILTER_VALIDATE_INT);
            $yearId = filter_var($this->request->getPost('tahun_ajaran_id'), FILTER_VALIDATE_INT);
            $date = trim((string) $this->request->getPost('tanggal'));
            if ($classId === false
                || $subjectId === false || $teacherId === false || $periodId === false || $yearId === false
                || ! $this->isValidDate($date)) {
                return $redirect->with('attendanceError', 'Data jadwal tidak valid. Kelas sesi yang sudah dicatat tidak dapat diganti.');
            }
            if (! $this->isSubjectAssignedToTeacher((int) $subjectId, (int) $teacherId)) {
                return $redirect->with('attendanceError', 'Mata pelajaran tersebut belum ditetapkan untuk guru yang dipilih.');
            }

            foreach ([
                ['table' => 'master_kelas', 'key' => 'kelas_id', 'id' => $classId],
                ['table' => 'master_mapel', 'key' => 'mapel_id', 'id' => $subjectId],
                ['table' => 'master_guru', 'key' => 'guru_id', 'id' => $teacherId],
                ['table' => 'master_jam_pelajaran', 'key' => 'jam_pelajaran_id', 'id' => $periodId],
                ['table' => 'master_tahun_ajaran', 'key' => 'tahun_ajaran_id', 'id' => $yearId],
            ] as $selection) {
                if ($this->db->table($selection['table'])->where($selection['key'], $selection['id'])->countAllResults() !== 1) {
                    return $redirect->with('attendanceError', 'Pilihan data absensi tidak ditemukan. Muat ulang halaman.');
                }
            }

            $existingDetails = $this->db->table('absensi_absensi_kelas_siswa')
                ->select('siswa_id')
                ->where('absensi_kelas_id', $id)
                ->get()
                ->getResultArray();
            $submittedAttendance = $this->request->getPost('attendance');
            if ($existingDetails === [] || ! is_array($submittedAttendance)) {
                return $redirect->with('attendanceError', 'Status siswa tidak ditemukan untuk diperbarui.');
            }

            $currentStudents = $this->db->table('master_siswa')
                ->select('siswa_id')
                ->where('kelas_id', $classId)
                ->where('status', 'aktif')
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
            if ($currentStudents === []) {
                return $redirect->with('attendanceError', 'Kelas yang dipilih belum memiliki siswa aktif.');
            }

            $studentIds = array_map(static fn (array $student): int => (int) $student['siswa_id'], $currentStudents);
            if ($classId === (int) $record['kelas_id']) {
                $activeIds = array_fill_keys($studentIds, true);
                foreach ($existingDetails as $student) {
                    $studentId = (int) $student['siswa_id'];
                    if (! isset($activeIds[$studentId])) {
                        $studentIds[] = $studentId;
                    }
                }
            }
            sort($studentIds);
            $submittedIds = array_map('intval', array_keys($submittedAttendance));
            sort($studentIds);
            sort($submittedIds);
            if ($studentIds !== $submittedIds) {
                return $redirect->with('attendanceError', 'Daftar siswa absensi tidak valid.');
            }

            $notes = $this->request->getPost('keterangan');
            $details = [];
            foreach ($studentIds as $studentId) {
                $status = $submittedAttendance[(string) $studentId] ?? null;
                $note = trim((string) (is_array($notes) ? ($notes[$studentId] ?? '') : ''));
                if (! in_array($status, ['hadir', 'sakit', 'izin', 'alpa'], true) || mb_strlen($note) > 500) {
                    return $redirect->with('attendanceError', 'Status atau keterangan siswa tidak valid.');
                }
                $details[] = [
                    'absensi_kelas_id' => $id,
                    'siswa_id' => $studentId,
                    'status' => $status,
                    'keterangan' => $note === '' ? null : $note,
                ];
            }

            $duplicate = $this->db->table('absensi_absensi_kelas')
                ->where('kelas_id', $classId)
                ->where('mapel_id', $subjectId)
                ->where('jam_pelajaran_id', $periodId)
                ->where('tahun_ajaran_id', $yearId)
                ->where('tanggal', $date)
                ->where('absensi_kelas_id !=', $id)
                ->countAllResults();
            if ($duplicate > 0) {
                return $redirect->with('attendanceError', 'Jadwal tersebut sudah memiliki catatan absensi.');
            }

            $this->db->transBegin();
            try {
                $updated = $this->db->table('absensi_absensi_kelas')
                    ->where('absensi_kelas_id', $id)
                    ->update([
                        'kelas_id' => $classId,
                        'mapel_id' => $subjectId,
                        'guru_id' => $teacherId,
                        'jam_pelajaran_id' => $periodId,
                        'tahun_ajaran_id' => $yearId,
                        'tanggal' => $date,
                    ]);
                if (! $updated) {
                    throw new DatabaseException('Could not update the class attendance session.');
                }

                $detailsDeleted = $this->db->table('absensi_absensi_kelas_siswa')
                    ->where('absensi_kelas_id', $id)
                    ->delete();
                if (! $detailsDeleted || ! $this->db->table('absensi_absensi_kelas_siswa')->insertBatch($details)) {
                    throw new DatabaseException('Could not replace class attendance student statuses.');
                }

                if ($this->db->transStatus() === false) {
                    throw new DatabaseException('Class attendance update transaction failed.');
                }
                $this->db->transCommit();
            } catch (DatabaseException $exception) {
                $this->db->transRollback();
                log_message('error', 'Failed to update class attendance {id}: {message}', [
                    'id' => $id,
                    'message' => $exception->getMessage(),
                ]);
                return $redirect->with('attendanceError', 'Perubahan absensi gagal disimpan. Coba kembali.');
            }

            if ($this->db->tableExists('whatsapp_daily_attendance_log')
                && $this->db->fieldExists('absensi_kelas_id', 'whatsapp_daily_attendance_log')) {
                $this->db->table('whatsapp_daily_attendance_log')
                    ->where('absensi_kelas_id', $id)
                    ->delete();
            }
            $notificationResult = (new \App\Libraries\ClassAttendanceWhatsAppService($this->db))->sendAbsenceNotifications($id);
            if (isset($notificationResult['error'])) {
                return redirect()->to(site_url('class-attendance'))
                    ->with('attendanceError', 'Absensi berhasil diperbarui, tetapi notifikasi WhatsApp gagal diproses: ' . $notificationResult['error']);
            }

            if (! empty($notificationResult['noAbsences'])) {
                return redirect()->to(site_url('class-attendance'))
                    ->with('attendanceMessage', 'Absensi kelas berhasil diperbarui. Tidak ada notifikasi dikirim karena semua siswa hadir.');
            }

            $notificationMessage = sprintf(
                'Absensi kelas diperbarui. Notifikasi ketidakhadiran: %d terkirim ke antrean Fonnte, %d gagal, %d siswa tanpa kontak.',
                $notificationResult['sent'],
                $notificationResult['failed'],
                $notificationResult['withoutContact']
            );

            return redirect()->to(site_url('class-attendance'))
                ->with($notificationResult['failed'] > 0 ? 'attendanceError' : 'attendanceMessage', $notificationMessage);
    }

    private function storeAfterValidation(
        int $classId,
        int $subjectId,
        int $teacherId,
        int $periodId,
        int $yearId,
        string $date,
        ?array $user
    ): RedirectResponse {
        if (! $this->isSubjectAssignedToTeacher($subjectId, $teacherId)) {
            return redirect()->to(site_url('class-attendance/create'))
                ->with('attendanceError', 'Mata pelajaran tersebut belum ditetapkan untuk guru yang dipilih.');
        }

        foreach ([
            ['table' => 'master_kelas', 'key' => 'kelas_id', 'id' => $classId],
            ['table' => 'master_mapel', 'key' => 'mapel_id', 'id' => $subjectId],
            ['table' => 'master_guru', 'key' => 'guru_id', 'id' => $teacherId],
            ['table' => 'master_jam_pelajaran', 'key' => 'jam_pelajaran_id', 'id' => $periodId],
            ['table' => 'master_tahun_ajaran', 'key' => 'tahun_ajaran_id', 'id' => $yearId],
        ] as $selection) {
            if ($this->db->table($selection['table'])->where($selection['key'], $selection['id'])->countAllResults() !== 1) {
                return redirect()->to(site_url('class-attendance/create'))
                    ->with('attendanceError', 'Pilihan data absensi tidak ditemukan. Muat ulang halaman.');
            }
        }

        $students = $this->db->table('master_siswa')
            ->select('siswa_id')
            ->where('kelas_id', $classId)
            ->where('status', 'aktif')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        if ($students === []) {
            return redirect()->to(site_url('class-attendance/create'))
                ->with('attendanceError', 'Kelas yang dipilih belum memiliki siswa aktif.');
        }

        $submittedAttendance = $this->request->getPost('attendance');
        if (! is_array($submittedAttendance)) {
            return redirect()->to(site_url('class-attendance/create'))
                ->with('attendanceError', 'Pilih status kehadiran setiap siswa di kelas.');
        }

        $studentIds = array_map(static fn (array $student): int => (int) $student['siswa_id'], $students);
        $details = [];
        foreach ($studentIds as $studentId) {
            $status = $submittedAttendance[(string) $studentId] ?? null;
            if (! in_array($status, ['hadir', 'sakit', 'izin', 'alpa'], true)) {
                return redirect()->to(site_url('class-attendance/create'))
                    ->with('attendanceError', 'Pastikan setiap siswa memiliki status kehadiran yang valid.');
            }

            $note = trim((string) ($this->request->getPost('keterangan')[$studentId] ?? ''));
            if (mb_strlen($note) > 500) {
                return redirect()->to(site_url('class-attendance/create'))
                    ->with('attendanceError', 'Keterangan maksimal 500 karakter.');
            }

            $details[] = [
                'siswa_id' => $studentId,
                'status' => $status,
                'keterangan' => $note === '' ? null : $note,
            ];
        }

        $sessionExists = $this->db->table('absensi_absensi_kelas')
            ->where('kelas_id', $classId)
            ->where('mapel_id', $subjectId)
            ->where('jam_pelajaran_id', $periodId)
            ->where('tahun_ajaran_id', $yearId)
            ->where('tanggal', $date)
            ->countAllResults();
        if ($sessionExists > 0) {
            return redirect()->to(site_url('class-attendance/create'))
                ->with('attendanceError', 'Absensi untuk kelas, mapel, jam, dan tanggal tersebut sudah pernah dicatat.');
        }

        $this->db->transBegin();
        try {
            $saved = $this->db->table('absensi_absensi_kelas')->insert([
                'kelas_id' => $classId,
                'mapel_id' => $subjectId,
                'guru_id' => $teacherId,
                'jam_pelajaran_id' => $periodId,
                'tahun_ajaran_id' => $yearId,
                'tanggal' => $date,
                'dicatat_oleh' => (int) ($user['user_id'] ?? 0),
            ]);

            if (! $saved) {
                throw new DatabaseException('Could not save the class attendance session.');
            }

            $sessionId = (int) $this->db->insertID();
            foreach ($details as &$detail) {
                $detail['absensi_kelas_id'] = $sessionId;
            }
            unset($detail);

            if (! $this->db->table('absensi_absensi_kelas_siswa')->insertBatch($details)) {
                throw new DatabaseException('Could not save class attendance student statuses.');
            }

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Class attendance database transaction failed.');
            }

            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            log_message('error', 'Failed to save class attendance: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('class-attendance/create'))
                ->with('attendanceError', 'Data absensi gagal disimpan. Periksa data lalu coba kembali.');
        }

        $notificationResult = (new \App\Libraries\ClassAttendanceWhatsAppService($this->db))->sendAbsenceNotifications($sessionId);
        if (isset($notificationResult['error'])) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceError', 'Absensi berhasil disimpan, tetapi notifikasi WhatsApp gagal diproses: ' . $notificationResult['error']);
        }

        if (! empty($notificationResult['noAbsences'])) {
            return redirect()->to(site_url('class-attendance'))
                ->with('attendanceMessage', 'Absensi kelas berhasil disimpan. Tidak ada notifikasi dikirim karena semua siswa hadir.');
        }

        $message = sprintf(
            'Absensi kelas berhasil disimpan. Notifikasi ketidakhadiran: %d terkirim ke antrean Fonnte, %d gagal, %d dilewati, %d siswa tanpa kontak.',
            $notificationResult['sent'],
            $notificationResult['failed'],
            $notificationResult['skipped'],
            $notificationResult['withoutContact']
        );

        return redirect()->to(site_url('class-attendance'))
            ->with($notificationResult['failed'] > 0 ? 'attendanceError' : 'attendanceMessage', $message);
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function isSubjectAssignedToTeacher(int $subjectId, int $teacherId): bool
    {
        return $this->db->table('teacher_mapel')
            ->where('mapel_id', $subjectId)
            ->where('guru_id', $teacherId)
            ->countAllResults() === 1;
    }
}
