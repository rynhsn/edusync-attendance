<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExtracurricularAttendanceController extends BaseController
{
    private const STATUSES = [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alpa' => 'Alpa',
    ];

    public function index()
    {
        $formClassId = (int) $this->request->getGet('kelas_id');
        $formDate = trim((string) ($this->request->getGet('tanggal') ?? date('Y-m-d')));
        $formActivity = trim((string) $this->request->getGet('nama_kegiatan'));

        $db = db_connect();
        $classes = $db->table('master_kelas')
            ->orderBy('kelas_tingkat')
            ->orderBy('kelas_nama')
            ->get()
            ->getResultArray();
        $activities = $this->getActivitiesForClass($db, $formClassId);
        if (! in_array($formActivity, $activities, true)) {
            $formActivity = '';
        }
        if ($formActivity === '' && count($activities) === 1) {
            $formActivity = $activities[0];
        }

        $students = [];

        if ($formClassId > 0 && $formActivity !== '' && $this->isValidDate($formDate)) {
            $students = $db->table('master_siswa')
                ->select('siswa_id, kelas_id, nama_siswa, nis')
                ->where('kelas_id', $formClassId)
                ->where('status', 'aktif')
                ->where('deleted_at', null)
                ->orderBy('nama_siswa')
                ->get()
                ->getResultArray();

            if ($students !== []) {
                $existingRows = $db->table('absensi_ekstrakurikuler')
                    ->where('nama_kegiatan', $formActivity)
                    ->where('tanggal', $formDate)
                    ->whereIn('siswa_id', array_column($students, 'siswa_id'))
                    ->get()
                    ->getResultArray();
                $existingByStudent = [];
                foreach ($existingRows as $row) {
                    $existingByStudent[(int) $row['siswa_id']] = $row;
                }

                foreach ($students as &$student) {
                    $existing = $existingByStudent[(int) $student['siswa_id']] ?? null;
                    $student['status'] = $existing['status'] ?? 'hadir';
                    $student['keterangan'] = $existing['keterangan'] ?? '';
                }
                unset($student);
            } else {
                foreach ($students as &$student) {
                    $student['status'] = 'hadir';
                    $student['keterangan'] = '';
                }
                unset($student);
            }
        }

        return view('report/extracurricular-attendance', [
            'title' => 'Absensi Ekstrakurikuler',
            'activePage' => 'extracurricular-attendance',
            'classes' => $classes,
            'students' => $students,
            'activities' => $activities,
            'statuses' => self::STATUSES,
            'formClassId' => $formClassId,
            'formDate' => $formDate,
            'formActivity' => $formActivity,
            'error' => session()->getFlashdata('extracurricularAttendanceError'),
            'message' => session()->getFlashdata('extracurricularAttendanceMessage'),
        ]);
    }

    public function report()
    {
        $filterFrom = trim((string) $this->request->getGet('mulai'));
        $filterTo = trim((string) $this->request->getGet('akhir'));
        $filterStudent = trim((string) $this->request->getGet('siswa'));
        $filterActivity = trim((string) $this->request->getGet('kegiatan'));
        $filterClassId = (int) $this->request->getGet('kelas_filter');
        $filterError = null;

        if (($filterFrom !== '' && ! $this->isValidDate($filterFrom))
            || ($filterTo !== '' && ! $this->isValidDate($filterTo))) {
            $filterError = 'Format tanggal filter tidak valid.';
        } elseif ($filterFrom !== '' && $filterTo !== '' && $filterFrom > $filterTo) {
            $filterError = 'Tanggal mulai tidak boleh melewati tanggal akhir.';
        }

        $db = db_connect();
        $classes = $db->table('master_kelas')
            ->orderBy('kelas_tingkat')
            ->orderBy('kelas_nama')
            ->get()
            ->getResultArray();
        $students = $db->table('master_siswa')
            ->select('siswa_id, nama_siswa, nis')
            ->where('deleted_at', null)
            ->orderBy('nama_siswa')
            ->get()
            ->getResultArray();
        $attendance = [];

        if ($filterError === null) {
            $builder = $db->table('absensi_ekstrakurikuler aea')
                ->select('aea.absensi_ekstrakurikuler_id, aea.nama_kegiatan, aea.tanggal, aea.status, aea.keterangan, ms.nama_siswa, ms.nis, mk.kelas_nama')
                ->join('master_siswa ms', 'ms.siswa_id = aea.siswa_id', 'left')
                ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left');

            if ($filterFrom !== '') {
                $builder->where('aea.tanggal >=', $filterFrom);
            }
            if ($filterTo !== '') {
                $builder->where('aea.tanggal <=', $filterTo);
            }
            if ($filterStudent !== '') {
                $builder->like('ms.nama_siswa', $filterStudent);
            }
            if ($filterActivity !== '') {
                $builder->like('aea.nama_kegiatan', $filterActivity);
            }
            if ($filterClassId > 0) {
                $builder->where('ms.kelas_id', $filterClassId);
            }

            $attendance = $builder
                ->orderBy('aea.tanggal', 'DESC')
                ->orderBy('aea.nama_kegiatan', 'ASC')
                ->orderBy('ms.nama_siswa', 'ASC')
                ->get()
                ->getResultArray();
        }

        return view('report/extracurricular-attendance-report', [
            'title' => 'Laporan Absensi Ekstrakurikuler',
            'activePage' => 'extracurricular-attendance-report',
            'classes' => $classes,
            'students' => $students,
            'attendance' => $attendance,
            'statuses' => self::STATUSES,
            'filterFrom' => $filterFrom,
            'filterTo' => $filterTo,
            'filterStudent' => $filterStudent,
            'filterActivity' => $filterActivity,
            'filterClassId' => $filterClassId,
            'filterError' => $filterError,
        ]);
    }

    public function downloadMonthlyStudentReportPdf(): ResponseInterface
    {
        $studentId = filter_var($this->request->getGet('siswa_id'), FILTER_VALIDATE_INT);
        $month = trim((string) $this->request->getGet('bulan'));
        if ($studentId === false || $studentId < 1 || ! $this->isValidMonth($month)) {
            return $this->response->setStatusCode(400)->setBody('Pilih siswa dan bulan yang valid untuk mengunduh laporan.');
        }

        $db = db_connect();
        $student = $db->table('master_siswa ms')
            ->select('ms.siswa_id, ms.nama_siswa, ms.nis, mk.kelas_nama')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left')
            ->where('ms.siswa_id', $studentId)
            ->get()
            ->getRowArray();
        if ($student === null) {
            return $this->response->setStatusCode(404)->setBody('Data siswa tidak ditemukan.');
        }

        $startDate = $month . '-01';
        $endDate = (new \DateTimeImmutable($startDate))->modify('last day of this month')->format('Y-m-d');
        $records = $db->table('absensi_ekstrakurikuler')
            ->select('nama_kegiatan, tanggal, status, keterangan')
            ->where('siswa_id', $studentId)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->orderBy('tanggal')
            ->orderBy('nama_kegiatan')
            ->get()
            ->getResultArray();

        $summary = array_fill_keys(array_keys(self::STATUSES), 0);
        foreach ($records as $record) {
            if (isset($summary[$record['status']])) {
                $summary[$record['status']]++;
            }
        }

        $reportData = [
            'student' => $student,
            'month' => $month,
            'monthLabel' => $this->formatMonthLabel($month),
            'records' => $records,
            'summary' => $summary,
            'statuses' => self::STATUSES,
        ];
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml(view('report/extracurricular-attendance-student-pdf', $reportData), 'UTF-8');

        try {
            $dompdf->render();
        } catch (\Dompdf\Exception $exception) {
            log_message('error', 'Dompdf failed to render monthly extracurricular attendance report: {message}', [
                'message' => $exception->getMessage(),
                'studentId' => $studentId,
                'month' => $month,
            ]);

            return $this->response->setStatusCode(500)->setBody(
                'PDF laporan absensi ekstrakurikuler gagal dibuat. Periksa log aplikasi untuk detail teknis.'
            );
        }

        $safeStudentName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $student['nama_siswa']) ?? 'siswa';
        $filename = 'laporan-absensi-ekstrakurikuler-' . trim($safeStudentName, '-') . '-' . $month . '.pdf';

        return $this->response
            ->download($filename, $dompdf->output(), true)
            ->setContentType('application/pdf');
    }

    public function save(): RedirectResponse
    {
        $classIdInput = $this->request->getPost('kelas_id');
        $dateInput = $this->request->getPost('tanggal');
        $activityInput = $this->request->getPost('nama_kegiatan');
        $classId = is_scalar($classIdInput) ? (int) $classIdInput : 0;
        $date = is_string($dateInput) ? trim($dateInput) : '';
        $activity = is_string($activityInput) ? trim($activityInput) : '';
        $statuses = $this->request->getPost('status');
        $notes = $this->request->getPost('keterangan');
        $redirectUrl = site_url('extracurricular-attendance') . '?' . http_build_query([
            'kelas_id' => $classId,
            'tanggal' => $date,
            'nama_kegiatan' => $activity,
        ]);

        if ($classId < 1 || ! $this->isValidDate($date) || $activity === '' || mb_strlen($activity) > 100) {
            return redirect()->to($redirectUrl)
                ->with('extracurricularAttendanceError', 'Kelas, tanggal, dan nama kegiatan wajib diisi dengan benar (maksimal 100 karakter untuk nama kegiatan).');
        }

        if (! is_array($statuses) || ! is_array($notes)) {
            return redirect()->to($redirectUrl)
                ->with('extracurricularAttendanceError', 'Data absensi siswa tidak valid.');
        }

        $db = db_connect();
        if (! in_array($activity, $this->getActivitiesForClass($db, $classId), true)) {
            return redirect()->to($redirectUrl)
                ->with('extracurricularAttendanceError', 'Pilih kegiatan ekstrakurikuler yang sudah terdaftar untuk kelas ini.');
        }

        $students = $db->table('master_siswa')
            ->select('siswa_id')
            ->where('kelas_id', $classId)
            ->where('status', 'aktif')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        if ($students === []) {
            return redirect()->to($redirectUrl)
                ->with('extracurricularAttendanceError', 'Tidak ada siswa aktif di kelas yang dipilih.');
        }

        $notesByStudent = [];
        foreach ($students as $student) {
            $studentId = (string) $student['siswa_id'];
            $status = $statuses[$studentId] ?? null;
            $note = $notes[$studentId] ?? '';
            if (! is_string($status) || ! isset(self::STATUSES[$status])) {
                return redirect()->to($redirectUrl)
                    ->with('extracurricularAttendanceError', 'Status absensi salah satu siswa tidak valid.');
            }
            if (! is_string($note) || mb_strlen($note) > 500) {
                return redirect()->to($redirectUrl)
                    ->with('extracurricularAttendanceError', 'Keterangan absensi maksimal 500 karakter.');
            }
            $notesByStudent[$studentId] = trim($note);
        }

        $existingRows = $db->table('absensi_ekstrakurikuler')
            ->where('nama_kegiatan', $activity)
            ->where('tanggal', $date)
            ->whereIn('siswa_id', array_column($students, 'siswa_id'))
            ->get()
            ->getResultArray();
        $existingByStudent = [];
        foreach ($existingRows as $row) {
            $existingByStudent[(int) $row['siswa_id']] = $row;
        }

        $db->transBegin();
        try {
            foreach ($students as $student) {
                $studentId = (int) $student['siswa_id'];
                $record = [
                    'status' => $statuses[(string) $studentId],
                    'keterangan' => $notesByStudent[(string) $studentId],
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $existing = $existingByStudent[$studentId] ?? null;

                if ($existing === null) {
                    $record['siswa_id'] = $studentId;
                    $record['nama_kegiatan'] = $activity;
                    $record['tanggal'] = $date;
                    $record['created_at'] = date('Y-m-d H:i:s');
                    if (! $db->table('absensi_ekstrakurikuler')->insert($record)) {
                        throw new DatabaseException('Inserting extracurricular attendance returned false.');
                    }
                } elseif (! $db->table('absensi_ekstrakurikuler')
                    ->where('absensi_ekstrakurikuler_id', $existing['absensi_ekstrakurikuler_id'])
                    ->update($record)) {
                    throw new DatabaseException('Updating extracurricular attendance returned false.');
                }
            }

            if ($db->transStatus() === false) {
                throw new DatabaseException('Saving extracurricular attendance failed.');
            }
            $db->transCommit();
        } catch (DatabaseException $exception) {
            $db->transRollback();
            log_message('error', 'Failed to save extracurricular attendance for {activity} on {date}: {message}', [
                'activity' => $activity,
                'date' => $date,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to($redirectUrl)
                ->with('extracurricularAttendanceError', 'Absensi ekstrakurikuler gagal disimpan. Silakan coba kembali.');
        }

        return redirect()->to($redirectUrl)
            ->with('extracurricularAttendanceMessage', 'Absensi ekstrakurikuler berhasil disimpan.');
    }

    private function isValidDate(string $date): bool
    {
        $parsedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsedDate !== false && $parsedDate->format('Y-m-d') === $date;
    }

    private function isValidMonth(string $month): bool
    {
        $parsedMonth = \DateTimeImmutable::createFromFormat('!Y-m', $month);

        return $parsedMonth !== false && $parsedMonth->format('Y-m') === $month;
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

        return $date === false
            ? $month
            : $monthNames[(int) $date->format('n')] . ' ' . $date->format('Y');
    }

    private function getActivitiesForClass(\CodeIgniter\Database\BaseConnection $db, int $classId): array
    {
        $activities = [];

        if ($db->tableExists('master_ekstrakurikuler')) {
            $masterActivities = $db->table('master_ekstrakurikuler')
                ->select('nama_kegiatan')
                ->where('is_active', 1)
                ->get()
                ->getResultArray();
            foreach ($masterActivities as $activity) {
                $name = trim((string) ($activity['nama_kegiatan'] ?? ''));
                if ($name !== '') {
                    $activities[] = $name;
                }
            }
        }

        $assessmentActivities = $db->table('academic_student_extracurricular')
            ->select('nama_kegiatan')
            ->distinct()
            ->where('kelas_id', $classId)
            ->get()
            ->getResultArray();
        foreach ($assessmentActivities as $activity) {
            $name = trim((string) ($activity['nama_kegiatan'] ?? ''));
            if ($name !== '') {
                $activities[] = $name;
            }
        }

        $attendanceActivities = $db->table('absensi_ekstrakurikuler aea')
            ->select('aea.nama_kegiatan')
            ->distinct()
            ->join('master_siswa ms', 'ms.siswa_id = aea.siswa_id')
            ->where('ms.kelas_id', $classId)
            ->get()
            ->getResultArray();
        foreach ($attendanceActivities as $activity) {
            $name = trim((string) ($activity['nama_kegiatan'] ?? ''));
            if ($name !== '') {
                $activities[] = $name;
            }
        }

        $activities = array_values(array_unique($activities));
        sort($activities, SORT_NATURAL | SORT_FLAG_CASE);

        return $activities;
    }
}
