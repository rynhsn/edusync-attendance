<?php

namespace App\Controllers;

use App\Models\CbtUjianModel;
use App\Models\CbtSoalModel;
use App\Models\CbtPesertaUjianModel;
use App\Models\CbtJawabanSiswaModel;
use App\Libraries\StudentSpreadsheet;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

class OnlineAssessmentController extends BaseController
{
    protected $helpers = ['url', 'form'];
    protected $db;
    protected $ujianModel;
    protected $soalModel;
    protected $pesertaModel;
    protected $jawabanModel;

    public function __construct()
    {
        $this->db           = db_connect();
        $this->ujianModel   = new CbtUjianModel();
        $this->soalModel    = new CbtSoalModel();
        $this->pesertaModel = new CbtPesertaUjianModel();
        $this->jawabanModel = new CbtJawabanSiswaModel();
    }

    protected function getAuthUser(): array
    {
        $authUser = session()->get('authUser');
        return is_array($authUser) ? $authUser : [];
    }

    protected function isSuperAdmin(array $authUser): bool
    {
        return ($authUser['role'] ?? '') === 'super_admin';
    }

    protected function resolveGuruId(array $authUser): ?int
    {
        $guruId = ! empty($authUser['guru_id']) ? (int) $authUser['guru_id'] : null;
        if ($guruId !== null && $guruId > 0) {
            return $guruId;
        }

        $userId = (int) ($authUser['user_id'] ?? 0);
        if ($userId > 0) {
            $syncedId = \App\Libraries\UserTeacherSync::syncUserToTeacher($userId);
            if ($syncedId) {
                $authUser['guru_id'] = $syncedId;
                session()->set('authUser', $authUser);
                return $syncedId;
            }
        }

        return null;
    }

    protected function canAccessExam(array $ujian, array $authUser): bool
    {
        if ($this->isSuperAdmin($authUser)) {
            return true;
        }

        $userId = (int) ($authUser['user_id'] ?? 0);
        $guruId = $this->resolveGuruId($authUser) ?? 0;

        $examCreator = ! empty($ujian['created_by']) ? (int) $ujian['created_by'] : 0;
        $examGuru    = ! empty($ujian['guru_id']) ? (int) $ujian['guru_id'] : 0;

        if ($userId > 0 && $examCreator === $userId) {
            return true;
        }

        if ($guruId > 0 && $examGuru === $guruId) {
            return true;
        }

        return false;
    }

    public function index()
    {
        $authUser = $this->getAuthUser();
        $isAdmin  = $this->isSuperAdmin($authUser);
        $userId   = (int) ($authUser['user_id'] ?? 0);
        $guruId   = $this->resolveGuruId($authUser);

        $academicYears = $this->db->table('master_tahun_ajaran')
            ->orderBy('is_active', 'DESC')
            ->orderBy('tahun_ajaran_nama', 'DESC')
            ->orderBy('semester', 'ASC')
            ->get()->getResultArray();

        $activeYear = null;
        foreach ($academicYears as $y) {
            if ((int) $y['is_active'] === 1) {
                $activeYear = $y;
                break;
            }
        }

        $filterTahunAjaran = $this->request->getGet('tahun_ajaran_id');
        $selectedYearFilter = $filterTahunAjaran !== null ? $filterTahunAjaran : 'active';

        if ($selectedYearFilter === 'active' || $selectedYearFilter === '') {
            $filterTahunAjaranId = null;
            $onlyActiveYear      = true;
        } elseif ($selectedYearFilter === 'all') {
            $filterTahunAjaranId = null;
            $onlyActiveYear      = false;
        } else {
            $filterTahunAjaranId = (int) $selectedYearFilter;
            $onlyActiveYear      = false;
        }

        // Hanya tampilkan ujian yang dibuat oleh user sendiri kecuali admin dapat melihat semuanya
        $ujians = $this->ujianModel->getWithRelations(null, $userId, $guruId, $isAdmin, $filterTahunAjaranId, $onlyActiveYear);

        // Ambil statistik per ujian: jumlah soal PG, Essay, dan jumlah peserta
        foreach ($ujians as &$u) {
            $ujianId = (int) $u['ujian_id'];
            $u['total_soal']  = $this->soalModel->where('ujian_id', $ujianId)->countAllResults();
            $u['soal_pg']     = $this->soalModel->where('ujian_id', $ujianId)->where('tipe_soal', 'pilihan_ganda')->countAllResults();
            $u['soal_essay']  = $this->soalModel->where('ujian_id', $ujianId)->where('tipe_soal', 'essay')->countAllResults();
            $u['total_peserta'] = $this->pesertaModel->where('ujian_id', $ujianId)->countAllResults();
            $u['peserta_selesai'] = $this->pesertaModel->where('ujian_id', $ujianId)->where('status_pengerjaan', 'selesai')->countAllResults();
        }
        unset($u);

        $classes  = $this->db->table('master_kelas')->orderBy('kelas_tingkat')->orderBy('kelas_nama')->get()->getResultArray();
        $teachers = $this->db->table('master_guru')->orderBy('guru_nama')->get()->getResultArray();

        // Data mapel berdasarkan penugasan di teacher_mapel (Gambar 2)
        $allMapels = $this->db->table('master_mapel')->orderBy('mapel_nama')->get()->getResultArray();

        // Mapping penugasan mapel guru untuk Admin
        $teacherMapelAssignments = $this->db->table('teacher_mapel')->select('guru_id, mapel_id')->get()->getResultArray();
        $teacherMapelMap = [];
        foreach ($teacherMapelAssignments as $row) {
            $g = (int) $row['guru_id'];
            $m = (int) $row['mapel_id'];
            if (! isset($teacherMapelMap[$g])) {
                $teacherMapelMap[$g] = [];
            }
            $teacherMapelMap[$g][] = $m;
        }

        // Jika user bukan admin (misal guru), saring mapels hanya yang ditugaskan ke guru ini
        $currentTeacher = null;
        if ($guruId) {
            foreach ($teachers as $t) {
                if ((int) $t['guru_id'] === $guruId) {
                    $currentTeacher = $t;
                    break;
                }
            }
        }

        if (! $isAdmin) {
            $assignedMapelIds = $guruId && isset($teacherMapelMap[$guruId]) ? $teacherMapelMap[$guruId] : [];
            $mapels = array_values(array_filter($allMapels, fn($m) => in_array((int) $m['mapel_id'], $assignedMapelIds, true)));
        } else {
            $mapels = $allMapels;
        }

        return view('panel/online-assessments/index', [
            'title'              => 'Ujian Online (CBT)',
            'activePage'         => 'online-assessments',
            'ujians'             => $ujians,
            'academicYears'      => $academicYears,
            'activeYear'         => $activeYear,
            'selectedYearFilter' => $selectedYearFilter,
            'mapels'             => $mapels,
            'allMapels'          => $allMapels,
            'classes'            => $classes,
            'teachers'           => $teachers,
            'isAdmin'            => $isAdmin,
            'currentGuruId'      => $guruId,
            'currentTeacher'     => $currentTeacher,
            'teacherMapelMap'    => $teacherMapelMap,
            'message'            => session()->getFlashdata('cbtMessage'),
            'error'              => session()->getFlashdata('cbtError'),
        ]);
    }

    public function create()
    {
        $authUser = $this->getAuthUser();
        $isAdmin  = $this->isSuperAdmin($authUser);
        $userId   = (int) ($authUser['user_id'] ?? 0);
        $myGuruId = $this->resolveGuruId($authUser);

        $judul = trim((string) $this->request->getPost('judul'));
        if ($judul === '') {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Judul ujian wajib diisi.');
        }

        $tahunAjaranId = ! empty($this->request->getPost('tahun_ajaran_id')) ? (int) $this->request->getPost('tahun_ajaran_id') : null;
        if (! $tahunAjaranId) {
            $activeYear = $this->db->table('master_tahun_ajaran')->where('is_active', 1)->get()->getRowArray();
            $tahunAjaranId = $activeYear ? (int) $activeYear['tahun_ajaran_id'] : null;
        }

        $durasi = max(1, (int) $this->request->getPost('durasi_menit'));
        $mapelId = ! empty($this->request->getPost('mapel_id')) ? (int) $this->request->getPost('mapel_id') : null;
        $kelasId = ! empty($this->request->getPost('kelas_id')) ? (int) $this->request->getPost('kelas_id') : null;

        // Guru Pengampu: Jika bukan admin, kunci ke guru_id milik user sendiri
        if (! $isAdmin) {
            $guruId = $myGuruId;
            // Validasi: pastikan mapel_id yang dipilih termasuk dalam penugasan teacher_mapel untuk guru ini
            if ($mapelId !== null && $guruId !== null) {
                $isAssigned = $this->db->table('teacher_mapel')
                    ->where('guru_id', $guruId)
                    ->where('mapel_id', $mapelId)
                    ->countAllResults() > 0;
                if (! $isAssigned) {
                    return redirect()->to(site_url('online-assessments'))
                        ->with('cbtError', 'Mata pelajaran yang dipilih tidak sesuai dengan penugasan mapel Anda di Pengaturan Mapel Guru.');
                }
            }
        } else {
            $guruId = ! empty($this->request->getPost('guru_id')) ? (int) $this->request->getPost('guru_id') : null;
        }

        $token = strtoupper(trim((string) $this->request->getPost('token')));
        if ($token === '') {
            $token = $this->generateExamToken();
        }

        $data = [
            'judul'             => $judul,
            'deskripsi'         => trim((string) $this->request->getPost('deskripsi')),
            'tahun_ajaran_id'   => $tahunAjaranId,
            'mapel_id'          => $mapelId,
            'kelas_id'          => $kelasId,
            'guru_id'           => $guruId,
            'created_by'        => $userId > 0 ? $userId : null,
            'durasi_menit'      => $durasi,
            'waktu_mulai'       => ! empty($this->request->getPost('waktu_mulai')) ? $this->request->getPost('waktu_mulai') : null,
            'waktu_selesai'     => ! empty($this->request->getPost('waktu_selesai')) ? $this->request->getPost('waktu_selesai') : null,
            'token'             => $token,
            'petunjuk'          => trim((string) $this->request->getPost('petunjuk')),
            'acak_soal'         => $this->request->getPost('acak_soal') ? 1 : 0,
            'acak_opsi'         => $this->request->getPost('acak_opsi') ? 1 : 0,
            'tampil_nilai'      => $this->request->getPost('tampil_nilai') ? 1 : 0,
            'tampil_pembahasan' => $this->request->getPost('tampil_pembahasan') ? 1 : 0,
            'status'            => (string) ($this->request->getPost('status') ?? 'aktif'),
        ];

        $insertedId = $this->ujianModel->insert($data, true);
        if (! $insertedId) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Gagal membuat ujian online.');
        }

        return redirect()->to(site_url('online-assessments/' . $insertedId . '/questions'))
            ->with('cbtMessage', "Ujian '{$judul}' berhasil dibuat dengan Token Ujian: {$token}. Silakan tambahkan butir soal pilihan ganda atau essay.");
    }

    public function update(int $id)
    {
        $ujian = $this->ujianModel->find($id);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        $isAdmin  = $this->isSuperAdmin($authUser);
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk mengedit ujian ini.');
        }

        $judul = trim((string) $this->request->getPost('judul'));
        if ($judul === '') {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Judul ujian wajib diisi.');
        }

        $tahunAjaranId = ! empty($this->request->getPost('tahun_ajaran_id')) ? (int) $this->request->getPost('tahun_ajaran_id') : null;
        if (! $tahunAjaranId) {
            $activeYear = $this->db->table('master_tahun_ajaran')->where('is_active', 1)->get()->getRowArray();
            $tahunAjaranId = $activeYear ? (int) $activeYear['tahun_ajaran_id'] : null;
        }

        $durasi = max(1, (int) $this->request->getPost('durasi_menit'));
        $mapelId = ! empty($this->request->getPost('mapel_id')) ? (int) $this->request->getPost('mapel_id') : null;
        $kelasId = ! empty($this->request->getPost('kelas_id')) ? (int) $this->request->getPost('kelas_id') : null;

        if (! $isAdmin) {
            $myGuruId = $this->resolveGuruId($authUser);
            $guruId = $myGuruId ?: $ujian['guru_id'];
            if ($mapelId !== null && $guruId !== null) {
                $isAssigned = $this->db->table('teacher_mapel')
                    ->where('guru_id', $guruId)
                    ->where('mapel_id', $mapelId)
                    ->countAllResults() > 0;
                if (! $isAssigned) {
                    return redirect()->to(site_url('online-assessments'))
                        ->with('cbtError', 'Mata pelajaran yang dipilih tidak sesuai dengan penugasan mapel Anda di Pengaturan Mapel Guru.');
                }
            }
        } else {
            $guruId  = ! empty($this->request->getPost('guru_id')) ? (int) $this->request->getPost('guru_id') : null;
        }

        $token = strtoupper(trim((string) $this->request->getPost('token')));
        if ($token === '') {
            $token = $this->generateExamToken();
        }

        $data = [
            'judul'             => $judul,
            'deskripsi'         => trim((string) $this->request->getPost('deskripsi')),
            'tahun_ajaran_id'   => $tahunAjaranId,
            'mapel_id'          => $mapelId,
            'kelas_id'          => $kelasId,
            'guru_id'           => $guruId,
            'durasi_menit'      => $durasi,
            'waktu_mulai'       => ! empty($this->request->getPost('waktu_mulai')) ? $this->request->getPost('waktu_mulai') : null,
            'waktu_selesai'     => ! empty($this->request->getPost('waktu_selesai')) ? $this->request->getPost('waktu_selesai') : null,
            'token'             => $token,
            'petunjuk'          => trim((string) $this->request->getPost('petunjuk')),
            'acak_soal'         => $this->request->getPost('acak_soal') ? 1 : 0,
            'acak_opsi'         => $this->request->getPost('acak_opsi') ? 1 : 0,
            'tampil_nilai'      => $this->request->getPost('tampil_nilai') ? 1 : 0,
            'tampil_pembahasan' => $this->request->getPost('tampil_pembahasan') ? 1 : 0,
            'status'            => (string) ($this->request->getPost('status') ?? 'aktif'),
        ];

        $this->ujianModel->update($id, $data);

        return redirect()->to(site_url('online-assessments'))->with('cbtMessage', "Pengaturan ujian berhasil diperbarui (Token Ujian aktif: {$token}).");
    }

    public function delete(int $id)
    {
        $ujian = $this->ujianModel->find($id);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk menghapus ujian ini.');
        }

        $this->ujianModel->delete($id);

        return redirect()->to(site_url('online-assessments'))->with('cbtMessage', 'Ujian beserta butir soal dan data nilai terkait berhasil dihapus.');
    }

    public function toggleStatus(int $id)
    {
        $ujian = $this->ujianModel->find($id);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk mengubah status ujian ini.');
        }

        $nextStatus = match ($ujian['status']) {
            'aktif'   => 'selesai',
            'selesai' => 'draft',
            default   => 'aktif',
        };

        $this->ujianModel->update($id, ['status' => $nextStatus]);

        return redirect()->to(site_url('online-assessments'))->with('cbtMessage', "Status ujian diubah menjadi '{$nextStatus}'.");
    }

    // Kelola butir soal
    public function questions(int $ujianId)
    {
        $ujian = $this->ujianModel->getWithRelations($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk melihat soal ujian ini.');
        }

        $questions = $this->soalModel->getByUjian($ujianId);

        return view('panel/online-assessments/questions', [
            'title'      => 'Kelola Soal: ' . $ujian['judul'],
            'activePage' => 'online-assessments',
            'ujian'      => $ujian,
            'questions'  => $questions,
            'message'    => session()->getFlashdata('cbtMessage'),
            'error'      => session()->getFlashdata('cbtError'),
        ]);
    }

    public function createQuestion(int $ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk menambah soal pada ujian ini.');
        }

        $tipeSoal   = $this->request->getPost('tipe_soal') === 'essay' ? 'essay' : 'pilihan_ganda';
        $pertanyaan = trim((string) $this->request->getPost('pertanyaan'));

        if ($pertanyaan === '') {
            return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))->with('cbtError', 'Teks pertanyaan wajib diisi.');
        }

        $bobot = max(0.1, (float) ($this->request->getPost('bobot_nilai') ?: 1.00));
        $count = $this->soalModel->where('ujian_id', $ujianId)->countAllResults();

        $data = [
            'ujian_id'     => $ujianId,
            'tipe_soal'    => $tipeSoal,
            'pertanyaan'   => $pertanyaan,
            'bobot_nilai'  => $bobot,
            'urutan'       => $count + 1,
        ];

        if ($tipeSoal === 'pilihan_ganda') {
            $data['opsi_a'] = trim((string) $this->request->getPost('opsi_a'));
            $data['opsi_b'] = trim((string) $this->request->getPost('opsi_b'));
            $data['opsi_c'] = trim((string) $this->request->getPost('opsi_c'));
            $data['opsi_d'] = trim((string) $this->request->getPost('opsi_d'));
            $data['opsi_e'] = trim((string) $this->request->getPost('opsi_e'));

            $kunci = strtoupper(trim((string) $this->request->getPost('kunci_jawaban')));
            if (! in_array($kunci, ['A', 'B', 'C', 'D', 'E'], true)) {
                return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
                    ->with('cbtError', 'Kunci jawaban pilihan ganda harus dipilih (A, B, C, D, atau E).');
            }
            $data['kunci_jawaban'] = $kunci;
        }

        $this->soalModel->insert($data);

        return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
            ->with('cbtMessage', 'Butir soal baru berhasil ditambahkan.');
    }

    public function updateQuestion(int $soalId)
    {
        $soal = $this->soalModel->find($soalId);
        if (! $soal) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Soal tidak ditemukan.');
        }

        $ujianId = (int) $soal['ujian_id'];
        $ujian   = $this->ujianModel->find($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
                ->with('cbtError', 'Anda tidak memiliki hak akses untuk mengedit butir soal ini.');
        }

        $tipeSoal   = $this->request->getPost('tipe_soal') === 'essay' ? 'essay' : 'pilihan_ganda';
        $pertanyaan = trim((string) $this->request->getPost('pertanyaan'));

        if ($pertanyaan === '') {
            return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))->with('cbtError', 'Teks pertanyaan wajib diisi.');
        }

        $bobot = max(0.1, (float) ($this->request->getPost('bobot_nilai') ?: 1.00));

        $data = [
            'tipe_soal'    => $tipeSoal,
            'pertanyaan'   => $pertanyaan,
            'bobot_nilai'  => $bobot,
        ];

        if ($tipeSoal === 'pilihan_ganda') {
            $data['opsi_a'] = trim((string) $this->request->getPost('opsi_a'));
            $data['opsi_b'] = trim((string) $this->request->getPost('opsi_b'));
            $data['opsi_c'] = trim((string) $this->request->getPost('opsi_c'));
            $data['opsi_d'] = trim((string) $this->request->getPost('opsi_d'));
            $data['opsi_e'] = trim((string) $this->request->getPost('opsi_e'));

            $kunci = strtoupper(trim((string) $this->request->getPost('kunci_jawaban')));
            if (! in_array($kunci, ['A', 'B', 'C', 'D', 'E'], true)) {
                return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
                    ->with('cbtError', 'Kunci jawaban pilihan ganda harus dipilih (A, B, C, D, atau E).');
            }
            $data['kunci_jawaban'] = $kunci;
        } else {
            $data['kunci_jawaban'] = null;
        }

        $this->soalModel->update($soalId, $data);

        return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
            ->with('cbtMessage', 'Soal berhasil diperbarui.');
    }

    public function deleteQuestion(int $soalId)
    {
        $soal = $this->soalModel->find($soalId);
        if (! $soal) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Soal tidak ditemukan.');
        }

        $ujianId = (int) $soal['ujian_id'];
        $ujian   = $this->ujianModel->find($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
                ->with('cbtError', 'Anda tidak memiliki hak akses untuk menghapus butir soal ini.');
        }

        $this->soalModel->delete($soalId);

        return redirect()->to(site_url('online-assessments/' . $ujianId . '/questions'))
            ->with('cbtMessage', 'Soal berhasil dihapus.');
    }

    /**
     * Download template butir soal dalam format XLSX atau CSV
     */
    public function downloadQuestionsTemplate(int $ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses ke ujian ini.');
        }

        $format = strtolower((string) ($this->request->getGet('format') ?? 'xlsx'));

        $columns = [
            'no',
            'tipe_soal',
            'pertanyaan',
            'opsi_a',
            'opsi_b',
            'opsi_c',
            'opsi_d',
            'opsi_e',
            'kunci_jawaban',
            'bobot_nilai',
        ];

        $sampleRows = [
            $columns,
            [
                '1',
                'PG',
                'Ibukota negara Republik Indonesia adalah...',
                'Jakarta',
                'Surabaya',
                'Bandung',
                'Medan',
                'Semarang',
                'A',
                '1',
            ],
            [
                '2',
                'PG',
                'Berapakah hasil perkalian dari 25 x 4?',
                '75',
                '80',
                '100',
                '125',
                '',
                'C',
                '1',
            ],
            [
                '3',
                'ESSAY',
                'Jelaskan proses terjadinya fotosintesis pada tumbuhan hijau dan sebutkan zat yang dihasilkan!',
                '',
                '',
                '',
                '',
                '',
                'Menghasilkan glukosa dan gas oksigen dari karbon dioksida dan air melalui bantuan klorofil dan cahaya matahari.',
                '5',
            ],
        ];

        $filename = 'template-soal-cbt-ujian-' . $ujianId;

        if ($format === 'csv') {
            return $this->formatCsvResponse($filename . '.csv', $sampleRows);
        }

        $spreadsheet = new StudentSpreadsheet();
        return $spreadsheet->download($filename . '.xlsx', $sampleRows);
    }

    /**
     * Export seluruh butir soal dari ujian ini ke format XLSX atau CSV
     */
    public function exportQuestions(int $ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses ke ujian ini.');
        }

        $format = strtolower((string) ($this->request->getGet('format') ?? 'xlsx'));
        $questions = $this->soalModel->where('ujian_id', $ujianId)->orderBy('urutan', 'ASC')->orderBy('soal_id', 'ASC')->findAll();

        $rows = [
            [
                'no',
                'tipe_soal',
                'pertanyaan',
                'opsi_a',
                'opsi_b',
                'opsi_c',
                'opsi_d',
                'opsi_e',
                'kunci_jawaban',
                'bobot_nilai',
            ],
        ];

        foreach ($questions as $idx => $q) {
            $rows[] = [
                (string) ($idx + 1),
                $q['tipe_soal'] === 'pilihan_ganda' ? 'PG' : 'ESSAY',
                (string) $q['pertanyaan'],
                (string) ($q['opsi_a'] ?? ''),
                (string) ($q['opsi_b'] ?? ''),
                (string) ($q['opsi_c'] ?? ''),
                (string) ($q['opsi_d'] ?? ''),
                (string) ($q['opsi_e'] ?? ''),
                (string) ($q['kunci_jawaban'] ?? ''),
                (string) ($q['bobot_nilai'] ?? '1'),
            ];
        }

        $slugTitle = url_title($ujian['judul'], '-', true) ?: 'ujian';
        $filename = 'soal-' . $slugTitle . '-' . $ujianId;

        if ($format === 'csv') {
            return $this->formatCsvResponse($filename . '.csv', $rows);
        }

        $spreadsheet = new StudentSpreadsheet();
        return $spreadsheet->download($filename . '.xlsx', $rows);
    }

    /**
     * Import butir soal dari file XLSX, XLS, atau CSV
     */
    public function importQuestions(int $ujianId)
    {
        $ujian = $this->ujianModel->find($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk mengimpor soal ke ujian ini.');
        }

        $file = $this->request->getFile('questions_file');
        $redirectUrl = site_url('online-assessments/' . $ujianId . '/questions');

        $fileValid = $file !== null && ($file->isValid() || (php_sapi_name() === 'cli' && is_file($file->getTempName())));
        if (! $fileValid || $file->hasMoved()) {
            return redirect()->to($redirectUrl)->with('cbtError', 'Pilih file spreadsheet (.XLSX, .XLS, atau .CSV) yang valid.');
        }

        $ext = strtolower($file->getClientExtension() ?: $file->getExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
            return redirect()->to($redirectUrl)->with('cbtError', 'Format file tidak didukung. Harap gunakan file berformat .XLSX, .XLS, atau .CSV.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return redirect()->to($redirectUrl)->with('cbtError', 'Ukuran file terlalu besar (maksimal 10 MB).');
        }

        $tempPath = $file->getTempName();
        $rows = [];

        try {
            if ($ext === 'xlsx') {
                $spreadsheet = new StudentSpreadsheet();
                $rows = $spreadsheet->read($tempPath);
            } elseif ($ext === 'csv') {
                $rows = $this->readDelimitedFile($tempPath);
            } else {
                // Untuk file .xls, coba baca dengan StudentSpreadsheet (jika struktur OpenXML/ZIP) lalu fallback ke delimited
                try {
                    $spreadsheet = new StudentSpreadsheet();
                    $rows = $spreadsheet->read($tempPath);
                } catch (\Throwable $e) {
                    $rows = $this->readDelimitedFile($tempPath);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error reading imported questions file: ' . $e->getMessage());
            return redirect()->to($redirectUrl)->with('cbtError', 'File tidak dapat dibaca atau rusak. Silakan simpan ulang file sebagai .XLSX atau .CSV kemudian unggah kembali.');
        }

        if (empty($rows) || count($rows) < 2) {
            return redirect()->to($redirectUrl)->with('cbtError', 'File kosong atau tidak memiliki baris data soal.');
        }

        // Ambil header dan normalisasi index kolom
        $headerRow = array_map(function ($val) {
            $cleaned = preg_replace('/^\xEF\xBB\xBF/', '', trim((string) $val));
            return strtolower((string) $cleaned);
        }, array_shift($rows));

        $colMap = [
            'tipe_soal'     => null,
            'pertanyaan'    => null,
            'opsi_a'        => null,
            'opsi_b'        => null,
            'opsi_c'        => null,
            'opsi_d'        => null,
            'opsi_e'        => null,
            'kunci_jawaban' => null,
            'bobot_nilai'   => null,
        ];

        foreach ($headerRow as $idx => $headerName) {
            if (in_array($headerName, ['tipe_soal', 'tipe', 'jenis', 'jenis_soal', 'type'], true)) {
                $colMap['tipe_soal'] = $idx;
            } elseif (in_array($headerName, ['pertanyaan', 'soal', 'soal_pertanyaan', 'tanya', 'question'], true)) {
                $colMap['pertanyaan'] = $idx;
            } elseif (in_array($headerName, ['opsi_a', 'pilihan_a', 'jawaban_a', 'a'], true)) {
                $colMap['opsi_a'] = $idx;
            } elseif (in_array($headerName, ['opsi_b', 'pilihan_b', 'jawaban_b', 'b'], true)) {
                $colMap['opsi_b'] = $idx;
            } elseif (in_array($headerName, ['opsi_c', 'pilihan_c', 'jawaban_c', 'c'], true)) {
                $colMap['opsi_c'] = $idx;
            } elseif (in_array($headerName, ['opsi_d', 'pilihan_d', 'jawaban_d', 'd'], true)) {
                $colMap['opsi_d'] = $idx;
            } elseif (in_array($headerName, ['opsi_e', 'pilihan_e', 'jawaban_e', 'e'], true)) {
                $colMap['opsi_e'] = $idx;
            } elseif (in_array($headerName, ['kunci_jawaban', 'kunci', 'jawaban_benar', 'key', 'answer'], true)) {
                $colMap['kunci_jawaban'] = $idx;
            } elseif (in_array($headerName, ['bobot_nilai', 'bobot', 'nilai', 'skor', 'score'], true)) {
                $colMap['bobot_nilai'] = $idx;
            }
        }

        // Jika pertanyaan tidak terdeteksi via nama kolom, fallback ke index standar template
        if ($colMap['pertanyaan'] === null) {
            $colMap['tipe_soal']     = 1;
            $colMap['pertanyaan']    = 2;
            $colMap['opsi_a']        = 3;
            $colMap['opsi_b']        = 4;
            $colMap['opsi_c']        = 5;
            $colMap['opsi_d']        = 6;
            $colMap['opsi_e']        = 7;
            $colMap['kunci_jawaban'] = 8;
            $colMap['bobot_nilai']   = 9;
        }

        $truncateExisting = (bool) $this->request->getPost('truncate_existing');
        if ($truncateExisting) {
            $this->soalModel->where('ujian_id', $ujianId)->delete();
            $currentOrder = 0;
        } else {
            $currentOrder = (int) ($this->soalModel->where('ujian_id', $ujianId)->selectMax('urutan')->first()['urutan'] ?? 0);
        }

        $insertedCount = 0;
        $pgCount = 0;
        $essayCount = 0;
        $skippedCount = 0;
        $toInsert = [];

        foreach ($rows as $row) {
            if (empty(array_filter($row, fn($c) => trim((string) $c) !== ''))) {
                continue;
            }

            $pertanyaan = trim((string) ($row[$colMap['pertanyaan']] ?? ''));
            if ($pertanyaan === '' || stripos($pertanyaan, '[CONTOH]') !== false) {
                $skippedCount++;
                continue;
            }

            $rawTipe = strtolower(trim((string) ($row[$colMap['tipe_soal']] ?? 'pg')));
            $isEssay = in_array($rawTipe, ['essay', 'uraian', 'esai'], true);
            $tipeSoal = $isEssay ? 'essay' : 'pilihan_ganda';

            $bobotRaw = (float) str_replace(',', '.', trim((string) ($row[$colMap['bobot_nilai']] ?? '1')));
            $bobot = $bobotRaw > 0 ? $bobotRaw : 1.00;

            $currentOrder++;

            if ($tipeSoal === 'essay') {
                $kunciGuide = trim((string) ($row[$colMap['kunci_jawaban']] ?? ''));
                $toInsert[] = [
                    'ujian_id'      => $ujianId,
                    'tipe_soal'     => 'essay',
                    'pertanyaan'    => $pertanyaan,
                    'opsi_a'        => null,
                    'opsi_b'        => null,
                    'opsi_c'        => null,
                    'opsi_d'        => null,
                    'opsi_e'        => null,
                    'kunci_jawaban' => $kunciGuide !== '' ? $kunciGuide : null,
                    'bobot_nilai'   => $bobot,
                    'urutan'        => $currentOrder,
                ];
                $essayCount++;
                $insertedCount++;
            } else {
                $opsiA = trim((string) ($row[$colMap['opsi_a']] ?? ''));
                $opsiB = trim((string) ($row[$colMap['opsi_b']] ?? ''));
                $opsiC = trim((string) ($row[$colMap['opsi_c']] ?? ''));
                $opsiD = trim((string) ($row[$colMap['opsi_d']] ?? ''));
                $opsiE = trim((string) ($row[$colMap['opsi_e']] ?? ''));

                if ($opsiA === '' || $opsiB === '') {
                    $skippedCount++;
                    continue;
                }

                $rawKunci = strtoupper(trim((string) ($row[$colMap['kunci_jawaban']] ?? 'A')));
                if (! in_array($rawKunci, ['A', 'B', 'C', 'D', 'E'], true)) {
                    $rawKunci = 'A';
                }

                $toInsert[] = [
                    'ujian_id'      => $ujianId,
                    'tipe_soal'     => 'pilihan_ganda',
                    'pertanyaan'    => $pertanyaan,
                    'opsi_a'        => $opsiA,
                    'opsi_b'        => $opsiB,
                    'opsi_c'        => $opsiC !== '' ? $opsiC : null,
                    'opsi_d'        => $opsiD !== '' ? $opsiD : null,
                    'opsi_e'        => $opsiE !== '' ? $opsiE : null,
                    'kunci_jawaban' => $rawKunci,
                    'bobot_nilai'   => $bobot,
                    'urutan'        => $currentOrder,
                ];
                $pgCount++;
                $insertedCount++;
            }
        }

        if ($toInsert === []) {
            return redirect()->to($redirectUrl)->with('cbtError', 'Tidak ada soal valid yang berhasil dibaca dari file. Pastikan format kolom sesuai dengan template.');
        }

        $this->soalModel->insertBatch($toInsert);

        $msg = "Berhasil mengimpor {$insertedCount} butir soal ({$pgCount} Pilihan Ganda, {$essayCount} Essay).";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris kosong/contoh dilewati).";
        }

        return redirect()->to($redirectUrl)->with('cbtMessage', $msg);
    }

    // Hasil & Nilai Ujian
    public function results(int $ujianId)
    {
        $ujian = $this->ujianModel->getWithRelations($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk melihat hasil ujian ini.');
        }

        $participants = $this->pesertaModel->getPesertaList($ujianId);

        // Ambil info soal
        $totalPg = $this->soalModel->where('ujian_id', $ujianId)->where('tipe_soal', 'pilihan_ganda')->countAllResults();
        $totalEssay = $this->soalModel->where('ujian_id', $ujianId)->where('tipe_soal', 'essay')->countAllResults();

        return view('panel/online-assessments/results', [
            'title'        => 'Hasil & Nilai: ' . $ujian['judul'],
            'activePage'   => 'online-assessments',
            'ujian'        => $ujian,
            'participants' => $participants,
            'totalPg'      => $totalPg,
            'totalEssay'   => $totalEssay,
            'message'      => session()->getFlashdata('cbtMessage'),
            'error'        => session()->getFlashdata('cbtError'),
        ]);
    }

    // Lembar Jawaban Siswa (Detail mana yang benar dan mana yang salah)
    public function studentSheet(int $pesertaUjianId)
    {
        $peserta = $this->pesertaModel
            ->select('cbt_peserta_ujian.*, master_siswa.nama_siswa, master_siswa.nis, master_siswa.nisn, master_siswa.no_peserta, master_kelas.kelas_nama, cbt_ujian.judul, cbt_ujian.durasi_menit, cbt_ujian.created_by, cbt_ujian.guru_id as ujian_guru_id')
            ->join('master_siswa', 'master_siswa.siswa_id = cbt_peserta_ujian.siswa_id', 'left')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
            ->join('cbt_ujian', 'cbt_ujian.ujian_id = cbt_peserta_ujian.ujian_id', 'left')
            ->where('cbt_peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if (! $peserta) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data peserta ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        $ujianCheck = [
            'created_by' => $peserta['created_by'] ?? null,
            'guru_id'    => $peserta['ujian_guru_id'] ?? null,
        ];
        if (! $this->canAccessExam($ujianCheck, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses untuk melihat lembar jawaban ini.');
        }

        $answers = $this->jawabanModel->getAnswersByPeserta($pesertaUjianId);

        return view('panel/online-assessments/student-sheet', [
            'title'      => 'Lembar Jawaban: ' . $peserta['nama_siswa'],
            'activePage' => 'online-assessments',
            'peserta'    => $peserta,
            'answers'    => $answers,
            'message'    => session()->getFlashdata('cbtMessage'),
            'error'      => session()->getFlashdata('cbtError'),
        ]);
    }

    // Penilaian Essay Manual oleh Guru
    public function gradeEssay()
    {
        $jawabanId      = (int) $this->request->getPost('jawaban_id');
        $nilai          = max(0, (float) $this->request->getPost('nilai_didapat'));
        $catatan        = trim((string) $this->request->getPost('catatan_guru'));
        $pesertaUjianId = (int) $this->request->getPost('peserta_ujian_id');

        $jawaban = $this->jawabanModel->find($jawabanId);
        if (! $jawaban) {
            return redirect()->back()->with('cbtError', 'Jawaban tidak ditemukan.');
        }

        $peserta = $this->pesertaModel->find($pesertaUjianId);
        if (! $peserta) {
            return redirect()->back()->with('cbtError', 'Data peserta tidak ditemukan.');
        }

        $ujian = $this->ujianModel->find((int) $peserta['ujian_id']);
        if (! $ujian) {
            return redirect()->back()->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->back()->with('cbtError', 'Anda tidak memiliki hak akses untuk menilai ujian ini.');
        }

        $soal = $this->soalModel->find($jawaban['soal_id']);
        $maxBobot = (float) ($soal['bobot_nilai'] ?? 10.0);
        if ($nilai > $maxBobot) {
            $nilai = $maxBobot;
        }

        $this->jawabanModel->update($jawabanId, [
            'nilai_didapat' => $nilai,
            'catatan_guru'  => $catatan,
            'is_correct'    => $nilai > 0 ? 1 : 0,
        ]);

        // Rekalkulasi total nilai essay peserta
        $allAnswers = $this->jawabanModel
            ->select('cbt_jawaban_siswa.nilai_didapat, cbt_soal.tipe_soal')
            ->join('cbt_soal', 'cbt_soal.soal_id = cbt_jawaban_siswa.soal_id')
            ->where('cbt_jawaban_siswa.peserta_ujian_id', $pesertaUjianId)
            ->findAll();

        $totalEssay = 0.0;
        foreach ($allAnswers as $ans) {
            if ($ans['tipe_soal'] === 'essay') {
                $totalEssay += (float) $ans['nilai_didapat'];
            }
        }

        $nilaiPg = (float) ($peserta['nilai_pg'] ?? 0.0);
        $totalAkhir = min(100.0, round($nilaiPg + $totalEssay, 2));

        $this->pesertaModel->update($pesertaUjianId, [
            'nilai_essay' => $totalEssay,
            'nilai_total' => $totalAkhir,
        ]);

        return redirect()->back()->with('cbtMessage', 'Nilai essay berhasil disimpan dan nilai total telah diperbarui.');
    }

    public function exportResults(int $ujianId)
    {
        $ujian = $this->ujianModel->getWithRelations($ujianId);
        if (! $ujian) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = $this->getAuthUser();
        if (! $this->canAccessExam($ujian, $authUser)) {
            return redirect()->to(site_url('online-assessments'))->with('cbtError', 'Anda tidak memiliki hak akses ke hasil ujian ini.');
        }

        $participants = $this->pesertaModel->getPesertaList($ujianId);

        return view('panel/online-assessments/export-results', [
            'title'        => 'Rekap Nilai Ujian: ' . $ujian['judul'],
            'ujian'        => $ujian,
            'participants' => $participants,
        ]);
    }

    /**
     * Generate unique uppercase random alphanumeric token for CBT exam
     */
    protected function generateExamToken(int $length = 6): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $maxIndex = strlen($chars) - 1;

        do {
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $chars[random_int(0, $maxIndex)];
            }
            $exists = $this->ujianModel->where('token', $token)->first();
        } while ($exists);

        return $token;
    }

    /**
     * Membaca file CSV / TSV dengan auto-detect delimiter koma, titik-koma, atau tab
     */
    private function readDelimitedFile(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membuka file CSV/delimit.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new RuntimeException('File kosong.');
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $delimiter = ',';
        $highestCount = 1;
        foreach ([',', ';', "\t"] as $candidate) {
            $count = count(str_getcsv($firstLine, $candidate, '"', ''));
            if ($count > $highestCount) {
                $highestCount = $count;
                $delimiter = $candidate;
            }
        }
        rewind($handle);

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Format array rows menjadi Response download CSV ber-BOM UTF-8
     */
    private function formatCsvResponse(string $filename, array $rows): ResponseInterface
    {
        $fp = fopen('php://temp', 'r+');
        // Add UTF-8 BOM agar terbaca sempurna di Microsoft Excel
        fputs($fp, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($fp, $row, ';');
        }
        rewind($fp);
        $csvData = stream_get_contents($fp);
        fclose($fp);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($csvData);
    }
}


