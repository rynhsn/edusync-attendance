<?php
$currentRole = (string) (session()->get('authUser')['role'] ?? 'user');
$menuGroups = \App\Libraries\RoleAccess::menu($currentRole);
$currentLocale = session()->get('locale') === 'en' ? 'en' : 'id';
$translatedGroupTitles = [
    'master-data' => 'Master Data',
    'master-academic' => 'Academic Master',
    'attendance-reports' => 'Attendance Reports',
    'settings' => 'Settings',
    'school-website' => 'School Website',
];
$indonesianGroupTitles = [
    'settings' => 'Pengaturan',
];
$translatedMenuLabels = [
    'panel' => 'Dashboard',
    'student' => 'Student Data',
    'parents' => 'Parent Data',
    'teachers' => 'Teacher Data',
    'teacher-duty-schedules' => 'Teacher Duty Schedule',
    'users' => 'User Data',
    'academic-years' => 'Academic Year',
    'major' => 'Majors',
    'class' => 'Classes',
    'subjects' => 'Subjects',
    'lesson-periods' => 'Lesson Periods',
    'extracurriculars' => 'Extracurricular Activities',
    'kokurikuler' => 'Co-curricular Activities',
    'grades' => 'Enter Grades',
    'grade-report' => 'Student Report Cards',
    'personality-assessment' => 'Personality Assessment',
    'extracurricular-assessment' => 'Extracurricular Assessment',
    'kokurikuler-assessment' => 'Co-curricular Assessment',
    'attendance' => 'RFID Attendance',
    'class-attendance' => 'Class Attendance Data',
    'extracurricular-attendance' => 'Extracurricular Attendance',
    'attendance-report' => 'RFID Attendance Report',
    'extracurricular-attendance-report' => 'Extracurricular Attendance Report',
    'class-attendance-report' => 'Class Attendance Report',
    'timesetting' => 'Time Settings',
    'attendance-status' => 'Attendance Status',
    'user-passwords' => 'Reset User Password',
    'sidebar-settings' => 'Role Menu Access Settings',
    'button-settings' => 'Button Access Settings',
    'menu-settings' => 'Menu Settings',
    'online-users' => 'Currently Logged-in Users',
    'teacher-subjects' => 'Teacher Subjects',
    'website-home-settings' => 'Homepage Settings',
    'website-spmb-schedule' => 'Online SPMB Schedule',
    'website-profile-settings' => 'Profile Settings',
    'website-program-settings' => 'Program Settings',
    'school-contact-settings' => 'Contact Settings',
    'website-content-achievements' => 'Achievements',
    'website-content-extracurriculars' => 'Extracurricular Activities',
    'website-content-testimonials' => 'Testimonials',
    'website-content-gallery' => 'Gallery',
    'website-content-events' => 'Events',
    'website-content-faq' => 'FAQ',
    'website-content-downloads' => 'PDF Downloads',
    'spmb-applications' => 'SPMB Applicants',
    'website-news' => 'Manage News',
    'website-messages' => 'Website Messages',
    'schoolku-news-public' => 'View News',
];
$translatedSubmenuLabels = [
    'academic-data' => 'Academic Data',
    'assessment' => 'Assessments',
    'attendance' => 'Attendance',
    'reports' => 'Reports',
    'website-settings' => 'Settings',
];

foreach ($menuGroups as &$menuGroup) {
    $groupKey = $menuGroup['key'] ?? null;
    if ($currentLocale === 'en' && $groupKey !== null && isset($translatedGroupTitles[$groupKey])) {
        $menuGroup['title'] = $translatedGroupTitles[$groupKey];
    } elseif ($groupKey !== null && isset($indonesianGroupTitles[$groupKey])) {
        $menuGroup['title'] = $indonesianGroupTitles[$groupKey];
    }

    foreach ($menuGroup['items'] as $itemKey => &$menuItem) {
        if ($currentLocale === 'en' && isset($translatedMenuLabels[$itemKey])) {
            $menuItem['label'] = $translatedMenuLabels[$itemKey];
        }
    }
    unset($menuItem);
}
unset($menuGroup);
?>
<!-- Sidebar -->
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-content">
        <nav class="sidebar-nav">
            <ul class="nav flex-column" id="sidebarMenuGroups">
                <?php foreach ($menuGroups as $groupIndex => $group) : ?>
                    <?php if ($group['title'] === null) : ?>
                        <?php foreach ($group['items'] as $itemKey => $item) : ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($activePage ?? '') === $item['page'] ? 'active' : ''; ?>" href="<?= site_url($item['url']); ?>" <?= $item['page'] === 'schoolku-news-public' ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <i class="bi <?= esc($item['icon']); ?>"></i>
                                    <span <?= str_starts_with((string) $itemKey, 'custom-') ? 'data-no-translate' : ''; ?>><?= esc($item['label']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php
                    $isMasterData = $group['title'] === 'Master Data';
                    $submenuId = $isMasterData ? 'masterDataSubmenu' : 'SettingSubmenu' . $groupIndex;
                    $groupPages = array_map(
                        static fn (array $item): string => $item['page'],
                        $group['items']
                    );
                    $isExpanded = in_array($activePage ?? '', $groupPages, true);
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#<?= esc($submenuId); ?>" data-bs-toggle="collapse" data-bs-target="#<?= esc($submenuId); ?>" aria-expanded="<?= $isExpanded ? 'true' : 'false'; ?>" aria-controls="<?= esc($submenuId); ?>">
                            <i class="bi <?= esc($group['icon'] ?? ($isMasterData ? 'bi-puzzle' : 'bi-folder2-open')); ?>"></i>
                            <span <?= isset($group['key']) ? '' : 'data-no-translate'; ?>><?= esc($group['title']); ?></span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse <?= $isExpanded ? 'show' : ''; ?>" id="<?= esc($submenuId); ?>" data-bs-parent="#sidebarMenuGroups">
                            <ul class="nav nav-submenu">
                                <?php $renderedSubmenus = []; ?>
                                <?php foreach ($group['items'] as $itemKey => $item) : ?>
                                    <?php if (isset($item['submenu'])) : ?>
                                        <?php
                                        $nestedMenuKey = $item['submenu'];
                                        if (isset($renderedSubmenus[$nestedMenuKey])) {
                                            continue;
                                        }
                                        $renderedSubmenus[$nestedMenuKey] = true;
                                        $nestedItems = array_filter(
                                            $group['items'],
                                            static fn (array $child): bool => ($child['submenu'] ?? null) === $nestedMenuKey
                                        );
                                        $nestedPages = array_map(
                                            static fn (array $child): string => $child['page'],
                                            $nestedItems
                                        );
                                        $nestedId = 'sidebarNested' . $groupIndex . ucfirst($nestedMenuKey);
                                        $nestedExpanded = in_array($activePage ?? '', $nestedPages, true);
                                        $nestedLabels = [
                                            'academic-data' => 'Data Akademik',
                                            'assessment' => 'Penilaian',
                                            'attendance' => 'Absensi',
                                            'reports' => 'Laporan',
                                            'website-settings' => 'Pengaturan',
                                        ];
                                        if ($currentLocale === 'en') {
                                            $nestedLabels = $translatedSubmenuLabels;
                                        }
                                        $nestedIcons = [
                                            'academic-data' => 'bi-collection',
                                            'assessment' => 'bi-ui-checks-grid',
                                            'attendance' => 'bi-clipboard-check',
                                            'reports' => 'bi-file-earmark-text',
                                            'website-settings' => 'bi-gear',
                                        ];
                                        ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?= $nestedExpanded ? 'active' : ''; ?>" href="#<?= esc($nestedId); ?>" data-bs-toggle="collapse" data-bs-target="#<?= esc($nestedId); ?>" aria-expanded="<?= $nestedExpanded ? 'true' : 'false'; ?>" aria-controls="<?= esc($nestedId); ?>">
                                                <i class="bi <?= esc($nestedIcons[$nestedMenuKey] ?? 'bi-folder2-open'); ?>"></i>
                                                <span><?= esc($nestedLabels[$nestedMenuKey] ?? ucfirst($nestedMenuKey)); ?></span>
                                                <i class="bi bi-chevron-down ms-auto"></i>
                                            </a>
                                            <div class="collapse <?= $nestedExpanded ? 'show' : ''; ?>" id="<?= esc($nestedId); ?>">
                                                <ul class="nav nav-submenu">
                                                    <?php foreach ($nestedItems as $nestedItemKey => $nestedItem) : ?>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?= ($activePage ?? '') === $nestedItem['page'] ? 'active' : ''; ?>" href="<?= site_url($nestedItem['url']); ?>">
                                                                <i class="bi <?= esc($nestedItem['icon']); ?>"></i>
                                                                <span <?= str_starts_with((string) $nestedItemKey, 'custom-') ? 'data-no-translate' : ''; ?>><?= esc($nestedItem['label']); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </li>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($activePage ?? '') === $item['page'] ? 'active' : ''; ?>" href="<?= site_url($item['url']); ?>">
                                            <i class="bi <?= esc($item['icon']); ?>"></i>
                                            <span <?= str_starts_with((string) $itemKey, 'custom-') ? 'data-no-translate' : ''; ?>><?= esc($item['label']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>

<!-- Sidebar Backdrop (mobile overlay) -->
<div class="sidebar-backdrop" aria-hidden="true"></div>
