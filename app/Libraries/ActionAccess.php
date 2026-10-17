<?php

namespace App\Libraries;

class ActionAccess
{
    private static ?array $savedSettings = null;
    private static ?array $savedUserSettings = null;

    public static function catalog(): array
    {
        return [
            'Absensi Kelas' => [
                'class-attendance.create' => 'Catat absensi kelas',
                'class-attendance.edit' => 'Edit absensi kelas',
                'class-attendance.delete' => 'Hapus absensi kelas',
                'class-attendance.whatsapp' => 'Kirim notifikasi dan rekap absensi melalui WhatsApp',
            ],
            'Data Kelas dan Jadwal' => [
                'class.manage' => 'Tambah atau edit kelas',
                'class.create' => 'Tambah kelas melalui API',
                'class.edit' => 'Edit kelas melalui API',
                'class.delete' => 'Hapus kelas',
                'major.manage' => 'Tambah atau edit jurusan',
                'major.create' => 'Tambah jurusan melalui API',
                'major.edit' => 'Edit jurusan melalui API',
                'major.delete' => 'Hapus jurusan',
                'timesetting.manage' => 'Kelola pengaturan waktu',
            ],
            'Data Siswa' => [
                'student.create' => 'Tambah siswa',
                'student.edit' => 'Edit siswa',
                'student.delete' => 'Hapus siswa',
                'student.status' => 'Ubah status siswa',
                'student.import' => 'Import siswa',
            ],
            'Master Akademik' => [
                'subjects.create' => 'Tambah mata pelajaran',
                'subjects.edit' => 'Edit mata pelajaran',
                'subjects.delete' => 'Hapus mata pelajaran',
                'teachers.create' => 'Tambah guru',
                'teachers.edit' => 'Edit guru',
                'teachers.delete' => 'Hapus guru',
                'parents.create' => 'Tambah orang tua',
                'parents.edit' => 'Edit orang tua',
                'parents.delete' => 'Hapus orang tua',
                'lesson-periods.create' => 'Tambah jam pelajaran',
                'lesson-periods.edit' => 'Edit jam pelajaran',
                'lesson-periods.delete' => 'Hapus jam pelajaran',
                'extracurriculars.create' => 'Tambah ekstrakurikuler',
                'extracurriculars.edit' => 'Edit ekstrakurikuler',
                'extracurriculars.delete' => 'Hapus ekstrakurikuler',
                'kokurikuler.create' => 'Tambah kokurikuler',
                'kokurikuler.edit' => 'Edit kokurikuler',
                'kokurikuler.delete' => 'Hapus kokurikuler',
                'academic-years.create' => 'Tambah tahun ajaran',
                'academic-years.edit' => 'Edit tahun ajaran',
                'academic-years.delete' => 'Hapus tahun ajaran',
                'attendance-status.manage' => 'Ubah status absensi',
            ],
            'Nilai dan Penilaian' => [
                'grades.save' => 'Simpan nilai siswa',
                'academic-assessments.save' => 'Simpan penilaian raport',
            ],
            'Absensi Ekstrakurikuler' => [
                'extracurricular-attendance.save' => 'Simpan absensi ekstrakurikuler',
            ],
            'Website Sekolah' => [
                'website-news.manage' => 'Tambah atau edit berita website',
                'website-news.delete' => 'Hapus berita website',
                'school-contact-settings.save' => 'Simpan pengaturan kontak sekolah',
                'website-home-settings.save' => 'Simpan pengaturan beranda website',
                'website-spmb-schedule.save' => 'Simpan jadwal SPMB Online',
                'website-profile-settings.save' => 'Simpan profil website',
                'website-programs.section' => 'Simpan pengaturan bagian program',
                'website-programs.manage' => 'Tambah atau edit program website',
                'website-programs.delete' => 'Hapus program website',
                'website-content.manage' => 'Tambah atau edit konten website',
                'website-content.delete' => 'Hapus konten website',
                'teacher-subjects.save' => 'Simpan penetapan mapel guru',
            ],
        ];
    }

    public static function defaults(string $role): array
    {
        if ($role === 'super_admin') {
            return array_fill_keys(array_keys(self::flatCatalog()), true);
        }

        // These actions were already available to all roles before configurable action access.
        if (in_array($role, ['user', 'kepala_sekolah', 'bendahara', 'kurikulum', 'operator'], true)) {
            return [
                'grades.save' => true,
                'academic-assessments.save' => true,
            ];
        }

        return [];
    }

    public static function can(string $actionKey, ?string $role = null): bool
    {
        if (! array_key_exists($actionKey, self::flatCatalog())) {
            return false;
        }

        $role ??= (string) (session()->get('authUser')['role'] ?? '');
        if ($role === 'super_admin') {
            return true;
        }

        $userId = (int) (session()->get('authUser')['user_id'] ?? 0);
        $userSettings = self::settingsForUser($userId);
        if (array_key_exists($actionKey, $userSettings)) {
            return $userSettings[$actionKey];
        }

        $saved = self::settingsFor($role);

        return $saved[$actionKey] ?? (self::defaults($role)[$actionKey] ?? false);
    }

    public static function settingsFor(string $role): array
    {
        if (self::$savedSettings === null) {
            self::$savedSettings = [];
            $rows = db_connect()->table('role_action_settings')
                ->select('role, action_key, is_enabled')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                self::$savedSettings[$row['role']][$row['action_key']] = (bool) $row['is_enabled'];
            }
        }

        return self::$savedSettings[$role] ?? [];
    }

    public static function settingsForUser(int $userId): array
    {
        if (self::$savedUserSettings === null) {
            self::$savedUserSettings = [];
            $rows = db_connect()->table('user_action_settings')
                ->select('user_id, action_key, is_enabled')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                self::$savedUserSettings[(int) $row['user_id']][$row['action_key']] = (bool) $row['is_enabled'];
            }
        }

        return self::$savedUserSettings[$userId] ?? [];
    }

    public static function flatCatalog(): array
    {
        $actions = [];
        foreach (self::catalog() as $group) {
            $actions += $group;
        }

        return $actions;
    }
}
