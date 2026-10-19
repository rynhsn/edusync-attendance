<?php

namespace App\Controllers;

use App\Models\SchoolkuWebsiteContentModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use DateTimeImmutable;

class SchoolkuWebsiteSettingsController extends BaseController
{
    private const HOME_DEFAULTS = [
        'hero_badge' => 'Sekolah Unggulan - Kurikulum Merdeka',
        'hero_title' => 'Selamat Datang di SekolahKu',
        'hero_subtitle' => 'Membangun Generasi Cerdas, Berkarakter, dan Berprestasi menuju Masa Depan Gemilang',
        'hero_btn_primary_text' => 'Jelajahi Sekolah',
        'hero_btn_primary_url' => '#profile',
        'hero_btn_secondary_text' => 'Hubungi Kami',
        'hero_btn_secondary_url' => '/news/contact',
        'hero_stats' => '[{"value":"1200+","label":"Siswa"},{"value":"85+","label":"Guru"},{"value":"150+","label":"Prestasi"}]',
        'counter_stats' => '[{"icon":"layer-group","number":"40","suffix":"+","label":"Rombongan Belajar"},{"icon":"calendar-alt","number":"25","suffix":"+","label":"Tahun Berdiri"},{"icon":"book-open","number":"24","suffix":"+","label":"Program Studi"},{"icon":"user-friends","number":"3000","suffix":"+","label":"Alumni"}]',
        'principal' => '{"smp":{"name":"Kepala Sekolah SMP","role":"Kepala Sekolah SMP","quote":"Bersama kita membangun generasi cerdas dan berkarakter.","photo":"","meta":[]},"smk":{"name":"Kepala Sekolah SMK","role":"Kepala Sekolah SMK","quote":"Bersama kita membangun generasi cerdas dan berkarakter.","photo":"","meta":[]}}',
    ];

    private const PROFILE_DEFAULTS = [
        'about' => '{"image":"https://placehold.co/600x400/0c4a6e/ffffff?text=SekolahKu","content_title":"Mewujudkan Pendidikan Berkualitas untuk Masa Depan","content_1":"","content_2":"","visi":"","misi":"","accreditation":"","accreditation_label":"Standar Nasional","highlights":[]}',
        'section_settings' => '{"profile":{"title":"Tentang Kami","subtitle":"Sekolah unggulan yang berkomitmen mencetak generasi penerus bangsa yang berkualitas"}}',
    ];

    private const PROGRAM_DEFAULTS = [
        'section_settings' => '{"programs":{"title":"Program Unggulan","subtitle":"Berbagai program unggulan untuk mengembangkan potensi dan bakat siswa","show":1}}',
    ];

    public function spmbSchedule()
    {
        $settings = (new SchoolkuWebsiteContentModel())->getSettings([
            'spmb_start_date' => '',
            'spmb_end_date' => '',
        ]);

        return view('panel/website-spmb-schedule', [
            'title' => 'Jadwal SPMB Online',
            'activePage' => 'website-spmb-schedule',
            'settings' => $settings,
            'schedule' => (new \App\Libraries\SpmbSchedule())->status(),
            'message' => session()->getFlashdata('websiteMessage'),
            'error' => session()->getFlashdata('websiteError'),
        ]);
    }

    public function saveSpmbSchedule()
    {
        $startDate = $this->request->getPost('spmb_start_date');
        $endDate = $this->request->getPost('spmb_end_date');
        if (! is_string($startDate) || ! is_string($endDate)) {
            return $this->redirectBack('website-settings/spmb-schedule', 'Tanggal jadwal SPMB tidak valid.');
        }
        $startDate = trim($startDate);
        $endDate = trim($endDate);

        if (($startDate === '') !== ($endDate === '')) {
            return $this->redirectBack('website-settings/spmb-schedule', 'Isi kedua tanggal atau kosongkan keduanya agar jadwal tidak dibatasi.');
        }
        if ($startDate !== '' && (! $this->isIsoDate($startDate) || ! $this->isIsoDate($endDate))) {
            return $this->redirectBack('website-settings/spmb-schedule', 'Tanggal harus valid dengan format hari, bulan, dan tahun.');
        }
        if ($startDate !== '' && $endDate < $startDate) {
            return $this->redirectBack('website-settings/spmb-schedule', 'Tanggal penutupan harus sama dengan atau setelah tanggal pembukaan.');
        }

        return $this->saveSettings('website-settings/spmb-schedule', [
            'spmb_start_date' => $startDate,
            'spmb_end_date' => $endDate,
        ]);
    }

    public function home()
    {
        $settings = (new SchoolkuWebsiteContentModel())->getSettings(self::HOME_DEFAULTS);
        $heroStats = $this->decodeArray($settings['hero_stats']);
        $counterStats = $this->decodeArray($settings['counter_stats']);
        $principal = $this->decodeArray($settings['principal']);
        if (! isset($principal['smp']) && ! isset($principal['smk'])) {
            $legacyPrincipal = $principal;
            $legacyPrincipal['role'] = ($legacyPrincipal['role'] ?? $legacyPrincipal['role_title'] ?? '') === 'Kepala Sekolah'
                ? 'Kepala Sekolah SMP'
                : ($legacyPrincipal['role'] ?? $legacyPrincipal['role_title'] ?? 'Kepala Sekolah SMP');
            $principal = [
                'smp' => $legacyPrincipal,
                'smk' => [
                    'name' => 'Kepala Sekolah SMK',
                    'role' => 'Kepala Sekolah SMK',
                    'quote' => '',
                    'photo' => '',
                    'meta' => [],
                ],
            ];
        }
        foreach (['smp', 'smk'] as $schoolLevel) {
            $principal[$schoolLevel] = array_merge([
                'name' => 'Kepala Sekolah ' . strtoupper($schoolLevel),
                'role' => 'Kepala Sekolah ' . strtoupper($schoolLevel),
                'quote' => '',
                'photo' => '',
                'meta' => [],
            ], is_array($principal[$schoolLevel] ?? null) ? $principal[$schoolLevel] : []);
        }

        return view('panel/website-home-settings', [
            'title' => 'Pengaturan Beranda',
            'activePage' => 'website-home-settings',
            'settings' => $settings,
            'heroStatsText' => $this->formatRows($heroStats, ['value', 'label']),
            'counterStatsText' => $this->formatRows($counterStats, ['number', 'suffix', 'label', 'icon']),
            'principal' => $principal,
            'message' => session()->getFlashdata('websiteMessage'),
            'error' => session()->getFlashdata('websiteError'),
        ]);
    }

    public function saveHome()
    {
        $rules = [
            'hero_badge' => 'permit_empty|max_length[150]',
            'hero_title' => 'required|max_length[200]',
            'hero_subtitle' => 'required|max_length[500]',
            'hero_btn_primary_text' => 'permit_empty|max_length[80]',
            'hero_btn_primary_url' => 'permit_empty|max_length[255]',
            'hero_btn_secondary_text' => 'permit_empty|max_length[80]',
            'hero_btn_secondary_url' => 'permit_empty|max_length[255]',
            'hero_stats' => 'permit_empty|max_length[2000]',
            'counter_stats' => 'permit_empty|max_length[3000]',
            'principal_smp_name' => 'permit_empty|max_length[200]',
            'principal_smp_role' => 'permit_empty|max_length[150]',
            'principal_smp_quote' => 'permit_empty|max_length[1000]',
            'principal_smk_name' => 'permit_empty|max_length[200]',
            'principal_smk_role' => 'permit_empty|max_length[150]',
            'principal_smk_quote' => 'permit_empty|max_length[1000]',
        ];
        if (! $this->validate($rules)) {
            return $this->redirectBack('website-settings/home', 'Periksa kembali kolom beranda.');
        }

        $values = [];
        foreach (array_slice(array_keys($rules), 0, 8) as $key) {
            $value = $this->request->getPost($key);
            if (! is_string($value)) {
                return $this->redirectBack('website-settings/home', 'Nilai pengaturan beranda tidak valid.');
            }
            $values[$key] = trim($value);
        }
        foreach (['hero_btn_primary_url', 'hero_btn_secondary_url'] as $key) {
            if (! $this->isSafeLink($values[$key])) {
                return $this->redirectBack('website-settings/home', 'Alamat tombol harus berupa tautan website, path lokal, atau anchor yang valid.');
            }
        }

        $heroStatsInput = $this->request->getPost('hero_stats');
        $counterStatsInput = $this->request->getPost('counter_stats');
        if (! is_string($heroStatsInput) || ! is_string($counterStatsInput)) {
            return $this->redirectBack('website-settings/home', 'Format statistik tidak valid.');
        }
        $heroStats = $this->parseRows($heroStatsInput, ['value', 'label']);
        $counterStats = $this->parseRows($counterStatsInput, ['number', 'suffix', 'label', 'icon']);
        if ($heroStats === null || $counterStats === null) {
            return $this->redirectBack('website-settings/home', 'Format statistik tidak valid. Gunakan satu baris per data dan pisahkan kolom dengan tanda |.');
        }

        $values['hero_stats'] = json_encode($heroStats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $values['counter_stats'] = json_encode($counterStats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $settingsModel = new SchoolkuWebsiteContentModel();
        $storedPrincipal = $this->decodeArray($settingsModel->getSettings(self::HOME_DEFAULTS)['principal']);
        if (! isset($storedPrincipal['smp']) && ! isset($storedPrincipal['smk'])) {
            $legacyPrincipal = $storedPrincipal;
            if (($legacyPrincipal['role'] ?? $legacyPrincipal['role_title'] ?? '') === 'Kepala Sekolah') {
                $legacyPrincipal['role'] = 'Kepala Sekolah SMP';
            }
            $storedPrincipal = [
                'smp' => $legacyPrincipal,
                'smk' => [
                    'name' => 'Kepala Sekolah SMK',
                    'role' => 'Kepala Sekolah SMK',
                    'quote' => '',
                    'photo' => '',
                    'meta' => [],
                ],
            ];
        }

        $principals = [];
        foreach (['smp', 'smk'] as $schoolLevel) {
            $name = $this->request->getPost('principal_' . $schoolLevel . '_name');
            $role = $this->request->getPost('principal_' . $schoolLevel . '_role');
            $quote = $this->request->getPost('principal_' . $schoolLevel . '_quote');
            if (! is_string($name) || ! is_string($role) || ! is_string($quote)) {
                return $this->redirectBack('website-settings/home', 'Data sambutan kepala sekolah tidak valid.');
            }
            $principals[$schoolLevel] = [
                'name' => trim($name),
                'role' => trim($role),
                'quote' => trim($quote),
                'photo' => (string) ($storedPrincipal[$schoolLevel]['photo'] ?? ''),
                'meta' => is_array($storedPrincipal[$schoolLevel]['meta'] ?? null)
                    ? $storedPrincipal[$schoolLevel]['meta']
                    : [],
            ];
        }

        $filesToStore = [];
        foreach (['smp', 'smk'] as $schoolLevel) {
            $file = $this->request->getFile('principal_' . $schoolLevel . '_photo');
            if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if (! $file->isValid() || $file->hasMoved() || $file->getSize() > 5 * 1024 * 1024) {
                return $this->redirectBack('website-settings/home', 'Foto kepala sekolah harus JPG, PNG, atau WebP yang valid dengan ukuran maksimal 5 MB.');
            }
            $image = @getimagesize($file->getTempName());
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = $image['mime'] ?? '';
            if ($image === false
                || ! isset($extensions[$mime])
                || $file->getMimeType() !== $mime) {
                return $this->redirectBack('website-settings/home', 'Foto kepala sekolah harus berupa JPG, PNG, atau WebP yang valid dengan ukuran maksimal 5 MB.');
            }
            $filesToStore[$schoolLevel] = [$file, $extensions[$mime]];
        }

        $storedPaths = [];
        foreach ($filesToStore as $schoolLevel => [$file, $extension]) {
            $directory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'school-website'
                . DIRECTORY_SEPARATOR . 'principals';
            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                log_message('error', 'Unable to create principal photo upload directory: {directory}', ['directory' => $directory]);
                $this->removePrincipalUploads($storedPaths);

                return $this->redirectBack('website-settings/home', 'Folder penyimpanan foto kepala sekolah gagal disiapkan.');
            }
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
            try {
                if (! $file->move($directory, $filename)) {
                    throw new \RuntimeException('Uploaded principal photo could not be moved.');
                }
            } catch (\RuntimeException $exception) {
                log_message('error', 'Failed to save principal photo for {level}: {message}', [
                    'level' => $schoolLevel,
                    'message' => $exception->getMessage(),
                ]);
                $this->removePrincipalUploads($storedPaths);

                return $this->redirectBack('website-settings/home', 'Foto kepala sekolah gagal disimpan.');
            }
            $uploadedPath = '/uploads/school-website/principals/' . $filename;
            $storedPaths[] = $uploadedPath;
            $principals[$schoolLevel]['photo'] = $uploadedPath;
        }

        try {
            $values['principal'] = json_encode(
                $principals,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
            );
            $settingsModel->saveSettings($values);
        } catch (\JsonException|DatabaseException $exception) {
            log_message('error', 'Failed to save homepage settings: {message}', ['message' => $exception->getMessage()]);
            $this->removePrincipalUploads($storedPaths);

            return $this->redirectBack('website-settings/home', 'Pengaturan website gagal disimpan. Silakan coba kembali.');
        }

        foreach (['smp', 'smk'] as $schoolLevel) {
            $oldPhoto = (string) ($storedPrincipal[$schoolLevel]['photo'] ?? '');
            if ($oldPhoto !== $principals[$schoolLevel]['photo']
                && ! in_array($oldPhoto, array_column($principals, 'photo'), true)) {
                $this->removeManagedPrincipalPhoto($oldPhoto);
            }
        }

        return redirect()->to(site_url('website-settings/home'))
            ->with('websiteMessage', 'Pengaturan berhasil disimpan dan ditampilkan di website publik.');
    }

    public function profile()
    {
        $settings = (new SchoolkuWebsiteContentModel())->getSettings(self::PROFILE_DEFAULTS);
        $about = $this->decodeArray($settings['about']);
        $sections = $this->decodeArray($settings['section_settings']);

        return view('panel/website-profile-settings', [
            'title' => 'Pengaturan Profil Sekolah',
            'activePage' => 'website-profile-settings',
            'about' => $about,
            'section' => $sections['profile'] ?? [],
            'message' => session()->getFlashdata('websiteMessage'),
            'error' => session()->getFlashdata('websiteError'),
        ]);
    }

    public function saveProfile()
    {
        $rules = [
            'section_title' => 'required|max_length[150]',
            'section_subtitle' => 'permit_empty|max_length[500]',
            'content_title' => 'required|max_length[200]',
            'image' => 'permit_empty|max_length[500]',
            'visi' => 'permit_empty|max_length[2000]',
            'misi' => 'permit_empty|max_length[3000]',
            'content_1' => 'permit_empty|max_length[3000]',
            'content_2' => 'permit_empty|max_length[3000]',
            'accreditation' => 'permit_empty|max_length[20]',
            'accreditation_label' => 'permit_empty|max_length[150]',
            'highlights' => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return $this->redirectBack('website-settings/profile', 'Periksa kembali kolom profil sekolah.');
        }

        $posted = [];
        foreach (array_keys($rules) as $field) {
            $value = $this->request->getPost($field);
            if (! is_string($value)) {
                return $this->redirectBack('website-settings/profile', 'Data profil sekolah tidak valid.');
            }
            $posted[$field] = trim($value);
        }

        if ($posted['image'] !== '' && ! $this->isSafeImageSource($posted['image'])) {
            return $this->redirectBack('website-settings/profile', 'Gambar profil harus menggunakan URL http/https atau path gambar lokal.');
        }
        $highlights = preg_split('/\R/', $posted['highlights']) ?: [];
        $highlights = array_values(array_filter(array_map('trim', $highlights), static fn (string $value): bool => $value !== ''));

        $settings = new SchoolkuWebsiteContentModel();
        $current = $settings->getSettings(self::PROFILE_DEFAULTS);
        $sections = $this->decodeArray($current['section_settings']);
        $sections['profile'] = [
            'title' => trim((string) $this->request->getPost('section_title')),
            'subtitle' => trim((string) $this->request->getPost('section_subtitle')),
            'show' => 1,
        ];
        $about = [];
        foreach (['content_title', 'image', 'visi', 'misi', 'content_1', 'content_2', 'accreditation', 'accreditation_label'] as $key) {
            $about[$key] = $posted[$key];
        }
        $about['highlights'] = $highlights;

        return $this->saveSettings('website-settings/profile', [
            'about' => json_encode($about, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'section_settings' => json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function programs()
    {
        $settings = new SchoolkuWebsiteContentModel();
        $stored = $settings->getSettings(self::PROGRAM_DEFAULTS);
        $sections = $this->decodeArray($stored['section_settings']);

        return view('panel/website-program-settings', [
            'title' => 'Pengaturan Program Sekolah',
            'activePage' => 'website-program-settings',
            'section' => $sections['programs'] ?? [],
            'programs' => $settings->getPrograms(),
            'message' => session()->getFlashdata('websiteMessage'),
            'error' => session()->getFlashdata('websiteError'),
        ]);
    }

    public function saveProgramsSection()
    {
        $rules = [
            'section_title' => 'required|max_length[150]',
            'section_subtitle' => 'permit_empty|max_length[500]',
        ];
        if (! $this->validate($rules)) {
            return $this->redirectBack('website-settings/programs', 'Judul dan deskripsi bagian program tidak valid.');
        }
        $title = $this->request->getPost('section_title');
        $subtitle = $this->request->getPost('section_subtitle');
        if (! is_string($title) || ! is_string($subtitle)) {
            return $this->redirectBack('website-settings/programs', 'Pengaturan bagian program tidak valid.');
        }

        $model = new SchoolkuWebsiteContentModel();
        $stored = $model->getSettings(self::PROGRAM_DEFAULTS);
        $sections = $this->decodeArray($stored['section_settings']);
        $sections['programs'] = [
            'title' => trim($title),
            'subtitle' => trim($subtitle),
            'show' => $this->request->getPost('show_programs') === '1' ? 1 : 0,
        ];

        return $this->saveSettings('website-settings/programs', [
            'section_settings' => json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function saveProgram()
    {
        $rules = [
            'title' => 'required|max_length[200]',
            'description' => 'permit_empty|max_length[3000]',
            'icon' => 'permit_empty|max_length[100]',
            'link_url' => 'permit_empty|max_length[255]',
            'link_text' => 'permit_empty|max_length[100]',
            'sort_order' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[255]',
            'show' => 'permit_empty|in_list[0,1]',
            'id' => 'permit_empty|is_natural_no_zero',
        ];
        if (! $this->validate($rules)) {
            return $this->redirectBack('website-settings/programs', 'Data program tidak valid.');
        }

        $idValue = $this->request->getPost('id');
        $id = is_string($idValue) && $idValue !== '' ? (int) $idValue : null;
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $icon = $this->request->getPost('icon');
        $linkText = $this->request->getPost('link_text');
        $linkUrl = $this->request->getPost('link_url');
        $sortOrder = $this->request->getPost('sort_order');
        if (
            ! is_string($title) || ! is_string($description) || ! is_string($icon)
            || ! is_string($linkText) || ! is_string($linkUrl) || ! is_string($sortOrder)
        ) {
            return $this->redirectBack('website-settings/programs', 'Data program tidak valid.');
        }
        if (! $this->isSafeLink(trim($linkUrl))) {
            return $this->redirectBack('website-settings/programs', 'URL program harus berupa tautan website, path lokal, atau anchor yang valid.');
        }
        return $this->saveProgramRecord([
            'title' => trim($title),
            'description' => trim($description),
            'icon' => trim($icon) ?: 'fas fa-book',
            'link_url' => trim($linkUrl) ?: null,
            'link_text' => trim($linkText) ?: 'Selengkapnya',
            'sort_order' => (int) $sortOrder,
            'show' => $this->request->getPost('show') === '1' ? 1 : 0,
        ], $id);
    }

    public function deleteProgram(int $id)
    {
        try {
            (new SchoolkuWebsiteContentModel())->deleteProgram($id);
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to delete website program {id}: {message}', [
                'id' => $id,
                'message' => $exception->getMessage(),
            ]);

            return $this->redirectBack('website-settings/programs', 'Program gagal dihapus.');
        }

        return redirect()->to(site_url('website-settings/programs'))
            ->with('websiteMessage', 'Program berhasil dihapus.');
    }

    private function saveProgramRecord(array $program, ?int $id)
    {
        try {
            (new SchoolkuWebsiteContentModel())->saveProgram($program, $id);
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save website program: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->redirectBack('website-settings/programs', 'Program gagal disimpan.');
        }

        return redirect()->to(site_url('website-settings/programs'))
            ->with('websiteMessage', 'Program berhasil disimpan.');
    }

    private function saveSettings(string $path, array $settings)
    {
        try {
            (new SchoolkuWebsiteContentModel())->saveSettings($settings);
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save website settings for {path}: {message}', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);

            return $this->redirectBack($path, 'Pengaturan website gagal disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url($path))
            ->with('websiteMessage', 'Pengaturan berhasil disimpan dan ditampilkan di website publik.');
    }

    private function redirectBack(string $path, string $message)
    {
        return redirect()->to(site_url($path))
            ->withInput()
            ->with('websiteError', $message);
    }

    private function decodeArray(?string $value): array
    {
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function isIsoDate(string $date): bool
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function formatRows(array $rows, array $fields): string
    {
        $lines = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $lines[] = implode('|', array_map(static fn (string $field): string => (string) ($row[$field] ?? ''), $fields));
        }

        return implode("\n", $lines);
    }

    private function parseRows(string $text, array $fields): ?array
    {
        $rows = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) !== count($fields) || in_array('', $parts, true)) {
                return null;
            }
            $rows[] = array_combine($fields, $parts);
        }

        return $rows;
    }

    private function isSafeLink(string $value): bool
    {
        if ($value === '' || preg_match('/^#[A-Za-z0-9_-]*$/', $value) === 1) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
        }

        return ! str_starts_with($value, '//')
            && preg_match('/^[A-Za-z0-9._~%\/?#=&+-]+$/', $value) === 1;
    }

    private function isSafeImageSource(string $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
        }

        return ! str_starts_with($value, '//')
            && preg_match('/^[A-Za-z0-9._~%\/-]+$/', $value) === 1;
    }

    private function removePrincipalUploads(array $paths): void
    {
        foreach ($paths as $path) {
            $this->removeManagedPrincipalPhoto((string) $path);
        }
    }

    private function removeManagedPrincipalPhoto(string $path): void
    {
        if (preg_match('#^/uploads/school-website/principals/[a-f0-9]{32}\.(?:jpg|png|webp)$#', $path) !== 1) {
            return;
        }

        $filePath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'));
        if (is_file($filePath) && ! unlink($filePath)) {
            log_message('error', 'Unable to remove replaced principal photo: {path}', ['path' => $filePath]);
        }
    }
}
