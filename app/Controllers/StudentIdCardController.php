<?php

namespace App\Controllers;

use App\Models\SchoolkuWebsiteContentModel;
use App\Models\StudentIdCardLayoutModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\Files\UploadedFile;

class StudentIdCardController extends BaseController
{
    private const TEMPLATE_KEYS = [
        'front' => 'student_id_card_front_template',
        'back' => 'student_id_card_back_template',
    ];

    private const LAYOUT_KEY = 'student_id_card_text_layout';
    private const CATEGORY_LAYOUT_KEY = 'student_id_card_category_layouts';
    private const CATEGORIES = [
        'smk' => 'Siswa SMK',
        'smp' => 'Siswa SMP',
        'teacher' => 'Guru',
    ];

    private const FONT_OPTIONS = [
        'Arial' => 'Arial',
        'Georgia' => 'Georgia',
        'Times New Roman' => 'Times New Roman',
        'Verdana' => 'Verdana',
        'Tahoma' => 'Tahoma',
        'Courier New' => 'Courier New',
    ];

    private const DEFAULT_LAYOUTS = [
        'smk' => [
            'front' => [
                'photo' => ['x' => 28, 'y' => 20, 'width' => 44, 'height' => 27.78, 'shape' => 'circle'],
                'name' => ['x' => 20, 'y' => 73, 'fontFamily' => 'Arial', 'fontSize' => 3.1, 'fontColor' => '#102b58'],
                'nis' => ['x' => 9, 'y' => 80, 'fontFamily' => 'Arial', 'fontSize' => 2.35, 'fontColor' => '#102b58'],
                'nisn' => ['x' => 9, 'y' => 86, 'fontFamily' => 'Arial', 'fontSize' => 2.1, 'fontColor' => '#102b58'],
                'birth' => ['x' => 9, 'y' => 92, 'fontFamily' => 'Arial', 'fontSize' => 2.1, 'fontColor' => '#102b58'],
                'address' => ['x' => 9, 'y' => 98, 'fontFamily' => 'Arial', 'fontSize' => 2.0, 'fontColor' => '#102b58'],
                'code' => ['x' => 25.0, 'y' => 84.0, 'width' => 50.0, 'height' => 12.0, 'type' => 'barcode', 'showText' => true],
            ],
        ],
        'smp' => [
            'front' => [
                'photo' => ['x' => 22.10, 'y' => 17.87, 'width' => 50.87, 'height' => 31.78, 'zoom' => 1.05, 'panX' => 0.0, 'panY' => 15.0, 'shape' => 'circle'],
                'name' => ['x' => 20, 'y' => 73, 'fontFamily' => 'Arial', 'fontSize' => 3.1, 'fontColor' => '#102b58'],
                'nis' => ['x' => 9, 'y' => 80, 'fontFamily' => 'Arial', 'fontSize' => 2.35, 'fontColor' => '#102b58'],
                'nisn' => ['x' => 9, 'y' => 86, 'fontFamily' => 'Arial', 'fontSize' => 2.1, 'fontColor' => '#102b58'],
                'birth' => ['x' => 9, 'y' => 92, 'fontFamily' => 'Arial', 'fontSize' => 2.1, 'fontColor' => '#102b58'],
                'address' => ['x' => 9, 'y' => 98, 'fontFamily' => 'Arial', 'fontSize' => 2.0, 'fontColor' => '#102b58'],
                'code' => ['x' => 25.0, 'y' => 84.0, 'width' => 50.0, 'height' => 12.0, 'type' => 'barcode', 'showText' => true],
            ],
        ],
        'teacher' => [
            'front' => [
                'photo' => ['x' => 22.10, 'y' => 17.87, 'width' => 50.87, 'height' => 31.78, 'zoom' => 1.05, 'panX' => 0.0, 'panY' => 15.0, 'shape' => 'circle'],
                'name' => ['x' => 10, 'y' => 58, 'fontFamily' => 'Arial', 'fontSize' => 3.1, 'fontColor' => '#102b58'],
                'nip' => ['x' => 10, 'y' => 68, 'fontFamily' => 'Arial', 'fontSize' => 2.35, 'fontColor' => '#102b58'],
                'email' => ['x' => 10, 'y' => 78, 'fontFamily' => 'Arial', 'fontSize' => 2.0, 'fontColor' => '#102b58'],
                'code' => ['x' => 25.0, 'y' => 84.0, 'width' => 50.0, 'height' => 12.0, 'type' => 'barcode', 'showText' => true],
            ],
        ],
    ];

    private const MAX_TEMPLATE_SIZE = 5242880;

    public function index()
    {
        $students = db_connect()->table('master_siswa ms')
            ->select('ms.siswa_id, ms.nama_siswa, ms.nis, ms.nisn, ms.kelas_id, ms.jk, ms.tempat_lahir, ms.tanggal_lahir, ms.alamat, ms.foto, ms.rfid_tag, ms.status, mk.kelas_nama AS kelas')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left')
            ->where('ms.deleted_at', null)
            ->orderBy('ms.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();
        $teachers = db_connect()->table('master_guru')
            ->select('guru_id, guru_nama, guru_nip, guru_email, guru_telepon, foto, rfid_tag')
            ->orderBy('guru_nama', 'ASC')
            ->get()
            ->getResultArray();
        $templateNames = $this->getTemplateSettings();
        $templates = [];
        $templateRatios = [];
        $templateCardWidths = [];
        $templateCardSizes = [];
        $templateDataUrls = [];
        foreach (self::CATEGORIES as $category => $label) {
            $side = 'front';
            $templates[$category][$side] = $this->templateUrl($templateNames[$category][$side]);
            $templateRatios[$category][$side] = $this->templateRatio($templateNames[$category][$side]);
            $cardSize = $this->cardSize($templateRatios[$category][$side]);
            $templateCardSizes[$category][$side] = $cardSize;
            $templateCardWidths[$category][$side] = $cardSize['width'];

            $tplPath = $this->templatePath($templateNames[$category][$side]);
            if ($tplPath !== null && is_file($tplPath)) {
                $mime = @mime_content_type($tplPath) ?: 'image/jpeg';
                $templateDataUrls[$category] = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($tplPath));
            } else {
                $templateDataUrls[$category] = null;
            }
        }

        foreach ($students as &$st) {
            $stPhoto = basename((string) ($st['foto'] ?? ''));
            $st['photo_url'] = ! empty($stPhoto) ? self::studentPhotoUrl($stPhoto) : null;
        }
        unset($st);

        foreach ($teachers as &$tc) {
            $photoName = basename((string) ($tc['foto'] ?? ''));
            if (empty($photoName) && ! empty($tc['guru_email'])) {
                $userPhoto = db_connect()->table('auth_users')
                    ->select('avatar_path')
                    ->where('guru_id', (int) ($tc['guru_id'] ?? 0))
                    ->orWhere('email', (string) $tc['guru_email'])
                    ->get()
                    ->getRowArray();
                if (! empty($userPhoto['avatar_path'])) {
                    $photoName = basename((string) $userPhoto['avatar_path']);
                    $profilePhotoPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $photoName;
                    $studentPhotoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $photoName;
                    if (is_file($profilePhotoPath) && ! is_file($studentPhotoPath)) {
                        @copy($profilePhotoPath, $studentPhotoPath);
                    }
                }
            }
            if (! empty($photoName)) {
                $baseNameOnly = pathinfo($photoName, PATHINFO_FILENAME);
                $cutoutCandidate = str_ends_with($baseNameOnly, '-cutout') ? $photoName : ($baseNameOnly . '-cutout.png');
                $cutoutPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $cutoutCandidate;
                if (is_file($cutoutPath)) {
                    $tc['photo_url'] = self::studentPhotoUrl($cutoutCandidate);
                } else {
                    $tc['photo_url'] = self::studentPhotoUrl($photoName);
                }
            } else {
                $tc['photo_url'] = null;
            }
        }
        unset($tc);

        $previewTeacher = $teachers[0] ?? [];
        $teacherPhotoName = (string) ($previewTeacher['foto'] ?? '');
        $previewTeacherPhotoUrl = null;
        if (! empty($teacherPhotoName)) {
            $candidateCutout = pathinfo($teacherPhotoName, PATHINFO_FILENAME) . '-cutout.png';
            $cutoutPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $candidateCutout;
            if (is_file($cutoutPath)) {
                $previewTeacherPhotoUrl = self::studentPhotoUrl($candidateCutout);
            } else {
                $previewTeacherPhotoUrl = self::studentPhotoUrl($teacherPhotoName);
            }
        }
        $previewStudent = $students[0] ?? [];
        $previewStudentRfid = ! empty($previewStudent['rfid_tag']) ? (string) $previewStudent['rfid_tag'] : (! empty($previewStudent['nis']) ? (string) $previewStudent['nis'] : '202409001');
        $previewTeacherRfid = ! empty($previewTeacher['rfid_tag']) ? (string) $previewTeacher['rfid_tag'] : (! empty($previewTeacher['guru_nip']) ? (string) $previewTeacher['guru_nip'] : '19850101-001');

        return view('panel/student-id-cards', [
            'title' => 'ID Card Siswa & Guru',
            'activePage' => 'student-id-cards',
            'students' => $students,
            'teachers' => $teachers,
            'categories' => self::CATEGORIES,
            'templates' => $templates,
            'templateRatios' => $templateRatios,
            'templateCardWidths' => $templateCardWidths,
            'templateCardSizes' => $templateCardSizes,
            'templateDataUrls' => $templateDataUrls,
            'fontOptions' => self::FONT_OPTIONS,
            'layout' => $this->getCategoryLayoutSettings(),
            'previewPhotoUrl' => $this->studentPhotoUrl((string) ($students[0]['foto'] ?? '')),
            'previewTeacher' => $previewTeacher,
            'previewTeacherPhotoUrl' => $previewTeacherPhotoUrl,
            'previewStudentRfid' => $previewStudentRfid,
            'previewTeacherRfid' => $previewTeacherRfid,
            'templateReady' => array_map(
                static fn(array $sides): bool => $sides['front'] !== null,
                $templates
            ),
            'canSaveTemplates' => \App\Libraries\ActionAccess::can('student-id-cards.templates'),
            'canSaveLayout' => \App\Libraries\ActionAccess::can('student-id-cards.layout'),
            'message' => session()->getFlashdata('idCardMessage'),
            'error' => session()->getFlashdata('idCardError'),
        ]);
    }

    public function saveLayout()
    {
        $encodedLayout = $this->request->getPost('layout');
        if (! is_string($encodedLayout)) {
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Posisi tulisan yang dikirim tidak valid.');
        }

        $submittedLayout = json_decode($encodedLayout, true);
        if (! is_array($submittedLayout)) {
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Posisi tulisan tidak dapat dibaca. Silakan atur ulang posisi lalu simpan.');
        }

        $layout = [];
        foreach (self::DEFAULT_LAYOUTS as $category => $sides) {
            if (! is_array($submittedLayout[$category] ?? null)) {
                return redirect()->to(site_url('student-id-cards'))
                    ->with('idCardError', 'Pengaturan layout SMK, SMP, dan Guru harus lengkap.');
            }
            $activeSides = ['front'];
            foreach ($activeSides as $side) {
                $fields = $sides[$side] ?? [];
                if (! is_array($submittedLayout[$category][$side] ?? null)) {
                    return redirect()->to(site_url('student-id-cards'))
                        ->with('idCardError', 'Pengaturan posisi sisi depan kartu tidak lengkap.');
                }
                foreach ($fields as $field => $default) {
                    $coordinates = $submittedLayout[$category][$side][$field] ?? null;
                    if (
                        ! is_array($coordinates)
                        || ! is_numeric($coordinates['x'] ?? null)
                        || ! is_numeric($coordinates['y'] ?? null)
                    ) {
                        return redirect()->to(site_url('student-id-cards'))
                            ->with('idCardError', 'Posisi salah satu tulisan tidak valid. Atur ulang posisi lalu simpan.');
                    }
                    $x = (float) $coordinates['x'];
                    $y = (float) $coordinates['y'];
                    if (! is_finite($x) || ! is_finite($y) || $x < 0 || $x > 100 || $y < 0 || $y > 100) {
                        return redirect()->to(site_url('student-id-cards'))
                            ->with('idCardError', 'Posisi tulisan harus berada di dalam area template.');
                    }
                    $layout[$category][$side][$field] = ['x' => round($x, 2), 'y' => round($y, 2)];
                    if ($field === 'photo') {
                        if (! is_numeric($coordinates['width'] ?? null) || ! is_numeric($coordinates['height'] ?? null)) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Ukuran foto pada kartu tidak valid.');
                        }
                        $width = (float) $coordinates['width'];
                        $height = (float) $coordinates['height'];
                        if (
                            ! is_finite($width) || ! is_finite($height)
                            || $width < 5 || $width > 100 || $height < 5 || $height > 100
                            || round($x + $width, 2) > 100.05 || round($y + $height, 2) > 100.05
                        ) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Posisi dan ukuran foto harus berada di dalam area template.');
                        }
                        $layout[$category][$side][$field]['width'] = round($width, 2);
                        $layout[$category][$side][$field]['height'] = round($height, 2);

                        $zoom = $coordinates['zoom'] ?? 1;
                        if (! is_numeric($zoom) || ! is_finite((float) $zoom) || (float) $zoom < 0.5 || (float) $zoom > 2.5) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Zoom foto harus berada di antara 0.5x dan 2.5x.');
                        }
                        $layout[$category][$side][$field]['zoom'] = round((float) $zoom, 2);
                        $panX = $coordinates['panX'] ?? 0;
                        $panY = $coordinates['panY'] ?? 0;
                        if (
                            ! is_numeric($panX) || ! is_numeric($panY)
                            || ! is_finite((float) $panX) || ! is_finite((float) $panY)
                            || (float) $panX < -80.05 || (float) $panX > 80.05
                            || (float) $panY < -80.05 || (float) $panY > 80.05
                        ) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Posisi geser foto harus berada di antara -80% dan 80%.');
                        }
                        $layout[$category][$side][$field]['panX'] = round((float) $panX, 2);
                        $layout[$category][$side][$field]['panY'] = round((float) $panY, 2);
                        $layout[$category][$side][$field]['shape'] = in_array($coordinates['shape'] ?? '', ['square', 'circle'], true) ? $coordinates['shape'] : 'circle';
                    } elseif ($field === 'code') {
                        $width = (float) ($coordinates['width'] ?? 50);
                        $height = (float) ($coordinates['height'] ?? 12);
                        if (! is_finite($width) || ! is_finite($height) || $width < 5 || $width > 100 || $height < 3 || $height > 100) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Ukuran Barcode / QR Code tidak valid.');
                        }
                        $layout[$category][$side][$field]['width'] = round($width, 2);
                        $layout[$category][$side][$field]['height'] = round($height, 2);
                        $layout[$category][$side][$field]['type'] = in_array($coordinates['type'] ?? '', ['barcode', 'qrcode', 'none'], true) ? $coordinates['type'] : 'barcode';
                        $layout[$category][$side][$field]['showText'] = ! empty($coordinates['showText']);
                    } else {
                        $fontFamily = $coordinates['fontFamily'] ?? null;
                        $fontSize = $coordinates['fontSize'] ?? null;
                        if (
                            ! is_string($fontFamily) || ! array_key_exists($fontFamily, self::FONT_OPTIONS)
                            || ! is_numeric($fontSize)
                        ) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Jenis dan ukuran huruf harus dipilih dengan benar.');
                        }
                        $fontSize = (float) $fontSize;
                        if (! is_finite($fontSize) || $fontSize < 1 || $fontSize > 8) {
                            return redirect()->to(site_url('student-id-cards'))
                                ->with('idCardError', 'Ukuran huruf harus antara 1 hingga 8 mm.');
                        }
                        $fontColor = (string) ($coordinates['fontColor'] ?? '#102b58');
                        if (! preg_match('/^#[0-9a-fA-F]{3,8}$/', $fontColor)) {
                            $fontColor = '#102b58';
                        }
                        $layout[$category][$side][$field]['fontFamily'] = $fontFamily;
                        $layout[$category][$side][$field]['fontSize'] = round($fontSize, 2);
                        $layout[$category][$side][$field]['fontColor'] = $fontColor;
                    }
                }
            }
        }

        $encodedSettings = json_encode($layout, JSON_THROW_ON_ERROR);
        try {
            (new StudentIdCardLayoutModel())->saveLayouts($layout);
            (new SchoolkuWebsiteContentModel())->saveSettings([
                self::CATEGORY_LAYOUT_KEY => $encodedSettings,
            ]);
            $savedSettings = (new SchoolkuWebsiteContentModel())->getSettings([
                self::CATEGORY_LAYOUT_KEY => '',
            ]);
            if (($savedSettings[self::CATEGORY_LAYOUT_KEY] ?? '') !== $encodedSettings) {
                throw new DatabaseException('Saved student ID card layout did not match the submitted layout.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save student ID card text layout: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Posisi tulisan gagal disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url('student-id-cards'))
            ->with('idCardMessage', 'Posisi tulisan berhasil disimpan dan akan dipakai saat mencetak ID card.');
    }

    public function saveTemplates()
    {
        $settings = $this->getTemplateSettings();
        $files = [];
        foreach (self::CATEGORIES as $category => $label) {
            $side = 'front';
            $file = $this->request->getFile($category . '_' . $side . '_template');
            if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $error = $this->validateTemplate($file);
            if ($error !== null) {
                return redirect()->to(site_url('student-id-cards'))
                    ->with('idCardError', $label . ' (depan): ' . $error);
            }

            $files[$category][$side] = $file;
        }

        if ($files === []) {
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Pilih template sisi depan untuk diunggah.');
        }

        $directory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'id-cards';
        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            log_message('error', 'Could not create the student ID card template directory.');
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Folder penyimpanan template ID card gagal disiapkan.');
        }

        $newFiles = [];
        try {
            foreach ($files as $category => $sides) {
                foreach ($sides as $side => $file) {
                    $extension = $file->getMimeType() === 'image/jpeg' ? 'jpg' : 'png';
                    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
                    $file->move($directory, $filename);
                    $newFiles[$category][$side] = $filename;
                }
            }
        } catch (\RuntimeException $exception) {
            $this->removeTemplates($newFiles);
            log_message('error', 'Failed to store a student ID card template: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Template ID card gagal disimpan.');
        }

        $values = [];
        foreach ($newFiles as $category => $sides) {
            foreach ($sides as $side => $filename) {
                $values[$this->categoryTemplateKey($category, $side)] = $filename;
            }
        }

        try {
            (new SchoolkuWebsiteContentModel())->saveSettings($values);
        } catch (DatabaseException $exception) {
            $this->removeTemplates($newFiles);
            log_message('error', 'Failed to save student ID card template settings: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Pengaturan template ID card gagal disimpan.');
        }

        foreach ($newFiles as $category => $sides) {
            foreach ($sides as $side => $filename) {
                $this->removeTemplate($settings[$category][$side]);
            }
        }

        return redirect()->to(site_url('student-id-cards'))
            ->with('idCardMessage', 'Template ID card berhasil disimpan.');
    }

    public function print(int $studentId, string $category = 'smp')
    {
        $sessionUser = session()->get('authUser');
        if (($sessionUser['role'] ?? '') === 'siswa' && (int) ($sessionUser['siswa_id'] ?? 0) !== $studentId) {
            return redirect()->to(site_url('profile'))
                ->with('profileError', 'Anda hanya dapat mengakses ID Card Anda sendiri.');
        }

        if (! in_array($category, ['smk', 'smp'], true)) {
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Jenjang ID card siswa tidak valid.');
        }
        $student = db_connect()->table('master_siswa ms')
            ->select('ms.siswa_id, ms.nama_siswa, ms.nis, ms.nisn, ms.jk, ms.tempat_lahir, ms.tanggal_lahir, ms.alamat, ms.foto, ms.rfid_tag, mk.kelas_nama AS kelas')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left')
            ->where('ms.siswa_id', $studentId)
            ->where('ms.deleted_at', null)
            ->get()
            ->getRowArray();

        if ($student === null) {
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Data siswa tidak ditemukan.');
        }

        return $this->renderCard($student, $category, false);
    }

    public function printTeacher(int $teacherId)
    {
        $sessionUser = session()->get('authUser');
        if (($sessionUser['role'] ?? '') === 'siswa') {
            return redirect()->to(site_url('profile'))
                ->with('profileError', 'Akses ditolak.');
        }

        $teacher = db_connect()->table('master_guru')
            ->select('guru_id, guru_nama, guru_nip, guru_email, guru_telepon, foto, rfid_tag')
            ->where('guru_id', $teacherId)
            ->get()
            ->getRowArray();
        if ($teacher === null) {
            return redirect()->to(site_url('student-id-cards'))
                ->with('idCardError', 'Data guru tidak ditemukan.');
        }

        return $this->renderCard($teacher, 'teacher', true);
    }

    private function renderCard(array $person, string $category, bool $isTeacher)
    {
        $settings = $this->getTemplateSettings()[$category];
        if ($this->templatePath($settings['front']) === null) {
            $sessionUser = session()->get('authUser');
            $redirectUrl = ($isTeacher || ($sessionUser['role'] ?? '') === 'siswa') ? site_url('profile') : site_url('student-id-cards');
            return redirect()->to($redirectUrl)
                ->with('idCardError', 'Unggah template sisi depan untuk ' . self::CATEGORIES[$category] . ' sebelum mencetak.');
        }

        $layout = $this->getCategoryLayoutSettings()[$category];
        $photoName = basename((string) ($person['foto'] ?? ''));

        if ($isTeacher) {
            if (empty($photoName) && ! empty($person['guru_email'])) {
                $userPhoto = db_connect()->table('auth_users')
                    ->select('avatar_path')
                    ->where('guru_id', (int) ($person['guru_id'] ?? 0))
                    ->orWhere('email', (string) $person['guru_email'])
                    ->get()
                    ->getRowArray();
                if (! empty($userPhoto['avatar_path'])) {
                    $photoName = basename((string) $userPhoto['avatar_path']);
                    $profilePhotoPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $photoName;
                    $studentPhotoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $photoName;
                    if (is_file($profilePhotoPath) && ! is_file($studentPhotoPath)) {
                        @copy($profilePhotoPath, $studentPhotoPath);
                    }
                }
            }

            if (! empty($photoName)) {
                $baseNameOnly = pathinfo($photoName, PATHINFO_FILENAME);
                $cutoutCandidate = str_ends_with($baseNameOnly, '-cutout') ? $photoName : ($baseNameOnly . '-cutout.png');
                $cutoutPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $cutoutCandidate;
                
                if (! is_file($cutoutPath)) {
                    $sourcePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $photoName;
                    if (! is_file($sourcePath)) {
                        $profileSource = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $photoName;
                        if (is_file($profileSource)) {
                            @copy($profileSource, $sourcePath);
                        }
                    }
                    if (is_file($sourcePath)) {
                        $scriptPath = ROOTPATH . 'scripts' . DIRECTORY_SEPARATOR . 'remove_student_photo_background.py';
                        $python = getenv('STUDENT_PHOTO_PYTHON') ?: 'python';
                        $command = [$python, $scriptPath, $sourcePath, $cutoutPath];
                        try {
                            $process = proc_open($command, [
                                0 => ['pipe', 'r'],
                                1 => ['pipe', 'w'],
                                2 => ['pipe', 'w'],
                            ], $pipes, ROOTPATH);
                            if (is_resource($process)) {
                                fclose($pipes[0]);
                                fclose($pipes[1]);
                                fclose($pipes[2]);
                                proc_close($process);
                            }
                        } catch (\Throwable $e) {}
                    }
                }

                if (is_file($cutoutPath)) {
                    $photoName = $cutoutCandidate;
                }
            }
        }

        $photoUrl = ! empty($photoName) ? $this->studentPhotoUrl($photoName) : null;

        if ($isTeacher) {
            $frontFields = [
                ['key' => 'name', 'value' => $person['guru_nama']],
                ['key' => 'nip', 'value' => 'NIP: ' . ($person['guru_nip'] ?? '-')],
                ['key' => 'email', 'value' => 'Email: ' . ($person['guru_email'] ?? '-')],
            ];
            $displayName = (string) $person['guru_nama'];
        } else {
            $frontFields = [
                ['key' => 'name', 'value' => $person['nama_siswa']],
                ['key' => 'nis', 'value' => $person['nis'] ?? '-'],
                ['key' => 'nisn', 'value' => $person['nisn'] ?? '-'],
                ['key' => 'birth', 'value' => trim(($person['tempat_lahir'] ?? '') . ', ' . ($person['tanggal_lahir'] ?? ''), ', ')],
                ['key' => 'address', 'value' => $person['alamat'] ?? '-'],
            ];
            $displayName = (string) $person['nama_siswa'];
        }

        $rfidValue = ! empty($person['rfid_tag']) ? (string) $person['rfid_tag'] : '';
        if ($rfidValue === '') {
            $rfidValue = $isTeacher ? (string) ($person['guru_nip'] ?? $person['guru_id']) : (string) ($person['nis'] ?? $person['nisn'] ?? $person['siswa_id']);
        }

        $frontRatio = $this->templateRatio($settings['front']);
        $frontTemplatePath = $this->templatePath($settings['front']);
        $frontTemplateUrl = $this->templateUrl($settings['front']);
        if ($frontTemplatePath !== null && is_file($frontTemplatePath)) {
            $mime = @mime_content_type($frontTemplatePath) ?: 'image/jpeg';
            $frontTemplateUrl = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($frontTemplatePath));
        }

        if (! empty($photoName)) {
            $localPhotoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'student-photos' . DIRECTORY_SEPARATOR . $photoName;
            if (is_file($localPhotoPath)) {
                $mime = @mime_content_type($localPhotoPath) ?: 'image/jpeg';
                $photoUrl = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($localPhotoPath));
            }
        }

        return view('panel/student-id-card-print', [
            'title' => 'ID Card - ' . $displayName,
            'personName' => $displayName,
            'cardCategory' => $category,
            'photoUrl' => $photoUrl,
            'frontTemplate' => $frontTemplateUrl,
            'layout' => $layout,
            'frontCardSize' => $this->cardSize($frontRatio),
            'frontFields' => $frontFields,
            'rfidValue' => $rfidValue,
        ]);
    }

    private function getTemplateSettings(): array
    {
        $keys = [];
        foreach (self::CATEGORIES as $category => $label) {
            $keys[] = $this->categoryTemplateKey($category, 'front');
        }
        $keys = array_merge($keys, array_values(self::TEMPLATE_KEYS));
        $values = (new SchoolkuWebsiteContentModel())->getSettings(array_fill_keys($keys, ''));
        $settings = [];
        foreach (self::CATEGORIES as $category => $label) {
            $side = 'front';
            $categoryValue = (string) ($values[$this->categoryTemplateKey($category, $side)] ?? '');
            $legacyKey = self::TEMPLATE_KEYS[$side];
            $settings[$category][$side] = $categoryValue !== ''
                ? $categoryValue
                : ($category === 'smp' ? (string) ($values[$legacyKey] ?? '') : '');
        }

        return $settings;
    }

    private function categoryTemplateKey(string $category, string $side): string
    {
        return 'student_id_card_' . $category . '_' . $side . '_template';
    }

    private function validateTemplate(UploadedFile $file): ?string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return 'File template tidak valid atau gagal diunggah.';
        }
        if ($file->getSize() > self::MAX_TEMPLATE_SIZE) {
            return 'Ukuran setiap template maksimal 5 MB.';
        }

        $mimeType = $file->getMimeType();
        $imageInfo = @getimagesize($file->getTempName());
        if (
            ! in_array($mimeType, ['image/jpeg', 'image/png'], true)
            || $imageInfo === false
            || ! in_array($imageInfo['mime'] ?? '', ['image/jpeg', 'image/png'], true)
        ) {
            return 'Template harus berupa gambar JPG atau PNG yang valid.';
        }
        if ($imageInfo[0] > 6000 || $imageInfo[1] > 6000) {
            return 'Dimensi template maksimal 6000 × 6000 piksel.';
        }

        return null;
    }

    private function templateUrl(string $filename): ?string
    {
        $path = $this->templatePath($filename);

        return $path !== null
            ? base_url('uploads/id-cards/' . rawurlencode(basename($filename)))
            : null;
    }

    private function templateRatio(string $filename): float
    {
        $path = $this->templatePath($filename);
        $imageInfo = $path !== null ? @getimagesize($path) : false;
        if ($imageInfo === false || $imageInfo[1] === 0) {
            return 1.586;
        }

        return $imageInfo[0] / $imageInfo[1];
    }

    private function cardSize(float $ratio): array
    {
        $pageWidth = 90.0;
        $pageHeight = 120.0;

        if ($ratio >= 1.35) {
            $width = 85.6;
        } elseif ($ratio >= 1.0) {
            $width = 78.0;
        } elseif ($ratio >= 0.80) {
            $width = 60.5;
        } else {
            $width = 54.0;
        }

        $height = round($width / $ratio, 2);
        if ($height > $pageHeight) {
            $height = $pageHeight;
            $width = round($height * $ratio, 2);
        }
        if ($width > $pageWidth) {
            $width = $pageWidth;
            $height = round($width / $ratio, 2);
        }

        return [
            'width' => round($width, 2),
            'height' => round($height, 2),
        ];
    }

    private function getCategoryLayoutSettings(): array
    {
        $settings = (new SchoolkuWebsiteContentModel())->getSettings([
            self::CATEGORY_LAYOUT_KEY => '',
            self::LAYOUT_KEY => '',
        ]);
        $savedLayouts = json_decode((string) ($settings[self::CATEGORY_LAYOUT_KEY] ?? ''), true);
        $legacyLayout = json_decode((string) ($settings[self::LAYOUT_KEY] ?? ''), true);
        $dbLayouts = (new StudentIdCardLayoutModel())->getLayouts();
        $layouts = self::DEFAULT_LAYOUTS;

        foreach (self::DEFAULT_LAYOUTS as $category => $sides) {
            foreach ($sides as $side => $fields) {
                foreach ($fields as $field => $default) {
                    $coordinates = $dbLayouts[$category][$side][$field] ?? ($savedLayouts[$category][$side][$field] ?? null);
                    if ($coordinates === null && $category === 'smp') {
                        $coordinates = $legacyLayout[$side][$field] ?? null;
                    }
                    if (
                        ! is_array($coordinates)
                        || ! is_numeric($coordinates['x'] ?? null)
                        || ! is_numeric($coordinates['y'] ?? null)
                    ) {
                        continue;
                    }
                    $x = (float) $coordinates['x'];
                    $y = (float) $coordinates['y'];
                    if (! is_finite($x) || ! is_finite($y) || $x < 0 || $x > 100 || $y < 0 || $y > 100) {
                        continue;
                    }
                    $layouts[$category][$side][$field]['x'] = $x;
                    $layouts[$category][$side][$field]['y'] = $y;
                    if ($field === 'photo') {
                        $width = (float) ($coordinates['width'] ?? $default['width']);
                        $height = (float) ($coordinates['height'] ?? $default['height']);
                        if (
                            is_finite($width) && is_finite($height)
                            && $width >= 5 && $width <= 100 && $height >= 5 && $height <= 100
                            && $x + $width <= 100 && $y + $height <= 100
                        ) {
                            $layouts[$category][$side][$field]['width'] = $width;
                            $layouts[$category][$side][$field]['height'] = $height;
                        }
                        $zoom = (float) ($coordinates['zoom'] ?? 1.0);
                        if (is_finite($zoom) && $zoom >= 0.5 && $zoom <= 2.5) {
                            $layouts[$category][$side][$field]['zoom'] = round($zoom, 2);
                        }
                        $panX = (float) ($coordinates['panX'] ?? 0.0);
                        $panY = (float) ($coordinates['panY'] ?? 0.0);
                        if (is_finite($panX) && is_finite($panY)) {
                            $layouts[$category][$side][$field]['panX'] = round(max(-80.0, min(80.0, $panX)), 2);
                            $layouts[$category][$side][$field]['panY'] = round(max(-80.0, min(80.0, $panY)), 2);
                        }
                        $layouts[$category][$side][$field]['shape'] = in_array((string) ($coordinates['shape'] ?? ''), ['square', 'circle'], true) ? $coordinates['shape'] : ($default['shape'] ?? 'circle');
                        continue;
                    }

                    if ($field === 'code') {
                        $width = (float) ($coordinates['width'] ?? $default['width']);
                        $height = (float) ($coordinates['height'] ?? $default['height']);
                        if (is_finite($width) && is_finite($height) && $width >= 5 && $width <= 100 && $height >= 3 && $height <= 100) {
                            $layouts[$category][$side][$field]['width'] = $width;
                            $layouts[$category][$side][$field]['height'] = $height;
                        }
                        $layouts[$category][$side][$field]['type'] = in_array((string) ($coordinates['type'] ?? ''), ['barcode', 'qrcode', 'none'], true) ? $coordinates['type'] : ($default['type'] ?? 'barcode');
                        $layouts[$category][$side][$field]['showText'] = isset($coordinates['showText']) ? (bool) $coordinates['showText'] : ($default['showText'] ?? true);
                        continue;
                    }

                    $fontFamily = $coordinates['fontFamily'] ?? $default['fontFamily'];
                    $fontSize = (float) ($coordinates['fontSize'] ?? $default['fontSize']);
                    $fontColor = (string) ($coordinates['fontColor'] ?? $default['fontColor'] ?? '#102b58');
                    if (! preg_match('/^#[0-9a-fA-F]{3,8}$/', $fontColor)) {
                        $fontColor = '#102b58';
                    }
                    if (
                        is_string($fontFamily) && array_key_exists($fontFamily, self::FONT_OPTIONS)
                        && is_finite($fontSize) && $fontSize >= 1 && $fontSize <= 8
                    ) {
                        $layouts[$category][$side][$field]['fontFamily'] = $fontFamily;
                        $layouts[$category][$side][$field]['fontSize'] = $fontSize;
                        $layouts[$category][$side][$field]['fontColor'] = $fontColor;
                    }
                }
            }
        }

        return $layouts;
    }

    public static function studentPhotoUrl(string $filename): ?string
    {
        $filename = basename($filename);
        if ($filename === '') {
            return null;
        }

        $encoded = rawurlencode($filename);
        if (preg_match('/\A[a-f0-9]{32}(?:-cutout)?\.(?:jpg|png|webp)\z/i', $filename) === 1) {
            return base_url('uploads/student-photos/' . $encoded);
        }

        return base_url('assets/img/avatars/' . $encoded);
    }

    private function templatePath(string $filename): ?string
    {
        if (preg_match('/\A[a-f0-9]{32}\.(?:jpg|png)\z/', $filename) !== 1) {
            return null;
        }

        $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'id-cards' . DIRECTORY_SEPARATOR . $filename;

        return is_file($path) ? $path : null;
    }

    private function removeTemplates(array $filenames): void
    {
        foreach ($filenames as $entry) {
            if (is_array($entry)) {
                $this->removeTemplates($entry);
                continue;
            }
            $this->removeTemplate((string) $entry);
        }
    }

    private function removeTemplate(string $filename): void
    {
        $path = $this->templatePath($filename);
        if ($path !== null && ! unlink($path)) {
            log_message('warning', 'Could not remove replaced student ID card template: {filename}', [
                'filename' => $filename,
            ]);
        }
    }
}
