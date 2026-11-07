<?php

namespace App\Controllers;

use App\Models\CbtUjianModel;
use App\Models\CbtSoalModel;
use App\Models\CbtPesertaUjianModel;
use App\Models\CbtJawabanSiswaModel;
use App\Models\StudentModel;

class CbtExamController extends BaseController
{
    protected $db;
    protected $ujianModel;
    protected $soalModel;
    protected $pesertaModel;
    protected $jawabanModel;
    protected $studentModel;

    public function __construct()
    {
        $this->db           = db_connect();
        $this->ujianModel   = new CbtUjianModel();
        $this->soalModel    = new CbtSoalModel();
        $this->pesertaModel = new CbtPesertaUjianModel();
        $this->jawabanModel = new CbtJawabanSiswaModel();
        $this->studentModel = new StudentModel();
    }

    // Portal Ujian Siswa (Halaman depan pemilihan ujian & login siswa)
    public function index()
    {
        $authUser = session()->get('authUser');
        if (! is_array($authUser) || empty($authUser['user_id'])) {
            return redirect()->to(site_url('login'))
                ->with('authError', 'Silakan login terlebih dahulu menggunakan username dan password yang diberikan oleh admin sekolah untuk mengakses Ujian Online (CBT).');
        }

        // Cek apakah user yang sedang login adalah siswa
        $loggedInStudent = null;
        if (! empty($authUser['siswa_id'])) {
            $loggedInStudent = $this->studentModel
                ->select('master_siswa.*, master_kelas.kelas_nama')
                ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
                ->find((int) $authUser['siswa_id']);
        } elseif (($authUser['role'] ?? '') === 'siswa') {
            // Jika role siswa tapi siswa_id belum terisi di session, cari berdasarkan nama atau email
            $foundSiswa = $this->studentModel->where('nama_siswa', $authUser['display_name'])->first();
            if ($foundSiswa) {
                $loggedInStudent = $this->studentModel
                    ->select('master_siswa.*, master_kelas.kelas_nama')
                    ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
                    ->find((int) $foundSiswa['siswa_id']);
                
                // Update session
                $authUser['siswa_id'] = (int) $foundSiswa['siswa_id'];
                session()->set('authUser', $authUser);
            }
        }

        // Query ujian yang aktif dan hanya dari tahun ajaran yang aktif
        $examBuilder = $this->ujianModel
            ->select('cbt_ujian.*, master_mapel.mapel_nama, master_kelas.kelas_nama, master_guru.guru_nama, master_tahun_ajaran.tahun_ajaran_nama, master_tahun_ajaran.semester as tahun_ajaran_semester, master_tahun_ajaran.is_active as tahun_ajaran_is_active')
            ->join('master_mapel', 'master_mapel.mapel_id = cbt_ujian.mapel_id', 'left')
            ->join('master_kelas', 'master_kelas.kelas_id = cbt_ujian.kelas_id', 'left')
            ->join('master_guru', 'master_guru.guru_id = cbt_ujian.guru_id', 'left')
            ->join('master_tahun_ajaran', 'master_tahun_ajaran.tahun_ajaran_id = cbt_ujian.tahun_ajaran_id', 'inner')
            ->where('cbt_ujian.status', 'aktif')
            ->where('master_tahun_ajaran.is_active', 1);

        // Jika siswa login dan memiliki kelas, tampilkan ujian yang sesuai kelasnya atau ujian umum
        if ($loggedInStudent && ! empty($loggedInStudent['kelas_id'])) {
            $studentClassId = (int) $loggedInStudent['kelas_id'];
            $examBuilder->groupStart()
                ->where('cbt_ujian.kelas_id', $studentClassId)
                ->orWhere('cbt_ujian.kelas_id IS NULL', null, false)
                ->orWhere('cbt_ujian.kelas_id', 0)
            ->groupEnd();
        }

        $activeExams = $examBuilder->orderBy('cbt_ujian.ujian_id', 'DESC')->findAll();

        foreach ($activeExams as &$exam) {
            $ujianId = (int) $exam['ujian_id'];
            $exam['total_soal'] = $this->soalModel->where('ujian_id', $ujianId)->countAllResults();

            if ($loggedInStudent) {
                $peserta = $this->pesertaModel
                    ->where('ujian_id', $ujianId)
                    ->where('siswa_id', (int) $loggedInStudent['siswa_id'])
                    ->first();
                $exam['peserta_status'] = $peserta ? $peserta['status_pengerjaan'] : null;
                $exam['peserta_id']     = $peserta ? $peserta['peserta_ujian_id'] : null;
                $exam['nilai_total']    = $peserta ? $peserta['nilai_total'] : null;
            }
        }
        unset($exam);

        // Jika bukan siswa (misal admin/guru sedang menguji coba atau memantau CBT)
        $students = [];
        if (! $loggedInStudent) {
            $students = $this->studentModel
                ->select('master_siswa.siswa_id, master_siswa.nama_siswa, master_siswa.nis, master_siswa.nisn, master_siswa.no_peserta, master_kelas.kelas_nama')
                ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
                ->where('master_siswa.status', 'aktif')
                ->orderBy('master_siswa.nama_siswa', 'ASC')
                ->findAll();
        }

        return view('cbt/index', [
            'title'           => 'Portal Ujian Online (CBT)',
            'activeExams'     => $activeExams,
            'loggedInStudent' => $loggedInStudent,
            'authUser'        => $authUser,
            'students'        => $students,
            'message'         => session()->getFlashdata('cbtMessage'),
            'error'           => session()->getFlashdata('cbtError'),
        ]);
    }

    // Mulai atau lanjutkan ujian
    public function start(int $ujianId)
    {
        $authUser = session()->get('authUser');
        if (! is_array($authUser) || empty($authUser['user_id'])) {
            return redirect()->to(site_url('login'))
                ->with('authError', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $ujian = $this->ujianModel
            ->select('cbt_ujian.*, master_tahun_ajaran.is_active as tahun_ajaran_is_active')
            ->join('master_tahun_ajaran', 'master_tahun_ajaran.tahun_ajaran_id = cbt_ujian.tahun_ajaran_id', 'left')
            ->where('cbt_ujian.ujian_id', $ujianId)
            ->first();

        if (! $ujian || $ujian['status'] !== 'aktif') {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Ujian tidak aktif atau tidak ditemukan.');
        }

        if ((int) ($ujian['tahun_ajaran_is_active'] ?? 0) !== 1) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Ujian ini tidak dapat dikerjakan karena tahun ajaran sudah tidak aktif.');
        }

        $student = null;
        $siswaId = 0;

        // Jika user yang sedang login adalah role siswa:
        if (($authUser['role'] ?? '') === 'siswa') {
            $currentSiswaId = (int) ($authUser['siswa_id'] ?? 0);
            if ($currentSiswaId <= 0) {
                $found = $this->studentModel->where('nama_siswa', $authUser['display_name'])->first();
                if ($found) {
                    $currentSiswaId = (int) $found['siswa_id'];
                }
            }

            if ($currentSiswaId <= 0) {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'Akun login Anda belum terhubung dengan data siswa di sistem. Silakan hubungi Administrator.');
            }

            $student = $this->studentModel
                ->select('master_siswa.*, master_kelas.kelas_nama')
                ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
                ->find($currentSiswaId);

            if (! $student) {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'Data identitas siswa Anda tidak ditemukan di sistem.');
            }

            $siswaId = (int) $student['siswa_id'];

            // Verifikasi NIS siswa sesuai instruksi user
            $inputNis = trim((string) $this->request->getPost('nis'));
            if ($inputNis === '') {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'Silakan masukkan NIS Anda untuk verifikasi identitas pengerjaan ujian.');
            }

            $studentNis = trim((string) ($student['nis'] ?? ''));
            if ($studentNis !== '' && strcasecmp($inputNis, $studentNis) !== 0) {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'NIS yang Anda masukkan tidak sesuai dengan data identitas Anda (' . esc($student['nama_siswa']) . '). Silakan masukkan NIS yang benar.');
            }
        } else {
            // Mode Admin / Guru preview testing
            $siswaId = (int) $this->request->getPost('siswa_id');
            $inputNis = trim((string) $this->request->getPost('nis'));

            if ($siswaId < 1 && $inputNis !== '') {
                $found = $this->studentModel
                    ->groupStart()
                        ->where('nis', $inputNis)
                        ->orWhere('nisn', $inputNis)
                        ->orWhere('no_peserta', $inputNis)
                    ->groupEnd()
                    ->first();

                if ($found) {
                    $siswaId = (int) $found['siswa_id'];
                }
            }

            if ($siswaId < 1) {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'Pilih atau masukkan identitas siswa yang valid untuk mode pengujian.');
            }

            $student = $this->studentModel->find($siswaId);
            if (! $student) {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'Data siswa tidak ditemukan.');
            }
        }

        // Cek batasan kelas (jika ujian ditentukan untuk kelas tertentu)
        if (! empty($ujian['kelas_id']) && (int) $ujian['kelas_id'] > 0) {
            if ((int) $ujian['kelas_id'] !== (int) ($student['kelas_id'] ?? 0)) {
                $targetClass = $this->db->table('master_kelas')->where('kelas_id', $ujian['kelas_id'])->get()->getRowArray();
                $targetClassName = $targetClass['kelas_nama'] ?? 'kelas lain';
                return redirect()->to(site_url('cbt'))->with('cbtError', "Ujian ini khusus diselenggarakan untuk kelas {$targetClassName}. Anda berada di kelas yang berbeda.");
            }
        }

        // Cek validitas token jika ujian mewajibkan token
        if (! empty($ujian['token'])) {
            $inputToken = strtoupper(trim((string) $this->request->getPost('token')));
            if ($inputToken !== strtoupper(trim($ujian['token']))) {
                return redirect()->to(site_url('cbt'))->with('cbtError', 'Token ujian salah. Silakan tanyakan token ujian kepada guru/pengawas yang bertugas.');
            }
        }

        // Cek apakah siswa sudah pernah memulai ujian ini
        $existing = $this->pesertaModel
            ->where('ujian_id', $ujianId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($existing) {
            if ($existing['status_pengerjaan'] === 'selesai') {
                return redirect()->to(site_url('cbt/review/' . $existing['peserta_ujian_id']))
                    ->with('cbtMessage', 'Anda sudah menyelesaikan ujian ini.');
            }

            // Lanjutkan sesi ujian yang sedang berjalan
            return redirect()->to(site_url('cbt/room/' . $existing['peserta_ujian_id']));
        }

        // Pastikan ada butir soal di ujian ini
        $questions = $this->soalModel->getByUjian($ujianId, (bool) $ujian['acak_soal']);
        if ($questions === []) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Ujian ini belum memiliki butir soal.');
        }

        $now = date('Y-m-d H:i:s');
        $durasiDetik = ((int) $ujian['durasi_menit']) * 60;

        $this->db->transBegin();

        try {
            $pesertaUjianId = $this->pesertaModel->insert([
                'ujian_id'          => $ujianId,
                'siswa_id'          => $siswaId,
                'waktu_mulai'       => $now,
                'sisa_detik'        => $durasiDetik,
                'status_pengerjaan' => 'sedang_mengerjakan',
            ], true);

            // Inisialisasi baris jawaban kosong untuk setiap soal
            foreach ($questions as $q) {
                $this->jawabanModel->insert([
                    'peserta_ujian_id' => $pesertaUjianId,
                    'soal_id'          => $q['soal_id'],
                    'jawaban_pg'       => null,
                    'jawaban_essay'    => null,
                    'is_correct'       => null,
                    'nilai_didapat'    => 0.00,
                    'ragu_ragu'        => 0,
                ]);
            }

            $this->db->transCommit();

            return redirect()->to(site_url('cbt/room/' . $pesertaUjianId));
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'CBT start error: ' . $e->getMessage());
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Terjadi kesalahan saat memulai ujian: ' . $e->getMessage());
        }
    }

    // Ruang Ujian CBT Interaktif
    public function room(int $pesertaUjianId)
    {
        $authUser = session()->get('authUser');
        if (! is_array($authUser) || empty($authUser['user_id'])) {
            return redirect()->to(site_url('login'))->with('authError', 'Sesi ujian Anda telah berakhir. Silakan login kembali.');
        }

        $peserta = $this->pesertaModel
            ->select('cbt_peserta_ujian.*, master_siswa.nama_siswa, master_siswa.nis, master_siswa.no_peserta, master_kelas.kelas_nama, cbt_ujian.judul, cbt_ujian.durasi_menit, cbt_ujian.acak_opsi')
            ->join('master_siswa', 'master_siswa.siswa_id = cbt_peserta_ujian.siswa_id', 'left')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
            ->join('cbt_ujian', 'cbt_ujian.ujian_id = cbt_peserta_ujian.ujian_id', 'left')
            ->where('cbt_peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if (! $peserta) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Sesi ujian tidak ditemukan.');
        }

        // Proteksi jika role adalah siswa: hanya boleh mengakses ujiannya sendiri
        if (($authUser['role'] ?? '') === 'siswa' && (int) ($peserta['siswa_id'] ?? 0) !== (int) ($authUser['siswa_id'] ?? 0)) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Anda tidak memiliki hak akses ke sesi ujian ini.');
        }

        if ($peserta['status_pengerjaan'] === 'selesai') {
            return redirect()->to(site_url('cbt/review/' . $pesertaUjianId));
        }

        // Hitung sisa waktu ujian
        $waktuMulai = strtotime($peserta['waktu_mulai']);
        $durasiDetik = ((int) $peserta['durasi_menit']) * 60;
        $waktuBerakhir = $waktuMulai + $durasiDetik;
        $sisaDetik = $waktuBerakhir - time();

        if ($sisaDetik <= 0) {
            // Waktu habis otomatis diselesaikan
            return $this->finish($pesertaUjianId);
        }

        // Ambil butir soal dan jawaban siswa yang sudah tersimpan
        $answers = $this->jawabanModel->getAnswersByPeserta($pesertaUjianId);

        return view('cbt/room', [
            'title'     => 'Ujian: ' . $peserta['judul'],
            'peserta'   => $peserta,
            'answers'   => $answers,
            'sisaDetik' => $sisaDetik,
        ]);
    }

    // Autosave jawaban secara AJAX (Real-Time)
    public function saveAnswer()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $pesertaUjianId = (int) $this->request->getPost('peserta_ujian_id');
        $soalId         = (int) $this->request->getPost('soal_id');
        $jawabanPg      = $this->request->getPost('jawaban_pg');
        $jawabanEssay   = $this->request->getPost('jawaban_essay');
        $raguRagu       = (int) $this->request->getPost('ragu_ragu');

        $peserta = $this->pesertaModel->find($pesertaUjianId);
        if (! $peserta || $peserta['status_pengerjaan'] === 'selesai') {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian sudah berakhir']);
        }

        $existing = $this->jawabanModel
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->where('soal_id', $soalId)
            ->first();

        $dataToSave = [
            'ragu_ragu' => $raguRagu,
        ];

        if ($jawabanPg !== null) {
            $dataToSave['jawaban_pg'] = trim((string) $jawabanPg);
        }
        if ($jawabanEssay !== null) {
            $dataToSave['jawaban_essay'] = trim((string) $jawabanEssay);
        }

        if ($existing) {
            $this->jawabanModel->update($existing['jawaban_id'], $dataToSave);
        } else {
            $dataToSave['peserta_ujian_id'] = $pesertaUjianId;
            $dataToSave['soal_id']          = $soalId;
            $this->jawabanModel->insert($dataToSave);
        }

        return $this->response->setJSON(['success' => true]);
    }

    // Submit / Selesaikan Ujian & Penilaian Otomatis Pilihan Ganda
    public function finish(int $pesertaUjianId)
    {
        $peserta = $this->pesertaModel->find($pesertaUjianId);
        if (! $peserta) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Sesi ujian tidak ditemukan.');
        }

        $authUser = session()->get('authUser');
        if (is_array($authUser) && ($authUser['role'] ?? '') === 'siswa' && (int) ($peserta['siswa_id'] ?? 0) !== (int) ($authUser['siswa_id'] ?? 0)) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Akses tidak sah.');
        }

        if ($peserta['status_pengerjaan'] === 'selesai') {
            return redirect()->to(site_url('cbt/review/' . $pesertaUjianId));
        }

        // Ambil semua soal dan jawaban siswa
        $answers = $this->jawabanModel->getAnswersByPeserta($pesertaUjianId);

        $totalPgBobot = 0.0;
        $pgEarnedBobot = 0.0;
        $jumlahBenar = 0;
        $jumlahSalah = 0;
        $jumlahKosong = 0;
        $hasEssay = false;

        $this->db->transBegin();

        try {
            foreach ($answers as $ans) {
                $soalId = (int) $ans['soal_id'];
                $tipeSoal = $ans['tipe_soal'];
                $bobot = (float) ($ans['bobot_nilai'] ?: 1.0);

                if ($tipeSoal === 'pilihan_ganda') {
                    $totalPgBobot += $bobot;
                    $studentChoice = strtoupper(trim((string) $ans['jawaban_pg']));
                    $kunci = strtoupper(trim((string) $ans['kunci_jawaban']));

                    if ($studentChoice === '') {
                        // Tidak dijawab
                        $jumlahKosong++;
                        $this->jawabanModel->update($ans['jawaban_id'], [
                            'is_correct'    => 0,
                            'nilai_didapat' => 0.00,
                        ]);
                    } elseif ($studentChoice === $kunci) {
                        // BENAR!
                        $jumlahBenar++;
                        $pgEarnedBobot += $bobot;
                        $this->jawabanModel->update($ans['jawaban_id'], [
                            'is_correct'    => 1,
                            'nilai_didapat' => $bobot,
                        ]);
                    } else {
                        // SALAH
                        $jumlahSalah++;
                        $this->jawabanModel->update($ans['jawaban_id'], [
                            'is_correct'    => 0,
                            'nilai_didapat' => 0.00,
                        ]);
                    }
                } elseif ($tipeSoal === 'essay') {
                    $hasEssay = true;
                    // Essay menunggu koreksi manual dari guru
                    $this->jawabanModel->update($ans['jawaban_id'], [
                        'is_correct' => null,
                    ]);
                }
            }

            // Hitung nilai PG dalam skala 0 - 100
            $nilaiPg = 0.00;
            if ($totalPgBobot > 0) {
                $nilaiPg = round(($pgEarnedBobot / $totalPgBobot) * 100, 2);
            }

            $nilaiTotal = $nilaiPg; // Jika belum ada essay, nilai total sama dengan nilai PG

            $this->pesertaModel->update($pesertaUjianId, [
                'waktu_selesai'        => date('Y-m-d H:i:s'),
                'jumlah_benar'         => $jumlahBenar,
                'jumlah_salah'         => $jumlahSalah,
                'jumlah_tidak_dijawab' => $jumlahKosong,
                'nilai_pg'             => $nilaiPg,
                'nilai_total'          => $nilaiTotal,
                'status_pengerjaan'    => 'selesai',
            ]);

            $this->db->transCommit();

            return redirect()->to(site_url('cbt/review/' . $pesertaUjianId))
                ->with('cbtMessage', 'Ujian berhasil diselesaikan! Hasil penilaian otomatis pilihan ganda Anda dapat dilihat di bawah ini.');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'CBT finish error: ' . $e->getMessage());
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Terjadi kesalahan saat memproses hasil ujian: ' . $e->getMessage());
        }
    }

    // Halaman Hasil & Pembahasan Jawaban (Kelihatan mana yang salah dan benar)
    public function review(int $pesertaUjianId)
    {
        $peserta = $this->pesertaModel
            ->select('cbt_peserta_ujian.*, master_siswa.nama_siswa, master_siswa.nis, master_siswa.no_peserta, master_kelas.kelas_nama, cbt_ujian.judul, cbt_ujian.durasi_menit, cbt_ujian.tampil_nilai, cbt_ujian.tampil_pembahasan')
            ->join('master_siswa', 'master_siswa.siswa_id = cbt_peserta_ujian.siswa_id', 'left')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
            ->join('cbt_ujian', 'cbt_ujian.ujian_id = cbt_peserta_ujian.ujian_id', 'left')
            ->where('cbt_peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if (! $peserta) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Data ujian tidak ditemukan.');
        }

        $authUser = session()->get('authUser');
        if (is_array($authUser) && ($authUser['role'] ?? '') === 'siswa' && (int) ($peserta['siswa_id'] ?? 0) !== (int) ($authUser['siswa_id'] ?? 0)) {
            return redirect()->to(site_url('cbt'))->with('cbtError', 'Anda tidak memiliki hak akses untuk melihat hasil ujian siswa lain.');
        }

        $answers = $this->jawabanModel->getAnswersByPeserta($pesertaUjianId);

        $totalPg = 0;
        $totalEssay = 0;
        foreach ($answers as $a) {
            if ($a['tipe_soal'] === 'pilihan_ganda') {
                $totalPg++;
            } else {
                $totalEssay++;
            }
        }

        return view('cbt/review', [
            'title'      => 'Hasil & Pembahasan Ujian: ' . $peserta['judul'],
            'peserta'    => $peserta,
            'answers'    => $answers,
            'totalPg'    => $totalPg,
            'totalEssay' => $totalEssay,
            'message'    => session()->getFlashdata('cbtMessage'),
            'error'      => session()->getFlashdata('cbtError'),
        ]);
    }
}
