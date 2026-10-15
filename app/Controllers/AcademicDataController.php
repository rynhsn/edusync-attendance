<?php

namespace App\Controllers;

use App\Models\AuthUserModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AcademicDataController extends Controller
{
    private BaseConnection $db;

    private array $types = [
        'subjects' => [
            'title' => 'Data Mapel',
            'table' => 'master_mapel',
            'key' => 'mapel_id',
            'activePage' => 'subjects',
            'columns' => [
                'mapel_kode' => 'Kode',
                'mapel_nama' => 'Nama Mapel',
                'kelompok_rapor' => 'Kelompok Raport SMK',
                'mapel_keterangan' => 'Keterangan',
            ],
            'fields' => [
                ['name' => 'mapel_kode', 'label' => 'Kode Mapel', 'maxlength' => 20],
                ['name' => 'mapel_nama', 'label' => 'Nama Mapel', 'required' => true, 'maxlength' => 100],
                [
                    'name' => 'kelompok_rapor',
                    'label' => 'Kelompok Raport SMK',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'nasional' => 'A. Muatan Nasional',
                        'kewilayahan' => 'B. Muatan Kewilayahan',
                        'dasar_keahlian' => 'C1. Dasar Bidang Keahlian',
                        'program_keahlian' => 'C2. Dasar Program Keahlian',
                    ],
                ],
                ['name' => 'mapel_keterangan', 'label' => 'Keterangan', 'type' => 'textarea'],
            ],
        ],
        'teachers' => [
            'title' => 'Data Guru',
            'table' => 'master_guru',
            'key' => 'guru_id',
            'activePage' => 'teachers',
            'columns' => [
                'guru_nip' => 'NIP',
                'guru_nama' => 'Nama Guru',
                'guru_email' => 'Email',
                'guru_telepon' => 'Telepon',
            ],
            'fields' => [
                ['name' => 'guru_nip', 'label' => 'NIP', 'maxlength' => 30],
                ['name' => 'guru_nama', 'label' => 'Nama Guru', 'required' => true, 'maxlength' => 100],
                ['name' => 'guru_email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'maxlength' => 254],
                ['name' => 'guru_telepon', 'label' => 'Nomor Telepon', 'maxlength' => 30],
            ],
        ],
        'parents' => [
            'title' => 'Data Orang Tua',
            'table' => 'master_orang_tua',
            'key' => 'orang_tua_id',
            'activePage' => 'parents',
            'columns' => [
                'nama_orang_tua' => 'Nama Orang Tua / Wali',
                'no_whatsapp' => 'No. WhatsApp',
            ],
            'fields' => [
                ['name' => 'nama_orang_tua', 'label' => 'Nama Orang Tua / Wali', 'required' => true, 'maxlength' => 100],
                ['name' => 'no_whatsapp', 'label' => 'No. WhatsApp', 'required' => true, 'maxlength' => 30, 'placeholder' => 'Contoh: 081234567890'],
            ],
        ],
        'lesson-periods' => [
            'title' => 'Data Jam Pelajaran',
            'table' => 'master_jam_pelajaran',
            'key' => 'jam_pelajaran_id',
            'activePage' => 'lesson-periods',
            'columns' => [
                'jam_ke' => 'Jam Ke',
                'jam_nama' => 'Nama Jam',
                'jam_mulai' => 'Jam Mulai',
                'jam_selesai' => 'Jam Selesai',
            ],
            'fields' => [
                ['name' => 'jam_ke', 'label' => 'Urutan Jam', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 30],
                ['name' => 'jam_nama', 'label' => 'Nama Jam', 'maxlength' => 50],
                ['name' => 'jam_mulai', 'label' => 'Jam Mulai', 'type' => 'time', 'required' => true],
                ['name' => 'jam_selesai', 'label' => 'Jam Selesai', 'type' => 'time', 'required' => true],
            ],
        ],
        'extracurriculars' => [
            'title' => 'Data Ekstrakurikuler',
            'table' => 'master_ekstrakurikuler',
            'key' => 'ekstrakurikuler_id',
            'activePage' => 'extracurriculars',
            'columns' => [
                'nama_kegiatan' => 'Nama Kegiatan',
                'deskripsi' => 'Deskripsi',
                'is_active' => 'Status',
            ],
            'fields' => [
                ['name' => 'nama_kegiatan', 'label' => 'Nama Kegiatan', 'required' => true, 'maxlength' => 100],
                ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Aktifkan kegiatan', 'type' => 'checkbox'],
            ],
        ],
        'kokurikuler' => [
            'title' => 'Data Kokurikuler',
            'table' => 'master_kokurikuler',
            'key' => 'kokurikuler_id',
            'activePage' => 'kokurikuler',
            'columns' => [
                'nama_kegiatan' => 'Nama Kegiatan',
                'deskripsi' => 'Deskripsi',
                'is_active' => 'Status',
            ],
            'fields' => [
                ['name' => 'nama_kegiatan', 'label' => 'Nama Kegiatan', 'required' => true, 'maxlength' => 100],
                ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Aktifkan kegiatan', 'type' => 'checkbox'],
            ],
        ],
        'academic-years' => [
            'title' => 'Data Tahun Ajaran',
            'table' => 'master_tahun_ajaran',
            'key' => 'tahun_ajaran_id',
            'activePage' => 'academic-years',
            'columns' => [
                'tahun_ajaran_nama' => 'Tahun Ajaran',
                'semester' => 'Semester',
                'tanggal_mulai' => 'Tanggal Mulai',
                'tanggal_selesai' => 'Tanggal Selesai',
                'is_active' => 'Status',
            ],
            'fields' => [
                ['name' => 'tahun_ajaran_nama', 'label' => 'Tahun Ajaran', 'required' => true, 'maxlength' => 20, 'placeholder' => '2026/2027'],
                ['name' => 'semester', 'label' => 'Semester', 'type' => 'select', 'required' => true, 'options' => [1 => 'Semester 1', 2 => 'Semester 2']],
                ['name' => 'tanggal_mulai', 'label' => 'Tanggal Mulai', 'type' => 'date'],
                ['name' => 'tanggal_selesai', 'label' => 'Tanggal Selesai', 'type' => 'date'],
                ['name' => 'is_active', 'label' => 'Jadikan tahun ajaran aktif', 'type' => 'checkbox'],
            ],
        ],
    ];

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index(string $type)
    {
        $config = $this->types[$type] ?? null;
        if ($config === null) {
            return redirect()->to(site_url('panel'));
        }

        $rows = $this->db->table($config['table'])
            ->orderBy($config['key'], 'DESC')
            ->get()
            ->getResultArray();
        $editRecord = null;
        if (! empty($this->request->getGet('edit'))) {
            $editId = filter_var($this->request->getGet('edit'), FILTER_VALIDATE_INT);
            if ($editId !== false && $editId > 0) {
                $editRecord = $this->db->table($config['table'])
                    ->where($config['key'], $editId)
                    ->get()
                    ->getRowArray();
            }
        }

        return view('panel/academic-data', [
            'title' => $config['title'],
            'activePage' => $config['activePage'],
            'type' => $type,
            'config' => $config,
            'rows' => $rows,
            'editRecord' => $editRecord,
            'message' => session()->getFlashdata('academicMessage'),
            'error' => session()->getFlashdata('academicError'),
            'warning' => session()->getFlashdata('academicWarning'),
        ]);
    }

    public function create(string $type)
    {
        return $this->save($type);
    }

    public function update(string $type, int $id)
    {
        return $this->save($type, $id);
    }

    public function delete(string $type, int $id)
    {
        $config = $this->types[$type] ?? null;
        if ($config === null || $id < 1) {
            return redirect()->to(site_url('panel'));
        }

        $record = $this->db->table($config['table'])->where($config['key'], $id)->get()->getRowArray();
        if ($record === null) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Data yang akan dihapus tidak ditemukan.');
        }

        if ($type === 'extracurriculars') {
            $usageCount = $this->db->table('academic_student_extracurricular')
                ->where('nama_kegiatan', $record['nama_kegiatan'])
                ->countAllResults();
            if ($usageCount > 0) {
                return redirect()->to(site_url($type))
                    ->with('academicError', 'Kegiatan ekstrakurikuler tidak bisa dihapus karena masih dipakai pada data penilaian siswa.');
            }
        } elseif ($type === 'parents') {
            if ($this->db->table('master_siswa')->where('orang_tua_id', $id)->countAllResults() > 0) {
                return redirect()->to(site_url($type))
                    ->with('academicError', 'Data orang tua tidak bisa dihapus karena masih dipilih oleh siswa.');
            }
        } else {
            $references = [
                'subjects' => ['mapel_id'],
                'teachers' => ['guru_id'],
                'lesson-periods' => ['jam_pelajaran_id'],
                'academic-years' => ['tahun_ajaran_id'],
            ];
            if (isset($references[$type])) {
                $foreignKey = $references[$type][0];
                $usageCount = $this->db->table('absensi_absensi_kelas')
                    ->where($foreignKey, $id)
                    ->countAllResults();

                if ($usageCount > 0) {
                    return redirect()->to(site_url($type))
                        ->with('academicError', 'Data tidak bisa dihapus karena sudah digunakan pada absensi kelas.');
                }
            }
        }

        if ($type === 'subjects'
            && $this->db->table('teacher_mapel')->where('mapel_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Mata pelajaran tidak bisa dihapus karena masih ditetapkan kepada guru.');
        }

        if ($type === 'subjects'
            && $this->db->table('academic_student_grades')->where('mapel_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Mata pelajaran tidak bisa dihapus karena sudah memiliki data nilai siswa.');
        }

        if ($type === 'teachers'
            && $this->db->table('teacher_mapel')->where('guru_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Data guru tidak bisa dihapus karena masih memiliki mata pelajaran.');
        }
        if ($type === 'teachers'
            && $this->db->table('academic_student_grades')->where('guru_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Data guru tidak bisa dihapus karena sudah memiliki data nilai siswa.');
        }

        if ($type === 'academic-years' && (int) $record['is_active'] === 1) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Tahun ajaran aktif tidak dapat dihapus. Aktifkan tahun ajaran lain terlebih dahulu.');
        }

        try {
            $deleted = $this->db->table($config['table'])->where($config['key'], $id)->delete();
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to delete academic data for {type}: {message}', [
                'type' => $type,
                'message' => $exception->getMessage(),
            ]);
            return redirect()->to(site_url($type))
                ->with('academicError', 'Data gagal dihapus karena masih digunakan data lain.');
        }

        if (! $deleted) {
            log_message('error', 'Delete query failed for academic data type {type}.', ['type' => $type]);
            return redirect()->to(site_url($type))
                ->with('academicError', 'Data gagal dihapus. Silakan coba kembali.');
        }

        return redirect()->to(site_url($type))
            ->with('academicMessage', $config['title'] . ' berhasil dihapus.');
    }

    private function save(string $type, ?int $id = null)
    {
        $config = $this->types[$type] ?? null;
        if ($config === null || ($id !== null && $id < 1)) {
            return redirect()->to(site_url('panel'));
        }
        $formUrl = site_url($type . ($id === null ? '' : '?edit=' . $id));

        $existing = $id !== null
            ? $this->db->table($config['table'])->where($config['key'], $id)->get()->getRowArray()
            : null;
        if ($id !== null && $existing === null) {
            return redirect()->to(site_url($type))
                ->with('academicError', 'Data yang akan diperbarui tidak ditemukan.');
        }

        $data = [];
        foreach ($config['fields'] as $field) {
            $name = $field['name'];
            $value = trim((string) $this->request->getPost($name));

            if (($field['type'] ?? '') === 'checkbox') {
                $data[$name] = $this->request->getPost($name) === '1' ? 1 : 0;
                continue;
            }

            if (($field['type'] ?? '') === 'select') {
                $matchedOption = false;
                foreach ($field['options'] as $optionValue => $optionLabel) {
                    if ((string) $optionValue === $value) {
                        $data[$name] = $optionValue;
                        $matchedOption = true;
                        break;
                    }
                }
                if (! $matchedOption) {
                    return redirect()->to($formUrl)
                        ->with('academicError', 'Pilihan ' . $field['label'] . ' tidak valid.');
                }
                continue;
            }

            if (! empty($field['required']) && $value === '') {
                return redirect()->to($formUrl)
                    ->with('academicError', $field['label'] . ' wajib diisi.');
            }

            if (isset($field['maxlength']) && mb_strlen($value) > $field['maxlength']) {
                return redirect()->to($formUrl)
                    ->with('academicError', $field['label'] . ' terlalu panjang.');
            }

            if ($value !== '' && ($field['type'] ?? '') === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                return redirect()->to($formUrl)
                    ->with('academicError', 'Format email guru tidak valid.');
            }

            if ($value !== '' && ($field['type'] ?? '') === 'number') {
                $number = filter_var($value, FILTER_VALIDATE_INT);
                if ($number === false || $number < $field['min'] || $number > $field['max']) {
                    return redirect()->to($formUrl)
                        ->with('academicError', $field['label'] . ' harus di antara ' . $field['min'] . ' dan ' . $field['max'] . '.');
                }
                $data[$name] = $number;
                continue;
            }

            $data[$name] = $value === '' ? null : $value;
        }

        if ($type === 'lesson-periods' && $data['jam_mulai'] >= $data['jam_selesai']) {
            return redirect()->to($formUrl)
                ->with('academicError', 'Jam selesai harus sesudah jam mulai.');
        }

        if ($type === 'academic-years'
            && ! empty($data['tanggal_mulai'])
            && ! empty($data['tanggal_selesai'])
            && $data['tanggal_mulai'] > $data['tanggal_selesai']) {
            return redirect()->to($formUrl)
                ->with('academicError', 'Tanggal mulai tidak boleh melewati tanggal selesai.');
        }

        if ($type === 'teachers') {
            $data['guru_email'] = strtolower((string) $data['guru_email']);
        }
        if ($type === 'parents') {
            if (preg_match('/^\+?[0-9\s().-]{8,30}$/D', (string) $data['no_whatsapp']) !== 1) {
                return redirect()->to($formUrl)
                    ->with('academicError', 'Nomor WhatsApp harus berupa nomor telepon valid.');
            }
        }

        $uniqueFields = [
            'subjects' => ['mapel_kode' => 'Kode mapel'],
            'teachers' => ['guru_nip' => 'NIP'],
            'parents' => ['no_whatsapp' => 'Nomor WhatsApp'],
            'lesson-periods' => ['jam_ke' => 'Urutan jam'],
            'extracurriculars' => ['nama_kegiatan' => 'Nama kegiatan'],
            'kokurikuler' => ['nama_kegiatan' => 'Nama kegiatan'],
            'academic-years' => ['tahun_ajaran_nama' => 'Tahun ajaran', 'semester' => 'Semester'],
        ];
        $uniqueBuilder = $this->db->table($config['table']);
        $hasUniqueValue = false;
        foreach ($uniqueFields[$type] as $fieldName => $label) {
            if (($data[$fieldName] ?? null) !== null) {
                $uniqueBuilder->where($fieldName, $data[$fieldName]);
                $hasUniqueValue = true;
            }
        }
        if ($id !== null && $hasUniqueValue) {
            $uniqueBuilder->where($config['key'] . ' !=', $id);
        }
        if ($hasUniqueValue && $uniqueBuilder->countAllResults() > 0) {
            return redirect()->to($formUrl)
                ->with('academicError', $type === 'academic-years'
                    ? 'Tahun ajaran dan semester tersebut sudah terdaftar.'
                    : reset($uniqueFields[$type]) . ' tersebut sudah digunakan.');
        }

        try {
            if (! $this->db->transBegin()) {
                throw new DatabaseException('Could not start the academic data transaction.');
            }

            if ($type === 'academic-years' && $data['is_active'] === 1) {
                $this->db->table('master_tahun_ajaran')->update(['is_active' => 0]);
            }

            $builder = $this->db->table($config['table']);
            $saved = $id === null
                ? $builder->insert($data)
                : $builder->where($config['key'], $id)->update($data);

            if (! $saved) {
                throw new DatabaseException('Saving academic data returned false.');
            }

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Academic data transaction failed.');
            }

            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            log_message('error', 'Failed to save academic data for {type}: {message}', [
                'type' => $type,
                'message' => $exception->getMessage(),
            ]);
            return redirect()->to($formUrl)
                ->with('academicError', 'Data gagal disimpan. Pastikan nilai unik belum digunakan dan coba kembali.');
        }

        $userSyncResult = 'not_applicable';
        $userSyncFailed = false;
        if ($type === 'teachers') {
            $teacherId = $id ?? (int) $this->db->insertID();
            try {
                if ($teacherId < 1) {
                    throw new DatabaseException('Could not determine the saved teacher ID.');
                }
                if (! $this->db->transBegin()) {
                    throw new DatabaseException('Could not start the teacher user synchronization transaction.');
                }
                $userSyncResult = $this->syncTeacherUser($teacherId, $data);
                if ($this->db->transStatus() === false) {
                    throw new DatabaseException('Teacher user synchronization transaction failed.');
                }
                $this->db->transCommit();
            } catch (DatabaseException $exception) {
                $this->db->transRollback();
                log_message('error', 'Failed to synchronize user account for teacher {id}: {message}', [
                    'id' => $teacherId,
                    'message' => $exception->getMessage(),
                ]);
                $userSyncFailed = true;
            }
        }

        $message = $config['title'] . ($id === null ? ' berhasil ditambahkan.' : ' berhasil diperbarui.');
        if ($type === 'teachers') {
            $message .= match ($userSyncResult) {
                'created' => ' Akun Data User juga dibuat; guru dapat mendaftar menggunakan email ini untuk mengatur password dan verifikasi.',
                'protected' => ' Email tersebut milik Super Admin, sehingga akunnya tidak diubah.',
                default => ' Data akun pengguna disinkronkan berdasarkan email.',
            };
        }

        if ($userSyncFailed) {
            return redirect()->to(site_url($type))
                ->with('academicWarning', 'Data guru berhasil disimpan, tetapi sinkronisasi akun Data User gagal. Periksa izin database dan log aplikasi.');
        }

        return redirect()->to(site_url($type))->with('academicMessage', $message);
    }

    private function syncTeacherUser(int $teacherId, array $teacher): string
    {
        $hasTeacherLink = $this->db->fieldExists('guru_id', 'auth_users');
        $users = new AuthUserModel();
        $linkedUser = $hasTeacherLink
            ? $users->where('guru_id', $teacherId)->first()
            : null;
        if ($linkedUser === null) {
            $linkedUser = (new AuthUserModel())->where('email', $teacher['guru_email'])->first();
        }

        if ($linkedUser !== null && $linkedUser['role'] === 'super_admin') {
            return 'protected';
        }

        if ($hasTeacherLink
            && $linkedUser !== null
            && ! empty($linkedUser['guru_id'])
            && (int) $linkedUser['guru_id'] !== $teacherId) {
            throw new DatabaseException('The teacher email is linked to another teacher account.');
        }

        if ($linkedUser === null) {
            $username = $this->availableTeacherUsername($teacher['guru_email']);
            $userData = [
                'email' => $teacher['guru_email'],
                'display_name' => $teacher['guru_nama'],
                'username' => $username,
                'password_hash' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                'role' => 'user',
                'is_verified' => 0,
            ];
            if ($hasTeacherLink) {
                $userData['guru_id'] = $teacherId;
            }
            $userId = $users->insert($userData, true);

            if ($userId === false) {
                throw new DatabaseException('Creating the teacher user account returned false.');
            }

            return 'created';
        }

        $userData = [
            'email' => $teacher['guru_email'],
            'display_name' => $teacher['guru_nama'],
        ];
        if ($hasTeacherLink) {
            $userData['guru_id'] = $teacherId;
        }
        if (strcasecmp((string) $linkedUser['email'], $teacher['guru_email']) !== 0) {
            $userData['is_verified'] = 0;
            $userData['otp_hash'] = null;
            $userData['otp_expires_at'] = null;
            $userData['otp_sent_at'] = null;
            $userData['otp_attempts'] = 0;
        }

        if (! $users->update($linkedUser['user_id'], $userData)) {
            throw new DatabaseException('Updating the linked teacher user account returned false.');
        }

        return 'synced';
    }

    private function availableTeacherUsername(string $email): string
    {
        $localPart = strtolower((string) strstr($email, '@', true));
        $base = preg_replace('/[^a-z0-9._-]/', '', $localPart) ?? '';
        if (strlen($base) < 3) {
            $base = 'guru' . $base;
        }
        $base = substr($base, 0, 32);
        $username = $base;
        $suffix = 2;

        while ($this->db->table('auth_users')->where('username', $username)->countAllResults() > 0) {
            $ending = (string) $suffix++;
            $username = substr($base, 0, 32 - strlen($ending)) . $ending;
        }

        return $username;
    }
}
