<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

class MasterDataSpreadsheetController extends Controller
{
    private BaseConnection $db;

    private array $types = [
        'major' => [
            'title' => 'jurusan',
            'table' => 'master_jurusan',
            'key' => 'jurusan_id',
            'columns' => ['jurusan_nama', 'jurusan_kode', 'jurusan_keterangan'],
            'sample' => ['[CONTOH] Teknik Komputer dan Jaringan', 'TKJ-CONTOH', 'Contoh - baris ini otomatis dilewati'],
            'unique' => ['jurusan_kode'],
            'route' => 'major',
        ],
        'class' => [
            'title' => 'kelas',
            'table' => 'master_kelas',
            'key' => 'kelas_id',
            'columns' => ['kelas_tingkat', 'kelas_nama', 'kelas_keterangan'],
            'sample' => ['7', '[CONTOH] VII A', 'Contoh - baris ini otomatis dilewati'],
            'unique' => [],
            'route' => 'class',
        ],
        'subjects' => [
            'title' => 'mapel',
            'table' => 'master_mapel',
            'key' => 'mapel_id',
            'columns' => ['mapel_kode', 'mapel_nama', 'kelompok_rapor', 'mapel_keterangan'],
            'sample' => ['MAPEL-CONTOH', '[CONTOH] Matematika', 'nasional', 'Contoh - baris ini otomatis dilewati'],
            'unique' => ['mapel_kode'],
            'route' => 'subjects',
        ],
        'teachers' => [
            'title' => 'guru',
            'table' => 'master_guru',
            'key' => 'guru_id',
            'columns' => ['guru_nip', 'guru_nama', 'guru_email', 'guru_telepon'],
            'sample' => ['NIP-CONTOH', '[CONTOH] Nama Guru', 'contoh@example.test', '081234567890'],
            'unique' => ['guru_nip'],
            'route' => 'teachers',
        ],
        'parents' => [
            'title' => 'orang-tua',
            'table' => 'master_orang_tua',
            'key' => 'orang_tua_id',
            'columns' => ['nama_orang_tua', 'no_whatsapp', 'nama_siswa'],
            'sample' => ['[CONTOH] Nama Orang Tua', '081234567890', '[CONTOH] Nama Siswa 1 | Nama Siswa 2'],
            'unique' => ['no_whatsapp'],
            'route' => 'parents',
        ],
        'lesson-periods' => [
            'title' => 'jam-pelajaran',
            'table' => 'master_jam_pelajaran',
            'key' => 'jam_pelajaran_id',
            'columns' => ['jam_ke', 'jam_nama', 'jam_mulai', 'jam_selesai'],
            'sample' => ['1', '[CONTOH] Jam Pertama', '07:00:00', '07:45:00'],
            'unique' => ['jam_ke'],
            'route' => 'lesson-periods',
        ],
        'academic-years' => [
            'title' => 'tahun-ajaran',
            'table' => 'master_tahun_ajaran',
            'key' => 'tahun_ajaran_id',
            'columns' => ['tahun_ajaran_nama', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_active'],
            'sample' => ['[CONTOH] 2026/2027', '1', '2026-07-01', '2026-12-31', '0'],
            'unique' => [],
            'route' => 'academic-years',
        ],
    ];

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function template(string $type): ResponseInterface
    {
        $config = $this->getType($type);
        $spreadsheet = new \App\Libraries\StudentSpreadsheet();

        return $spreadsheet->download(
            'template-import-' . $config['title'] . '.xlsx',
            [$config['columns'], $config['sample']]
        );
    }

    public function export(string $type): ResponseInterface
    {
        $config = $this->getType($type);
        $rows = [$config['columns']];
        if ($type === 'parents') {
            $parents = $this->db->table('master_orang_tua')
                ->orderBy('orang_tua_id', 'ASC')
                ->get()
                ->getResultArray();
            foreach ($parents as $parent) {
                $students = $this->db->table('master_siswa')
                    ->select('nama_siswa')
                    ->where('orang_tua_id', $parent['orang_tua_id'])
                    ->orderBy('nama_siswa', 'ASC')
                    ->get()
                    ->getResultArray();
                $rows[] = [
                    (string) $parent['nama_orang_tua'],
                    (string) $parent['no_whatsapp'],
                    implode(' | ', array_column($students, 'nama_siswa')),
                ];
            }
        } else {
            $records = $this->db->table($config['table'])
                ->select(implode(', ', $config['columns']))
                ->orderBy($config['key'], 'ASC')
                ->get()
                ->getResultArray();
        foreach ($records as $record) {
            $rows[] = array_map(
                static fn ($column): string => (string) ($record[$column] ?? ''),
                $config['columns']
            );
        }
        }

        return (new \App\Libraries\StudentSpreadsheet())->download(
            'data-' . $config['title'] . '.xlsx',
            $rows
        );
    }

    public function import(string $type)
    {
        $config = $this->getType($type);
        $file = $this->request->getFile('master_data_file');
        $redirect = redirect()->to(site_url($config['route']));

        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return $redirect->with('masterDataImportError', 'Pilih file XLSX, CSV, atau TSV yang valid untuk diimpor.');
        }

        $extension = strtolower($file->getExtension());
        if (! in_array($extension, ['xlsx', 'csv', 'tsv'], true) || $file->getSize() > 5 * 1024 * 1024) {
            return $redirect->with('masterDataImportError', 'File harus berformat XLSX, CSV, atau TSV dan berukuran maksimal 5 MB.');
        }

        try {
            $spreadsheet = new \App\Libraries\StudentSpreadsheet();
            $rows = $extension === 'xlsx'
                ? $spreadsheet->read($file->getTempName())
                : $this->readDelimitedFile($file->getTempName());
        } catch (RuntimeException $exception) {
            log_message('warning', 'Could not read uploaded {type} spreadsheet: {message}', [
                'type' => $type,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('masterDataImportError', 'File tidak valid atau tidak dapat dibaca. Unduh template XLSX yang tersedia.');
        }

        if ($rows === []) {
            return $redirect->with('masterDataImportError', 'File tidak berisi header atau data.');
        }

        $header = array_map('trim', array_shift($rows));
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) ($header[0] ?? '')) ?? (string) ($header[0] ?? '');
        if ($header !== $config['columns']) {
            return $redirect->with('masterDataImportError', 'Kolom atau urutan header tidak sesuai. Unduh dan gunakan template import yang tersedia.');
        }

        [$records, $errors] = $this->validateRows($type, $config, $rows);
        if ($records === [] && $errors === []) {
            $errors[] = 'File tidak berisi data untuk diimpor.';
        }
        if (count($records) > 3000) {
            $errors[] = 'Jumlah data melebihi batas 3.000 baris dalam satu kali impor.';
        }
        if ($errors !== []) {
            return $redirect->with(
                'masterDataImportError',
                implode("\n", array_slice($errors, 0, 20))
                    . (count($errors) > 20 ? "\nDan " . (count($errors) - 20) . ' masalah lainnya.' : '')
            );
        }

        try {
            if (! $this->db->transBegin()) {
                throw new DatabaseException('Could not start master data import transaction.');
            }

            $academicDataController = $type === 'teachers' ? new AcademicDataController() : null;
            foreach ($records as $record) {
                $studentIds = $record['_student_ids'] ?? [];
                unset($record['_student_ids']);
                if ($type === 'academic-years' && $record['is_active'] === 1
                    && ! $this->db->table('master_tahun_ajaran')->where('is_active', 1)->update(['is_active' => 0])) {
                    throw new DatabaseException('Could not deactivate current academic year.');
                }
                if (! $this->db->table($config['table'])->insert($record)) {
                    throw new DatabaseException('Master data insert returned false.');
                }

                if ($type === 'parents' && $studentIds !== []) {
                    $parentId = (int) $this->db->insertID();
                    if (! $this->db->table('master_siswa')->whereIn('siswa_id', $studentIds)->update(['orang_tua_id' => $parentId])) {
                        throw new DatabaseException('Could not save imported parent-student links.');
                    }
                } elseif ($type === 'teachers') {
                    $academicDataController->syncImportedTeacher((int) $this->db->insertID(), $record);
                } elseif ($type === 'class') {
                    $classId = (int) $this->db->insertID();
                    $defaults = [];
                    foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day) {
                        $defaults[] = [
                            'kelas_id' => $classId,
                            'hari' => $day,
                            'jam_masuk' => '07:00:00',
                            'jam_pulang' => '14:00:00',
                        ];
                    }
                    if (! $this->db->table('absensi_konfigurasi_waktu')->insertBatch($defaults)) {
                        throw new DatabaseException('Could not create default class time settings.');
                    }
                }
            }

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Master data import transaction failed.');
            }
            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            log_message('error', 'Master data import failed for {type}: {message}', [
                'type' => $type,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('masterDataImportError', 'Import gagal dan tidak ada data yang disimpan. Periksa file lalu coba kembali.');
        }

        return $redirect->with(
            'masterDataImportMessage',
            count($records) . ' data ' . $config['title'] . ' berhasil diimpor.'
        );
    }

    private function validateRows(string $type, array $config, array $rows): array
    {
        $records = [];
        $errors = [];
        $seen = [];
        foreach ($config['unique'] as $column) {
            $seen[$column] = [];
        }

        foreach ($config['unique'] as $column) {
            $existing = $this->db->table($config['table'])
                ->select($column)
                ->where($column . ' !=', '')
                ->get()
                ->getResultArray();
            foreach ($existing as $record) {
                if ($record[$column] !== null) {
                    $seen[$column][mb_strtolower(trim((string) $record[$column]))] = true;
                }
            }
        }
        if ($type === 'class') {
            foreach ($this->db->table('master_kelas')->select('kelas_tingkat, kelas_nama')->get()->getResultArray() as $class) {
                $seen['class'][(int) $class['kelas_tingkat'] . ':' . mb_strtolower(trim((string) $class['kelas_nama']))] = true;
            }
        }
        if ($type === 'academic-years') {
            foreach ($this->db->table('master_tahun_ajaran')->select('tahun_ajaran_nama, semester')->get()->getResultArray() as $year) {
                $seen['academic-year'][mb_strtolower(trim($year['tahun_ajaran_nama'])) . ':' . (int) $year['semester']] = true;
            }
        }
        $studentsByName = [];
        if ($type === 'parents') {
            foreach ($this->db->table('master_siswa')->select('siswa_id, nama_siswa')->get()->getResultArray() as $student) {
                $studentsByName[mb_strtolower(trim($student['nama_siswa']))][] = (int) $student['siswa_id'];
            }
        }
        $assignedStudentIds = [];
        $activeAcademicYears = 0;

        foreach ($rows as $index => $values) {
            $rowNumber = $index + 2;
            if ($values === [] || count(array_filter($values, static fn ($value): bool => trim((string) $value) !== '')) === 0) {
                continue;
            }
            if (count($values) > count($config['columns'])) {
                $errors[] = "Baris {$rowNumber}: jumlah kolom tidak sesuai.";
                continue;
            }

            $values = array_pad($values, count($config['columns']), '');
            $item = array_combine($config['columns'], array_map(
                static fn ($value): string => trim((string) $value),
                $values
            ));
            if ($item === false) {
                $errors[] = "Baris {$rowNumber}: data tidak dapat dibaca.";
                continue;
            }
            $isExample = false;
            foreach ($item as $value) {
                if (str_contains(mb_strtolower($value), '[contoh]')) {
                    $isExample = true;
                    break;
                }
            }
            if ($isExample) {
                continue;
            }

            $rowErrors = [];
            $record = $item;
            switch ($type) {
                case 'major':
                    $this->validateText($item, 'jurusan_nama', 'Nama jurusan', true, 50, $rowErrors);
                    $this->validateText($item, 'jurusan_kode', 'Kode jurusan', true, 10, $rowErrors);
                    $this->validateText($item, 'jurusan_keterangan', 'Keterangan', false, 65535, $rowErrors);
                    break;
                case 'class':
                    $grade = filter_var($item['kelas_tingkat'], FILTER_VALIDATE_INT);
                    if ($grade === false || $grade < 1 || $grade > 12) {
                        $rowErrors[] = 'tingkat harus berupa angka 1 sampai 12';
                    }
                    $this->validateText($item, 'kelas_nama', 'Nama kelas', true, 50, $rowErrors);
                    $this->validateText($item, 'kelas_keterangan', 'Keterangan', false, 65535, $rowErrors);
                    $record['kelas_tingkat'] = $grade === false ? 0 : $grade;
                    break;
                case 'subjects':
                    $this->validateText($item, 'mapel_kode', 'Kode mapel', false, 20, $rowErrors);
                    $this->validateText($item, 'mapel_nama', 'Nama mapel', true, 100, $rowErrors);
                    if (! in_array($item['kelompok_rapor'], ['nasional', 'kewilayahan', 'dasar_keahlian', 'program_keahlian'], true)) {
                        $rowErrors[] = 'kelompok_rapor harus nasional, kewilayahan, dasar_keahlian, atau program_keahlian';
                    }
                    $this->validateText($item, 'mapel_keterangan', 'Keterangan', false, 65535, $rowErrors);
                    break;
                case 'teachers':
                    $this->validateText($item, 'guru_nip', 'NIP', false, 30, $rowErrors);
                    $this->validateText($item, 'guru_nama', 'Nama guru', true, 100, $rowErrors);
                    $email = $item['guru_email'];
                    if ($email === '' || mb_strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        $rowErrors[] = 'guru_email wajib diisi dengan format email yang valid (maksimal 254 karakter)';
                    }
                    $this->validateText($item, 'guru_telepon', 'Nomor telepon', false, 30, $rowErrors);
                    break;
                case 'parents':
                    $this->validateText($item, 'nama_orang_tua', 'Nama orang tua/wali', true, 100, $rowErrors);
                    $phone = $item['no_whatsapp'];
                    if (preg_match('/^\+?[0-9\s().-]{8,30}$/D', $phone) !== 1) {
                        $rowErrors[] = 'no_whatsapp harus berupa nomor telepon valid';
                    }
                    $studentIds = [];
                    if ($item['nama_siswa'] !== '') {
                        $studentNames = array_map('trim', explode('|', $item['nama_siswa']));
                        foreach ($studentNames as $studentName) {
                            $matchingStudentIds = $studentsByName[mb_strtolower($studentName)] ?? [];
                            if (count($matchingStudentIds) !== 1) {
                                $rowErrors[] = 'nama_siswa "' . $studentName . '" tidak ditemukan atau tidak unik; pilih siswa melalui form jika ada nama duplikat';
                                continue;
                            }
                            $studentId = $matchingStudentIds[0];
                            if (isset($assignedStudentIds[$studentId])) {
                                $rowErrors[] = 'siswa "' . $studentName . '" tercantum lebih dari sekali dalam file';
                                continue;
                            }
                            $assignedStudentIds[$studentId] = true;
                            $studentIds[] = $studentId;
                        }
                    }
                    $record = [
                        'nama_orang_tua' => $item['nama_orang_tua'],
                        'no_whatsapp' => $phone,
                        '_student_ids' => $studentIds,
                    ];
                    break;
                case 'lesson-periods':
                    $period = filter_var($item['jam_ke'], FILTER_VALIDATE_INT);
                    if ($period === false || $period < 1 || $period > 30) {
                        $rowErrors[] = 'jam_ke harus berupa angka 1 sampai 30';
                    }
                    $this->validateText($item, 'jam_nama', 'Nama jam', false, 50, $rowErrors);
                    foreach (['jam_mulai', 'jam_selesai'] as $timeColumn) {
                        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $item[$timeColumn]) !== 1) {
                            $rowErrors[] = $timeColumn . ' harus berformat HH:MM atau HH:MM:SS';
                        } elseif (strlen($item[$timeColumn]) === 5) {
                            $record[$timeColumn] = $item[$timeColumn] . ':00';
                        }
                    }
                    if (isset($record['jam_mulai'], $record['jam_selesai'])
                        && $record['jam_mulai'] >= $record['jam_selesai']) {
                        $rowErrors[] = 'jam_selesai harus lebih besar dari jam_mulai';
                    }
                    $record['jam_ke'] = $period === false ? 0 : $period;
                    break;
                case 'academic-years':
                    $this->validateText($item, 'tahun_ajaran_nama', 'Tahun ajaran', true, 20, $rowErrors);
                    $semester = filter_var($item['semester'], FILTER_VALIDATE_INT);
                    if (! in_array($semester, [1, 2], true)) {
                        $rowErrors[] = 'semester harus 1 atau 2';
                    }
                    foreach (['tanggal_mulai', 'tanggal_selesai'] as $dateColumn) {
                        if ($item[$dateColumn] !== '' && ! $this->isValidDate($item[$dateColumn])) {
                            $rowErrors[] = $dateColumn . ' harus berformat YYYY-MM-DD';
                        }
                    }
                    if ($item['tanggal_mulai'] !== '' && $item['tanggal_selesai'] !== ''
                        && $item['tanggal_mulai'] > $item['tanggal_selesai']) {
                        $rowErrors[] = 'tanggal_selesai harus sama dengan atau setelah tanggal_mulai';
                    }
                    if (! in_array($item['is_active'], ['0', '1'], true)) {
                        $rowErrors[] = 'is_active harus 0 atau 1';
                    } elseif ($item['is_active'] === '1' && ++$activeAcademicYears > 1) {
                        $rowErrors[] = 'hanya satu tahun ajaran dalam file yang boleh memiliki is_active 1';
                    }
                    $record['semester'] = $semester === false ? 0 : $semester;
                    $record['is_active'] = (int) $item['is_active'];
                    break;
            }

            foreach ($config['unique'] as $column) {
                $value = mb_strtolower(trim((string) ($item[$column] ?? '')));
                if ($type === 'lesson-periods' && $column === 'jam_ke' && $period !== false) {
                    $value = (string) $period;
                }
                if ($value === '') {
                    continue;
                }
                if (isset($seen[$column][$value])) {
                    $rowErrors[] = $column . ' sudah terdaftar atau duplikat di file';
                } else {
                    $seen[$column][$value] = true;
                }
            }

            if ($type === 'class') {
                $classKey = $record['kelas_tingkat'] . ':' . mb_strtolower($item['kelas_nama']);
                if (isset($seen['class'][$classKey])) {
                    $rowErrors[] = 'kombinasi tingkat dan nama kelas sudah terdaftar atau duplikat di file';
                } else {
                    $seen['class'][$classKey] = true;
                }
            }
            if ($type === 'academic-years' && $semester !== false) {
                $yearKey = mb_strtolower(trim($item['tahun_ajaran_nama'])) . ':' . $semester;
                if (isset($seen['academic-year'][$yearKey])) {
                    $rowErrors[] = 'kombinasi tahun ajaran dan semester sudah terdaftar atau duplikat di file';
                } else {
                    $seen['academic-year'][$yearKey] = true;
                }
            }

            if ($rowErrors !== []) {
                $errors[] = "Baris {$rowNumber}: " . implode(', ', array_unique($rowErrors)) . '.';
                continue;
            }

            if ($type === 'teachers') {
                $record['guru_email'] = strtolower($record['guru_email']);
            }
            foreach ($record as $column => $value) {
                if ($value === '' && ! in_array($column, ['jurusan_keterangan', 'kelas_keterangan', '_student_ids'], true)) {
                    $record[$column] = null;
                }
            }
            $records[] = $record;
            if (count($records) > 3000) {
                break;
            }
        }

        return [$records, $errors];
    }

    private function validateText(array $item, string $column, string $label, bool $required, int $maxLength, array &$errors): void
    {
        $value = $item[$column];
        if (($required && $value === '') || mb_strlen($value) > $maxLength) {
            $errors[] = $label . ($required ? ' wajib diisi' : '') . ' dan maksimal ' . $maxLength . ' karakter';
        }
    }

    private function readDelimitedFile(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Could not open uploaded master data file.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new RuntimeException('Uploaded master data file is empty.');
        }
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $delimiter = "\t";
        $highestCount = 1;
        foreach (["\t", ';', ','] as $candidate) {
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

    private function getType(string $type): array
    {
        if (! isset($this->types[$type])) {
            throw new RuntimeException('Jenis data master tidak valid.');
        }

        return $this->types[$type];
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
