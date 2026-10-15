<?php
$currentRole = (string) (session()->get('authUser')['role'] ?? 'user');
$menuGroups = \App\Libraries\RoleAccess::menu($currentRole);
?>
<!-- Sidebar -->
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-content">
        <nav class="sidebar-nav">
            <ul class="nav flex-column" id="sidebarMenuGroups">
                <?php foreach ($menuGroups as $groupIndex => $group) : ?>
                    <?php if ($group['title'] === null) : ?>
                        <?php foreach ($group['items'] as $item) : ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($activePage ?? '') === $item['page'] ? 'active' : ''; ?>" href="<?= site_url($item['url']); ?>" <?= $item['page'] === 'schoolku-news-public' ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <i class="bi <?= esc($item['icon']); ?>"></i>
                                    <span><?= esc($item['label']); ?></span>
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
                            <span><?= esc($group['title']); ?></span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse <?= $isExpanded ? 'show' : ''; ?>" id="<?= esc($submenuId); ?>" data-bs-parent="#sidebarMenuGroups">
                            <ul class="nav nav-submenu">
                                <?php $renderedSubmenus = []; ?>
                                <?php foreach ($group['items'] as $item) : ?>
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
                                            'assessment' => 'Penilaian',
                                            'attendance' => 'Absensi',
                                            'reports' => 'Laporan',
                                        ];
                                        $nestedIcons = [
                                            'assessment' => 'bi-ui-checks-grid',
                                            'attendance' => 'bi-clipboard-check',
                                            'reports' => 'bi-file-earmark-text',
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
                                                    <?php foreach ($nestedItems as $nestedItem) : ?>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?= ($activePage ?? '') === $nestedItem['page'] ? 'active' : ''; ?>" href="<?= site_url($nestedItem['url']); ?>">
                                                                <i class="bi <?= esc($nestedItem['icon']); ?>"></i>
                                                                <span><?= esc($nestedItem['label']); ?></span>
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
                                            <span><?= esc($item['label']); ?></span>
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
