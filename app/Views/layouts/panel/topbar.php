<?php
$currentUser = session()->get('authUser') ?? [];
$recentNotifications = [];
$unreadNotificationCount = 0;
if (($currentUser['role'] ?? '') === 'super_admin') {
    $notificationModel = new \App\Models\AdminNotificationModel();
    if ($notificationModel->isAvailable()) {
        $recentNotifications = $notificationModel->latestForUser((int) $currentUser['user_id']);
        $unreadNotificationCount = $notificationModel->unreadCountForUser((int) $currentUser['user_id']);
    }
}
?>
<!-- Header -->
<header class="admin-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <!-- Logo/Brand - Now first on the left -->
            <a class="navbar-brand d-flex align-items-center" href="./index.html">
                <img src="<?= base_url('assets/img/branding/logo.png'); ?>" alt="Logo Miftahul Huda" height="32" class="d-inline-block align-text-top me-2">
                <span class="h4 mb-0 fw-bold text-primary-emphasis">Miftahul Huda</span>
            </a>

            <!-- Sidebar Toggle -->
            <button class="hamburger-menu" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>

            <!-- Search Bar with Alpine.js -->
            <div class="search-container flex-grow-1 mx-4" x-data="searchComponent">
                <div class="position-relative">
                    <input type="search"
                        class="form-control"
                        placeholder="Search... (Ctrl+K)"
                        x-model="query"
                        @input="search()"
                        data-search-input
                        aria-label="Search">
                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3"></i>

                    <!-- Search Results Dropdown -->
                    <div x-show="results.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="position-absolute top-100 start-0 w-100 bg-white border rounded-2 shadow-lg mt-1 z-3">
                        <template x-for="result in results" :key="result.title">
                            <a :href="result.url" class="d-block px-3 py-2 text-decoration-none text-dark border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-text me-2 text-muted"></i>
                                    <span x-text="result.title"></span>
                                    <small class="ms-auto text-muted" x-text="result.type"></small>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Side Icons -->
            <div class="navbar-nav flex-row">
                <!-- Theme Toggle with Alpine.js -->
                <div x-data="themeSwitch">
                    <button class="btn btn-outline-secondary me-2"
                        type="button"
                        @click="toggle()"
                        data-bs-toggle="tooltip"
                        data-bs-placement="bottom"
                        title="Toggle theme">
                        <i class="bi bi-sun-fill" x-show="currentTheme === 'light'"></i>
                        <i class="bi bi-moon-fill" x-show="currentTheme === 'dark'"></i>
                    </button>
                </div>

                <?php $currentLocale = session()->get('locale') === 'en' ? 'en' : 'id'; ?>
                <div class="btn-group me-2" role="group" aria-label="Pilih bahasa">
                    <?php foreach (['id' => 'In', 'en' => 'Eng'] as $localeCode => $localeLabel) : ?>
                        <form method="post" action="<?= site_url('language'); ?>" class="d-inline">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="locale" value="<?= esc($localeCode); ?>">
                            <button class="btn btn-sm <?= $currentLocale === $localeCode ? 'btn-primary' : 'btn-outline-secondary'; ?>"
                                type="submit"
                                aria-pressed="<?= $currentLocale === $localeCode ? 'true' : 'false'; ?>"
                                aria-label="<?= $localeCode === 'id' ? 'Bahasa Indonesia' : 'English'; ?>">
                                <?= esc($localeLabel); ?>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>

                <!-- Fullscreen Toggle (hidden on phones) -->
                <button class="btn btn-outline-secondary me-2 d-none d-lg-inline-block"
                    type="button"
                    data-fullscreen-toggle
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title="Toggle fullscreen">
                    <i class="bi bi-arrows-fullscreen icon-hover"></i>
                </button>

                <?php if (($currentUser['role'] ?? '') === 'super_admin') : ?>
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary position-relative"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="Notifikasi">
                            <i class="bi bi-bell"></i>
                            <?php if ($unreadNotificationCount > 0) : ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount; ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notifikasi</h6></li>
                            <?php if ($recentNotifications === []) : ?>
                                <li><span class="dropdown-item-text text-muted small">Belum ada notifikasi.</span></li>
                            <?php else : ?>
                                <?php foreach ($recentNotifications as $notification) : ?>
                                    <li>
                                        <a class="dropdown-item text-wrap <?= $notification['read_at'] === null ? 'fw-semibold' : ''; ?>" href="<?= site_url('notifications/open/' . (int) $notification['id']); ?>">
                                            <span data-no-translate><?= esc($notification['title']); ?></span>
                                            <small class="d-block text-muted" data-no-translate><?= esc($notification['message']); ?></small>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="<?= site_url('notifications'); ?>">Lihat semua notifikasi</a></li>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary d-flex align-items-center"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <?php if (! empty($currentUser['avatar_path'])) : ?>
                            <img src="<?= site_url('profile/photo'); ?>"
                                alt="Foto profil"
                                width="24"
                                height="24"
                                class="rounded-circle me-2">
                        <?php else : ?>
                            <i class="bi bi-person-circle me-2" aria-hidden="true"></i>
                        <?php endif; ?>
                        <span class="d-none d-md-inline"><?= esc($currentUser['display_name'] ?? 'Akun'); ?></span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= site_url('profile'); ?>"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="post" action="<?= site_url('auth/logout'); ?>">
                                <?= csrf_field(); ?>
                                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>