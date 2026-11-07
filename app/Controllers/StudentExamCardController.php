<?php

namespace App\Controllers;

use App\Libraries\ActionAccess;
use App\Models\ClassModel;
use App\Models\SchoolkuWebsiteContentModel;
use App\Models\StudentModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StudentExamCardController extends BaseController
{
    private const DEFAULT_SETTINGS = [
        'student_exam_card_title'       => 'KARTU ASESMENT SUMATIF AKHIR TAHUN',
        'student_exam_card_subtitle'    => 'TAHUN PELAJARAN 2022/2023',
        'student_exam_card_city_date'   => 'Pandeglang, 03 Juni 2023',
        'student_exam_card_signer_role' => 'Kepala Sekolah',
        'student_exam_card_signer_name' => 'Uswatun Hasanah, S.Pd',
        'student_exam_card_logo'           => 'smp',
        'student_exam_card_custom_logo'    => '',
        'student_exam_card_barcode'        => 'none',
        'student_exam_card_custom_barcode' => '',
        'student_exam_card_layout'         => 'landscape_4',
    ];

    private StudentModel $studentModel;
    private ClassModel $classModel;
    private SchoolkuWebsiteContentModel $contentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->classModel   = new ClassModel();
        $this->contentModel = new SchoolkuWebsiteContentModel();
    }

    public function index()
    {
        $selectedClass = $this->request->getGet('kelas_id');
        $keyword       = trim((string) $this->request->getGet('q'));

        $builder = $this->studentModel
            ->select('master_siswa.*, master_kelas.kelas_nama as kelas')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left');

        if (! empty($selectedClass) && $selectedClass !== 'all') {
            $builder->where('master_siswa.kelas_id', (int) $selectedClass);
        }

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('master_siswa.nama_siswa', $keyword)
                ->orLike('master_siswa.nis', $keyword)
                ->orLike('master_siswa.nisn', $keyword)
                ->orLike('master_siswa.no_peserta', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('master_kelas.kelas_nama', 'ASC')
            ->orderBy('master_siswa.nama_siswa', 'ASC');

        $students = $builder->findAll();

        // Summary counts
        $totalStudents = count($students);
        $withNumber    = 0;
        foreach ($students as $s) {
            if (! empty($s['no_peserta'])) {
                $withNumber++;
            }
        }

        $classes  = $this->classModel->orderBy('kelas_nama', 'ASC')->findAll();
        $settings = $this->getSettings();

        // Sample student for live preview
        $previewStudent = $students[0] ?? [
            'siswa_id'      => 0,
            'nama_siswa'    => 'Azzahra Putriyani Muhtian',
            'no_peserta'    => '035',
            'tempat_lahir'  => 'Tangerang',
            'tanggal_lahir' => '2009-11-26',
            'nis'           => '22.23.134',
            'kelas'         => 'VII A',
        ];

        return view('panel/student-exam-cards', [
            'title'          => 'Cetak Kartu Siswa',
            'activePage'     => 'student-exam-cards',
            'students'       => $students,
            'classes'        => $classes,
            'selectedClass'  => $selectedClass,
            'keyword'        => $keyword,
            'totalStudents'  => $totalStudents,
            'withNumber'     => $withNumber,
            'settings'       => $settings,
            'logoUrl'        => $this->resolveLogoUrl($settings),
            'barcodeUrl'     => $this->resolveBarcodeUrl($settings),
            'previewStudent' => $previewStudent,
            'canSettings'    => ActionAccess::can('student-exam-cards.settings'),
            'canPrint'       => ActionAccess::can('student-exam-cards.print'),
        ]);
    }

    public function saveSettings()
    {
        if (! ActionAccess::can('student-exam-cards.settings')) {
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Anda tidak memiliki hak akses untuk mengubah pengaturan ini.');
        }

        $currentSettings   = $this->getSettings();
        $customLogoName    = $currentSettings['student_exam_card_custom_logo'] ?? '';
        $customBarcodeName = $currentSettings['student_exam_card_custom_barcode'] ?? '';

        $logoFile = $this->request->getFile('custom_logo_file');
        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
            if (in_array($logoFile->getMimeType(), $allowedMimes, true)) {
                $uploadDir = FCPATH . 'uploads/exam_card';
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $customLogoName = $logoFile->getRandomName();
                $logoFile->move($uploadDir, $customLogoName);
            }
        }

        $barcodeFile = $this->request->getFile('custom_barcode_file');
        if ($barcodeFile && $barcodeFile->isValid() && ! $barcodeFile->hasMoved()) {
            $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
            if (in_array($barcodeFile->getMimeType(), $allowedMimes, true)) {
                $uploadDir = FCPATH . 'uploads/exam_card';
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $customBarcodeName = $barcodeFile->getRandomName();
                $barcodeFile->move($uploadDir, $customBarcodeName);
            }
        }

        $barcode = trim((string) $this->request->getPost('student_exam_card_barcode'));
        if (! in_array($barcode, ['none', 'smp', 'smk', 'custom'], true)) {
            $barcode = 'none';
        }

        $newSettings = [
            'student_exam_card_title'          => trim((string) $this->request->getPost('student_exam_card_title')),
            'student_exam_card_subtitle'       => trim((string) $this->request->getPost('student_exam_card_subtitle')),
            'student_exam_card_city_date'      => trim((string) $this->request->getPost('student_exam_card_city_date')),
            'student_exam_card_signer_role'    => trim((string) $this->request->getPost('student_exam_card_signer_role')),
            'student_exam_card_signer_name'    => trim((string) $this->request->getPost('student_exam_card_signer_name')),
            'student_exam_card_logo'           => trim((string) $this->request->getPost('student_exam_card_logo')),
            'student_exam_card_custom_logo'    => $customLogoName,
            'student_exam_card_barcode'        => $barcode,
            'student_exam_card_custom_barcode' => $customBarcodeName,
            'student_exam_card_layout'         => trim((string) $this->request->getPost('student_exam_card_layout')),
        ];

        // Sanitize defaults if empty
        if ($newSettings['student_exam_card_title'] === '') {
            $newSettings['student_exam_card_title'] = self::DEFAULT_SETTINGS['student_exam_card_title'];
        }
        if ($newSettings['student_exam_card_subtitle'] === '') {
            $newSettings['student_exam_card_subtitle'] = self::DEFAULT_SETTINGS['student_exam_card_subtitle'];
        }
        if ($newSettings['student_exam_card_city_date'] === '') {
            $newSettings['student_exam_card_city_date'] = self::DEFAULT_SETTINGS['student_exam_card_city_date'];
        }
        if ($newSettings['student_exam_card_signer_role'] === '') {
            $newSettings['student_exam_card_signer_role'] = self::DEFAULT_SETTINGS['student_exam_card_signer_role'];
        }
        if ($newSettings['student_exam_card_signer_name'] === '') {
            $newSettings['student_exam_card_signer_name'] = self::DEFAULT_SETTINGS['student_exam_card_signer_name'];
        }
        if (! in_array($newSettings['student_exam_card_logo'], ['smp', 'smk', 'custom'], true)) {
            $newSettings['student_exam_card_logo'] = 'smp';
        }
        if (! in_array($newSettings['student_exam_card_layout'], ['landscape_4', 'landscape_2', 'portrait_2', 'single'], true)) {
            $newSettings['student_exam_card_layout'] = 'landscape_4';
        }

        $this->contentModel->saveSettings($newSettings);

        return redirect()->to(site_url('student-exam-cards'))
            ->with('success', 'Pengaturan kartu siswa berhasil disimpan.');
    }

    public function saveNumber()
    {
        if (! ActionAccess::can('student-exam-cards.settings')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk mengubah nomor peserta.',
            ])->setStatusCode(403);
        }

        $siswaId   = (int) $this->request->getPost('siswa_id');
        $noPeserta = trim((string) $this->request->getPost('no_peserta'));

        if ($siswaId <= 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ID siswa tidak valid.',
            ])->setStatusCode(400);
        }

        $student = $this->studentModel->find($siswaId);
        if (! $student) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data siswa tidak ditemukan.',
            ])->setStatusCode(404);
        }

        $this->studentModel->update($siswaId, [
            'no_peserta' => $noPeserta !== '' ? $noPeserta : null,
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Nomor peserta berhasil disimpan.',
            'no_peserta' => $noPeserta,
        ]);
    }

    public function generateNumbers()
    {
        if (! ActionAccess::can('student-exam-cards.settings')) {
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Anda tidak memiliki hak akses untuk generate nomor peserta.');
        }

        $classId     = $this->request->getPost('kelas_id');
        $prefix      = trim((string) $this->request->getPost('prefix'));
        $digits      = max(1, min(6, (int) $this->request->getPost('digits')));
        $startNumber = max(1, (int) $this->request->getPost('start_number'));
        $overwrite   = (bool) $this->request->getPost('overwrite');
        $orderBy     = $this->request->getPost('order_by') === 'nis' ? 'master_siswa.nis' : 'master_siswa.nama_siswa';

        $builder = $this->studentModel
            ->select('master_siswa.siswa_id, master_siswa.nama_siswa, master_siswa.nis, master_siswa.no_peserta')
            ->orderBy($orderBy, 'ASC');

        if (! empty($classId) && $classId !== 'all') {
            $builder->where('master_siswa.kelas_id', (int) $classId);
        }

        $students = $builder->findAll();
        if (empty($students)) {
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Tidak ada data siswa yang ditemukan untuk di-generate.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $currentNum = $startNumber;
            $updatedCount = 0;

            foreach ($students as $student) {
                if (! $overwrite && ! empty($student['no_peserta'])) {
                    continue;
                }

                $formattedNum = str_pad((string) $currentNum, $digits, '0', STR_PAD_LEFT);
                $finalNo      = $prefix . $formattedNum;

                $this->studentModel->update($student['siswa_id'], [
                    'no_peserta' => $finalNo,
                ]);

                $currentNum++;
                $updatedCount++;
            }

            if ($db->transStatus() === false) {
                throw new DatabaseException('Gagal melakukan generate nomor peserta.');
            }

            $db->transCommit();

            return redirect()->to(site_url('student-exam-cards' . ($classId ? '?kelas_id=' . urlencode($classId) : '')))
                ->with('success', "Berhasil men-generate nomor peserta untuk {$updatedCount} siswa.");
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Terjadi kesalahan saat generate nomor peserta: ' . $e->getMessage());
        }
    }

    public function printSingle(int $siswaId)
    {
        return $this->printCards([$siswaId]);
    }

    public function print()
    {
        $selected = $this->request->getPost('selected_students')
            ?? $this->request->getGet('selected_students');

        if (! empty($selected)) {
            $ids = is_array($selected) ? array_map('intval', $selected) : array_map('intval', explode(',', (string) $selected));
            return $this->printCards($ids);
        }

        // If specific student ID passed in query
        $singleId = (int) $this->request->getGet('siswa_id');
        if ($singleId > 0) {
            return $this->printCards([$singleId]);
        }

        // Filter by class or print all
        $classId = $this->request->getGet('kelas_id') ?? $this->request->getPost('kelas_id');
        $builder = $this->studentModel
            ->select('master_siswa.siswa_id')
            ->orderBy('master_siswa.nama_siswa', 'ASC');

        if (! empty($classId) && $classId !== 'all') {
            $builder->where('master_siswa.kelas_id', (int) $classId);
        }

        $rows = $builder->findAll();
        $ids  = array_column($rows, 'siswa_id');

        if (empty($ids)) {
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Tidak ada siswa yang dipilih untuk dicetak.');
        }

        return $this->printCards($ids);
    }

    private function printCards(array $studentIds)
    {
        $studentIds = array_filter(array_map('intval', $studentIds));
        if (empty($studentIds)) {
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Pilih minimal satu siswa untuk dicetak kartunya.');
        }

        $students = $this->studentModel
            ->select('master_siswa.*, master_kelas.kelas_nama as kelas')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
            ->whereIn('master_siswa.siswa_id', $studentIds)
            ->orderBy('master_kelas.kelas_nama', 'ASC')
            ->orderBy('master_siswa.nama_siswa', 'ASC')
            ->findAll();

        if (empty($students)) {
            return redirect()->to(site_url('student-exam-cards'))
                ->with('error', 'Data siswa yang dipilih tidak ditemukan.');
        }

        $settings = $this->getSettings();
        $logoUrl  = $this->resolveLogoUrl($settings);

        // Process student fields
        foreach ($students as &$s) {
            $s['formatted_ttl'] = $this->formatTtl($s['tempat_lahir'] ?? '', $s['tanggal_lahir'] ?? '');
            $s['display_no_peserta'] = ! empty($s['no_peserta']) ? $s['no_peserta'] : '-';
            $s['display_nis'] = ! empty($s['nis']) ? $s['nis'] : '-';
        }
        unset($s);

        return view('panel/student-exam-card-print', [
            'students'   => $students,
            'settings'   => $settings,
            'logoUrl'    => $logoUrl,
            'barcodeUrl' => $this->resolveBarcodeUrl($settings),
            'title'      => 'Cetak ' . ($settings['student_exam_card_title'] ?? 'Kartu Ujian Siswa'),
        ]);
    }

    private function getSettings(): array
    {
        return $this->contentModel->getSettings(self::DEFAULT_SETTINGS);
    }

    private function resolveLogoUrl(array $settings): string
    {
        $logoType = $settings['student_exam_card_logo'] ?? 'smp';

        if ($logoType === 'custom' && ! empty($settings['student_exam_card_custom_logo'])) {
            $customPath = FCPATH . 'uploads/exam_card/' . $settings['student_exam_card_custom_logo'];
            if (file_exists($customPath)) {
                return base_url('uploads/exam_card/' . $settings['student_exam_card_custom_logo']);
            }
        }

        if ($logoType === 'smk') {
            return base_url('assets/img/logo/smk.png');
        }

        return base_url('assets/img/logo/smp.png');
    }

    private function resolveBarcodeUrl(array $settings): ?string
    {
        $barcodeType = $settings['student_exam_card_barcode'] ?? 'none';

        if ($barcodeType === 'smp') {
            $path = FCPATH . 'assets/img/logo/ttd-smp.png';
            if (file_exists($path)) {
                return base_url('assets/img/logo/ttd-smp.png');
            }
        }

        if ($barcodeType === 'smk') {
            $path = FCPATH . 'assets/img/logo/ttd-smk.png';
            if (file_exists($path)) {
                return base_url('assets/img/logo/ttd-smk.png');
            }
        }

        if ($barcodeType === 'custom' && ! empty($settings['student_exam_card_custom_barcode'])) {
            $customPath = FCPATH . 'uploads/exam_card/' . $settings['student_exam_card_custom_barcode'];
            if (file_exists($customPath)) {
                return base_url('uploads/exam_card/' . $settings['student_exam_card_custom_barcode']);
            }
        }

        return null;
    }

    public static function formatIndonesianDate(?string $dateStr, bool $leadingZeroDay = true): string
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            return '';
        }
        $timestamp = strtotime($dateStr);
        if ($timestamp === false) {
            return $dateStr;
        }
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthIndex = (int) date('n', $timestamp);
        $monthName  = $months[$monthIndex] ?? date('F', $timestamp);
        $day        = $leadingZeroDay ? date('d', $timestamp) : date('j', $timestamp);
        $year       = date('Y', $timestamp);

        return "{$day} {$monthName} {$year}";
    }

    public static function formatTtl(?string $place, ?string $dateStr): string
    {
        $place = trim((string) $place);
        $dateFormatted = self::formatIndonesianDate($dateStr, false);

        if ($place !== '' && $dateFormatted !== '') {
            return "{$place}, {$dateFormatted}";
        }
        if ($place !== '') {
            return $place;
        }
        return $dateFormatted !== '' ? $dateFormatted : '-';
    }
}
