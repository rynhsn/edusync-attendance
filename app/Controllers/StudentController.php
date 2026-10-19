<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class StudentController extends ResourceController
{
    protected $studentModel;

    public function __construct()
    {
        $this->studentModel = new \App\Models\StudentModel();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $classModel = new \App\Models\ClassModel();
        $data = [
            'title' => 'Siswa',
            'activePage' => 'student',
            'students' => $this->studentModel->getAllStudent(),
            'classes' => $classModel->findAll(),
            'parents' => db_connect()->table('master_orang_tua')->orderBy('nama_orang_tua')->get()->getResultArray(),
            'importMessage' => session()->getFlashdata('studentImportMessage'),
            'importError' => session()->getFlashdata('studentImportError'),
            'canImportStudents' => \App\Libraries\ActionAccess::can('student.import'),
            'canExportStudents' => \App\Libraries\ActionAccess::can('student.export'),
        ];

        return view('student/index', $data);
    }

    public function importTemplate()
    {
        return $this->excelDownload('template-import-siswa.xlsx', [
            ['nama_siswa', 'nis', 'nisn', 'jk', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'rfid_tag', 'kelas', 'status'],
            ['[CONTOH] Nama Siswa', '2026-001', '0123456789', 'l', 'Pandeglang', '2012-05-17', 'Contoh alamat', 'TAG-CONTOH-001', 'VII A', 'CONTOH - baris ini otomatis dilewati'],
            ['[CONTOH] Nama Siswi', '2026-002', '0123456790', 'p', 'Pandeglang', '2012-08-23', 'Contoh alamat', 'TAG-CONTOH-002', 'VII A', 'CONTOH - baris ini otomatis dilewati'],
        ]);
    }

    public function export()
    {
        $students = $this->studentModel
            ->select('master_siswa.nama_siswa, master_siswa.nis, master_siswa.nisn, master_siswa.jk, master_siswa.tempat_lahir, master_siswa.tanggal_lahir, master_siswa.alamat, master_siswa.rfid_tag, master_kelas.kelas_nama AS kelas, master_siswa.status')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
            ->findAll();

        $rows = [[
            'nama_siswa',
            'nis',
            'nisn',
            'jk',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'rfid_tag',
            'kelas',
            'status',
        ]];
        foreach ($students as $student) {
            $rows[] = array_map(
                static fn ($value): string => (string) ($value ?? ''),
                $student
            );
        }

        return $this->excelDownload('data-siswa.xlsx', $rows);
    }

    public function import()
    {
        $file = $this->request->getFile('student_file');
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to(site_url('student'))
                ->with('studentImportError', 'Pilih file CSV atau TSV yang valid untuk diimpor.');
        }
        $extension = strtolower($file->getExtension());
        if (! in_array($extension, ['csv', 'tsv', 'xlsx'], true) || $file->getSize() > 5 * 1024 * 1024) {
            return redirect()->to(site_url('student'))
                ->with('studentImportError', 'File harus berformat Excel (.xlsx), CSV, atau TSV dan berukuran maksimal 5 MB.');
        }

        try {
            $spreadsheet = new \App\Libraries\StudentSpreadsheet();
            if ($extension === 'xlsx') {
                $spreadsheetRows = $spreadsheet->read($file->getTempName());
            } else {
                $spreadsheetRows = $this->readDelimitedFile($file->getTempName());
            }
        } catch (\RuntimeException $exception) {
            log_message('warning', 'Could not read uploaded student spreadsheet: {message}', [
                'message' => $exception->getMessage(),
            ]);
            return redirect()->to(site_url('student'))
                ->with('studentImportError', 'File Excel tidak valid atau tidak dapat dibaca. Gunakan template XLSX yang tersedia.');
        }
        if ($spreadsheetRows === []) {
            return redirect()->to(site_url('student'))
                ->with('studentImportError', 'File tidak berisi header atau data siswa.');
        }

        $header = array_shift($spreadsheetRows);
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) ($header[0] ?? '')) ?? (string) ($header[0] ?? '');
        $legacyColumns = ['nama_siswa', 'nis', 'nisn', 'jk', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'rfid_tag', 'kelas', 'status'];
        $legacyParentColumns = ['nama_orang_tua', 'no_whatsapp_orang_tua'];
        $header = array_map('trim', $header);
        $hasLegacyParentData = $header === array_merge($legacyColumns, $legacyParentColumns);
        if (! $hasLegacyParentData && $header !== $legacyColumns) {
            return redirect()->to(site_url('student'))
                ->with('studentImportError', 'Kolom atau urutan header tidak sesuai. Unduh dan gunakan template import yang tersedia.');
        }
        $requiredColumns = $hasLegacyParentData ? array_merge($legacyColumns, $legacyParentColumns) : $legacyColumns;

        $classes = (new \App\Models\ClassModel())->findAll();
        $classesByName = [];
        foreach ($classes as $class) {
            $classesByName[mb_strtolower(trim($class['kelas_nama']))] = (int) $class['kelas_id'];
        }

        $rows = [];
        $errors = [];
        $rowNumber = 1;
        foreach ($spreadsheetRows as $values) {
            $rowNumber++;
            if ($values === [null] || $values === [] || count(array_filter($values, static fn ($value): bool => trim((string) $value) !== '')) === 0) {
                continue;
            }
            $values = array_pad($values, count($requiredColumns), '');
            if (count($values) !== count($requiredColumns)) {
                $errors[] = "Baris {$rowNumber}: jumlah kolom tidak sesuai.";
                continue;
            }

            $item = array_combine($requiredColumns, array_map(
                static fn ($value): string => trim((string) $value),
                $values
            ));
            if ($item === false) {
                $errors[] = "Baris {$rowNumber}: data tidak dapat dibaca.";
                continue;
            }
            if (str_starts_with(mb_strtolower($item['status']), 'contoh')) {
                continue;
            }

            $gender = match (mb_strtolower($item['jk'])) {
                'l', 'laki-laki', 'laki laki' => 'l',
                'p', 'perempuan' => 'p',
                '' => null,
                default => 'invalid',
            };
            $status = match (mb_strtolower($item['status'])) {
                '', 'aktif' => 'aktif',
                'tidak_aktif', 'tidak aktif', 'nonaktif' => 'tidak_aktif',
                default => 'invalid',
            };
            $className = mb_strtolower($item['kelas']);
            $classId = $className === '' ? null : ($classesByName[$className] ?? 0);
            $birthDate = $this->normalizeImportDate($item['tanggal_lahir']);

            $rowErrors = [];
            if ($item['nama_siswa'] === '' || mb_strlen($item['nama_siswa']) > 100) {
                $rowErrors[] = 'nama_siswa wajib diisi (maksimal 100 karakter)';
            }
            if (mb_strlen($item['nis']) > 20) {
                $rowErrors[] = 'nis maksimal 20 karakter';
            }
            if (mb_strlen($item['nisn']) > 10) {
                $rowErrors[] = 'nisn maksimal 10 karakter';
            }
            if ($gender === 'invalid') {
                $rowErrors[] = 'jk harus l atau p';
            }
            if (mb_strlen($item['tempat_lahir']) > 50) {
                $rowErrors[] = 'tempat_lahir maksimal 50 karakter';
            }
            if ($birthDate === false) {
                $rowErrors[] = 'tanggal_lahir harus YYYY-MM-DD atau DD/MM/YYYY';
            }
            if (mb_strlen($item['rfid_tag']) > 50) {
                $rowErrors[] = 'rfid_tag maksimal 50 karakter';
            }
            if (mb_strlen($item['nama_orang_tua'] ?? '') > 100) {
                $rowErrors[] = 'nama_orang_tua maksimal 100 karakter';
            }
            if (mb_strlen($item['no_whatsapp_orang_tua'] ?? '') > 30
                || (($item['no_whatsapp_orang_tua'] ?? '') !== ''
                    && preg_match('/^\+?[0-9\s().-]{8,30}$/D', $item['no_whatsapp_orang_tua']) !== 1)) {
                $rowErrors[] = 'no_whatsapp_orang_tua harus berupa nomor telepon valid (maksimal 30 karakter)';
            }
            if ($hasLegacyParentData
                && trim((string) ($item['nama_orang_tua'] ?? '')) !== ''
                && trim((string) ($item['no_whatsapp_orang_tua'] ?? '')) === '') {
                $rowErrors[] = 'orang tua harus memiliki nomor WhatsApp dan terdaftar melalui menu Data Orang Tua';
            }
            if ($status === 'invalid') {
                $rowErrors[] = 'status harus aktif atau tidak_aktif';
            }
            if ($classId === 0) {
                $rowErrors[] = 'kelas tidak ditemukan; gunakan nama kelas yang terdaftar';
            }

            $parentId = null;
            if ($hasLegacyParentData && trim((string) $item['no_whatsapp_orang_tua']) !== '') {
                $parent = db_connect()->table('master_orang_tua')
                    ->select('orang_tua_id')
                    ->where('no_whatsapp', trim($item['no_whatsapp_orang_tua']))
                    ->get()
                    ->getRowArray();
                if ($parent === null) {
                    $rowErrors[] = 'orang tua belum terdaftar; tambahkan melalui menu Data Orang Tua terlebih dahulu';
                } else {
                    $parentId = (int) $parent['orang_tua_id'];
                }
            }

            if ($rowErrors !== []) {
                $errors[] = "Baris {$rowNumber}: " . implode(', ', $rowErrors) . '.';
                continue;
            }

            $rows[] = [
                'nama_siswa' => $item['nama_siswa'],
                'orang_tua_id' => $parentId,
                'nis' => $item['nis'] === '' ? null : $item['nis'],
                'nisn' => $item['nisn'] === '' ? null : $item['nisn'],
                'jk' => $gender,
                'tempat_lahir' => $item['tempat_lahir'] === '' ? null : $item['tempat_lahir'],
                'tanggal_lahir' => $birthDate === '' ? null : $birthDate,
                'alamat' => $item['alamat'] === '' ? null : $item['alamat'],
                'rfid_tag' => $item['rfid_tag'] === '' ? null : $item['rfid_tag'],
                'kelas_id' => $classId,
                'foto' => '1.png',
                'status' => $status,
            ];
            if (count($rows) > 3000) {
                $errors[] = 'Jumlah data melebihi batas 3.000 siswa dalam satu kali import.';
                break;
            }
        }

        if ($rows === [] && $errors === []) {
            $errors[] = 'File tidak berisi data siswa untuk diimpor.';
        }

        $duplicateErrors = $this->findImportDuplicates($rows);
        $errors = array_merge($errors, $duplicateErrors);
        if ($errors !== []) {
            return redirect()->to(site_url('student'))
                ->with('studentImportError', implode("\n", array_slice($errors, 0, 20))
                    . (count($errors) > 20 ? "\nDan " . (count($errors) - 20) . ' masalah lainnya.' : ''));
        }

        $db = db_connect();
        try {
            if (! $db->transBegin()) {
                throw new DatabaseException('Could not start student import transaction.');
            }
            foreach ($rows as $row) {
                if (! $this->studentModel->insert($row)) {
                    throw new DatabaseException('Student import insert returned false.');
                }
            }
            if ($db->transStatus() === false) {
                throw new DatabaseException('Student import transaction failed.');
            }
            $db->transCommit();
        } catch (DatabaseException $exception) {
            $db->transRollback();
            log_message('error', 'Student CSV import failed: {message}', ['message' => $exception->getMessage()]);
            return redirect()->to(site_url('student'))
                ->with('studentImportError', 'Import gagal dan tidak ada data yang disimpan. Periksa file lalu coba kembali.');
        }

        return redirect()->to(site_url('student'))
            ->with('studentImportMessage', count($rows) . ' data siswa berhasil diimpor.');
    }

    private function excelDownload(string $filename, array $rows): ResponseInterface
    {
        return (new \App\Libraries\StudentSpreadsheet())->download($filename, $rows);
    }

    private function readDelimitedFile(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Could not open uploaded student data file.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new \RuntimeException('Uploaded student data file is empty.');
        }
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $delimiter = $this->detectCsvDelimiter($firstLine);
        rewind($handle);
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    private function detectCsvDelimiter(string $line): string
    {
        $counts = [];
        foreach ([ "\t", ';', ',' ] as $delimiter) {
            $counts[$delimiter] = count(str_getcsv($line, $delimiter, '"', ''));
        }
        arsort($counts);

        return (string) array_key_first($counts);
    }

    private function findImportDuplicates(array $rows): array
    {
        $errors = [];
        $seen = ['nis' => [], 'nisn' => [], 'rfid_tag' => []];
        foreach ($rows as $index => $row) {
            foreach ($seen as $field => &$values) {
                $value = $row[$field];
                if ($value === null) {
                    continue;
                }
                if (isset($values[$value])) {
                    $errors[] = 'Baris data ' . ($index + 2) . ": {$field} duplikat di dalam file.";
                } else {
                    $values[$value] = true;
                }
            }
            unset($values);
        }

        $db = db_connect();
        foreach (['nis', 'nisn', 'rfid_tag'] as $field) {
            $values = array_values(array_unique(array_filter(
                array_column($rows, $field),
                static fn ($value): bool => $value !== null && $value !== ''
            )));
            if ($values === []) {
                continue;
            }
            $existing = $db->table('master_siswa')
                ->select($field)
                ->whereIn($field, $values)
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
            foreach ($existing as $student) {
                $errors[] = "Nilai {$field} " . $student[$field] . ' sudah digunakan oleh siswa lain.';
            }
        }

        return $errors;
    }

    private function normalizeImportDate(string $value): string|false
    {
        if ($value === '') {
            return '';
        }
        foreach (['!Y-m-d', '!d/m/Y', '!d-m-Y'] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date !== false
                && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }

        return false;
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $response['data'] = $this->studentModel->getAllStudent();

        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['jk_display'] = JK[$value['jk'] ?? ''] ?? '-';
        }

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $data = $this->validatedStudentInput();

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        $data['foto'] = '1.png';
        $data['status'] = 'aktif';
        $id = $this->studentModel->insert($data, true);

        if ($id === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Data siswa gagal disimpan.',
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Data siswa berhasil ditambahkan.',
            'data' => $this->studentModel->getStudent(['master_siswa.siswa_id' => $id]),
        ]);
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        if ($id === null || $this->studentModel->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Data siswa tidak ditemukan.',
            ]);
        }

        $data = $this->validatedStudentInput();

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        if (!$this->studentModel->update($id, $data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Data siswa gagal diperbarui.',
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => $this->studentModel->getStudent(['master_siswa.siswa_id' => $id]),
        ]);
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        if ($id === null || $this->studentModel->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Data siswa tidak ditemukan.',
            ]);
        }

        if (!$this->studentModel->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Data siswa gagal dihapus.',
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'message' => 'Data siswa berhasil dihapus.',
        ]);
    }

    public function setStatus($id = null)
    {
        if ($id === null || $this->studentModel->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Data siswa tidak ditemukan.',
            ]);
        }

        $status = $this->request->getPost('status');
        if (! in_array($status, ['aktif', 'tidak_aktif'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Status siswa tidak valid.',
            ]);
        }

        if (! $this->studentModel->update($id, ['status' => $status])) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Status siswa gagal diperbarui.',
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'message' => 'Status siswa berhasil diperbarui.',
            'status' => $status,
        ]);
    }

    private function validatedStudentInput(): array|\CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->request->getPost([
            'nama_siswa',
            'orang_tua_id',
            'nis',
            'nisn',
            'jk',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'rfid_tag',
            'kelas_id',
            'status',
        ]);

        $rules = [
            'nama_siswa' => 'required|max_length[100]',
            'orang_tua_id' => 'permit_empty|is_natural_no_zero',
            'nis' => 'permit_empty|max_length[20]',
            'nisn' => 'permit_empty|max_length[10]',
            'jk' => 'permit_empty|in_list[l,p]',
            'tempat_lahir' => 'permit_empty|max_length[50]',
            'tanggal_lahir' => 'permit_empty|valid_date[Y-m-d]',
            'alamat' => 'permit_empty',
            'rfid_tag' => 'permit_empty|max_length[50]',
            'kelas_id' => 'permit_empty|is_natural_no_zero',
            'status' => 'permit_empty|in_list[aktif,tidak_aktif]',
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Periksa kembali data yang dimasukkan.',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        if (empty($data['orang_tua_id'])) {
            $data['orang_tua_id'] = null;
        } elseif (db_connect()->table('master_orang_tua')
            ->where('orang_tua_id', $data['orang_tua_id'])->countAllResults() !== 1) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Data orang tua/wali yang dipilih tidak ditemukan.',
                'errors' => ['orang_tua_id' => 'Pilih data orang tua/wali yang terdaftar.'],
            ]);
        }

        if (empty($data['kelas_id'])) {
            $data['kelas_id'] = null;
        } elseif ((new \App\Models\ClassModel())->find($data['kelas_id']) === null) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Kelas yang dipilih tidak ditemukan.',
            ]);
        }

        return $data;
    }
}
