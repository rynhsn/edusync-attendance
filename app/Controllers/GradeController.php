<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;

class GradeController extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $hasKnowledgeGrade = $this->db->fieldExists('nilai_pengetahuan', 'academic_student_grades');
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
        $subjects = $this->db->table('master_mapel')
            ->select('mapel_id, mapel_kode, mapel_nama')
            ->orderBy('mapel_nama')
            ->get()
            ->getResultArray();
        $teachers = $this->db->table('master_guru')
            ->select('guru_id, guru_nip, guru_nama')
            ->orderBy('guru_nama')
            ->get()
            ->getResultArray();

        $assignments = $this->db->table('teacher_mapel')
            ->select('guru_id, mapel_id')
            ->get()
            ->getResultArray();
        $teachersBySubject = [];
        foreach ($assignments as $assignment) {
            $teachersBySubject[(int) $assignment['mapel_id']][] = (int) $assignment['guru_id'];
        }

        $selected = [
            'kelas_id' => $this->getSelectedId('kelas_id'),
            'tahun_ajaran_id' => $this->getSelectedId('tahun_ajaran_id'),
            'mapel_id' => $this->getSelectedId('mapel_id'),
            'guru_id' => $this->getSelectedId('guru_id'),
        ];
        $studentRows = [];
        $selectionError = null;

        if (! in_array(null, $selected, true)) {
            $selectionError = $this->validateSelection($selected);
        }

        if ($selectionError === null && ! in_array(null, $selected, true)) {
            $studentRows = $this->db->table('master_siswa ms')
                ->select(
                    'ms.siswa_id, ms.nama_siswa, ms.nis, ag.nilai_uts, ag.nilai_uas, '
                        . ($hasKnowledgeGrade ? 'ag.nilai_pengetahuan' : 'NULL AS nilai_pengetahuan')
                        . ', ag.nilai_praktik',
                    false
                )
                ->join(
                    'academic_student_grades ag',
                    'ag.siswa_id = ms.siswa_id'
                        . ' AND ag.kelas_id = ' . (int) $selected['kelas_id']
                        . ' AND ag.mapel_id = ' . (int) $selected['mapel_id']
                        . ' AND ag.guru_id = ' . (int) $selected['guru_id']
                        . ' AND ag.tahun_ajaran_id = ' . (int) $selected['tahun_ajaran_id'],
                    'left'
                )
                ->where('ms.kelas_id', $selected['kelas_id'])
                ->where('ms.status', 'aktif')
                ->where('ms.deleted_at', null)
                ->orderBy('ms.nama_siswa')
                ->get()
                ->getResultArray();
        }

        return view('panel/grades', [
            'title' => 'Input Nilai Siswa',
            'activePage' => 'grades',
            'classes' => $classes,
            'years' => $years,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'teachersBySubject' => $teachersBySubject,
            'selected' => $selected,
            'studentRows' => $studentRows,
            'hasKnowledgeGrade' => $hasKnowledgeGrade,
            'selectionError' => $selectionError,
            'message' => session()->getFlashdata('gradeMessage'),
            'error' => session()->getFlashdata('gradeError'),
        ]);
    }

    public function student(int $studentId)
    {
        $selected = [
            'kelas_id' => $this->getSelectedId('kelas_id'),
            'tahun_ajaran_id' => $this->getSelectedId('tahun_ajaran_id'),
            'mapel_id' => $this->getSelectedId('mapel_id'),
            'guru_id' => $this->getSelectedId('guru_id'),
        ];
        if ($studentId < 1 || in_array(null, $selected, true)) {
            return redirect()->to(site_url('grades'))
                ->with('gradeError', 'Pilih kelas, tahun ajaran, mapel, dan guru terlebih dahulu.');
        }

        $selectionError = $this->validateSelection($selected);
        if ($selectionError !== null) {
            return redirect()->to(site_url('grades'))
                ->with('gradeError', $selectionError);
        }

        $student = $this->db->table('master_siswa ms')
            ->select('ms.siswa_id, ms.nama_siswa, ms.nis, mk.kelas_tingkat, mk.kelas_nama')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id')
            ->where('ms.siswa_id', $studentId)
            ->where('ms.kelas_id', $selected['kelas_id'])
            ->where('ms.status', 'aktif')
            ->where('ms.deleted_at', null)
            ->get()
            ->getRowArray();
        if ($student === null) {
            return redirect()->to(site_url('grades?' . http_build_query($selected)))
                ->with('gradeError', 'Siswa tidak ditemukan pada kelas yang dipilih.');
        }

        $hasKnowledgeGrade = $this->db->fieldExists('nilai_pengetahuan', 'academic_student_grades');
        $grade = $this->db->table('academic_student_grades')
            ->select(
                'nilai_uts, nilai_uas, ' . ($hasKnowledgeGrade ? 'nilai_pengetahuan' : 'NULL AS nilai_pengetahuan')
                    . ', nilai_praktik, created_at, updated_at',
                false
            )
            ->where('siswa_id', $studentId)
            ->where('kelas_id', $selected['kelas_id'])
            ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
            ->where('mapel_id', $selected['mapel_id'])
            ->where('guru_id', $selected['guru_id'])
            ->get()
            ->getRowArray();
        $context = $this->db->table('master_mapel mm')
            ->select('mm.mapel_nama, mg.guru_nama, mta.tahun_ajaran_nama, mta.semester')
            ->join('teacher_mapel tm', 'tm.mapel_id = mm.mapel_id AND tm.guru_id = ' . (int) $selected['guru_id'])
            ->join('master_guru mg', 'mg.guru_id = tm.guru_id')
            ->join('master_tahun_ajaran mta', 'mta.tahun_ajaran_id = ' . (int) $selected['tahun_ajaran_id'])
            ->where('mm.mapel_id', $selected['mapel_id'])
            ->where('mta.tahun_ajaran_id', $selected['tahun_ajaran_id'])
            ->get()
            ->getRowArray();

        return view('panel/grade-student-detail', [
            'title' => 'Detail Nilai Siswa',
            'activePage' => 'grades',
            'student' => $student,
            'grade' => $grade,
            'context' => $context,
            'hasKnowledgeGrade' => $hasKnowledgeGrade,
            'backUrl' => site_url('grades?' . http_build_query($selected)),
        ]);
    }

    public function save(): RedirectResponse
    {
        $selected = [
            'kelas_id' => $this->postSelectedId('kelas_id'),
            'tahun_ajaran_id' => $this->postSelectedId('tahun_ajaran_id'),
            'mapel_id' => $this->postSelectedId('mapel_id'),
            'guru_id' => $this->postSelectedId('guru_id'),
        ];
        $redirect = redirect()->to(site_url('grades?' . http_build_query($selected)));
        if (in_array(null, $selected, true)) {
            return redirect()->to(site_url('grades'))->with('gradeError', 'Pilih kelas, tahun ajaran, mata pelajaran, dan guru yang valid.');
        }

        $selectionError = $this->validateSelection($selected);
        if ($selectionError !== null) {
            return $redirect->with('gradeError', $selectionError);
        }

        $students = $this->db->table('master_siswa')
            ->select('siswa_id')
            ->where('kelas_id', $selected['kelas_id'])
            ->where('status', 'aktif')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
        if ($students === []) {
            return $redirect->with('gradeError', 'Kelas yang dipilih belum memiliki siswa aktif.');
        }

        $submitted = $this->request->getPost('nilai');
        if (! is_array($submitted)) {
            return $redirect->with('gradeError', 'Daftar nilai siswa tidak valid.');
        }
        $hasKnowledgeGrade = $this->db->fieldExists('nilai_pengetahuan', 'academic_student_grades');
        if (! $hasKnowledgeGrade) {
            foreach ($submitted as $scoreSet) {
                if (is_array($scoreSet) && trim((string) ($scoreSet['pengetahuan'] ?? '')) !== '') {
                    return $redirect->with(
                        'gradeError',
                        'Nilai Pengetahuan belum dapat disimpan karena migrasi database belum diterapkan. Hubungi administrator database.'
                    );
                }
            }
        }
        $studentIds = array_map(static fn (array $student): string => (string) $student['siswa_id'], $students);
        $submittedIds = array_map('strval', array_keys($submitted));
        sort($studentIds);
        sort($submittedIds);
        if ($studentIds !== $submittedIds) {
            return $redirect->with('gradeError', 'Daftar siswa yang dikirim tidak sesuai dengan siswa aktif di kelas.');
        }

        $rows = [];
        foreach ($students as $student) {
            $studentId = (string) $student['siswa_id'];
            $scoreSet = $submitted[$studentId] ?? null;
            if (! is_array($scoreSet) || array_diff(array_keys($scoreSet), ['uts', 'uas', 'pengetahuan', 'praktik']) !== []) {
                return $redirect->with('gradeError', 'Format nilai siswa tidak valid.');
            }

            $scores = [];
            foreach (['uts', 'uas', 'pengetahuan', 'praktik'] as $component) {
                $value = $scoreSet[$component] ?? '';
                if (! is_string($value) && ! is_numeric($value)) {
                    return $redirect->with('gradeError', 'Format nilai harus berupa angka.');
                }
                $value = trim((string) $value);
                if ($value === '') {
                    if ($component === 'pengetahuan' && ! $hasKnowledgeGrade) {
                        continue;
                    }
                    $scores['nilai_' . $component] = null;
                    continue;
                }
                if (! is_numeric($value) || (float) $value < 0 || (float) $value > 100) {
                    return $redirect->with('gradeError', 'Nilai UTS, UAS, Pengetahuan, dan Praktik harus berada pada rentang 0 sampai 100.');
                }
                $scores['nilai_' . $component] = number_format((float) $value, 2, '.', '');
            }

            $rows[] = [
                'siswa_id' => (int) $student['siswa_id'],
                'kelas_id' => $selected['kelas_id'],
                'mapel_id' => $selected['mapel_id'],
                'guru_id' => $selected['guru_id'],
                'tahun_ajaran_id' => $selected['tahun_ajaran_id'],
                ...$scores,
                'dicatat_oleh' => (int) (session()->get('authUser')['user_id'] ?? 0),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->transBegin();
        try {
            foreach ($rows as $row) {
                $existing = $this->db->table('academic_student_grades')
                    ->select('nilai_id')
                    ->where('siswa_id', $row['siswa_id'])
                    ->where('kelas_id', $row['kelas_id'])
                    ->where('mapel_id', $row['mapel_id'])
                    ->where('guru_id', $row['guru_id'])
                    ->where('tahun_ajaran_id', $row['tahun_ajaran_id'])
                    ->get()
                    ->getRowArray();
                if ($existing === null) {
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $saved = $this->db->table('academic_student_grades')->insert($row);
                } else {
                    $saved = $this->db->table('academic_student_grades')
                        ->where('nilai_id', $existing['nilai_id'])
                        ->update($row);
                }
                if (! $saved) {
                    throw new DatabaseException('Could not save student grade.');
                }
            }
            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Student grade transaction failed.');
            }
            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            log_message('error', 'Failed to save student grades: {message}', ['message' => $exception->getMessage()]);

            return $redirect->with('gradeError', 'Nilai siswa gagal disimpan. Silakan coba kembali.');
        }

        return $redirect->with('gradeMessage', 'Nilai siswa berhasil disimpan.');
    }

    private function validateSelection(array $selected): ?string
    {
        foreach ([
            ['table' => 'master_kelas', 'key' => 'kelas_id', 'id' => $selected['kelas_id']],
            ['table' => 'master_tahun_ajaran', 'key' => 'tahun_ajaran_id', 'id' => $selected['tahun_ajaran_id']],
            ['table' => 'master_mapel', 'key' => 'mapel_id', 'id' => $selected['mapel_id']],
            ['table' => 'master_guru', 'key' => 'guru_id', 'id' => $selected['guru_id']],
        ] as $item) {
            if ($item['id'] === null || $this->db->table($item['table'])->where($item['key'], $item['id'])->countAllResults() !== 1) {
                return 'Salah satu pilihan kelas, tahun ajaran, mapel, atau guru tidak ditemukan.';
            }
        }

        if ($this->db->table('teacher_mapel')
            ->where('guru_id', $selected['guru_id'])
            ->where('mapel_id', $selected['mapel_id'])
            ->countAllResults() !== 1) {
            return 'Guru tersebut belum ditetapkan untuk mata pelajaran yang dipilih.';
        }

        return null;
    }

    private function getSelectedId(string $key): ?int
    {
        $value = $this->request->getGet($key);
        if (! is_string($value) || $value === '') {
            return null;
        }

        $id = filter_var($value, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? $id : null;
    }

    private function postSelectedId(string $key): ?int
    {
        $value = $this->request->getPost($key);
        if (! is_string($value) || $value === '') {
            return null;
        }

        $id = filter_var($value, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? $id : null;
    }
}
