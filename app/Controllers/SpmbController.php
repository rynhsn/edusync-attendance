<?php

namespace App\Controllers;

use App\Libraries\SpmbProofPdf;
use App\Libraries\SpmbSchedule;
use App\Models\AdminNotificationModel;
use App\Models\MasterExtracurricularModel;
use App\Models\SchoolContactSettingsModel;
use App\Models\SpmbApplicationModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\ResponseInterface;

class SpmbController extends BaseController
{
    private const TEXT_FIELDS = [
        'contact_email', 'contact_phone', 'parent_phone',
        'student_nik', 'student_nisn', 'student_name', 'gender', 'birth_place', 'birth_date',
        'address', 'rt', 'rw', 'postal_code', 'province', 'regency', 'district', 'village',
        'previous_school', 'graduation_year', 'education_level',
        'parent_name', 'parent_relationship', 'parent_job',
    ];

    private const DOCUMENTS = [
        'family_card' => ['label' => 'Kartu Keluarga (KK)', 'required' => true, 'image_only' => false],
        'birth_certificate' => ['label' => 'Akta kelahiran / surat keterangan lahir', 'required' => true, 'image_only' => false],
        'guardian_identity' => ['label' => 'KTP orang tua / wali', 'required' => true, 'image_only' => false],
        'previous_school_certificate' => ['label' => 'Ijazah / SKL jenjang sebelumnya', 'required' => true, 'image_only' => false],
        'recent_photo' => ['label' => 'Pas foto terbaru', 'required' => true, 'image_only' => true],
    ];

    public function form(): ResponseInterface|string
    {
        $schedule = (new SpmbSchedule())->status();
        if (! $schedule['is_open']) {
            return $this->response
                ->setStatusCode(403)
                ->setBody(view('sekolahku/spmb-closed', $this->publicPageData() + [
                    'schedule' => $schedule,
                ]));
        }

        return view('sekolahku/spmb', $this->publicPageData() + [
            'errors' => session()->getFlashdata('spmbErrors') ?? [],
            'error' => session()->getFlashdata('spmbError'),
            'old' => session()->getFlashdata('_ci_old_input')['post'] ?? [],
            'documentFields' => self::DOCUMENTS,
        ]);
    }

    public function submit(): ResponseInterface|string
    {
        if (! (new SpmbSchedule())->status()['is_open']) {
            return redirect()->to(site_url('news/spmb'));
        }

        $honeypot = $this->request->getPost('website');
        if (is_string($honeypot) && trim($honeypot) !== '') {
            return $this->formError('Formulir tidak dapat diproses.');
        }

        $rules = [
            'contact_email' => 'required|valid_email|max_length[254]',
            'contact_phone' => 'required|regex_match[/^[0-9+().\-\s]{8,25}$/]',
            'parent_phone' => 'required|regex_match[/^[0-9+().\-\s]{8,25}$/]',
            'student_nik' => 'required|regex_match[/^[0-9]{16}$/]',
            'student_nisn' => 'required|regex_match[/^[0-9]{10}$/]',
            'student_name' => 'required|max_length[150]',
            'gender' => 'required|in_list[L,P]',
            'birth_place' => 'required|max_length[100]',
            'birth_date' => 'required|valid_date[Y-m-d]',
            'address' => 'required|max_length[1000]',
            'rt' => 'required|regex_match[/^[0-9]{1,3}$/]',
            'rw' => 'required|regex_match[/^[0-9]{1,3}$/]',
            'postal_code' => 'required|regex_match[/^[0-9]{5,6}$/]',
            'province' => 'required|max_length[100]',
            'regency' => 'required|max_length[100]',
            'district' => 'required|max_length[100]',
            'village' => 'required|max_length[100]',
            'previous_school' => 'required|max_length[200]',
            'graduation_year' => 'required|regex_match[/^(19|20)[0-9]{2}$/]',
            'education_level' => 'required|in_list[SD,SMP,SMA,SMK]',
            'parent_name' => 'required|max_length[150]',
            'parent_relationship' => 'required|in_list[ayah,ibu,wali]',
            'parent_job' => 'required|max_length[150]',
            'declaration' => 'required|in_list[1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('news/spmb'))
                ->withInput()
                ->with('spmbErrors', $this->validator->getErrors())
                ->with('spmbError', 'Periksa kembali data pendaftaran yang ditandai.');
        }

        $data = [];
        foreach (self::TEXT_FIELDS as $field) {
            $value = $this->request->getPost($field);
            if (! is_string($value)) {
                return $this->formError('Data pendaftaran tidak valid.');
            }
            $data[$field] = trim($value);
        }
        $files = [];
        foreach (self::DOCUMENTS as $key => $document) {
            $file = $this->request->getFile($key);
            if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
                if ($document['required']) {
                    return $this->formError('Dokumen wajib diunggah: ' . $document['label'] . '.');
                }
                continue;
            }

            $validationMessage = $this->validateDocument($file, $document['image_only']);
            if ($validationMessage !== null) {
                return $this->formError($document['label'] . ': ' . $validationMessage);
            }
            $files[$key] = $file;
        }

        $registrationCode = 'SPMB-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
        $uploadDirectory = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'spmb' . DIRECTORY_SEPARATOR . $registrationCode;
        if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0700, true) && ! is_dir($uploadDirectory)) {
            log_message('error', 'Unable to create private SPMB document storage directory.');

            return $this->formError('Penyimpanan dokumen sedang tidak tersedia. Silakan coba kembali.');
        }

        $storedDocuments = [];
        foreach ($files as $key => $file) {
            $mime = $file->getMimeType();
            $extension = match ($mime) {
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                default => null,
            };
            if ($extension === null) {
                $this->removeUploadedFiles($uploadDirectory, $storedDocuments);

                return $this->formError('Jenis berkas tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG.');
            }

            $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
            try {
                $file->move($uploadDirectory, $storedName);
            } catch (\RuntimeException $exception) {
                log_message('error', 'Failed to move an SPMB document: {message}', ['message' => $exception->getMessage()]);
                $this->removeUploadedFiles($uploadDirectory, $storedDocuments);

                return $this->formError('Dokumen gagal disimpan. Periksa ukuran berkas lalu coba kembali.');
            }

            $storedPath = $uploadDirectory . DIRECTORY_SEPARATOR . $storedName;
            if (! chmod($uploadDirectory, 0700) || ! chmod($storedPath, 0600)) {
                log_message('error', 'Unable to restrict permissions on private SPMB document storage.');
                $this->removeUploadedFiles($uploadDirectory, $storedDocuments + [$key => ['stored_name' => $storedName]]);

                return $this->formError('Dokumen gagal diamankan saat disimpan. Silakan coba kembali.');
            }

            $storedDocuments[$key] = [
                'label' => self::DOCUMENTS[$key]['label'],
                'stored_name' => $storedName,
                'original_name' => $this->safeDownloadName($file->getClientName(), $key . '.' . $extension),
                'mime' => $mime,
                'size' => $file->getSize(),
            ];
        }

        $application = [
            'registration_code' => $registrationCode,
            'student_name' => $data['student_name'],
            'student_nik' => $data['student_nik'],
            'student_nisn' => $data['student_nisn'],
            'contact_email' => $data['contact_email'],
            'application_data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'documents' => json_encode($storedDocuments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'status' => 'submitted',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            (new SpmbApplicationModel())->create($application);
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save SPMB application to EduSync: {message}', [
                'message' => $exception->getMessage(),
            ]);
            $this->removeUploadedFiles($uploadDirectory, $storedDocuments);

            return $this->formError('Pendaftaran belum dapat disimpan. Silakan coba kembali atau hubungi panitia.');
        }

        (new AdminNotificationModel())->create(
            'spmb_application',
            'Pendaftaran SPMB baru',
            $data['student_name'] . ' mengirim pendaftaran ' . $registrationCode . '.',
            'spmb/applications'
        );

        $proof = SpmbProofPdf::generate($registrationCode, $application);

        return $this->response
            ->download('bukti-pendaftaran-' . strtolower($registrationCode) . '.pdf', $proof, true)
            ->setContentType('application/pdf');
    }

    private function publicPageData(): array
    {
        $contact = (new SchoolContactSettingsModel())->getSettings();

        return [
            'page_title' => 'SPMB Online - SekolahKu',
            'site_name' => 'SekolahKu',
            'site_tagline' => 'Pendaftaran peserta didik baru',
            'site_description' => 'Formulir pendaftaran SPMB Online SekolahKu.',
            'site_logo_text' => 'SekolahKu',
            'site_logo_icon' => 'fas fa-graduation-cap',
            'contact_phone' => $contact['phone'],
            'contact_email' => $contact['email'],
            'contact_address' => $contact['address'],
            'contact_hours' => $contact['hours'],
            'social_facebook' => '',
            'social_instagram' => '',
            'social_youtube' => '',
            'social_tiktok' => '',
            'footer_description' => 'Formulir pendaftaran peserta didik baru SekolahKu.',
            'footer_copyright' => 'All rights reserved.',
            'footer_services' => (new MasterExtracurricularModel())->getActiveActivities(),
            'footer_links' => [],
            'spmb_url' => site_url('news/spmb'),
            'theme_color' => 'default',
        ];
    }

    public function applications()
    {
        $applications = (new SpmbApplicationModel())->getLatest();
        foreach ($applications as &$application) {
            $application['application_data'] = json_decode($application['application_data'], true) ?: [];
            $application['documents'] = json_decode($application['documents'], true) ?: [];
        }
        unset($application);

        return view('panel/spmb-applications', [
            'title' => 'Pendaftar SPMB',
            'activePage' => 'spmb-applications',
            'applications' => $applications,
        ]);
    }

    public function proof(int $id): ResponseInterface
    {
        $application = (new SpmbApplicationModel())->findById($id);
        if ($application === null) {
            return $this->response->setStatusCode(404);
        }

        $applicationData = json_decode($application['application_data'], true) ?: [];
        $mergedApplication = array_merge($applicationData, $application);
        $proof = SpmbProofPdf::generate($application['registration_code'], $mergedApplication);

        return $this->response
            ->download('bukti-pendaftaran-' . strtolower($application['registration_code']) . '.pdf', $proof, true)
            ->setContentType('application/pdf');
    }

    public function document(int $id, string $key): ResponseInterface
    {
        if (! array_key_exists($key, self::DOCUMENTS)) {
            return $this->response->setStatusCode(404);
        }

        $application = (new SpmbApplicationModel())->findById($id);
        if ($application === null) {
            return $this->response->setStatusCode(404);
        }

        $documents = json_decode($application['documents'], true) ?: [];
        $document = $documents[$key] ?? null;
        if (! is_array($document) || ! isset($document['stored_name'])) {
            return $this->response->setStatusCode(404);
        }
        $storedName = basename((string) $document['stored_name']);
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'spmb' . DIRECTORY_SEPARATOR
            . $application['registration_code'] . DIRECTORY_SEPARATOR . $storedName;
        if (! is_file($path)) {
            return $this->response->setStatusCode(404);
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            log_message('error', 'Unable to read a private SPMB document for download.');

            return $this->response->setStatusCode(500);
        }

        $extension = strtolower(pathinfo($storedName, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => null,
        };
        if ($mime === null) {
            log_message('error', 'Unsupported stored SPMB document type for application {id}.', ['id' => $id]);

            return $this->response->setStatusCode(500);
        }

        $downloadName = $this->safeDownloadName(
            (string) ($document['original_name'] ?? ''),
            $key . '.' . $extension
        );
        if ($this->request->getGet('view') === '1') {
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="' . $downloadName . '"')
                ->setHeader('X-Content-Type-Options', 'nosniff')
                ->setHeader('Cache-Control', 'private, no-store')
                ->setBody($contents);
        }

        return $this->response
            ->download($downloadName, $contents, true)
            ->setContentType($mime);
    }

    private function formError(string $message): ResponseInterface
    {
        return redirect()->to(site_url('news/spmb'))
            ->withInput()
            ->with('spmbError', $message);
    }

    private function validateDocument(UploadedFile $file, bool $imageOnly): ?string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return 'Berkas tidak berhasil diunggah.';
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            return 'Ukuran maksimal setiap berkas adalah 2 MB.';
        }

        $mime = $file->getMimeType();
        $allowed = $imageOnly
            ? ['image/jpeg', 'image/png']
            : ['application/pdf', 'image/jpeg', 'image/png'];
        if (! in_array($mime, $allowed, true)) {
            return $imageOnly
                ? 'Gunakan foto berformat JPEG atau PNG.'
                : 'Gunakan berkas berformat PDF, JPEG, atau PNG.';
        }

        return null;
    }

    private function removeUploadedFiles(string $directory, array $documents): void
    {
        foreach ($documents as $document) {
            $path = $directory . DIRECTORY_SEPARATOR . basename($document['stored_name']);
            if (is_file($path) && ! unlink($path)) {
                log_message('error', 'Unable to clean up an incomplete SPMB upload.');
            }
        }
        if (is_dir($directory) && ! rmdir($directory)) {
            log_message('error', 'Unable to clean up an incomplete SPMB upload directory.');
        }
    }

    private function safeDownloadName(string $name, string $fallback): string
    {
        $name = str_replace('\\', '/', $name);
        $name = basename($name);
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name) ?? '';
        $fallback = str_replace('\\', '/', $fallback);
        $fallback = basename($fallback);
        $fallback = preg_replace('/[^A-Za-z0-9._-]/', '_', $fallback) ?? '';

        return $name !== '' ? $name : ($fallback !== '' ? $fallback : 'download');
    }
}
