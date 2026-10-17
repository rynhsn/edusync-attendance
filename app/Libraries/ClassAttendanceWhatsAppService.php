<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class ClassAttendanceWhatsAppService
{
    private const DAILY_REPORT_LOG_SESSION_ID = 0;

    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null, private ?WhatsAppAttendanceNotifier $notifier = null)
    {
        $this->db = $db ?? db_connect();
        $this->notifier ??= new WhatsAppAttendanceNotifier();
    }

    public function sendDailyClassReport(int $classId, string $date): array
    {
        if (! $this->notifier->isConfigured()) {
            return [
                'error' => 'Konfigurasi WhatsApp belum lengkap: '
                    . implode(', ', $this->notifier->missingConfiguration()) . '.',
            ];
        }

        if (! $this->db->tableExists('whatsapp_daily_attendance_log')
            || ! $this->db->fieldExists('absensi_kelas_id', 'whatsapp_daily_attendance_log')) {
            return ['error' => 'Log WhatsApp belum mendukung notifikasi per sesi. Terapkan pembaruan log WhatsApp terlebih dahulu.'];
        }

        $class = $this->db->table('master_kelas')
            ->select('kelas_id, kelas_nama')
            ->where('kelas_id', $classId)
            ->get()
            ->getRowArray();
        if ($class === null) {
            return ['error' => 'Data kelas tidak ditemukan.'];
        }

        $students = $this->db->table('master_siswa ms')
            ->select('ms.siswa_id, ms.nama_siswa, ms.orang_tua_id, mo.nama_orang_tua, mo.no_whatsapp, ase.absensi_id, ase.waktu_masuk_aktual, ase.waktu_pulang_aktual, asam.status_absensi AS arrival_status, asak.status_absensi AS departure_status')
            ->join('master_orang_tua mo', 'mo.orang_tua_id = ms.orang_tua_id', 'left')
            ->join('absensi_absensi_sekolah ase', 'ase.siswa_id = ms.siswa_id AND ase.tanggal = ' . $this->db->escape($date), 'left', false)
            ->join('absensi_status_absensi asam', 'asam.status_absensi_id = ase.status_absensi_masuk_id', 'left')
            ->join('absensi_status_absensi asak', 'asak.status_absensi_id = ase.status_absensi_pulang_id', 'left')
            ->where('ms.kelas_id', $classId)
            ->where('ms.status', 'aktif')
            ->where('ms.deleted_at', null)
            ->orderBy('ms.nama_siswa')
            ->get()
            ->getResultArray();

        if ($students === []) {
            return ['noStudents' => true, 'sent' => 0, 'failed' => 0, 'withoutContact' => 0];
        }

        $sessions = $this->db->table('absensi_absensi_kelas ak')
            ->select('ak.absensi_kelas_id, mm.mapel_nama, mj.jam_ke, mj.jam_mulai, mj.jam_selesai')
            ->join('master_mapel mm', 'mm.mapel_id = ak.mapel_id')
            ->join('master_jam_pelajaran mj', 'mj.jam_pelajaran_id = ak.jam_pelajaran_id')
            ->where('ak.kelas_id', $classId)
            ->where('ak.tanggal', $date)
            ->orderBy('mj.jam_ke')
            ->orderBy('ak.absensi_kelas_id')
            ->get()
            ->getResultArray();

        $sessionIds = array_map(
            static fn (array $session): int => (int) $session['absensi_kelas_id'],
            $sessions
        );
        $lessonRows = [];
        if ($sessionIds !== []) {
            $lessonRows = $this->db->table('absensi_absensi_kelas_siswa aks')
                ->select('aks.absensi_kelas_id, aks.siswa_id, aks.status, aks.keterangan')
                ->whereIn('aks.absensi_kelas_id', $sessionIds)
                ->get()
                ->getResultArray();
        }

        $lessonStatusBySessionAndStudent = [];
        foreach ($lessonRows as $lessonRow) {
            $lessonStatusBySessionAndStudent[(int) $lessonRow['absensi_kelas_id']][(int) $lessonRow['siswa_id']] = $lessonRow;
        }

        $hasAttendanceRecords = $sessions !== [];
        $parents = [];
        $withoutContact = 0;
        foreach ($students as $student) {
            if (! empty($student['waktu_masuk_aktual']) || ! empty($student['waktu_pulang_aktual'])) {
                $hasAttendanceRecords = true;
            }

            $parentId = (int) ($student['orang_tua_id'] ?? 0);
            $phone = trim((string) ($student['no_whatsapp'] ?? ''));
            if ($parentId < 1 || $phone === '') {
                $withoutContact++;
                continue;
            }

            $parents[$parentId] ??= [
                'phone' => $phone,
                'students' => [],
            ];

            $lessons = [];
            foreach ($sessions as $session) {
                $lessonRecord = $lessonStatusBySessionAndStudent[(int) $session['absensi_kelas_id']][(int) $student['siswa_id']] ?? null;
                $lessons[] = [
                    'subject' => (string) $session['mapel_nama'],
                    'period' => (string) $session['jam_ke'],
                    'start_time' => substr((string) $session['jam_mulai'], 0, 5),
                    'end_time' => substr((string) $session['jam_selesai'], 0, 5),
                    'status' => $lessonRecord !== null
                        ? ucfirst((string) $lessonRecord['status'])
                        : 'Tidak tercatat',
                    'note' => trim((string) ($lessonRecord['keterangan'] ?? '')),
                ];
            }

            $parents[$parentId]['students'][] = [
                'name' => (string) $student['nama_siswa'],
                'arrival_time' => $student['waktu_masuk_aktual'] !== null
                    ? (string) $student['waktu_masuk_aktual']
                    : null,
                'arrival_status' => $student['arrival_status'] !== null
                    ? (string) $student['arrival_status']
                    : null,
                'departure_time' => $student['waktu_pulang_aktual'] !== null
                    ? (string) $student['waktu_pulang_aktual']
                    : null,
                'departure_status' => $student['departure_status'] !== null
                    ? (string) $student['departure_status']
                    : null,
                'lessons' => $lessons,
            ];
        }

        if (! $hasAttendanceRecords) {
            return ['noRecords' => true, 'sent' => 0, 'failed' => 0, 'withoutContact' => 0];
        }

        $result = [
            'reports' => count($parents),
            'sent' => 0,
            'failed' => 0,
            'withoutContact' => $withoutContact,
            'logErrors' => 0,
        ];
        $logSessionIds = $sessionIds !== [] ? $sessionIds : [self::DAILY_REPORT_LOG_SESSION_ID];
        $formattedDate = date('d/m/Y', strtotime($date));

        foreach ($parents as $parentId => $parent) {
            $now = date('Y-m-d H:i:s');
            foreach ($logSessionIds as $sessionId) {
                $this->db->query(
                    'INSERT IGNORE INTO whatsapp_daily_attendance_log '
                        . '(tanggal, orang_tua_id, absensi_kelas_id, status, attempts, created_at, updated_at) '
                        . 'VALUES (?, ?, ?, ?, 0, ?, ?)',
                    [$date, $parentId, $sessionId, 'pending', $now, $now]
                );
                $this->db->query(
                    'UPDATE whatsapp_daily_attendance_log '
                        . 'SET status = ?, attempts = attempts + 1, last_attempt_at = ?, last_error = NULL, updated_at = ? '
                        . 'WHERE tanggal = ? AND orang_tua_id = ? AND absensi_kelas_id = ?',
                    ['sending', $now, $now, $date, $parentId, $sessionId]
                );
            }

            $accepted = $this->notifier->sendDailyClassAttendanceReport(
                $parent['phone'],
                (string) $parent['students'][0]['name'],
                $formattedDate,
                (string) $class['kelas_nama'],
                $parent['students']
            );
            $status = $accepted ? 'sent' : 'failed';
            $error = $accepted ? null : 'Fonnte menolak atau gagal menerima permintaan.';
            foreach ($logSessionIds as $sessionId) {
                $updated = $this->db->table('whatsapp_daily_attendance_log')
                    ->where('tanggal', $date)
                    ->where('orang_tua_id', $parentId)
                    ->where('absensi_kelas_id', $sessionId)
                    ->update([
                        'status' => $status,
                        'sent_at' => $accepted ? $now : null,
                        'last_error' => $error,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                if (! $updated) {
                    $result['logErrors']++;
                    log_message('error', 'Could not save daily class WhatsApp report status for parent {parent}, class {class}, session {session}.', [
                        'parent' => $parentId,
                        'class' => $classId,
                        'session' => $sessionId,
                    ]);
                }
            }

            $result[$accepted ? 'sent' : 'failed']++;
        }

        return $result;
    }

    public function sendAbsenceNotifications(int $attendanceId): array
    {
        if (! $this->notifier->isConfigured()) {
            return [
                'error' => 'Konfigurasi WhatsApp belum lengkap: '
                    . implode(', ', $this->notifier->missingConfiguration()) . '.',
            ];
        }

        $session = $this->db->table('absensi_absensi_kelas ak')
            ->select('ak.tanggal, mk.kelas_nama, mm.mapel_nama, mj.jam_ke, mj.jam_mulai, mj.jam_selesai')
            ->join('master_kelas mk', 'mk.kelas_id = ak.kelas_id')
            ->join('master_mapel mm', 'mm.mapel_id = ak.mapel_id')
            ->join('master_jam_pelajaran mj', 'mj.jam_pelajaran_id = ak.jam_pelajaran_id')
            ->where('ak.absensi_kelas_id', $attendanceId)
            ->get()
            ->getRowArray();

        if ($session === null) {
            return ['error' => 'Data sesi absensi kelas tidak ditemukan.'];
        }

        if (! $this->db->tableExists('whatsapp_daily_attendance_log')
            || ! $this->db->fieldExists('absensi_kelas_id', 'whatsapp_daily_attendance_log')) {
            return ['error' => 'Log WhatsApp belum mendukung notifikasi per mapel. Terapkan update_whatsapp_daily_attendance_log_for_class.sql terlebih dahulu.'];
        }

        $rows = $this->db->table('absensi_absensi_kelas_siswa aks')
            ->select('aks.absensi_kelas_siswa_id, aks.siswa_id, aks.status, aks.keterangan, ms.nama_siswa, ms.orang_tua_id, mo.nama_orang_tua, mo.no_whatsapp')
            ->join('master_siswa ms', 'ms.siswa_id = aks.siswa_id')
            ->join('master_orang_tua mo', 'mo.orang_tua_id = ms.orang_tua_id', 'left')
            ->where('aks.absensi_kelas_id', $attendanceId)
            ->where('aks.status !=', 'hadir')
            ->orderBy('ms.nama_siswa')
            ->get()
            ->getResultArray();

        $parents = [];
        $withoutContact = 0;
        foreach ($rows as $row) {
            $parentId = (int) ($row['orang_tua_id'] ?? 0);
            $phone = trim((string) ($row['no_whatsapp'] ?? ''));
            if ($parentId < 1 || $phone === '') {
                $withoutContact++;
                continue;
            }

            $parents[$parentId] ??= [
                'name' => trim((string) ($row['nama_orang_tua'] ?? '')),
                'phone' => $phone,
                'students' => [],
            ];
            $parents[$parentId]['students'][] = [
                'name' => $row['nama_siswa'],
                'status' => $row['status'],
                'note' => trim((string) ($row['keterangan'] ?? '')),
            ];
        }

        if ($rows === []) {
            return [
                'noAbsences' => true,
                'reports' => 0,
                'sent' => 0,
                'failed' => 0,
                'skipped' => 0,
                'withoutContact' => 0,
            ];
        }

        $result = [
            'reports' => count($parents),
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
            'withoutContact' => $withoutContact,
        ];
        $date = date('d/m/Y', strtotime($session['tanggal']));

        foreach ($parents as $parentId => $parent) {
            $now = date('Y-m-d H:i:s');
            $staleBefore = date('Y-m-d H:i:s', strtotime('-30 minutes'));
            $this->db->query(
                'INSERT IGNORE INTO whatsapp_daily_attendance_log '
                    . '(tanggal, orang_tua_id, absensi_kelas_id, status, attempts, created_at, updated_at) '
                    . 'VALUES (?, ?, ?, ?, 0, ?, ?)',
                [$session['tanggal'], $parentId, $attendanceId, 'pending', $now, $now]
            );

            $this->db->query(
                'UPDATE whatsapp_daily_attendance_log '
                    . 'SET status = ?, attempts = attempts + 1, last_attempt_at = ?, last_error = NULL, updated_at = ? '
                    . 'WHERE tanggal = ? AND orang_tua_id = ? AND absensi_kelas_id = ? '
                    . 'AND (status IN (?, ?) OR (status = ? AND last_attempt_at < ?))',
                ['sending', $now, $now, $session['tanggal'], $parentId, $attendanceId, 'pending', 'failed', 'sending', $staleBefore]
            );

            if ($this->db->affectedRows() !== 1) {
                $result['skipped']++;
                continue;
            }

            $accepted = $this->notifier->sendClassAbsenceNotification(
                $parent['phone'],
                $parent['name'],
                $date,
                $session['kelas_nama'],
                $session['mapel_nama'],
                (string) $session['jam_ke'],
                $session['jam_mulai'],
                $session['jam_selesai'],
                $parent['students']
            );
            $status = $accepted ? 'sent' : 'failed';
            $updated = $this->db->table('whatsapp_daily_attendance_log')
                ->where('tanggal', $session['tanggal'])
                ->where('absensi_kelas_id', $attendanceId)
                ->where('orang_tua_id', $parentId)
                ->update([
                    'status' => $status,
                    'sent_at' => $accepted ? $now : null,
                    'last_error' => $accepted ? null : 'Fonnte menolak atau gagal menerima permintaan.',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (! $updated) {
                log_message('error', 'Could not save class WhatsApp attendance status for parent {parent} in session {session}.', [
                    'parent' => $parentId,
                    'session' => $attendanceId,
                ]);
            }

            $result[$accepted ? 'sent' : 'failed']++;
        }

        return $result;
    }
}
