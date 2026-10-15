<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;

class AcademicAssessmentController extends BaseController
{
    private BaseConnection $db;

    private const VALUES = ['A', 'B', 'C', 'D'];

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index(string $type)
    {
        if (! in_array($type, ['personality', 'extracurricular', 'kokurikuler'], true)) {
            return redirect()->to(site_url('panel'));
        }

        $classes = $this->db->table('master_kelas')
            ->select('kelas_id, kelas_tingkat, kelas_nama')
            ->orderBy('kelas_tingkat')
            ->orderBy('kelas_nama')
            ->get()
            ->getResultArray();
        $years = $this->db->table('master_tahun_ajaran')
            ->select('tahun_ajaran_id, tahun_ajaran_nama, semester, is_active')
            ->orderBy('is_active', 'DESC')
            ->orderBy('tahun_ajaran_nama', 'DESC')
            ->orderBy('semester')
            ->get()
            ->getResultArray();
        $selected = [
            'kelas_id' => $this->getPositiveInt($this->request->getGet('kelas_id')),
            'tahun_ajaran_id' => $this->getPositiveInt($this->request->getGet('tahun_ajaran_id')),
            'siswa_id' => $this->getPositiveInt($this->request->getGet('siswa_id')),
        ];
        $students = [];
        $student = null;
        $personalityAspects = [];
        $personalityGrades = [];
        $extracurriculars = [];
        $extracurricularOptions = [];
        $kokurikulerGrades = [];
        $kokurikulerOptions = [];
        $kokurikulerTableAvailable = $type !== 'kokurikuler'
            || $this->db->tableExists('academic_student_kokurikuler');
        $selectionError = null;
        if (! $kokurikulerTableAvailable) {
            $selectionError = 'Tabel nilai Kokurikuler belum tersedia. Jalankan create_kokurikuler_assessments.sql di phpMyAdmin, lalu muat ulang halaman.';
        }

        if ($type === 'extracurricular' && $this->db->tableExists('master_ekstrakurikuler')) {
            $masterActivities = $this->db->table('master_ekstrakurikuler')
                ->select('nama_kegiatan')
                ->where('is_active', 1)
                ->orderBy('nama_kegiatan')
                ->get()
                ->getResultArray();
            $extracurricularOptions = array_values(array_unique(array_filter(array_map(static fn (array $row): string => trim((string) ($row['nama_kegiatan'] ?? '')), $masterActivities))));
        }

        if ($type === 'kokurikuler' && $this->db->tableExists('master_kokurikuler')) {
            $masterActivities = $this->db->table('master_kokurikuler')
                ->select('nama_kegiatan')
                ->where('is_active', 1)
                ->orderBy('nama_kegiatan')
                ->get()
                ->getResultArray();
            $kokurikulerOptions = array_values(array_unique(array_filter(array_map(static fn (array $row): string => trim((string) ($row['nama_kegiatan'] ?? '')), $masterActivities))));
        }

        if ($type === 'personality') {
            $personalityAspects = $this->db->table('academic_personality_aspects')
                ->where('is_active', 1)
                ->orderBy('urutan')
                ->orderBy('aspek_id')
                ->get()
                ->getResultArray();
        }

        if ($selected['kelas_id'] !== null) {
            $students = $this->db->table('master_siswa')
                ->select('siswa_id, nama_siswa, nis')
                ->where('kelas_id', $selected['kelas_id'])
                ->groupStart()
                ->where('status', 'aktif')
                ->orWhere('status', '')
                ->groupEnd()
                ->where('deleted_at', null)
                ->orderBy('nama_siswa')
                ->get()
                ->getResultArray();
        }

        if ($selected['siswa_id'] !== null && $selected['kelas_id'] !== null && $selected['tahun_ajaran_id'] !== null) {
            $student = $this->db->table('master_siswa')
                ->select('siswa_id, nama_siswa, nis')
                ->where('siswa_id', $selected['siswa_id'])
                ->where('kelas_id', $selected['kelas_id'])
                ->groupStart()
                ->where('status', 'aktif')
                ->orWhere('status', '')
                ->groupEnd()
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if ($student === null) {
                $selectionError = 'Siswa tidak ditemukan pada kelas yang dipilih atau sudah tidak aktif.';
            } elseif ($kokurikulerTableAvailable) {
                if ($type === 'personality') {
                    $personalityGrades = $this->db->table('academic_student_personality_scores')
                        ->select('aspek_id, nilai')
                        ->where('siswa_id', $selected['siswa_id'])
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->get()
                        ->getResultArray();
                    $personalityGrades = array_column($personalityGrades, 'nilai', 'aspek_id');
                } elseif ($type === 'extracurricular') {
                    $extracurriculars = $this->db->table('academic_student_extracurricular')
                        ->select('ekstrakurikuler_id, nama_kegiatan, nilai')
                        ->where('siswa_id', $selected['siswa_id'])
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->orderBy('nama_kegiatan')
                        ->get()
                        ->getResultArray();

                    $existingActivities = $this->db->table('academic_student_extracurricular')
                        ->select('nama_kegiatan')
                        ->distinct()
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->get()
                        ->getResultArray();
                    foreach ($existingActivities as $activity) {
                        $name = trim((string) ($activity['nama_kegiatan'] ?? ''));
                        if ($name !== '' && ! in_array($name, $extracurricularOptions, true)) {
                            $extracurricularOptions[] = $name;
                        }
                    }
                    sort($extracurricularOptions, SORT_NATURAL | SORT_FLAG_CASE);
                } else {
                    $kokurikulerGrades = $this->db->table('academic_student_kokurikuler')
                        ->select('nama_kegiatan, nilai')
                        ->where('siswa_id', $selected['siswa_id'])
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->orderBy('nama_kegiatan')
                        ->get()
                        ->getResultArray();

                    $existingActivities = $this->db->table('academic_student_kokurikuler')
                        ->select('nama_kegiatan')
                        ->distinct()
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->get()
                        ->getResultArray();
                    foreach ($existingActivities as $activity) {
                        $name = trim((string) ($activity['nama_kegiatan'] ?? ''));
                        if ($name !== '' && ! in_array($name, $kokurikulerOptions, true)) {
                            $kokurikulerOptions[] = $name;
                        }
                    }
                    sort($kokurikulerOptions, SORT_NATURAL | SORT_FLAG_CASE);
                }
            }
        }

        return view('panel/academic-assessment', [
            'title' => match ($type) {
                'personality' => 'Penilaian Kepribadian',
                'extracurricular' => 'Penilaian Ekstrakurikuler',
                'kokurikuler' => 'Penilaian Kokurikuler',
            },
            'activePage' => match ($type) {
                'personality' => 'personality-assessment',
                'extracurricular' => 'extracurricular-assessment',
                'kokurikuler' => 'kokurikuler-assessment',
            },
            'type' => $type,
            'classes' => $classes,
            'years' => $years,
            'students' => $students,
            'selected' => $selected,
            'student' => $student,
            'personalityAspects' => $personalityAspects,
            'personalityGrades' => $personalityGrades,
            'extracurriculars' => $extracurriculars,
            'extracurricularOptions' => $extracurricularOptions,
            'kokurikulerGrades' => $kokurikulerGrades,
            'kokurikulerOptions' => $kokurikulerOptions,
            'kokurikulerTableAvailable' => $kokurikulerTableAvailable,
            'values' => self::VALUES,
            'selectionError' => $selectionError,
            'message' => session()->getFlashdata('assessmentMessage'),
            'error' => session()->getFlashdata('assessmentError'),
        ]);
    }

    public function save(string $type): RedirectResponse
    {
        if (! in_array($type, ['personality', 'extracurricular', 'kokurikuler'], true)) {
            return redirect()->to(site_url('panel'));
        }

        if ($type === 'personality' && str_ends_with((string) $this->request->getPost('action'), '_aspect')) {
            return $this->managePersonalityAspect((string) $this->request->getPost('action'));
        }

        $selected = [
            'kelas_id' => $this->getPositiveInt($this->request->getPost('kelas_id')),
            'tahun_ajaran_id' => $this->getPositiveInt($this->request->getPost('tahun_ajaran_id')),
            'siswa_id' => $this->getPositiveInt($this->request->getPost('siswa_id')),
        ];
        $redirect = redirect()->to(site_url(
            'academic-assessments/' . $type . '?' . http_build_query($selected)
        ));

        if ($type === 'kokurikuler' && ! $this->db->tableExists('academic_student_kokurikuler')) {
            return $redirect->with(
                'assessmentError',
                'Tabel nilai Kokurikuler belum tersedia. Jalankan create_kokurikuler_assessments.sql di phpMyAdmin, lalu coba kembali.'
            );
        }

        if (in_array(null, $selected, true)
            || $this->db->table('master_kelas')->where('kelas_id', $selected['kelas_id'])->countAllResults() !== 1
            || $this->db->table('master_tahun_ajaran')->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])->countAllResults() !== 1) {
            return redirect()->to(site_url('academic-assessments/' . $type))
                ->with('assessmentError', 'Pilih kelas, tahun ajaran, dan siswa yang valid.');
        }

        $student = $this->db->table('master_siswa')
            ->select('siswa_id')
            ->where('siswa_id', $selected['siswa_id'])
            ->where('kelas_id', $selected['kelas_id'])
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status', '')
            ->groupEnd()
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        if ($student === null) {
            return $redirect->with('assessmentError', 'Siswa tidak ditemukan pada kelas yang dipilih atau sudah tidak aktif.');
        }

        $now = date('Y-m-d H:i:s');
        $userId = (int) (session()->get('authUser')['user_id'] ?? 0);
        $this->db->transBegin();
        try {
            if ($type === 'personality') {
                $submittedGrades = $this->request->getPost('nilai_aspek');
                if (! is_array($submittedGrades) || count($submittedGrades) > 100) {
                    return $this->rollbackWithError($redirect, 'Data nilai kepribadian tidak valid.');
                }
                $activeAspects = $this->db->table('academic_personality_aspects')
                    ->select('aspek_id, nama_aspek')
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                $activeAspectIds = array_map('intval', array_column($activeAspects, 'aspek_id'));
                $grades = [];
                foreach ($submittedGrades as $aspectId => $grade) {
                    $id = $this->getPositiveInt((string) $aspectId);
                    if ($id === null || ! in_array($id, $activeAspectIds, true) || ! is_string($grade)) {
                        return $this->rollbackWithError($redirect, 'Daftar aspek kepribadian telah berubah. Muat ulang halaman dan coba kembali.');
                    }
                    $grade = strtoupper(trim($grade));
                    if ($grade !== '' && ! in_array($grade, self::VALUES, true)) {
                        return $this->rollbackWithError($redirect, 'Nilai kepribadian harus berupa A, B, C, atau D.');
                    }
                    $grades[$id] = $grade !== '' ? $grade : null;
                }

                foreach ($activeAspects as $aspect) {
                    $aspectId = (int) $aspect['aspek_id'];
                    $existing = $this->db->table('academic_student_personality_scores')
                        ->select('nilai_id')
                        ->where('siswa_id', $selected['siswa_id'])
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->where('aspek_id', $aspectId)
                        ->get()
                        ->getRowArray();
                    $data = [
                        ...$selected,
                        'aspek_id' => $aspectId,
                        'nilai' => $grades[$aspectId] ?? null,
                        'dicatat_oleh' => $userId,
                        'updated_at' => $now,
                    ];
                    if ($existing === null) {
                        $data['created_at'] = $now;
                        $saved = $this->db->table('academic_student_personality_scores')->insert($data);
                    } else {
                        $saved = $this->db->table('academic_student_personality_scores')
                            ->where('nilai_id', $existing['nilai_id'])
                            ->update($data);
                    }
                    if (! $saved) {
                        throw new DatabaseException('Saving personality assessment returned false.');
                    }
                }
            } else {
                $isKokurikuler = $type === 'kokurikuler';
                $assessmentTable = $isKokurikuler ? 'academic_student_kokurikuler' : 'academic_student_extracurricular';
                $masterTable = $isKokurikuler ? 'master_kokurikuler' : 'master_ekstrakurikuler';
                $activityLabel = $isKokurikuler ? 'kokurikuler' : 'ekstrakurikuler';
                $activities = $this->request->getPost('kegiatan');
                $grades = $this->request->getPost('nilai');
                if (! is_array($activities) || ! is_array($grades) || count($activities) !== count($grades) || count($activities) > 50) {
                    return $this->rollbackWithError($redirect, 'Daftar kegiatan ' . $activityLabel . ' tidak valid.');
                }

                $rows = [];
                $seenActivities = [];
                $allowedActivities = [];
                if ($this->db->tableExists($masterTable)) {
                    $masterActivities = $this->db->table($masterTable)
                        ->select('nama_kegiatan')
                        ->where('is_active', 1)
                        ->get()
                        ->getResultArray();
                    $allowedActivities = array_column($masterActivities, 'nama_kegiatan');
                }
                $existingActivities = $this->db->table($assessmentTable)
                    ->select('nama_kegiatan')
                    ->distinct()
                    ->where('kelas_id', $selected['kelas_id'])
                    ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                    ->get()
                    ->getResultArray();
                $allowedActivities = array_values(array_unique(array_filter(array_map(
                    static fn ($name): string => trim((string) $name),
                    array_merge(
                        $allowedActivities,
                        array_column($existingActivities, 'nama_kegiatan')
                    )
                ))));

                foreach ($activities as $index => $activity) {
                    if (! is_string($activity) || ! is_string($grades[$index] ?? null)) {
                        return $this->rollbackWithError($redirect, 'Data kegiatan ' . $activityLabel . ' tidak valid.');
                    }
                    $activity = trim($activity);
                    $grade = strtoupper(trim($grades[$index]));
                    if ($activity === '' && $grade === '') {
                        continue;
                    }
                    if ($activity === '' || mb_strlen($activity) > 100) {
                        return $this->rollbackWithError($redirect, 'Nama kegiatan wajib diisi dan maksimal 100 karakter.');
                    }
                    if (! in_array($activity, $allowedActivities, true)) {
                        return $this->rollbackWithError($redirect, 'Pilih kegiatan dari daftar ' . ucfirst($activityLabel) . ' yang tersedia.');
                    }
                    if ($grade !== '' && ! in_array($grade, self::VALUES, true)) {
                        return $this->rollbackWithError($redirect, 'Nilai kegiatan harus berupa A, B, C, atau D.');
                    }
                    $normalizedActivity = mb_strtolower($activity);
                    if (isset($seenActivities[$normalizedActivity])) {
                        return $this->rollbackWithError($redirect, 'Nama kegiatan tidak boleh duplikat.');
                    }
                    $seenActivities[$normalizedActivity] = true;
                    $rows[] = [
                        ...$selected,
                        'nama_kegiatan' => $activity,
                        'nilai' => $grade !== '' ? $grade : null,
                        'dicatat_oleh' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $deleted = $this->db->table($assessmentTable)
                    ->where('siswa_id', $selected['siswa_id'])
                    ->where('kelas_id', $selected['kelas_id'])
                    ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                    ->delete();
                if (! $deleted && $this->db->transStatus() === false) {
                    throw new DatabaseException('Replacing extracurricular assessment failed.');
                }
                foreach ($rows as $row) {
                    if (! $this->db->table($assessmentTable)->insert($row)) {
                        throw new DatabaseException('Saving ' . $activityLabel . ' assessment returned false.');
                    }
                }
            }

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Student assessment transaction failed.');
            }
            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            log_message('error', 'Failed to save student {type} assessment: {message}', [
                'type' => $type,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('assessmentError', 'Penilaian gagal disimpan. Silakan coba kembali.');
        }

        return $redirect->with(
            'assessmentMessage',
            $type === 'personality'
                ? 'Penilaian kepribadian berhasil disimpan.'
                : 'Penilaian ' . ($type === 'kokurikuler' ? 'kokurikuler' : 'ekstrakurikuler') . ' berhasil disimpan.'
        );
    }

    private function managePersonalityAspect(string $action): RedirectResponse
    {
        $selected = [
            'kelas_id' => $this->getPositiveInt($this->request->getPost('kelas_id')),
            'tahun_ajaran_id' => $this->getPositiveInt($this->request->getPost('tahun_ajaran_id')),
            'siswa_id' => $this->getPositiveInt($this->request->getPost('siswa_id')),
        ];
        $query = http_build_query(array_filter(
            $selected,
            static fn ($value): bool => $value !== null
        ));
        $redirect = redirect()->to(site_url(
            'academic-assessments/personality' . ($query !== '' ? '?' . $query : '')
        ));
        $aspectId = $this->getPositiveInt($this->request->getPost('aspek_id'));
        $name = trim((string) $this->request->getPost('nama_aspek'));

        if (! in_array($action, ['add_aspect', 'update_aspect', 'delete_aspect'], true)) {
            return $redirect->with('assessmentError', 'Aksi aspek kepribadian tidak valid.');
        }
        if ($action !== 'delete_aspect' && ($name === '' || mb_strlen($name) > 100)) {
            return $redirect->with('assessmentError', 'Nama aspek wajib diisi dan maksimal 100 karakter.');
        }
        if ($action !== 'add_aspect' && $aspectId === null) {
            return $redirect->with('assessmentError', 'Aspek kepribadian yang dipilih tidak valid.');
        }

        if ($action !== 'add_aspect') {
            $existing = $this->db->table('academic_personality_aspects')
                ->where('aspek_id', $aspectId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            if ($existing === null) {
                return $redirect->with('assessmentError', 'Aspek kepribadian tidak ditemukan atau sudah dihapus.');
            }
        }

        if ($action === 'delete_aspect') {
            $archived = $this->db->table('academic_personality_aspects')
                ->where('aspek_id', $aspectId)
                ->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
            if (! $archived) {
                return $redirect->with('assessmentError', 'Aspek kepribadian gagal dihapus.');
            }

            return $redirect->with('assessmentMessage', 'Aspek kepribadian dihapus dari pilihan baru. Nilai raport sebelumnya tetap tersimpan.');
        }

        $duplicate = $this->db->table('academic_personality_aspects')
            ->where('nama_aspek', $name)
            ->where('aspek_id !=', $aspectId ?? 0)
            ->countAllResults();
        if ($duplicate > 0) {
            return $redirect->with('assessmentError', 'Nama aspek kepribadian sudah digunakan.');
        }

        $data = [
            'nama_aspek' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($action === 'add_aspect') {
            $data += [
                'is_active' => 1,
                'urutan' => (int) $this->db->table('academic_personality_aspects')->countAllResults() + 1,
                'dicatat_oleh' => (int) (session()->get('authUser')['user_id'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $saved = $this->db->table('academic_personality_aspects')->insert($data);
        } else {
            $saved = $this->db->table('academic_personality_aspects')
                ->where('aspek_id', $aspectId)
                ->update($data);
        }

        if (! $saved) {
            log_message('error', 'Failed to {action} personality aspect {aspectId}.', [
                'action' => $action,
                'aspectId' => $aspectId,
            ]);

            return $redirect->with('assessmentError', 'Aspek kepribadian gagal disimpan.');
        }

        return $redirect->with(
            'assessmentMessage',
            $action === 'add_aspect' ? 'Aspek kepribadian berhasil ditambahkan.' : 'Aspek kepribadian berhasil diperbarui.'
        );
    }

    private function rollbackWithError(RedirectResponse $redirect, string $message): RedirectResponse
    {
        $this->db->transRollback();

        return $redirect->with('assessmentError', $message);
    }

    private function getPositiveInt($value): ?int
    {
        if (! is_string($value) && ! is_int($value)) {
            return null;
        }
        $id = filter_var($value, FILTER_VALIDATE_INT);

        return $id !== false && $id > 0 ? $id : null;
    }
}
