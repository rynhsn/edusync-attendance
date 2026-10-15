<?php

namespace App\Controllers;

use App\Models\SchoolkuWebsiteContentModel;
use CodeIgniter\HTTP\RedirectResponse;

class SchoolWebsiteContentController extends BaseController
{
    private const SECTIONS = [
        'achievements' => [
            'label' => 'Prestasi',
            'key' => 'website_achievements',
            'upload' => ['column' => 'photo', 'kind' => 'image'],
            'columns' => ['student_name', 'achievement', 'level', 'year'],
            'fields' => [
                'student_name' => ['label' => 'Nama siswa', 'type' => 'text', 'required' => true],
                'class_name' => ['label' => 'Kelas', 'type' => 'text'],
                'achievement' => ['label' => 'Prestasi', 'type' => 'text', 'required' => true],
                'level' => ['label' => 'Tingkat', 'type' => 'text', 'required' => true],
                'year' => ['label' => 'Tahun', 'type' => 'number'],
                'medal' => ['label' => 'Medali', 'type' => 'select', 'options' => ['' => 'Tidak ditentukan', 'Emas' => 'Emas', 'Perak' => 'Perak', 'Perunggu' => 'Perunggu', 'Partisipasi' => 'Partisipasi']],
            ],
        ],
        'extracurriculars' => [
            'label' => 'Ekstrakurikuler',
            'key' => 'website_extracurriculars',
            'columns' => ['title', 'description'],
            'fields' => [
                'title' => ['label' => 'Nama kegiatan', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
                'icon' => ['label' => 'Ikon Font Awesome', 'type' => 'text', 'max_length' => 100, 'hint' => 'Contoh: fas fa-futbol'],
                'icon_color' => ['label' => 'Warna ikon', 'type' => 'color', 'max_length' => 20],
            ],
        ],
        'testimonials' => [
            'label' => 'Testimoni',
            'key' => 'website_testimonials',
            'upload' => ['column' => 'photo', 'kind' => 'image'],
            'columns' => ['name', 'role', 'quote'],
            'fields' => [
                'name' => ['label' => 'Nama', 'type' => 'text', 'required' => true],
                'role' => ['label' => 'Keterangan', 'type' => 'text', 'hint' => 'Contoh: Alumni atau Orang Tua Siswa'],
                'quote' => ['label' => 'Isi testimoni', 'type' => 'textarea', 'required' => true],
            ],
        ],
        'gallery' => [
            'label' => 'Galeri',
            'key' => 'website_gallery',
            'upload' => ['column' => 'image', 'kind' => 'image', 'required' => true],
            'columns' => ['caption', 'category', 'is_featured'],
            'fields' => [
                'category' => ['label' => 'Kategori', 'type' => 'text'],
                'caption' => ['label' => 'Keterangan foto', 'type' => 'text', 'required' => true],
                'is_featured' => ['label' => 'Tampilkan sebagai unggulan', 'type' => 'checkbox'],
            ],
        ],
        'events' => [
            'label' => 'Agenda',
            'key' => 'website_events',
            'columns' => ['title', 'event_date', 'location'],
            'fields' => [
                'title' => ['label' => 'Nama kegiatan', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
                'location' => ['label' => 'Lokasi', 'type' => 'text'],
                'event_date' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true],
                'event_time' => ['label' => 'Waktu', 'type' => 'text', 'hint' => 'Contoh: 08.00–10.00 WIB'],
            ],
        ],
        'faq' => [
            'label' => 'FAQ',
            'key' => 'website_faq',
            'columns' => ['question', 'answer', 'category'],
            'fields' => [
                'question' => ['label' => 'Pertanyaan', 'type' => 'text', 'required' => true],
                'answer' => ['label' => 'Jawaban', 'type' => 'textarea', 'required' => true],
                'category' => ['label' => 'Kategori', 'type' => 'text'],
            ],
        ],
        'downloads' => [
            'label' => 'Download PDF',
            'key' => 'website_downloads',
            'upload' => ['column' => 'file_path', 'kind' => 'pdf', 'required' => true],
            'columns' => ['title', 'category', 'file_size'],
            'fields' => [
                'category' => ['label' => 'Kategori', 'type' => 'text', 'required' => true, 'hint' => 'Contoh: Jadwal Pelajaran'],
                'title' => ['label' => 'Nama file', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
            ],
        ],
    ];

    public static function sectionData(): array
    {
        $defaults = [];
        foreach (self::SECTIONS as $config) {
            $defaults[$config['key']] = '[]';
        }

        $settings = (new SchoolkuWebsiteContentModel())->getSettings($defaults);
        $sections = [];
        foreach (self::SECTIONS as $name => $config) {
            $items = json_decode((string) $settings[$config['key']], true);
            $items = is_array($items) ? $items : [];
            $items = array_values(array_filter($items, 'is_array'));
            $sections[$name] = $items;
        }

        return $sections;
    }

    public function index(string $section = 'achievements')
    {
        if (! isset(self::SECTIONS[$section])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $config = self::SECTIONS[$section];
        $items = self::sectionData()[$section];
        usort($items, static fn (array $a, array $b): int =>
            ((int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0))
            ?: ((int) ($b['id'] ?? 0) <=> (int) ($a['id'] ?? 0))
        );

        $editId = filter_var($this->request->getGet('edit'), FILTER_VALIDATE_INT);
        $item = [];
        foreach ($items as $record) {
            if ((int) ($record['id'] ?? 0) === (int) $editId && $editId !== false) {
                $item = $record;
                break;
            }
        }

        return view('panel/website-content', [
            'title' => 'Konten Website - ' . $config['label'],
            'activePage' => 'website-content-' . $section,
            'sections' => self::SECTIONS,
            'sectionKey' => $section,
            'config' => $config,
            'items' => $items,
            'item' => $item,
            'message' => session()->getFlashdata('websiteContentMessage'),
            'error' => session()->getFlashdata('websiteContentError'),
        ]);
    }

    public function save(string $section): RedirectResponse
    {
        if (! isset(self::SECTIONS[$section])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $config = self::SECTIONS[$section];
        $idInput = $this->request->getPost('id');
        if (! is_string($idInput) && $idInput !== null) {
            return $this->redirectWithError($section, 'ID konten tidak valid.');
        }
        $id = (int) $idInput;
        $items = self::sectionData()[$section];
        $index = null;
        foreach ($items as $itemIndex => $record) {
            if ($id > 0 && (int) ($record['id'] ?? 0) === $id) {
                $index = $itemIndex;
                break;
            }
        }
        if ($id > 0 && $index === null) {
            return $this->redirectWithError($section, 'Data yang ingin diperbarui tidak ditemukan.', $id);
        }
        $existing = $index === null ? [] : $items[$index];

        $record = [];
        foreach ($config['fields'] as $key => $field) {
            if ($field['type'] === 'checkbox') {
                $record[$key] = $this->request->getPost($key) === '1' ? 1 : 0;
                continue;
            }
            $value = $this->request->getPost($key);
            if (! is_string($value)) {
                return $this->redirectWithError($section, 'Format data tidak valid.', $id ?: null);
            }
            $value = trim($value);
            if (($field['required'] ?? false) && $value === '') {
                return $this->redirectWithError($section, 'Kolom "' . $field['label'] . '" wajib diisi.', $id ?: null);
            }
            if (mb_strlen($value) > ($field['type'] === 'textarea' ? 5000 : ($field['max_length'] ?? 255))) {
                return $this->redirectWithError($section, 'Kolom "' . $field['label'] . '" terlalu panjang.', $id ?: null);
            }
            if ($field['type'] === 'number' && $value !== '' && (! ctype_digit($value) || (int) $value < 1900 || (int) $value > 2200)) {
                return $this->redirectWithError($section, 'Tahun prestasi tidak valid.', $id ?: null);
            }
            if ($field['type'] === 'date' && $value !== '') {
                $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
                if ($date === false || $date->format('Y-m-d') !== $value) {
                    return $this->redirectWithError($section, 'Tanggal kegiatan tidak valid.', $id ?: null);
                }
            }
            if ($field['type'] === 'select' && ! array_key_exists($value, $field['options'])) {
                return $this->redirectWithError($section, 'Pilihan "' . $field['label'] . '" tidak valid.', $id ?: null);
            }
            if ($field['type'] === 'color' && $value !== '' && preg_match('/^#[0-9a-fA-F]{6}$/', $value) !== 1) {
                return $this->redirectWithError($section, 'Warna ikon harus berupa kode warna hex.', $id ?: null);
            }
            $record[$key] = $value;
        }

        $sortOrder = $this->request->getPost('sort_order');
        if (! is_string($sortOrder) || ($sortOrder !== '' && (! ctype_digit($sortOrder) || (int) $sortOrder > 1000000))) {
            return $this->redirectWithError($section, 'Urutan harus berupa bilangan bulat positif.', $id ?: null);
        }

        $record['id'] = $id > 0 ? $id : $this->nextId($items);
        $record['sort_order'] = $sortOrder === '' ? 0 : (int) $sortOrder;
        $record['show'] = $this->request->getPost('show') === '1' ? 1 : 0;
        $record['created_at'] = $existing['created_at'] ?? date('Y-m-d H:i:s');
        $record['updated_at'] = date('Y-m-d H:i:s');

        $upload = $config['upload'] ?? null;
        $newFile = null;
        if ($upload !== null) {
            $file = $this->request->getFile('upload');
            if ($file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE) {
                $newFile = $this->storeUpload($file, $section, $upload['kind']);
                if ($newFile === null) {
                    return $this->redirectWithError(
                        $section,
                        $upload['kind'] === 'pdf'
                            ? 'Unggah file PDF yang valid, maksimal 10 MB.'
                            : 'Unggah foto JPG, PNG, atau WebP yang valid, maksimal 5 MB.',
                        $id ?: null
                    );
                }
                $record[$upload['column']] = $newFile;
                if ($upload['kind'] === 'pdf') {
                    $record['file_size'] = $this->formatFileSize($file->getSize());
                }
            } elseif (isset($existing[$upload['column']])) {
                $record[$upload['column']] = $existing[$upload['column']];
                if (isset($existing['file_size'])) {
                    $record['file_size'] = $existing['file_size'];
                }
            } elseif ($upload['required'] ?? false) {
                return $this->redirectWithError($section, 'Silakan pilih file untuk diunggah.', $id ?: null);
            }
        }

        if ($index === null) {
            $items[] = $record;
        } else {
            $items[$index] = $record;
        }

        if (! $this->saveSection($config, $items)) {
            if ($newFile !== null) {
                $this->removeManagedFile($section, $newFile);
            }

            return $this->redirectWithError($section, 'Konten gagal disimpan. Silakan periksa akses penyimpanan dan coba kembali.', $id ?: null);
        }

        if ($newFile !== null && ! empty($existing[$upload['column']])) {
            $this->removeManagedFile($section, $existing[$upload['column']]);
        }

        return redirect()->to(site_url('website-content/' . $section))
            ->with('websiteContentMessage', 'Konten berhasil disimpan.');
    }

    public function delete(string $section, int $id): RedirectResponse
    {
        if (! isset(self::SECTIONS[$section])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $config = self::SECTIONS[$section];
        $items = self::sectionData()[$section];
        $index = null;
        foreach ($items as $itemIndex => $record) {
            if ((int) ($record['id'] ?? 0) === $id) {
                $index = $itemIndex;
                break;
            }
        }
        if ($index === null) {
            return redirect()->to(site_url('website-content/' . $section))
                ->with('websiteContentError', 'Data yang ingin dihapus tidak ditemukan.');
        }

        $deletedItem = $items[$index];
        array_splice($items, $index, 1);
        if (! $this->saveSection($config, $items)) {
            return redirect()->to(site_url('website-content/' . $section))
                ->with('websiteContentError', 'Konten gagal dihapus. Silakan coba kembali.');
        }

        if (isset($config['upload']) && ! empty($deletedItem[$config['upload']['column']])) {
            $this->removeManagedFile($section, $deletedItem[$config['upload']['column']]);
        }

        return redirect()->to(site_url('website-content/' . $section))
            ->with('websiteContentMessage', 'Konten berhasil dihapus.');
    }

    private function saveSection(array $config, array $items): bool
    {
        try {
            $json = json_encode(array_values($items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            (new SchoolkuWebsiteContentModel())->saveSettings([$config['key'] => $json]);

            return true;
        } catch (\JsonException|\CodeIgniter\Database\Exceptions\DatabaseException $exception) {
            log_message('error', 'Unable to save school website section {section}: {message}', [
                'section' => $config['key'],
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function storeUpload($file, string $section, string $kind): ?string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        if ($file->getSize() > ($kind === 'pdf' ? 10 : 5) * 1024 * 1024) {
            return null;
        }

        if ($kind === 'pdf') {
            if ($file->getMimeType() !== 'application/pdf') {
                return null;
            }
            $handle = fopen($file->getTempName(), 'rb');
            if ($handle === false) {
                return null;
            }
            $signature = fread($handle, 5);
            fclose($handle);
            if ($signature !== '%PDF-') {
                return null;
            }
            $extension = 'pdf';
        } else {
            $image = @getimagesize($file->getTempName());
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if ($image === false || ! isset($extensions[$image['mime'] ?? ''])) {
                return null;
            }
            $extension = $extensions[$image['mime']];
        }

        $directory = $kind === 'pdf'
            ? WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'school-website' . DIRECTORY_SEPARATOR . $section
            : FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'school-website' . DIRECTORY_SEPARATOR . $section;
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            log_message('error', 'Unable to create school website upload directory: {directory}', ['directory' => $directory]);

            return null;
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        if (! $file->move($directory, $filename)) {
            log_message('error', 'Unable to move uploaded school website file into place.');

            return null;
        }

        return $kind === 'pdf' ? $filename : '/uploads/school-website/' . $section . '/' . $filename;
    }

    private function removeManagedFile(string $section, string $relativePath): void
    {
        if ($section === 'downloads') {
            if (preg_match('/^[a-f0-9]{32}\.pdf$/', $relativePath) !== 1) {
                return;
            }
            $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'school-website'
                . DIRECTORY_SEPARATOR . $section . DIRECTORY_SEPARATOR . $relativePath;
        } else {
            if (preg_match(
                '#^/uploads/school-website/' . preg_quote($section, '#') . '/[a-f0-9]{32}\.(?:jpg|png|webp)$#',
                $relativePath
            ) !== 1) {
                return;
            }
            $path = FCPATH . ltrim($relativePath, '/');
        }

        if (is_file($path) && ! unlink($path)) {
            log_message('error', 'Unable to remove school website upload: {path}', ['path' => $path]);
        }
    }

    private function nextId(array $items): int
    {
        $ids = array_map(static fn (array $item): int => (int) ($item['id'] ?? 0), $items);

        return ($ids === [] ? 0 : max($ids)) + 1;
    }

    private function formatFileSize(int $bytes): string
    {
        return $bytes >= 1024 * 1024
            ? number_format($bytes / (1024 * 1024), 1) . ' MB'
            : number_format($bytes / 1024, 0) . ' KB';
    }

    private function redirectWithError(string $section, string $message, ?int $editId = null): RedirectResponse
    {
        $url = 'website-content/' . $section;
        if ($editId !== null) {
            $url .= '?edit=' . $editId;
        }

        return redirect()->to(site_url($url))
            ->withInput()
            ->with('websiteContentError', $message);
    }
}
