<!DOCTYPE html>
<html lang="en" data-coreui-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.5.0/dist/css/coreui.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="<?= base_url('css/simplebar.css') ?>" rel="stylesheet">
    <style>
        .sidebar-brand { overflow:hidden; }
        .sidebar-nav {
            --cui-sidebar-nav-padding-x: 0.25rem;
            --cui-sidebar-nav-padding-y: 0.25rem;
            --cui-sidebar-nav-gap: 0;
            --cui-sidebar-nav-link-padding-x: 0.75rem;
            --cui-sidebar-nav-link-padding-y: 0.5rem;
            --cui-sidebar-nav-link-icon-margin: 0.5rem;
            --cui-sidebar-nav-link-icon-width: 1rem;
            --cui-sidebar-nav-link-icon-height: 1rem;
            --cui-sidebar-nav-link-icon-font-size: 1rem;
        }
        .sidebar-nav .nav-title {
            margin-top: 0;
        }
        .sidebar-nav .nav-link {
            border-left: 3px solid transparent;
            border-radius: 0 !important;
        }
        .sidebar-nav .nav-link.active {
            border-left-color: #60a5fa;
            background: linear-gradient(90deg, rgba(96, 165, 250, 0.15) 0%, transparent 100%);
            color: #60a5fa;
            font-weight: 500;
        }
        .sidebar-nav[data-simplebar] {
            overflow: visible !important;
            max-height: calc(100vh - 60px);
        }
        .wrapper {
            padding-inline: var(--cui-sidebar-occupy-start, 0) var(--cui-sidebar-occupy-end, 0);
            transition: padding .15s;
        }
        .table-crud thead th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--cui-secondary-color);
            border-bottom: 2px solid var(--cui-border-color);
            padding: 0.75rem 1rem;
            background: transparent;
        }
        .table-crud tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--cui-border-color);
        }
        .table-crud tbody tr:hover {
            background-color: rgba(var(--cui-primary-rgb), 0.03);
        }
        .table-crud tbody tr:last-child td {
            border-bottom: none;
        }
        .news-pagination {
            display:flex;
            gap:4px;
        }
        .np-btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:36px;
            height:36px;
            padding:0 8px;
            border-radius:var(--cui-border-radius);
            border:1px solid var(--cui-border-color);
            font-size:0.85rem;
            color:var(--cui-body-color);
            background:var(--cui-body-bg);
            text-decoration:none;
            transition:all .15s;
        }
        .np-btn:hover {
            border-color:var(--cui-primary);
            color:var(--cui-primary);
        }
        .np-btn.active {
            background:var(--cui-primary);
            border-color:var(--cui-primary);
            color:#fff;
        }
    </style>
</head>
<body class="small">
    <?php
    $uri = service('uri');
    $activeBase = $uri->getSegment(1) . '/' . $uri->getSegment(2);
    ?>
    <div class="sidebar sidebar-sm sidebar-dark sidebar-fixed sidebar-narrow-unfoldable border-end" id="sidebar">
        <div class="sidebar-header border-bottom">
            <div class="sidebar-brand">
                <h6 class="sidebar-brand-full fw-bold mb-0 d-flex align-items-center gap-2" style="min-width:0"><span class="text-truncate" style="min-width:0;flex:1"><?= esc($site_name ?? 'Sekolahku') ?></span></h6>
                <span class="sidebar-brand-narrow"><i class="fas fa-school"></i></span>
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
        </div>
        <ul class="sidebar-nav simplebar-scrollable-y" data-simplebar>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/dashboard' ? 'active' : '' ?>" href="/admin/dashboard"><i class="nav-icon fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li class="nav-title">Konten</li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/news' ? 'active' : '' ?>" href="/admin/news"><i class="nav-icon fas fa-newspaper"></i>Berita</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/teachers' ? 'active' : '' ?>" href="/admin/teachers"><i class="nav-icon fas fa-chalkboard-teacher"></i>Guru</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/programs' ? 'active' : '' ?>" href="/admin/programs"><i class="nav-icon fas fa-book"></i>Program</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/extracurriculars' ? 'active' : '' ?>" href="/admin/extracurriculars"><i class="nav-icon fas fa-futbol"></i>Ekskul</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/achievements' ? 'active' : '' ?>" href="/admin/achievements"><i class="nav-icon fas fa-trophy"></i>Prestasi</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/testimonials' ? 'active' : '' ?>" href="/admin/testimonials"><i class="nav-icon fas fa-quote-right"></i>Testimoni</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/events' ? 'active' : '' ?>" href="/admin/events"><i class="nav-icon fas fa-calendar"></i>Agenda</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/gallery' ? 'active' : '' ?>" href="/admin/gallery"><i class="nav-icon fas fa-images"></i>Galeri</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/faq' ? 'active' : '' ?>" href="/admin/faq"><i class="nav-icon fas fa-question-circle"></i>FAQ</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/downloads' ? 'active' : '' ?>" href="/admin/downloads"><i class="nav-icon fas fa-download"></i>Download</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/pages' ? 'active' : '' ?>" href="/admin/pages"><i class="nav-icon fas fa-file-alt"></i>Halaman</a></li>
            <li class="nav-title">Layanan</li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/messages' ? 'active' : '' ?>" href="/admin/messages"><i class="nav-icon fas fa-envelope"></i>Pesan</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/comments' ? 'active' : '' ?>" href="/admin/comments"><i class="nav-icon fas fa-comments"></i>Komentar</a></li>
            <li class="nav-title">Sistem</li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/uploads' ? 'active' : '' ?>" href="/admin/uploads"><i class="nav-icon fas fa-cloud-upload-alt"></i>Upload</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/users' ? 'active' : '' ?>" href="/admin/users"><i class="nav-icon fas fa-users"></i>Pengguna</a></li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/settings' ? 'active' : '' ?>" href="/admin/settings"><i class="nav-icon fas fa-cog"></i>Pengaturan</a></li>
            <li class="nav-title">Navigasi</li>
            <li class="nav-item"><a class="nav-link <?= $activeBase === 'admin/menus' ? 'active' : '' ?>" href="/admin/menus"><i class="nav-icon fas fa-bars"></i>Menu</a></li>
            <li class="nav-item"><a class="nav-link" href="/" target="_blank"><i class="nav-icon fas fa-external-link-alt"></i>Lihat Situs</a></li>
        </ul>
        <div class="sidebar-footer border-top d-none d-md-flex">
            <button class="sidebar-toggler" type="button"></button>
        </div>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
        <header class="header header-sticky bg-white border-bottom px-4 py-2 d-flex justify-content-between align-items-center">
            <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-flex align-items-center">
                <a href="/admin/profile" class="btn btn-light btn-sm me-3 rounded-pill border"><i class="fas fa-user me-1"></i><?= $user['fullName'] ?? $user['username'] ?? 'Admin' ?></a>
                <a href="/logout" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </header>
        <main class="main flex-grow-1 p-4 bg-body-tertiary">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-coreui-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-coreui-dismiss="alert"></button></div>
            <?php endif; ?>
            <?= $this->renderSection('content') ?>
        </main>
        <footer class="footer border-top px-4 py-3 text-body-tertiary small bg-white">
            <div class="d-flex justify-content-between w-100">
                <span>&copy; <?= date('Y') ?> CMS Sekolahku <?= APP_VERSION ?>. All rights reserved.</span>
                <span>CMS by <a href="https://sekolahku.web.id" target="_blank" class="text-decoration-none fw-medium" style="color:#6366f1">sekolahku.web.id</a></span>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.5.0/dist/js/coreui.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="<?= base_url('js/simplebar.min.js') ?>"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const sidebar = document.querySelector(".sidebar");
        const toggler = document.querySelector(".sidebar-toggler");
        if (!sidebar || !toggler) return;

        let saved = window.localStorage.getItem("cmsSidebarState");
        if (!saved) {
            saved = "narrow";
            window.localStorage.setItem("cmsSidebarState", "narrow");
        }
        if (saved === "narrow") {
            sidebar.classList.add("sidebar-narrow-unfoldable");
        } else {
            sidebar.classList.remove("sidebar-narrow-unfoldable");
        }

        toggler.addEventListener("click", () => {
            const narrow = sidebar.classList.toggle("sidebar-narrow-unfoldable");
            window.localStorage.setItem("cmsSidebarState", narrow ? "narrow" : "wide");
        });
    });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
