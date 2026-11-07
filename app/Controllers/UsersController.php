<?php

namespace App\Controllers;

use App\Libraries\RoleAccess;
use App\Libraries\StudentSpreadsheet;
use App\Libraries\UserTeacherSync;
use App\Models\AuthUserModel;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

class UsersController extends BaseController
{
    public function index()
    {
        // Pastikan semua user memiliki relasi data guru (auto-sync)
        UserTeacherSync::syncAllUnlinkedUsers();

        $search = trim((string) $this->request->getGet('search'));
        $roleFilter = trim((string) $this->request->getGet('role'));

        $db = db_connect();
        $builder = $db->table('auth_users u')
            ->select('u.user_id, u.display_name, u.username, u.email, u.role, u.status, u.is_verified, u.created_at, u.guru_id, u.siswa_id, mg.guru_nip, mg.guru_telepon, mg.rfid_tag, ms.nama_siswa, ms.nis, mk.kelas_nama')
            ->join('master_guru mg', 'mg.guru_id = u.guru_id', 'left')
            ->join('master_siswa ms', 'ms.siswa_id = u.siswa_id', 'left')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.display_name', $search)
                ->orLike('u.username', $search)
                ->orLike('u.email', $search)
                ->orLike('ms.nama_siswa', $search)
                ->orLike('ms.nis', $search)
                ->orLike('mk.kelas_nama', $search)
                ->orLike('mg.guru_nip', $search)
                ->groupEnd();
        }

        if ($roleFilter !== '' && $roleFilter !== 'all') {
            $builder->where('u.role', $roleFilter);
        }

        $users = $builder->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $students = $db->table('master_siswa ms')
            ->select('ms.siswa_id, ms.nama_siswa, ms.nis, mk.kelas_nama, u.user_id as existing_user_id, u.username as existing_username')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left')
            ->join('auth_users u', 'u.siswa_id = ms.siswa_id', 'left')
            ->where('ms.status', 'aktif')
            ->orderBy('ms.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();

        return view('panel/users', [
            'title'      => 'Data User',
            'activePage' => 'users',
            'users'      => $users,
            'students'   => $students,
            'roles'      => RoleAccess::ROLES,
            'search'     => $search,
            'roleFilter' => $roleFilter,
            'message'    => session()->getFlashdata('userMessage'),
            'error'      => session()->getFlashdata('userError'),
        ]);
    }

    public function create()
    {
        $redirect = redirect()->to(site_url('users'));
        $role = trim((string) $this->request->getPost('role'));
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $password = (string) $this->request->getPost('password');
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $displayName = trim((string) $this->request->getPost('display_name'));
        $siswaId = (int) $this->request->getPost('siswa_id');

        if (! array_key_exists($role, RoleAccess::ROLES)) {
            return $redirect->with('userError', 'Role yang dipilih tidak valid.');
        }

        if (preg_match('/^[a-z0-9._-]{3,32}$/', $username) !== 1) {
            return $redirect->with('userError', 'Username harus 3–32 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.');
        }

        if (strlen($password) < 8 || strlen($password) > 72) {
            return $redirect->with('userError', 'Password harus terdiri dari 8–72 karakter.');
        }

        $usersModel = new AuthUserModel();
        if ($usersModel->where('username', $username)->first() !== null) {
            return $redirect->with('userError', "Username '{$username}' sudah digunakan oleh akun lain.");
        }

        $db = db_connect();

        if ($role === 'siswa') {
            if ($siswaId <= 0) {
                return $redirect->with('userError', 'Silakan pilih siswa yang akan dibuatkan akun login.');
            }

            $siswa = $db->table('master_siswa')->where('siswa_id', $siswaId)->get()->getRowArray();
            if (! $siswa) {
                return $redirect->with('userError', 'Data siswa tidak ditemukan.');
            }

            // Cek apakah siswa ini sudah memiliki akun
            $existingSiswaAccount = $usersModel->where('siswa_id', $siswaId)->first();
            if ($existingSiswaAccount !== null) {
                return $redirect->with('userError', "Siswa {$siswa['nama_siswa']} sudah memiliki akun login (Username: {$existingSiswaAccount['username']}).");
            }

            if ($displayName === '') {
                $displayName = $siswa['nama_siswa'];
            }

            if ($email === '') {
                $email = $username . '@siswa.edusync';
            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
                return $redirect->with('userError', 'Format email tidak valid.');
            }

            // Cek email duplikat
            if ($usersModel->where('email', $email)->first() !== null) {
                return $redirect->with('userError', "Email '{$email}' sudah digunakan oleh akun lain.");
            }

            $insertData = [
                'display_name'  => $displayName,
                'username'      => $username,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role'          => 'siswa',
                'siswa_id'      => $siswaId,
                'guru_id'       => null,
                'is_verified'   => 1,
                'status'        => 'active',
            ];

            if (! $usersModel->insert($insertData)) {
                return $redirect->with('userError', 'Gagal membuat akun siswa. Silakan coba kembali.');
            }

            return $redirect->with('userMessage', "Akun login siswa untuk {$displayName} (Username: {$username}) berhasil dibuat dan langsung aktif.");
        }

        // Untuk role selain siswa
        if ($displayName === '' || mb_strlen($displayName) > 100) {
            return $redirect->with('userError', 'Nama tampilan wajib diisi dan maksimal 100 karakter.');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
            return $redirect->with('userError', 'Format email tidak valid.');
        }

        if ($usersModel->where('email', $email)->first() !== null) {
            return $redirect->with('userError', "Email '{$email}' sudah digunakan oleh akun lain.");
        }

        $teacherId = null;
        if ($role !== 'siswa') {
            $teacherId = UserTeacherSync::ensureTeacherForRegistration($email, $displayName);
        }

        $insertData = [
            'display_name'  => $displayName,
            'username'      => $username,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role,
            'guru_id'       => $teacherId,
            'siswa_id'      => null,
            'is_verified'   => 1,
            'status'        => 'active',
        ];

        if (! $usersModel->insert($insertData)) {
            return $redirect->with('userError', 'Gagal membuat user. Silakan coba kembali.');
        }

        return $redirect->with('userMessage', "User baru {$displayName} (" . RoleAccess::label($role) . ") berhasil dibuat.");
    }

    public function setRole(int $id)
    {
        $role = $this->request->getPost('role');
        $redirect = redirect()->to(site_url('users'));
        if ($id < 1 || ! is_string($role) || ! array_key_exists($role, RoleAccess::ROLES)) {
            return $redirect->with('userError', 'Role yang dipilih tidak valid.');
        }

        $currentUser = session()->get('authUser');
        if ((int) ($currentUser['user_id'] ?? 0) === $id) {
            return $redirect->with('userError', 'Role akun yang sedang digunakan tidak dapat diubah dari halaman ini.');
        }

        $users = new AuthUserModel();
        $target = $users->find($id);
        if ($target === null) {
            return $redirect->with('userError', 'Akun yang akan diubah tidak ditemukan.');
        }

        $wasNonaktif = ($target['status'] ?? 'active') === 'nonaktif';
        if ($target['role'] === $role && ! $wasNonaktif) {
            return $redirect->with('userMessage', 'Role akun tersebut sudah sesuai.');
        }

        if ($target['role'] === 'super_admin'
            && $users->where('role', 'super_admin')->countAllResults() <= 1) {
            return $redirect->with('userError', 'Role Super Admin terakhir tidak dapat diubah.');
        }

        $configuredAdminEmail = strtolower(trim((string) env('auth.super_admin_email')));
        if ($target['role'] === 'super_admin'
            && strcasecmp((string) $target['email'], $configuredAdminEmail) === 0) {
            return $redirect->with('userError', 'Akun Super Admin utama tidak dapat diturunkan rolenya.');
        }

        $updateData = ['role' => $role];
        if ($wasNonaktif) {
            $updateData['status'] = 'active';
        }

        if (! $users->update($id, $updateData)) {
            log_message('error', 'Failed to change EduSync user role for user {id}.', ['id' => $id]);
            return $redirect->with('userError', 'Role gagal diperbarui. Silakan coba kembali.');
        }

        $message = 'Role ' . RoleAccess::label($role) . ' berhasil diterapkan';
        if ($wasNonaktif) {
            $message .= ' dan akun telah diaktifkan.';
        } else {
            $message .= '.';
        }

        return $redirect->with('userMessage', $message);
    }

    public function update(int $id)
    {
        $redirect = redirect()->to(site_url('users'));
        if ($id < 1) {
            return $redirect->with('userError', 'ID user tidak valid.');
        }

        $result = UserTeacherSync::updateUserAndTeacher($id, $this->request->getPost());
        if (! $result['success']) {
            return $redirect->with('userError', $result['message']);
        }

        // Jika user yang diedit adalah akun yang sedang login, update session
        $currentUser = session()->get('authUser');
        if ((int) ($currentUser['user_id'] ?? 0) === $id) {
            $updatedUser = (new AuthUserModel())->find($id);
            if ($updatedUser) {
                session()->set('authUser', [
                    'user_id'      => (int) $updatedUser['user_id'],
                    'email'        => $updatedUser['email'],
                    'display_name' => $updatedUser['display_name'],
                    'username'     => $updatedUser['username'],
                    'avatar_path'  => $updatedUser['avatar_path'],
                    'role'         => $updatedUser['role'],
                ]);
            }
        }

        return $redirect->with('userMessage', $result['message']);
    }

    public function delete(int $id)
    {
        $redirect = redirect()->to(site_url('users'));
        if ($id < 1) {
            return $redirect->with('userError', 'ID user tidak valid.');
        }

        $currentUser = session()->get('authUser');
        if ((int) ($currentUser['user_id'] ?? 0) === $id) {
            return $redirect->with('userError', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        $users = new AuthUserModel();
        $target = $users->find($id);
        if ($target === null) {
            return $redirect->with('userError', 'Akun user tidak ditemukan.');
        }

        if ($target['role'] === 'super_admin'
            && $users->where('role', 'super_admin')->countAllResults() <= 1) {
            return $redirect->with('userError', 'Akun Super Admin terakhir tidak dapat dihapus.');
        }

        $configuredAdminEmail = strtolower(trim((string) env('auth.super_admin_email')));
        if ($target['role'] === 'super_admin'
            && strcasecmp((string) $target['email'], $configuredAdminEmail) === 0) {
            return $redirect->with('userError', 'Akun Super Admin utama tidak dapat dihapus.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $teacherId = ! empty($target['guru_id']) ? (int) $target['guru_id'] : 0;

            // Hapus user
            $users->delete($id);

            // Jika ada guru terkait, cek apakah memiliki ketergantungan di jadwal piket, nilai, atau absensi
            if ($teacherId > 0) {
                $hasSchedule = $db->table('jadwal_piket_guru')->where('guru_id', $teacherId)->countAllResults() > 0;
                $hasGrades   = $db->table('transkrip_mapel')->where('guru_id', $teacherId)->countAllResults() > 0;
                $hasAttend   = $db->table('absensi_absensi_sekolah')->where('guru_id', $teacherId)->countAllResults() > 0;

                // Jika tidak ada data terkait, kita bisa hapus guru juga agar bersih
                if (! $hasSchedule && ! $hasGrades && ! $hasAttend) {
                    $db->table('master_guru')->where('guru_id', $teacherId)->delete();
                }
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus user.');
            }

            $db->transCommit();

            return $redirect->with('userMessage', 'Akun user ' . ($target['display_name'] ?: $target['username']) . ' berhasil dihapus.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return $redirect->with('userError', 'Terjadi kesalahan saat menghapus akun: ' . $e->getMessage());
        }
    }

    public function passwords()
    {
        $currentUserId = (int) (session()->get('authUser')['user_id'] ?? 0);
        $users = (new AuthUserModel())
            ->select('user_id, display_name, username, email, role, is_verified')
            ->where('role !=', 'super_admin')
            ->where('user_id !=', $currentUserId)
            ->orderBy('display_name', 'ASC')
            ->findAll();

        return view('panel/user-passwords', [
            'title'      => 'Reset Password User',
            'activePage' => 'user-passwords',
            'users'      => $users,
            'message'    => session()->getFlashdata('passwordResetMessage'),
            'error'      => session()->getFlashdata('passwordResetError'),
        ]);
    }

    public function resetPassword(int $id)
    {
        $redirect = redirect()->to(site_url('user-passwords'));
        $password = $this->request->getPost('password');
        $confirmation = $this->request->getPost('password_confirm');

        if (! is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
            return $redirect->with('passwordResetError', 'Password baru harus terdiri dari 8–72 karakter.');
        }
        if (! is_string($confirmation) || ! hash_equals($password, $confirmation)) {
            return $redirect->with('passwordResetError', 'Konfirmasi password baru tidak cocok.');
        }
        if ((int) (session()->get('authUser')['user_id'] ?? 0) === $id) {
            return $redirect->with('passwordResetError', 'Password akun yang sedang digunakan harus diubah melalui halaman Profil.');
        }

        $users = new AuthUserModel();
        $target = $users->find($id);
        if ($target === null || $target['role'] === 'super_admin') {
            return $redirect->with('passwordResetError', 'Akun user tidak ditemukan atau tidak dapat direset dari halaman ini.');
        }

        if (! $users->update($id, ['password_hash' => password_hash($password, PASSWORD_DEFAULT)])) {
            log_message('error', 'Failed to reset password for user {id}.', ['id' => $id]);
            return $redirect->with('passwordResetError', 'Password gagal direset. Silakan coba kembali.');
        }

        return $redirect->with('passwordResetMessage', 'Password akun ' . ($target['display_name'] ?: $target['username'] ?: $target['email']) . ' berhasil direset.');
    }

    /**
     * Unduh template spreadsheet (.xlsx, .xls, .csv) untuk import user siswa
     */
    public function importTemplate(): ResponseInterface
    {
        $format = strtolower((string) $this->request->getGet('format'));
        if (! in_array($format, ['xlsx', 'xls', 'csv'], true)) {
            $format = 'xlsx';
        }

        // Ambil beberapa contoh siswa aktif dari master_siswa untuk memudahkan operator
        $db = db_connect();
        $sampleStudents = $db->table('master_siswa ms')
            ->select('ms.nis, ms.nama_siswa')
            ->where('ms.status', 'aktif')
            ->orderBy('ms.siswa_id', 'ASC')
            ->limit(3)
            ->get()
            ->getResultArray();

        $rows = [
            ['nis', 'nama_siswa', 'username', 'password', 'email', 'status'],
        ];

        if (! empty($sampleStudents)) {
            foreach ($sampleStudents as $st) {
                $cleanUser = strtolower(preg_replace('/[^a-z0-9]/', '.', (string) $st['nama_siswa']));
                $cleanUser = trim(preg_replace('/\.+/', '.', $cleanUser), '.');
                if ($cleanUser === '') {
                    $cleanUser = 'siswa.' . ($st['nis'] ?: 'user');
                }
                $rows[] = [
                    '[CONTOH] ' . ($st['nis'] ?: '2026-001'),
                    (string) $st['nama_siswa'],
                    $cleanUser,
                    'siswa123',
                    $cleanUser . '@siswa.edusync',
                    'active',
                ];
            }
        } else {
            $rows[] = ['[CONTOH] 24.25.187', 'A. Rizky Maulana', 'rizky.maulana', 'siswa123', 'rizky.maulana@siswa.edusync', 'active'];
            $rows[] = ['[CONTOH] 24.25.188', 'Citra Nur Fadillah', 'citra.fadillah', 'siswa123', 'citra.fadillah@siswa.edusync', 'active'];
        }

        $filename = 'template-import-user-siswa';

        if ($format === 'csv') {
            return $this->formatCsvResponse($filename . '.csv', $rows);
        }

        $spreadsheet = new StudentSpreadsheet();
        return $spreadsheet->download($filename . '.' . ($format === 'xls' ? 'xls' : 'xlsx'), $rows);
    }

    /**
     * Export data user ke file spreadsheet (.xlsx, .xls, .csv)
     */
    public function export(): ResponseInterface
    {
        $format = strtolower((string) $this->request->getGet('format'));
        if (! in_array($format, ['xlsx', 'xls', 'csv'], true)) {
            $format = 'xlsx';
        }

        $roleFilter = trim((string) $this->request->getGet('role'));
        $search = trim((string) $this->request->getGet('search'));

        $db = db_connect();
        $builder = $db->table('auth_users u')
            ->select('u.user_id, u.display_name, u.username, u.email, u.role, u.status, u.is_verified, u.created_at, mg.guru_nip, mg.guru_telepon, ms.nama_siswa, ms.nis, mk.kelas_nama')
            ->join('master_guru mg', 'mg.guru_id = u.guru_id', 'left')
            ->join('master_siswa ms', 'ms.siswa_id = u.siswa_id', 'left')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left');

        if ($roleFilter === 'siswa') {
            $builder->where('u.role', 'siswa');
        } elseif ($roleFilter !== '' && $roleFilter !== 'all' && array_key_exists($roleFilter, RoleAccess::ROLES)) {
            $builder->where('u.role', $roleFilter);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.display_name', $search)
                ->orLike('u.username', $search)
                ->orLike('u.email', $search)
                ->orLike('ms.nama_siswa', $search)
                ->orLike('ms.nis', $search)
                ->orLike('mk.kelas_nama', $search)
                ->orLike('mg.guru_nip', $search)
                ->groupEnd();
        }

        $users = $builder->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $roles = RoleAccess::ROLES;

        $rows = [
            [
                'No',
                'Nama Lengkap',
                'Username',
                'Email',
                'Peran / Role',
                'Tipe Akun',
                'Data Terhubung',
                'Kelas',
                'NIS / NIP',
                'Status Akun',
                'Status Email',
                'Tanggal Terdaftar',
            ],
        ];

        foreach ($users as $idx => $u) {
            $isSiswa = ($u['role'] === 'siswa');
            $tipeAkun = $isSiswa ? 'Siswa' : 'Guru / Staf';
            $dataTerhubung = $isSiswa ? ($u['nama_siswa'] ?: '-') : ($u['display_name'] ?: '-');
            $kelas = $isSiswa ? ($u['kelas_nama'] ?: '-') : '-';
            $nisNip = $isSiswa ? ($u['nis'] ?: '-') : ($u['guru_nip'] ?: '-');
            $statusAkun = ($u['status'] === 'nonaktif') ? 'Nonaktif' : 'Aktif';
            $statusEmail = ! empty($u['is_verified']) ? 'Terverifikasi' : 'Belum Verifikasi';
            $tglDaftar = ! empty($u['created_at']) ? date('Y-m-d H:i:s', strtotime($u['created_at'])) : '-';

            $rows[] = [
                (string) ($idx + 1),
                (string) ($u['display_name'] ?: '-'),
                (string) ($u['username'] ?: '-'),
                (string) ($u['email'] ?: '-'),
                (string) ($roles[$u['role']] ?? $u['role']),
                $tipeAkun,
                (string) $dataTerhubung,
                (string) $kelas,
                (string) $nisNip,
                $statusAkun,
                $statusEmail,
                $tglDaftar,
            ];
        }

        $filename = 'data-user-edusync-' . ($roleFilter === 'siswa' ? 'siswa-' : '') . date('Y-m-d');

        if ($format === 'csv') {
            return $this->formatCsvResponse($filename . '.csv', $rows);
        }

        $spreadsheet = new StudentSpreadsheet();
        return $spreadsheet->download($filename . '.' . ($format === 'xls' ? 'xls' : 'xlsx'), $rows);
    }

    /**
     * Import massal akun login siswa dari file XLSX, XLS, atau CSV
     */
    public function import()
    {
        $redirect = redirect()->to(site_url('users'));
        $file = $this->request->getFile('user_file');

        $fileValid = $file !== null && ($file->isValid() || (php_sapi_name() === 'cli' && is_file($file->getTempName())));
        if (! $fileValid || $file->hasMoved()) {
            return $redirect->with('userError', 'Pilih file spreadsheet (.XLSX, .XLS, atau .CSV) yang valid.');
        }

        $ext = strtolower($file->getClientExtension() ?: $file->getExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
            return $redirect->with('userError', 'Format file tidak didukung. Harap gunakan file berformat .XLSX, .XLS, atau .CSV.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return $redirect->with('userError', 'Ukuran file terlalu besar (maksimal 10 MB).');
        }

        $tempPath = $file->getTempName();
        $rows = [];

        try {
            if ($ext === 'xlsx') {
                $spreadsheet = new StudentSpreadsheet();
                $rows = $spreadsheet->read($tempPath);
            } elseif ($ext === 'csv') {
                $rows = $this->readDelimitedFile($tempPath);
            } else {
                // Untuk file .xls, coba StudentSpreadsheet lalu fallback ke delimited
                try {
                    $spreadsheet = new StudentSpreadsheet();
                    $rows = $spreadsheet->read($tempPath);
                } catch (\Throwable $e) {
                    $rows = $this->readDelimitedFile($tempPath);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error reading user spreadsheet: ' . $e->getMessage());
            return $redirect->with('userError', 'File spreadsheet tidak dapat dibaca atau rusak. Gunakan template yang disediakan.');
        }

        if (empty($rows) || count($rows) < 2) {
            return $redirect->with('userError', 'File kosong atau tidak memiliki baris data user siswa.');
        }

        // Normalisasi header
        $headerRow = array_map(function ($val) {
            $cleaned = preg_replace('/^\xEF\xBB\xBF/', '', trim((string) $val));
            return strtolower((string) $cleaned);
        }, array_shift($rows));

        $colMap = [
            'nis'        => null,
            'nama_siswa' => null,
            'username'   => null,
            'password'   => null,
            'email'      => null,
            'status'     => null,
        ];

        foreach ($headerRow as $idx => $headerName) {
            if (in_array($headerName, ['nis', 'no_induk', 'nomor_induk', 'nisn'], true)) {
                $colMap['nis'] = $idx;
            } elseif (in_array($headerName, ['nama_siswa', 'nama', 'nama_lengkap', 'display_name'], true)) {
                $colMap['nama_siswa'] = $idx;
            } elseif (in_array($headerName, ['username', 'user', 'uname'], true)) {
                $colMap['username'] = $idx;
            } elseif (in_array($headerName, ['password', 'sandi', 'pass', 'pwd'], true)) {
                $colMap['password'] = $idx;
            } elseif (in_array($headerName, ['email', 'surel'], true)) {
                $colMap['email'] = $idx;
            } elseif (in_array($headerName, ['status', 'status_akun'], true)) {
                $colMap['status'] = $idx;
            }
        }

        if ($colMap['nis'] === null && $colMap['nama_siswa'] === null) {
            return $redirect->with('userError', 'File harus memiliki minimal kolom "nis" atau "nama_siswa". Silakan unduh template yang sesuai.');
        }

        $db = db_connect();

        // Pre-fetch all students in master_siswa
        $allStudents = $db->table('master_siswa')
            ->select('siswa_id, nama_siswa, nis, nisn, status')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $studentsByNis = [];
        $studentsByName = [];
        foreach ($allStudents as $st) {
            if (! empty($st['nis'])) {
                $studentsByNis[trim((string) $st['nis'])] = $st;
            }
            if (! empty($st['nisn'])) {
                $studentsByNis[trim((string) $st['nisn'])] = $st;
            }
            $cleanName = mb_strtolower(trim((string) $st['nama_siswa']));
            if ($cleanName !== '') {
                $studentsByName[$cleanName] = $st;
            }
        }

        // Pre-fetch existing auth_users
        $allUsers = $db->table('auth_users')
            ->select('user_id, username, email, siswa_id')
            ->get()
            ->getResultArray();

        $existingUsernames = [];
        $existingEmails = [];
        $existingSiswaIds = [];
        foreach ($allUsers as $u) {
            if (! empty($u['username'])) {
                $existingUsernames[strtolower(trim((string) $u['username']))] = (int) $u['user_id'];
            }
            if (! empty($u['email'])) {
                $existingEmails[strtolower(trim((string) $u['email']))] = (int) $u['user_id'];
            }
            if (! empty($u['siswa_id'])) {
                $existingSiswaIds[(int) $u['siswa_id']] = (int) $u['user_id'];
            }
        }

        $insertedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $toInsert = [];

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2;

            $rawNis = ($colMap['nis'] !== null && isset($row[$colMap['nis']])) ? trim((string) $row[$colMap['nis']]) : '';
            $rawNama = ($colMap['nama_siswa'] !== null && isset($row[$colMap['nama_siswa']])) ? trim((string) $row[$colMap['nama_siswa']]) : '';
            $rawUsername = ($colMap['username'] !== null && isset($row[$colMap['username']])) ? trim((string) $row[$colMap['username']]) : '';
            $rawPassword = ($colMap['password'] !== null && isset($row[$colMap['password']])) ? trim((string) $row[$colMap['password']]) : '';
            $rawEmail = ($colMap['email'] !== null && isset($row[$colMap['email']])) ? trim((string) $row[$colMap['email']]) : '';
            $rawStatus = ($colMap['status'] !== null && isset($row[$colMap['status']])) ? strtolower(trim((string) $row[$colMap['status']])) : 'active';

            // Lewati jika contoh atau kosong
            if (stripos($rawNis, '[contoh]') !== false || stripos($rawNama, '[contoh]') !== false) {
                continue;
            }
            if ($rawNis === '' && $rawNama === '' && $rawUsername === '') {
                continue;
            }

            // Cari siswa yang cocok
            $student = null;
            if ($rawNis !== '' && isset($studentsByNis[$rawNis])) {
                $student = $studentsByNis[$rawNis];
            } elseif ($rawNama !== '' && isset($studentsByName[mb_strtolower($rawNama)])) {
                $student = $studentsByName[mb_strtolower($rawNama)];
            }

            if ($student === null) {
                $identifier = $rawNis !== '' ? "NIS {$rawNis}" : "Nama '{$rawNama}'";
                $errors[] = "Baris {$rowNumber}: Data siswa dengan {$identifier} tidak ditemukan di Data Siswa.";
                continue;
            }

            $siswaId = (int) $student['siswa_id'];
            $namaSiswa = $student['nama_siswa'];

            // Cek apakah siswa ini sudah memiliki akun login
            if (isset($existingSiswaIds[$siswaId])) {
                $skippedCount++;
                continue;
            }

            // Tentukan username
            $finalUsername = '';
            if ($rawUsername !== '') {
                $candidate = strtolower(preg_replace('/[^a-z0-9._-]/', '', $rawUsername));
                if (strlen($candidate) >= 3 && strlen($candidate) <= 32) {
                    $finalUsername = $candidate;
                }
            }

            if ($finalUsername === '') {
                $candidate = strtolower(preg_replace('/[^a-z0-9]/', '.', $namaSiswa));
                $candidate = trim(preg_replace('/\.+/', '.', $candidate), '.');
                if (strlen($candidate) < 3) {
                    $candidate = 'siswa.' . (! empty($student['nis']) ? $student['nis'] : $siswaId);
                }
                if (strlen($candidate) > 28) {
                    $candidate = substr($candidate, 0, 28);
                }
                $finalUsername = $candidate;
            }

            // Pastikan username unik
            $baseUsername = $finalUsername;
            $counter = 1;
            while (isset($existingUsernames[$finalUsername])) {
                $counter++;
                $finalUsername = substr($baseUsername, 0, 28) . $counter;
            }
            $existingUsernames[$finalUsername] = true;

            // Tentukan email
            $finalEmail = '';
            if ($rawEmail !== '' && filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
                $finalEmail = strtolower($rawEmail);
            }
            if ($finalEmail === '' || isset($existingEmails[$finalEmail])) {
                $finalEmail = $finalUsername . '@siswa.edusync';
                $emailCounter = 1;
                while (isset($existingEmails[$finalEmail])) {
                    $emailCounter++;
                    $finalEmail = $finalUsername . $emailCounter . '@siswa.edusync';
                }
            }
            $existingEmails[$finalEmail] = true;

            // Tentukan password
            $finalPassword = (strlen($rawPassword) >= 8 && strlen($rawPassword) <= 72) ? $rawPassword : 'siswa123';

            $accountStatus = ($rawStatus === 'nonaktif') ? 'nonaktif' : 'active';

            $toInsert[] = [
                'display_name'  => $namaSiswa,
                'username'      => $finalUsername,
                'email'         => $finalEmail,
                'password_hash' => password_hash($finalPassword, PASSWORD_DEFAULT),
                'role'          => 'siswa',
                'siswa_id'      => $siswaId,
                'guru_id'       => null,
                'is_verified'   => 1,
                'status'        => $accountStatus,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];

            // Tandai siswaId sudah dibuatkan akun agar tidak duplikat jika ada baris berulang di file
            $existingSiswaIds[$siswaId] = true;
            $insertedCount++;
        }

        if (! empty($toInsert)) {
            $usersModel = new AuthUserModel();
            $usersModel->insertBatch($toInsert);
        }

        $msgParts = [];
        if ($insertedCount > 0) {
            $msgParts[] = "Berhasil membuat {$insertedCount} akun user siswa baru.";
        }
        if ($skippedCount > 0) {
            $msgParts[] = "{$skippedCount} siswa dilewati karena sudah memiliki akun.";
        }

        $successMsg = implode(' ', $msgParts);
        if ($insertedCount === 0 && $skippedCount === 0 && empty($errors)) {
            return $redirect->with('userError', 'Tidak ada baris data siswa yang dapat diproses dari file.');
        }

        if (! empty($errors)) {
            $errPreview = implode('<br>', array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $errPreview .= '<br>...dan ' . (count($errors) - 10) . ' catatan lainnya.';
            }
            if ($insertedCount > 0) {
                return $redirect->with('userMessage', $successMsg)->with('userError', $errPreview);
            }
            return $redirect->with('userError', $errPreview);
        }

        return $redirect->with('userMessage', $successMsg);
    }

    /**
     * Membaca file CSV / TSV dengan auto-detect delimiter koma, titik-koma, atau tab
     */
    private function readDelimitedFile(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membuka file CSV/delimit.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new RuntimeException('File kosong.');
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $delimiter = ',';
        $highestCount = 1;
        foreach ([',', ';', "\t"] as $candidate) {
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

    /**
     * Format array rows menjadi Response download CSV ber-BOM UTF-8
     */
    private function formatCsvResponse(string $filename, array $rows): ResponseInterface
    {
        $fp = fopen('php://temp', 'r+');
        // Add UTF-8 BOM agar aksen dan karakter Indonesia terbaca sempurna di Microsoft Excel
        fputs($fp, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($fp, $row, ';');
        }
        rewind($fp);
        $csvData = stream_get_contents($fp);
        fclose($fp);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($csvData);
    }
}
