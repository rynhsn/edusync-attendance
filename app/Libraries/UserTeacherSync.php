<?php

namespace App\Libraries;

use App\Models\AuthUserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class UserTeacherSync
{
    /**
     * Pastikan data guru ada untuk email dan nama tertentu saat registrasi user,
     * dan kembalikan guru_id untuk di-link ke auth_users.
     */
    public static function ensureTeacherForRegistration(string $email, string $displayName): int
    {
        $db = db_connect();
        $email = strtolower(trim($email));
        $displayName = trim($displayName);

        // Cari apakah guru dengan email ini sudah ada di master_guru
        $existing = $db->table('master_guru')
            ->where('guru_email', $email)
            ->get()
            ->getRowArray();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $teacherId = (int) $existing['guru_id'];
            // Jika nama guru di master_guru masih kosong atau default, perbarui dengan display_name
            if (empty($existing['guru_nama']) || $existing['guru_nama'] === 'Guru') {
                $db->table('master_guru')
                    ->where('guru_id', $teacherId)
                    ->update([
                        'guru_nama'   => $displayName,
                        'updated_at'  => $now,
                    ]);
            }
            return $teacherId;
        }

        // Jika belum ada, buat data guru baru
        $db->table('master_guru')->insert([
            'guru_nama'    => $displayName !== '' ? $displayName : 'Guru',
            'guru_email'   => $email,
            'guru_nip'     => null,
            'guru_telepon' => null,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        return (int) $db->insertID();
    }

    /**
     * Sinkronisasi akun user ke data guru (membuat baru atau memperbarui).
     */
    public static function syncUserToTeacher(int $userId, array $extraTeacherData = []): ?int
    {
        $db = db_connect();
        $usersModel = new AuthUserModel();
        $user = $usersModel->find($userId);

        if (! $user || ($user['role'] ?? '') === 'siswa') {
            return null;
        }

        $email       = strtolower(trim((string) $user['email']));
        $displayName = trim((string) $user['display_name']);
        if ($displayName === '') {
            $displayName = trim((string) $user['username']);
        }
        $now = date('Y-m-d H:i:s');

        $teacherId = ! empty($user['guru_id']) ? (int) $user['guru_id'] : null;
        $teacher   = null;

        if ($teacherId !== null) {
            $teacher = $db->table('master_guru')->where('guru_id', $teacherId)->get()->getRowArray();
        }

        if (! $teacher && $email !== '') {
            $teacher = $db->table('master_guru')->where('guru_email', $email)->get()->getRowArray();
            if ($teacher) {
                $teacherId = (int) $teacher['guru_id'];
                $usersModel->update($userId, ['guru_id' => $teacherId]);
            }
        }

        $dataToUpdate = [
            'guru_nama'  => $displayName,
            'guru_email' => $email,
            'updated_at' => $now,
        ];

        if (array_key_exists('guru_nip', $extraTeacherData)) {
            $nip = trim((string) $extraTeacherData['guru_nip']);
            $dataToUpdate['guru_nip'] = $nip !== '' ? $nip : null;
        }
        if (array_key_exists('guru_telepon', $extraTeacherData)) {
            $telp = trim((string) $extraTeacherData['guru_telepon']);
            $dataToUpdate['guru_telepon'] = $telp !== '' ? $telp : null;
        }
        if (array_key_exists('tempat_lahir', $extraTeacherData)) {
            $tempat = trim((string) $extraTeacherData['tempat_lahir']);
            $dataToUpdate['tempat_lahir'] = $tempat !== '' ? $tempat : null;
        }
        if (array_key_exists('tanggal_lahir', $extraTeacherData)) {
            $tgl = trim((string) $extraTeacherData['tanggal_lahir']);
            $dataToUpdate['tanggal_lahir'] = $tgl !== '' ? $tgl : null;
        }
        if (array_key_exists('alamat', $extraTeacherData)) {
            $alamat = trim((string) $extraTeacherData['alamat']);
            $dataToUpdate['alamat'] = $alamat !== '' ? $alamat : null;
        }
        if (array_key_exists('rt', $extraTeacherData)) {
            $rt = trim((string) $extraTeacherData['rt']);
            $dataToUpdate['rt'] = $rt !== '' ? $rt : null;
        }
        if (array_key_exists('rw', $extraTeacherData)) {
            $rw = trim((string) $extraTeacherData['rw']);
            $dataToUpdate['rw'] = $rw !== '' ? $rw : null;
        }
        if (array_key_exists('rfid_tag', $extraTeacherData)) {
            $rfid = trim((string) $extraTeacherData['rfid_tag']);
            $dataToUpdate['rfid_tag'] = $rfid !== '' ? $rfid : null;
        }
        if (array_key_exists('foto', $extraTeacherData)) {
            $foto = trim((string) $extraTeacherData['foto']);
            $dataToUpdate['foto'] = $foto !== '' ? $foto : null;
        }

        if ($teacher) {
            $db->table('master_guru')->where('guru_id', $teacherId)->update($dataToUpdate);
            return $teacherId;
        }

        // Buat baru jika belum ada
        $dataToInsert = array_merge([
            'guru_nama'    => $displayName,
            'guru_email'   => $email,
            'guru_nip'     => null,
            'guru_telepon' => null,
            'created_at'   => $now,
            'updated_at'   => $now,
        ], $dataToUpdate);

        $db->table('master_guru')->insert($dataToInsert);
        $newTeacherId = (int) $db->insertID();

        $usersModel->update($userId, ['guru_id' => $newTeacherId]);

        return $newTeacherId;
    }

    /**
     * Memastikan semua user di auth_users yang belum memiliki guru_id disinkronkan ke master_guru.
     */
    public static function syncAllUnlinkedUsers(): int
    {
        $db = db_connect();
        $unlinked = $db->table('auth_users')
            ->select('user_id, display_name, username, email, guru_id, role')
            ->where('role !=', 'siswa')
            ->groupStart()
                ->where('guru_id IS NULL', null, false)
                ->orWhere('guru_id', 0)
            ->groupEnd()
            ->get()
            ->getResultArray();

        $syncedCount = 0;
        foreach ($unlinked as $u) {
            $teacherId = self::syncUserToTeacher((int) $u['user_id']);
            if ($teacherId !== null) {
                $syncedCount++;
            }
        }

        return $syncedCount;
    }

    /**
     * Update user di Data User sekaligus sinkron ke master_guru.
     * Mengembalikan array ['success' => bool, 'message' => string].
     */
    public static function updateUserAndTeacher(int $userId, array $postData): array
    {
        $db = db_connect();
        $usersModel = new AuthUserModel();
        $user = $usersModel->find($userId);

        if (! $user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        $displayName = trim((string) ($postData['display_name'] ?? ''));
        $username    = strtolower(trim((string) ($postData['username'] ?? '')));
        $email       = strtolower(trim((string) ($postData['email'] ?? '')));
        $role        = (string) ($postData['role'] ?? $user['role']);
        $password    = (string) ($postData['password'] ?? '');
        $guruNip     = trim((string) ($postData['guru_nip'] ?? ''));
        $guruTelepon = trim((string) ($postData['guru_telepon'] ?? ''));
        $rfidTag     = trim((string) ($postData['rfid_tag'] ?? ''));

        // Validasi input
        if ($displayName === '' || mb_strlen($displayName) > 100) {
            return ['success' => false, 'message' => 'Nama wajib diisi dan maksimal 100 karakter.'];
        }

        if (preg_match('/^[a-z0-9._-]{3,32}$/', $username) !== 1) {
            return ['success' => false, 'message' => 'Username harus 3–32 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.'];
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
            return ['success' => false, 'message' => 'Format email tidak valid.'];
        }

        if (! array_key_exists($role, RoleAccess::ROLES)) {
            return ['success' => false, 'message' => 'Role tidak valid.'];
        }

        // Cek username unik
        $dupUsername = $usersModel->where('username', $username)->where('user_id !=', $userId)->first();
        if ($dupUsername) {
            return ['success' => false, 'message' => 'Username sudah digunakan oleh akun lain.'];
        }

        // Cek email unik
        $dupEmail = $usersModel->where('email', $email)->where('user_id !=', $userId)->first();
        if ($dupEmail) {
            return ['success' => false, 'message' => 'Email sudah digunakan oleh akun lain.'];
        }

        // Validasi password jika diisi
        if ($password !== '' && (strlen($password) < 8 || strlen($password) > 72)) {
            return ['success' => false, 'message' => 'Password baru harus terdiri dari 8–72 karakter.'];
        }

        // Cek proteksi super admin terakhir
        if ($user['role'] === 'super_admin' && $role !== 'super_admin') {
            $superAdminCount = $usersModel->where('role', 'super_admin')->countAllResults();
            if ($superAdminCount <= 1) {
                return ['success' => false, 'message' => 'Role Super Admin terakhir tidak dapat diturunkan.'];
            }
            $configuredAdminEmail = strtolower(trim((string) env('auth.super_admin_email')));
            if (strcasecmp((string) $user['email'], $configuredAdminEmail) === 0) {
                return ['success' => false, 'message' => 'Akun Super Admin utama tidak dapat diturunkan rolenya.'];
            }
        }

        $currentTeacherId = ! empty($user['guru_id']) ? (int) $user['guru_id'] : 0;
        if ($currentTeacherId === 0 && ! empty($user['email'])) {
            $existingTeacher = $db->table('master_guru')->where('guru_email', strtolower(trim((string) $user['email'])))->get()->getRowArray();
            if ($existingTeacher) {
                $currentTeacherId = (int) $existingTeacher['guru_id'];
            }
        }

        // Cek NIP unik jika diisi dan bukan role siswa
        if ($role !== 'siswa' && $guruNip !== '') {
            $builder = $db->table('master_guru')->where('guru_nip', $guruNip);
            if ($currentTeacherId > 0) {
                $builder->where('guru_id !=', $currentTeacherId);
            }
            if ($builder->countAllResults() > 0) {
                return ['success' => false, 'message' => "NIP '{$guruNip}' sudah digunakan oleh guru lain."];
            }
        }

        // Cek RFID tag unik jika diisi dan bukan role siswa
        if ($role !== 'siswa' && $rfidTag !== '') {
            $builderTeacher = $db->table('master_guru')->where('rfid_tag', $rfidTag);
            if ($currentTeacherId > 0) {
                $builderTeacher->where('guru_id !=', $currentTeacherId);
            }
            if ($builderTeacher->countAllResults() > 0) {
                return ['success' => false, 'message' => "RFID Tag '{$rfidTag}' sudah digunakan oleh guru lain."];
            }

            if ($db->table('master_siswa')->where('rfid_tag', $rfidTag)->countAllResults() > 0) {
                return ['success' => false, 'message' => "RFID Tag '{$rfidTag}' sudah digunakan oleh siswa."];
            }
        }

        $db->transBegin();

        try {
            // Update auth_users
            $userUpdateData = [
                'display_name' => $displayName,
                'username'     => $username,
                'email'        => $email,
                'role'         => $role,
            ];
            $status = (string) ($postData['status'] ?? ($user['status'] ?? 'active'));
            if (in_array($status, ['active', 'nonaktif'], true)) {
                $userUpdateData['status'] = $status;
            }
            if ($password !== '') {
                $userUpdateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $usersModel->update($userId, $userUpdateData);

            // Update master_guru jika bukan siswa
            if ($role !== 'siswa') {
                $extraData = [
                    'guru_nip'     => $guruNip,
                    'guru_telepon' => $guruTelepon,
                    'rfid_tag'     => $rfidTag,
                ];

                self::syncUserToTeacher($userId, $extraData);
            }

            if ($db->transStatus() === false) {
                throw new DatabaseException('Transaksi pembaharuan user dan guru gagal.');
            }

            $db->transCommit();

            return ['success' => true, 'message' => 'Data User dan Data Guru berhasil diperbarui.'];
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Update user and teacher error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()];
        }
    }
}
