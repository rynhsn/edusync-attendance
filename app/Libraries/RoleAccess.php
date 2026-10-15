<?php

namespace App\Libraries;

class RoleAccess
{
    private static ?array $savedSettings = null;
    private static ?array $menuCatalogSections = null;

    public const ROLES = [
        'super_admin' => 'Super Admin',
        'user' => 'User',
        'kepala_sekolah' => 'Kepala Sekolah',
        'bendahara' => 'Bendahara',
        'kurikulum' => 'Kurikulum',
        'operator' => 'Operator',
    ];

    public static function label(string $role): string
    {
        return self::ROLES[$role] ?? 'User';
    }

    public static function dashboardDescription(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Kelola data sekolah, pengguna, dan seluruh pencatatan absensi.',
            'kepala_sekolah' => 'Pantau ringkasan kehadiran dan laporan sekolah.',
            'bendahara' => 'Pantau ringkasan kehadiran sekolah. Modul keuangan belum tersedia.',
            'kurikulum' => 'Kelola dan pantau informasi akademik sesuai akses yang diberikan Super Admin.',
            'operator' => 'Kelola operasional sekolah sesuai akses yang diberikan Super Admin.',
            default => 'Selamat datang di EduSync. Akses menu Anda disesuaikan dengan role akun.',
        };
    }

    public static function menu(string $role): array
    {
        $groups = [
            [
                'title' => null,
                'items' => [
                    'panel' => ['label' => 'Dashboard', 'url' => 'panel', 'icon' => 'bi-speedometer2', 'page' => 'panel'],
                ],
            ],
            [
                'title' => 'Master Data',
                'key' => 'master-data',
                'items' => [
                    'student' => ['label' => 'Data Siswa', 'url' => 'student', 'icon' => 'bi-people', 'page' => 'student'],
                    'parents' => ['label' => 'Data Orang Tua', 'url' => 'parents', 'icon' => 'bi-person-hearts', 'page' => 'parents'],
                    'teachers' => ['label' => 'Data Guru', 'url' => 'teachers', 'icon' => 'bi-person-workspace', 'page' => 'teachers'],
                    'users' => ['label' => 'Data User', 'url' => 'users', 'icon' => 'bi-person-gear', 'page' => 'users'],
                ],
            ],
            [
                'title' => 'Master Akademik',
                'key' => 'master-academic',
                'items' => [
                    'academic-years' => ['label' => 'Tahun Ajaran', 'url' => 'academic-years', 'icon' => 'bi-calendar3', 'page' => 'academic-years'],
                    'major' => ['label' => 'Data Jurusan', 'url' => 'major', 'icon' => 'bi-diagram-3', 'page' => 'major'],
                    'class' => ['label' => 'Data Kelas', 'url' => 'class', 'icon' => 'bi-door-open', 'page' => 'class'],
                    'subjects' => ['label' => 'Data Mapel', 'url' => 'subjects', 'icon' => 'bi-book', 'page' => 'subjects'],
                    'lesson-periods' => ['label' => 'Data Jam Pelajaran', 'url' => 'lesson-periods', 'icon' => 'bi-clock', 'page' => 'lesson-periods'],
                    'extracurriculars' => ['label' => 'Data Ekstrakurikuler', 'url' => 'extracurriculars', 'icon' => 'bi-stars', 'page' => 'extracurriculars'],
                    'kokurikuler' => ['label' => 'Data Kokurikuler', 'url' => 'kokurikuler', 'icon' => 'bi-mortarboard', 'page' => 'kokurikuler'],
                    'grades' => ['label' => 'Input Nilai', 'url' => 'grades', 'icon' => 'bi-clipboard-data', 'page' => 'grades', 'submenu' => 'assessment'],
                    'grade-report' => ['label' => 'Raport Siswa', 'url' => 'report/grades', 'icon' => 'bi-file-earmark-medical', 'page' => 'grade-report'],
                    'personality-assessment' => ['label' => 'Kepribadian', 'url' => 'academic-assessments/personality', 'icon' => 'bi-person-heart', 'page' => 'personality-assessment', 'submenu' => 'assessment'],
                    'extracurricular-assessment' => ['label' => 'Ekstrakurikuler', 'url' => 'academic-assessments/extracurricular', 'icon' => 'bi-stars', 'page' => 'extracurricular-assessment', 'submenu' => 'assessment'],
                    'kokurikuler-assessment' => ['label' => 'Kokurikuler', 'url' => 'academic-assessments/kokurikuler', 'icon' => 'bi-mortarboard', 'page' => 'kokurikuler-assessment', 'submenu' => 'assessment'],
                ],
            ],
            [
                'title' => 'Laporan Absensi',
                'key' => 'attendance-reports',
                'items' => [
                    'attendance' => ['label' => 'Absensi RFID', 'url' => 'attendance', 'icon' => 'bi-upc-scan', 'page' => 'attendance', 'submenu' => 'attendance'],
                    'class-attendance' => ['label' => 'Data Absensi Kelas', 'url' => 'class-attendance', 'icon' => 'bi-journal-check', 'page' => 'class-attendance', 'submenu' => 'attendance'],
                    'extracurricular-attendance' => ['label' => 'Absensi Ekstrakurikuler', 'url' => 'extracurricular-attendance', 'icon' => 'bi-stars', 'page' => 'extracurricular-attendance', 'submenu' => 'attendance'],
                    'attendance-report' => ['label' => 'Laporan Absensi RFID', 'url' => 'report/attendance', 'icon' => 'bi-file-earmark-text', 'page' => 'attendance-report', 'submenu' => 'reports'],
                    'extracurricular-attendance-report' => ['label' => 'Laporan Absensi Ekstrakurikuler', 'url' => 'report/extracurricular-attendance', 'icon' => 'bi-file-earmark-text', 'page' => 'extracurricular-attendance-report', 'submenu' => 'reports'],
                    'class-attendance-report' => ['label' => 'Laporan Absensi Kelas', 'url' => 'class-attendance-report', 'icon' => 'bi-file-earmark-bar-graph', 'page' => 'class-attendance-report', 'submenu' => 'reports'],
                ],
            ],
            [
                'title' => 'Settings',
                'key' => 'settings',
                'items' => [
                    'timesetting' => ['label' => 'Pengaturan Waktu', 'url' => 'timesetting', 'icon' => 'bi-clock-history', 'page' => 'timesetting'],
                    'attendance-status' => ['label' => 'Status Absensi', 'url' => 'attendance-status', 'icon' => 'bi-list-check', 'page' => 'attendance-status'],
                    'user-passwords' => ['label' => 'Reset Password User', 'url' => 'user-passwords', 'icon' => 'bi-key', 'page' => 'user-passwords'],
                    'sidebar-settings' => ['label' => 'Setting Akses Menu Role', 'url' => 'sidebar-settings', 'icon' => 'bi-layout-sidebar', 'page' => 'sidebar-settings'],
                    'button-settings' => ['label' => 'Pengaturan Akses Tombol', 'url' => 'button-settings', 'icon' => 'bi-toggles', 'page' => 'button-settings'],
                    'menu-settings' => ['label' => 'Pengaturan Menu', 'url' => 'menu-settings', 'icon' => 'bi-list-nested', 'page' => 'menu-settings'],
                    'teacher-subjects' => ['label' => 'Mapel Guru', 'url' => 'teacher-subjects', 'icon' => 'bi-journal-bookmark', 'page' => 'teacher-subjects'],
                ],
            ],
            [
                'title' => 'Website Sekolah',
                'key' => 'school-website',
                'items' => [
                    'website-home-settings' => ['label' => 'Pengaturan Beranda', 'url' => 'website-settings/home', 'icon' => 'bi-house', 'page' => 'website-home-settings'],
                    'website-profile-settings' => ['label' => 'Pengaturan Profil', 'url' => 'website-settings/profile', 'icon' => 'bi-building', 'page' => 'website-profile-settings'],
                    'website-program-settings' => ['label' => 'Pengaturan Program', 'url' => 'website-settings/programs', 'icon' => 'bi-grid', 'page' => 'website-program-settings'],
                    'school-contact-settings' => ['label' => 'Pengaturan Kontak', 'url' => 'school-contact-settings', 'icon' => 'bi-telephone', 'page' => 'school-contact-settings'],
                    'website-content-achievements' => ['label' => 'Prestasi', 'url' => 'website-content/achievements', 'icon' => 'bi-trophy', 'page' => 'website-content-achievements'],
                    'website-content-extracurriculars' => ['label' => 'Ekstrakurikuler', 'url' => 'website-content/extracurriculars', 'icon' => 'bi-stars', 'page' => 'website-content-extracurriculars'],
                    'website-content-testimonials' => ['label' => 'Testimoni', 'url' => 'website-content/testimonials', 'icon' => 'bi-chat-quote', 'page' => 'website-content-testimonials'],
                    'website-content-gallery' => ['label' => 'Galeri', 'url' => 'website-content/gallery', 'icon' => 'bi-images', 'page' => 'website-content-gallery'],
                    'website-content-events' => ['label' => 'Agenda', 'url' => 'website-content/events', 'icon' => 'bi-calendar-event', 'page' => 'website-content-events'],
                    'website-content-faq' => ['label' => 'FAQ', 'url' => 'website-content/faq', 'icon' => 'bi-question-circle', 'page' => 'website-content-faq'],
                    'website-content-downloads' => ['label' => 'Download PDF', 'url' => 'website-content/downloads', 'icon' => 'bi-file-earmark-pdf', 'page' => 'website-content-downloads'],
                    'spmb-applications' => ['label' => 'Pendaftar SPMB', 'url' => 'spmb/applications', 'icon' => 'bi-person-vcard', 'page' => 'spmb-applications'],
                    'website-news' => ['label' => 'Kelola News', 'url' => 'website-news', 'icon' => 'bi-newspaper', 'page' => 'website-news'],
                    'website-messages' => ['label' => 'Pesan Website', 'url' => 'website-messages', 'icon' => 'bi-envelope', 'page' => 'website-messages'],
                    'schoolku-news-public' => ['label' => 'Lihat News', 'url' => 'news', 'icon' => 'bi-box-arrow-up-right', 'page' => 'schoolku-news-public'],
                ],
            ],
        ];

        $customMenuModel = new \App\Models\CustomSidebarMenuModel();
        $sectionItems = $customMenuModel->getForBuiltInSections();
        $iconOverrides = $customMenuModel->getBuiltInIconOverrides();
        foreach ($groups as &$group) {
            if (isset($group['key'], $iconOverrides[$group['key']])) {
                $group['icon'] = $iconOverrides[$group['key']];
            }
            foreach ($group['items'] as $itemKey => &$item) {
                if (isset($iconOverrides[$itemKey])) {
                    $item['icon'] = $iconOverrides[$itemKey];
                }
            }
            unset($item);

            foreach ($sectionItems[$group['key'] ?? ''] ?? [] as $item) {
                if (self::customLinkIsVisible($role, $item['url'], 'custom-' . $item['id'])) {
                    $group['items']['custom-' . $item['id']] = self::customMenuItem($item);
                }
            }
            $group['items'] = array_filter(
                $group['items'],
                static fn (array $item, string $key): bool => self::isEnabled($role, $key),
                ARRAY_FILTER_USE_BOTH
            );
        }
        unset($group);

        foreach ($customMenuModel->getTree() as $customMenu) {
            $items = [];
            foreach ($customMenu['children'] as $child) {
                if (self::customLinkIsVisible($role, $child['url'], 'custom-' . $child['id'])) {
                    $items['custom-' . $child['id']] = self::customMenuItem($child);
                }
            }

            if ($items !== []) {
                $groups[] = [
                    'title' => $customMenu['label'],
                    'icon' => $customMenu['icon'],
                    'items' => $items,
                ];
            } elseif ($customMenu['url'] !== '' && self::customLinkIsVisible($role, $customMenu['url'], 'custom-' . $customMenu['id'])) {
                $groups[] = [
                    'title' => null,
                    'items' => ['custom-' . $customMenu['id'] => self::customMenuItem($customMenu)],
                ];
            }
        }

        return array_values(array_filter($groups, static fn (array $group): bool => $group['items'] !== []));
    }

    public static function builtInMenuIconSettings(): array
    {
        $items = [];
        foreach (self::menu('super_admin') as $group) {
            if (isset($group['key'])) {
                $items[] = [
                    'key' => $group['key'],
                    'label' => $group['title'],
                    'parent' => 'Menu utama',
                    'icon' => $group['icon'] ?? ($group['title'] === 'Master Data' ? 'bi-puzzle' : 'bi-folder2-open'),
                ];
            }

            foreach ($group['items'] as $key => $item) {
                if (str_starts_with((string) $key, 'custom-')) {
                    continue;
                }

                $items[] = [
                    'key' => (string) $key,
                    'label' => $item['label'],
                    'parent' => $group['title'] ?? 'Menu utama',
                    'icon' => $item['icon'],
                ];
            }
        }

        return $items;
    }

    public static function catalog(): array
    {
        $catalog = [];
        foreach (self::catalogSections() as $items) {
            $catalog += $items;
        }

        return $catalog;
    }

    public static function catalogSections(): array
    {
        if (self::$menuCatalogSections !== null) {
            return self::$menuCatalogSections;
        }

        $sections = [
            'Umum' => [
                'panel' => 'Dashboard',
            ],
            'Master Data' => [
                'student' => 'Data Siswa',
                'parents' => 'Data Orang Tua',
                'teachers' => 'Data Guru',
                'users' => 'Data User',
            ],
            'Master Akademik' => [
                'academic-years' => 'Tahun Ajaran',
                'major' => 'Data Jurusan',
                'class' => 'Data Kelas',
                'subjects' => 'Data Mapel',
                'lesson-periods' => 'Data Jam Pelajaran',
                'extracurriculars' => 'Data Ekstrakurikuler',
                'kokurikuler' => 'Data Kokurikuler',
                'grades' => 'Input Nilai',
                'grade-report' => 'Raport Siswa',
                'personality-assessment' => 'Penilaian Kepribadian',
                'extracurricular-assessment' => 'Penilaian Ekstrakurikuler',
                'kokurikuler-assessment' => 'Penilaian Kokurikuler',
            ],
            'Laporan Absensi' => [
                'attendance' => 'Absensi RFID',
                'class-attendance' => 'Data Absensi Kelas',
                'attendance-report' => 'Laporan Absensi RFID',
                'extracurricular-attendance' => 'Absensi Ekstrakurikuler',
                'extracurricular-attendance-report' => 'Laporan Absensi Ekstrakurikuler',
                'class-attendance-report' => 'Laporan Absensi Kelas',
            ],
            'Pengaturan' => [
                'timesetting' => 'Pengaturan Waktu',
                'attendance-status' => 'Status Absensi',
                'user-passwords' => 'Reset Password User',
                'sidebar-settings' => 'Setting Akses Menu Role',
                'button-settings' => 'Pengaturan Akses Tombol',
                'menu-settings' => 'Pengaturan Menu',
                'teacher-subjects' => 'Mapel Guru',
            ],
            'Website Sekolah' => [
                'website-home-settings' => 'Pengaturan Beranda',
                'website-profile-settings' => 'Pengaturan Profil',
                'website-program-settings' => 'Pengaturan Program',
                'school-contact-settings' => 'Pengaturan Kontak',
                'website-content-achievements' => 'Prestasi',
                'website-content-extracurriculars' => 'Ekstrakurikuler',
                'website-content-testimonials' => 'Testimoni',
                'website-content-gallery' => 'Galeri',
                'website-content-events' => 'Agenda',
                'website-content-faq' => 'FAQ',
                'website-content-downloads' => 'Download PDF',
                'spmb-applications' => 'Pendaftar SPMB',
                'website-news' => 'Kelola News',
                'website-messages' => 'Pesan Website',
                'schoolku-news-public' => 'Lihat News',
            ],
        ];

        $customMenuModel = new \App\Models\CustomSidebarMenuModel();
        $builtInSections = self::menuSections();
        foreach ($customMenuModel->getForBuiltInSections() as $sectionKey => $items) {
            if (! isset($builtInSections[$sectionKey])) {
                continue;
            }
            foreach ($items as $item) {
                $sections[$builtInSections[$sectionKey]]['custom-' . $item['id']] = $item['label'];
            }
        }

        foreach ($customMenuModel->getTree() as $customMenu) {
            if ($customMenu['children'] !== []) {
                $sectionTitle = 'Menu Kustom: ' . $customMenu['label'] . ' (' . $customMenu['id'] . ')';
                foreach ($customMenu['children'] as $child) {
                    $sections[$sectionTitle]['custom-' . $child['id']] = $child['label'];
                }
            } elseif ($customMenu['url'] !== '') {
                $sections['Menu Kustom']['custom-' . $customMenu['id']] = $customMenu['label'];
            }
        }

        self::$menuCatalogSections = $sections;

        return self::$menuCatalogSections;
    }

    public static function menuSections(): array
    {
        return [
            'master-data' => 'Master Data',
            'master-academic' => 'Master Akademik',
            'attendance-reports' => 'Laporan Absensi',
            'settings' => 'Settings',
            'school-website' => 'Website Sekolah',
        ];
    }

    public static function defaults(string $role): array
    {
        $defaults = match ($role) {
            'super_admin' => array_fill_keys(array_keys(self::catalog()), true),
            'kepala_sekolah', 'bendahara' => [
                'panel' => true,
                'class-attendance' => true,
                'attendance-report' => true,
                'extracurricular-attendance' => true,
                'extracurricular-attendance-report' => true,
                'class-attendance-report' => true,
            ],
            'user' => ['panel' => true, 'student' => true],
            'kurikulum', 'operator' => ['panel' => true],
            default => ['panel' => true],
        };

        $defaults['schoolku-news-public'] = true;

        return $defaults;
    }

    public static function isEnabled(string $role, string $menuKey): bool
    {
        if (! array_key_exists($menuKey, self::catalog())) {
            return false;
        }

        $saved = self::settingsFor($role);

        return array_key_exists($menuKey, $saved)
            ? $saved[$menuKey]
            : (self::defaults($role)[$menuKey] ?? false);
    }

    public static function settingsFor(string $role): array
    {
        if (self::$savedSettings === null) {
            self::$savedSettings = [];
            $rows = db_connect()->table('role_sidebar_settings')
                ->select('role, menu_key, is_enabled')
                ->get()
                ->getResultArray();
            foreach ($rows as $row) {
                self::$savedSettings[$row['role']][$row['menu_key']] = (bool) $row['is_enabled'];
            }
        }

        return self::$savedSettings[$role] ?? [];
    }

    public static function menuKeyForUri(string $uri): ?string
    {
        $uri = trim($uri, '/');
        $prefixes = [
            'website-settings/home' => 'website-home-settings',
            'website-settings/profile' => 'website-profile-settings',
            'website-settings/programs' => 'website-program-settings',
            'website-content/achievements' => 'website-content-achievements',
            'website-content/extracurriculars' => 'website-content-extracurriculars',
            'website-content/testimonials' => 'website-content-testimonials',
            'website-content/gallery' => 'website-content-gallery',
            'website-content/events' => 'website-content-events',
            'website-content/faq' => 'website-content-faq',
            'website-content/downloads' => 'website-content-downloads',
            'website-content' => 'website-content',
            'school-contact-settings' => 'school-contact-settings',
            'spmb/applications' => 'spmb-applications',
            'website-messages' => 'website-messages',
            'teacher-subjects' => 'teacher-subjects',
            'parents' => 'parents',
            'website-news' => 'website-news',
            'user-passwords' => 'user-passwords',
            'sidebar-settings' => 'sidebar-settings',
            'button-settings' => 'button-settings',
            'menu-settings' => 'menu-settings',
            'report/attendance' => 'attendance-report',
            'report/extracurricular-attendance' => 'extracurricular-attendance-report',
            'extracurricular-attendance' => 'extracurricular-attendance',
            'class-attendance-report' => 'class-attendance-report',
            'report/grades' => 'grade-report',
            'academic-assessments/personality' => 'personality-assessment',
            'academic-assessments/extracurricular' => 'extracurricular-assessment',
            'academic-assessments/kokurikuler' => 'kokurikuler-assessment',
            'class-attendance' => 'class-attendance',
            'attendance' => 'attendance',
            'academic-years' => 'academic-years',
            'lesson-periods' => 'lesson-periods',
            'kokurikuler' => 'kokurikuler',
            'extracurriculars' => 'extracurriculars',
            'grades' => 'grades',
            'attendance-status' => 'attendance-status',
            'timesetting' => 'timesetting',
            'subjects' => 'subjects',
            'teachers' => 'teachers',
            'student' => 'student',
            'class' => 'class',
            'major' => 'major',
            'users' => 'users',
        ];

        foreach ($prefixes as $prefix => $key) {
            if ($uri === $prefix || str_starts_with($uri, $prefix . '/')) {
                return $key;
            }
        }

        return null;
    }

    private static function customLinkIsVisible(string $role, string $url, string $permissionKey): bool
    {
        if ($role === 'super_admin') {
            return true;
        }

        $menuKey = self::menuKeyForUri(trim($url, '/'));

        return $menuKey !== null
            && self::isEnabled($role, $permissionKey)
            && self::isEnabled($role, $menuKey);
    }

    private static function customMenuItem(array $item): array
    {
        return [
            'label' => $item['label'],
            'url' => $item['url'],
            'icon' => $item['icon'],
            'page' => self::menuKeyForUri(trim($item['url'], '/')) ?? 'custom-' . $item['id'],
        ];
    }
}
