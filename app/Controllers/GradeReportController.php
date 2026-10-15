<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class GradeReportController extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index(): ResponseInterface|string
    {
        return $this->buildReport();
    }

    public function download(): ResponseInterface
    {
        return $this->buildReport(true);
    }

    private function buildReport(bool $pdfDownload = false): ResponseInterface|string
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

        $selected = [
            'kelas_id' => $this->getPositiveInt($this->request->getGet('kelas_id')),
            'tahun_ajaran_id' => $this->getPositiveInt($this->request->getGet('tahun_ajaran_id')),
            'siswa_id' => $this->getPositiveInt($this->request->getGet('siswa_id')),
        ];
        $selectionError = null;
        if ($selected['kelas_id'] !== null && ! in_array($selected['kelas_id'], array_map('intval', array_column($classes, 'kelas_id')), true)) {
            $selectionError = 'Kelas yang dipilih tidak ditemukan.';
        } elseif ($selected['tahun_ajaran_id'] !== null && ! in_array($selected['tahun_ajaran_id'], array_map('intval', array_column($years, 'tahun_ajaran_id')), true)) {
            $selectionError = 'Tahun ajaran yang dipilih tidak ditemukan.';
        }
        $students = [];
        $student = null;
        $subjects = [];
        $attendance = ['sakit' => 0, 'izin' => 0, 'alpa' => 0];
        $personalityAssessments = [];
        $extracurriculars = [];
        $kokurikulerGrades = [];
        $generate = $this->request->getGet('generate') === '1';
        $prepare = $this->request->getGet('prepare') === '1';
        $paperSize = $this->request->getGet('paper_size');
        if (! is_string($paperSize) || ! in_array($paperSize, ['A4', 'Legal'], true)) {
            $paperSize = 'A4';
        }
        $comment = $this->getText($this->request->getGet('catatan'), 1000);
        $patComponent = $this->request->getGet('pat_component');
        if ($patComponent === null) {
            $patComponent = 'uas';
        } elseif (! is_string($patComponent) || ! in_array($patComponent, ['uts', 'uas'], true)) {
            $patComponent = 'uas';
            $selectionError = 'Pilih nilai PAT dari UTS atau UAS.';
        }
        $kkmInput = $this->request->getGet('kkm');
        $kkm = $this->getPositiveInt($kkmInput);
        if ($kkmInput !== null && ($kkm === null || $kkm > 100)) {
            $selectionError = 'KKM harus berupa angka antara 1 sampai 100.';
        }
        $kkm = $kkm !== null && $kkm <= 100 ? $kkm : 75;

        if ($selected['kelas_id'] !== null) {
            $students = $this->db->table('master_siswa')
                ->select('siswa_id, nama_siswa, nis, nisn')
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

        if ($selected['kelas_id'] !== null && $selected['tahun_ajaran_id'] !== null) {
            $studentQuery = $this->db->table('master_siswa ms')
                ->select('ms.siswa_id, ms.nama_siswa, ms.nis, ms.nisn, ms.tempat_lahir, ms.tanggal_lahir, ms.alamat, mk.kelas_tingkat, mk.kelas_nama, mta.tahun_ajaran_nama, mta.semester')
                ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id')
                ->join('master_tahun_ajaran mta', 'mta.tahun_ajaran_id = ' . (int) $selected['tahun_ajaran_id'])
                ->where('ms.siswa_id', $selected['siswa_id'] ?? 0)
                ->where('ms.kelas_id', $selected['kelas_id'])
                ->groupStart()
                ->where('ms.status', 'aktif')
                ->orWhere('ms.status', '')
                ->groupEnd()
                ->where('ms.deleted_at', null);
            $student = $selected['siswa_id'] === null ? null : $studentQuery->get()->getRowArray();

            if ($selected['siswa_id'] !== null && $student === null) {
                $selectionError = 'Siswa tidak ditemukan pada kelas yang dipilih atau sudah tidak aktif.';
            }

            if ($student !== null) {
                $personalityAssessments = $this->db->table('academic_personality_aspects a')
                    ->select('a.aspek_id, a.nama_aspek, s.nilai')
                    ->join(
                        'academic_student_personality_scores s',
                        's.aspek_id = a.aspek_id AND s.siswa_id = ' . (int) $student['siswa_id']
                            . ' AND s.kelas_id = ' . (int) $selected['kelas_id']
                            . ' AND s.tahun_ajaran_id = ' . (int) $selected['tahun_ajaran_id'],
                        'left'
                    )
                    ->groupStart()
                    ->where('a.is_active', 1)
                    ->orWhere('s.nilai IS NOT NULL', null, false)
                    ->groupEnd()
                    ->orderBy('a.urutan')
                    ->orderBy('a.aspek_id')
                    ->get()
                    ->getResultArray();
                $extracurriculars = $this->db->table('academic_student_extracurricular')
                    ->select('nama_kegiatan, nilai')
                    ->where('siswa_id', $student['siswa_id'])
                    ->where('kelas_id', $selected['kelas_id'])
                    ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                    ->orderBy('nama_kegiatan')
                    ->get()
                    ->getResultArray();
                if ($this->db->tableExists('academic_student_kokurikuler')) {
                    $kokurikulerGrades = $this->db->table('academic_student_kokurikuler')
                        ->select('nama_kegiatan, nilai')
                        ->where('siswa_id', $student['siswa_id'])
                        ->where('kelas_id', $selected['kelas_id'])
                        ->where('tahun_ajaran_id', $selected['tahun_ajaran_id'])
                        ->orderBy('nama_kegiatan')
                        ->get()
                        ->getResultArray();
                }

                $knowledgeGradeExpression = $hasKnowledgeGrade
                    ? 'AVG(nilai_pengetahuan) AS nilai_pengetahuan'
                    : 'NULL AS nilai_pengetahuan';
                $gradeSubquery = '(SELECT mapel_id, AVG(nilai_uts) AS nilai_uts, AVG(nilai_uas) AS nilai_uas, '
                    . $knowledgeGradeExpression
                    . ', AVG(nilai_praktik) AS nilai_praktik'
                    . ' FROM academic_student_grades'
                    . ' WHERE siswa_id = ' . (int) $student['siswa_id']
                    . ' AND kelas_id = ' . (int) $selected['kelas_id']
                    . ' AND tahun_ajaran_id = ' . (int) $selected['tahun_ajaran_id']
                    . ' GROUP BY mapel_id) gs';
                $subjects = $this->db->table('master_mapel mm')
                    ->select('mm.mapel_id, mm.mapel_kode, mm.mapel_nama, mm.kelompok_rapor, gs.nilai_uts, gs.nilai_uas, gs.nilai_pengetahuan, gs.nilai_praktik', false)
                    ->join($gradeSubquery, 'gs.mapel_id = mm.mapel_id', 'left', false)
                    ->orderBy('mm.mapel_nama')
                    ->get()
                    ->getResultArray();
                $groupOrder = [
                    'nasional' => 0,
                    'kewilayahan' => 1,
                    'dasar_keahlian' => 2,
                    'program_keahlian' => 3,
                ];
                usort($subjects, static function (array $left, array $right) use ($groupOrder): int {
                    $groupComparison = ($groupOrder[$left['kelompok_rapor']] ?? 0)
                        <=> ($groupOrder[$right['kelompok_rapor']] ?? 0);

                    return $groupComparison !== 0
                        ? $groupComparison
                        : strcasecmp($left['mapel_nama'], $right['mapel_nama']);
                });

                foreach ($subjects as &$subject) {
                    $knowledgeScores = array_filter(
                        [$subject['nilai_uts'], $subject['nilai_uas']],
                        static fn ($score): bool => $score !== null
                    );
                    $allScores = array_filter(
                        [$subject['nilai_uts'], $subject['nilai_uas'], $subject['nilai_praktik']],
                        static fn ($score): bool => $score !== null
                    );
                    $subject['nilai_pengetahuan'] = $subject['nilai_pengetahuan'] !== null
                        ? (float) $subject['nilai_pengetahuan']
                        : ($knowledgeScores === []
                            ? null
                            : array_sum(array_map('floatval', $knowledgeScores)) / count($knowledgeScores));
                    $finalScores = $subject['nilai_pengetahuan'] === null
                        ? $allScores
                        : array_filter(
                            [$subject['nilai_pengetahuan'], $subject['nilai_praktik']],
                            static fn ($score): bool => $score !== null
                        );
                    $subject['nilai_akhir'] = $finalScores === []
                        ? null
                        : array_sum(array_map('floatval', $finalScores)) / count($finalScores);
                    $subject['predikat_pengetahuan'] = $this->predicate($subject['nilai_pengetahuan']);
                    $subject['predikat_keterampilan'] = $this->predicate(
                        $subject['nilai_praktik'] === null ? null : (float) $subject['nilai_praktik']
                    );
                    $subject['predikat'] = $this->predicate($subject['nilai_akhir']);
                    $subject['terbilang'] = $subject['nilai_akhir'] === null
                        ? '-'
                        : $this->numberToIndonesianWords((int) round($subject['nilai_akhir']));
                    $subject['deskripsi'] = $this->description($subject['nilai_akhir'], $kkm);
                    $subject['has_scores'] = $allScores !== [];
                    $subject['nilai_pat'] = $subject['nilai_' . $patComponent];
                }
                unset($subject);

                $attendanceRow = $this->db->table('absensi_absensi_kelas_siswa aks')
                    ->select(
                        "SUM(CASE WHEN aks.status = 'sakit' THEN 1 ELSE 0 END) AS sakit, "
                        . "SUM(CASE WHEN aks.status = 'izin' THEN 1 ELSE 0 END) AS izin, "
                        . "SUM(CASE WHEN aks.status = 'alpa' THEN 1 ELSE 0 END) AS alpa",
                        false
                    )
                    ->join('absensi_absensi_kelas ak', 'ak.absensi_kelas_id = aks.absensi_kelas_id')
                    ->where('aks.siswa_id', $student['siswa_id'])
                    ->where('ak.tahun_ajaran_id', $selected['tahun_ajaran_id'])
                    ->get()
                    ->getRowArray();
                if ($attendanceRow !== null) {
                    foreach ($attendance as $status => $_) {
                        $attendance[$status] = (int) ($attendanceRow[$status] ?? 0);
                    }
                }
            }
        }

        if ($generate && in_array(null, $selected, true) && $selectionError === null) {
            $selectionError = 'Pilih kelas, tahun ajaran, dan siswa sebelum membuat raport.';
        }

        $availableSubjectIds = array_map(
            static fn (array $subject): int => (int) $subject['mapel_id'],
            $subjects
        );
        $requestedSubjects = $this->request->getGet('mapel_id');
        if ($requestedSubjects === null && $generate) {
            $selectedSubjectIds = [];
        } elseif ($requestedSubjects === null && $prepare) {
            $selectedSubjectIds = $availableSubjectIds;
        } elseif (is_array($requestedSubjects)) {
            $selectedSubjectIds = [];
            foreach ($requestedSubjects as $subjectId) {
                $id = $this->getPositiveInt($subjectId);
                if ($id === null || ! in_array($id, $availableSubjectIds, true)) {
                    $selectionError = 'Pilihan mata pelajaran tidak valid.';
                    $selectedSubjectIds = [];
                    break;
                }
                if (! in_array($id, $selectedSubjectIds, true)) {
                    $selectedSubjectIds[] = $id;
                }
            }
        } else {
            $selectedSubjectIds = [];
            if ($requestedSubjects !== null) {
                $selectionError = 'Format pilihan mata pelajaran tidak valid.';
            }
        }

        if ($generate && $selectedSubjectIds === [] && $selectionError === null) {
            $selectionError = 'Pilih minimal satu mata pelajaran untuk ditampilkan pada raport.';
        }

        $reportSubjects = array_values(array_filter(
            $subjects,
            static fn (array $subject): bool => in_array((int) $subject['mapel_id'], $selectedSubjectIds, true)
        ));
        $reportAvailable = $generate && $student !== null && $selectionError === null;

        $viewData = [
            'title' => 'Raport Siswa',
            'activePage' => 'grade-report',
            'classes' => $classes,
            'years' => $years,
            'students' => $students,
            'subjects' => $subjects,
            'reportSubjects' => $reportSubjects,
            'selected' => $selected,
            'selectedSubjectIds' => $selectedSubjectIds,
            'selectionError' => $selectionError,
            'reportAvailable' => $reportAvailable,
            'comment' => $comment,
            'kkm' => $kkm,
            'patComponent' => $patComponent,
            'hasKnowledgeGrade' => $hasKnowledgeGrade,
            'attendance' => $attendance,
            'personalityAssessments' => $personalityAssessments,
            'extracurriculars' => $extracurriculars,
            'kokurikulerGrades' => $kokurikulerGrades,
            'template' => $student !== null && (int) $student['kelas_tingkat'] <= 9 ? 'smp' : 'smk',
            'student' => $student,
            'pdfMode' => $pdfDownload,
            'paperSize' => $paperSize,
            'reportLogoData' => '',
        ];

        if (! $pdfDownload) {
            return view('panel/grade-report', $viewData);
        }
        if (! $reportAvailable) {
            return $this->response->setStatusCode(400)->setBody(
                $selectionError ?? 'Data raport belum siap untuk diunduh.'
            );
        }

        $logoPath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo'
            . DIRECTORY_SEPARATOR . ($viewData['template'] === 'smp' ? 'smp-report.jpg' : 'smk-report.jpg');
        if (! is_file($logoPath)) {
            log_message('error', 'Unable to read school logo for grade report PDF: {path}', ['path' => $logoPath]);

            return $this->response->setStatusCode(500)->setBody('Logo sekolah tidak dapat dimuat untuk PDF.');
        }
        $logoContents = file_get_contents($logoPath);
        if ($logoContents === false) {
            log_message('error', 'Unable to read school logo for grade report PDF: {path}', ['path' => $logoPath]);

            return $this->response->setStatusCode(500)->setBody('Logo sekolah tidak dapat dimuat untuk PDF.');
        }
        $viewData['reportLogoData'] = 'data:image/jpeg;base64,' . base64_encode($logoContents);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $orientation = 'portrait';
        $fitLevel = 0;
        do {
            $viewData['pdfFitLevel'] = $fitLevel;
            $dompdf = new Dompdf($options);
            $dompdf->setPaper($paperSize, $orientation);
            $dompdf->loadHtml(view('panel/grade-report', $viewData), 'UTF-8');
            try {
                $dompdf->render();
            } catch (\Dompdf\Exception $exception) {
                log_message('error', 'Dompdf failed to render a grade report: {message}', [
                    'message' => $exception->getMessage(),
                ]);

                return $this->response->setStatusCode(500)->setBody(
                    'PDF raport gagal dibuat. Periksa log aplikasi untuk detail teknis.'
                );
            }
            if ($dompdf->getCanvas()->get_page_count() === 1) {
                break;
            }
            unset($dompdf);
            gc_collect_cycles();
            $fitLevel++;
        } while ($fitLevel <= 8);

        if ($dompdf->getCanvas()->get_page_count() !== 1) {
            log_message('error', 'Grade report PDF could not be fitted onto one page for student {studentId}.', [
                'studentId' => $selected['siswa_id'],
            ]);

            return $this->response->setStatusCode(422)->setBody(
                'Isi raport terlalu panjang untuk dimuat pada satu halaman. Kurangi mata pelajaran atau data penilaian.'
            );
        }

        $studentName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $student['nama_siswa']) ?? 'siswa';
        $filename = 'raport-' . trim($studentName, '-') . '-' . $paperSize . '.pdf';

        return $this->response
            ->download($filename, $dompdf->output(), true)
            ->setContentType('application/pdf');
    }

    private function getPositiveInt($value): ?int
    {
        if (! is_string($value) && ! is_int($value)) {
            return null;
        }

        $id = filter_var($value, FILTER_VALIDATE_INT);

        return $id !== false && $id > 0 ? $id : null;
    }

    private function getText($value, int $maxLength): string
    {
        if (! is_string($value)) {
            return '';
        }

        return mb_substr(trim($value), 0, $maxLength);
    }

    private function predicate(?float $score): string
    {
        if ($score === null) {
            return '-';
        }
        if ($score >= 90) {
            return 'A';
        }
        if ($score >= 80) {
            return 'B';
        }
        if ($score >= 70) {
            return 'C';
        }

        return 'D';
    }

    private function description(?float $score, int $kkm): string
    {
        if ($score === null) {
            return 'Nilai belum diinput';
        }

        return $score >= $kkm
            ? 'Tercapai'
            : 'Belum tercapai';
    }

    private function numberToIndonesianWords(int $number): string
    {
        $words = [
            0 => 'nol',
            1 => 'satu',
            2 => 'dua',
            3 => 'tiga',
            4 => 'empat',
            5 => 'lima',
            6 => 'enam',
            7 => 'tujuh',
            8 => 'delapan',
            9 => 'sembilan',
        ];

        if ($number < 0 || $number > 100) {
            return (string) $number;
        }
        if ($number < 10) {
            return $words[$number];
        }
        if ($number === 10) {
            return 'sepuluh';
        }
        if ($number === 11) {
            return 'sebelas';
        }
        if ($number < 20) {
            return $words[$number - 10] . ' belas';
        }
        if ($number < 100) {
            $tens = intdiv($number, 10);
            $ones = $number % 10;

            return $words[$tens] . ' puluh' . ($ones > 0 ? ' ' . $words[$ones] : '');
        }

        return 'seratus';
    }
}
