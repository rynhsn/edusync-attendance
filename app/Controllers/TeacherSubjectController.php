<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

class TeacherSubjectController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $teachers = $db->table('master_guru')
            ->select('guru_id, guru_nip, guru_nama')
            ->orderBy('guru_nama')
            ->get()
            ->getResultArray();
        $subjects = $db->table('master_mapel')
            ->select('mapel_id, mapel_kode, mapel_nama')
            ->orderBy('mapel_nama')
            ->get()
            ->getResultArray();

        $requestedValue = $this->request->getGet('guru_id');
        $requestedId = is_string($requestedValue)
            ? filter_var($requestedValue, FILTER_VALIDATE_INT)
            : false;
        $selectedTeacherId = $requestedId !== false && $requestedId !== null
            ? (int) $requestedId
            : 0;
        $validTeacherIds = array_map(static fn (array $teacher): int => (int) $teacher['guru_id'], $teachers);
        if (! in_array($selectedTeacherId, $validTeacherIds, true)) {
            $selectedTeacherId = $validTeacherIds[0] ?? 0;
        }

        $assignedSubjectIds = $selectedTeacherId > 0
            ? array_map(
                'intval',
                array_column(
                    $db->table('teacher_mapel')->select('mapel_id')->where('guru_id', $selectedTeacherId)->get()->getResultArray(),
                    'mapel_id'
                )
            )
            : [];

        return view('panel/teacher-subjects', [
            'title' => 'Pengaturan Mata Pelajaran Guru',
            'activePage' => 'teacher-subjects',
            'teachers' => $teachers,
            'subjects' => $subjects,
            'selectedTeacherId' => $selectedTeacherId,
            'assignedSubjectIds' => $assignedSubjectIds,
            'message' => session()->getFlashdata('teacherAccessMessage'),
            'error' => session()->getFlashdata('teacherAccessError'),
        ]);
    }

    public function save()
    {
        $teacherValue = $this->request->getPost('guru_id');
        $teacherId = is_string($teacherValue)
            ? filter_var($teacherValue, FILTER_VALIDATE_INT)
            : false;
        if ($teacherId === false || $teacherId === null || $teacherId < 1) {
            return redirect()->to(site_url('teacher-subjects'))
                ->with('teacherAccessError', 'Data guru yang dipilih tidak valid.');
        }
        $redirect = redirect()->to(site_url('teacher-subjects?guru_id=' . $teacherId));
        $db = db_connect();

        if ($db->table('master_guru')->where('guru_id', $teacherId)->countAllResults() !== 1) {
            return $redirect->with('teacherAccessError', 'Data guru tidak ditemukan.');
        }

        $submitted = $this->request->getPost('subjects');
        if ($submitted !== null && ! is_array($submitted)) {
            return $redirect->with('teacherAccessError', 'Daftar mata pelajaran tidak valid.');
        }
        $submitted ??= [];
        $subjectIds = [];
        foreach ($submitted as $subjectId) {
            if (! is_string($subjectId) || filter_var($subjectId, FILTER_VALIDATE_INT) === false || (int) $subjectId < 1) {
                return $redirect->with('teacherAccessError', 'Pilihan mata pelajaran tidak valid.');
            }
            $subjectIds[] = (int) $subjectId;
        }
        if (count($subjectIds) !== count(array_unique($subjectIds))) {
            return $redirect->with('teacherAccessError', 'Daftar mata pelajaran berisi pilihan ganda.');
        }
        if ($subjectIds !== []) {
            $existingSubjectIds = array_map(
                'intval',
                array_column(
                    $db->table('master_mapel')->select('mapel_id')->whereIn('mapel_id', $subjectIds)->get()->getResultArray(),
                    'mapel_id'
                )
            );
            sort($existingSubjectIds);
            $submittedIds = $subjectIds;
            sort($submittedIds);
            if ($existingSubjectIds !== $submittedIds) {
                return $redirect->with('teacherAccessError', 'Satu atau lebih mata pelajaran tidak ditemukan.');
            }
        }

        $db->transBegin();
        try {
            if ($db->table('teacher_mapel')->where('guru_id', $teacherId)->delete() === false) {
                throw new DatabaseException('Could not remove existing teacher subject assignments.');
            }
            if ($subjectIds !== []) {
                $rows = array_map(
                    static fn (int $subjectId): array => ['guru_id' => $teacherId, 'mapel_id' => $subjectId],
                    $subjectIds
                );
                if (! $db->table('teacher_mapel')->insertBatch($rows)) {
                    throw new DatabaseException('Could not save teacher subject assignments.');
                }
            }
            if ($db->transStatus() === false) {
                throw new DatabaseException('Teacher subject assignment transaction failed.');
            }
            $db->transCommit();
        } catch (DatabaseException $exception) {
            $db->transRollback();
            log_message('error', 'Failed to save subject assignments for teacher {id}: {message}', [
                'id' => $teacherId,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('teacherAccessError', 'Mata pelajaran guru gagal disimpan. Silakan coba kembali.');
        }

        return $redirect->with('teacherAccessMessage', 'Mata pelajaran guru berhasil disimpan.');
    }
}
