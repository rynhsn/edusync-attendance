<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Kita kunci base href langsung ke folder template di public -->
    <base href="<?= base_url('metis-1.0.0/dist-modern/') ?>">

    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="description" content="Modern Bootstrap 5 Admin Template - Clean, responsive dashboard">
    <meta name="keywords" content="bootstrap, admin, dashboard, template, modern, responsive">
    <meta name="author" content="Bootstrap Admin Template">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Modern Bootstrap Admin Template">
    <meta property="og:description" content="Clean and modern admin dashboard template built with Bootstrap 5">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" type="image/x-icon">

    <!-- Title -->
    <title><?= esc($title ?? 'Dashboard') ?> - EduSync</title>

    <!-- Theme Color -->
    <meta name="theme-color" content="#2563eb">

    <!-- PWA Manifest & JS Template Utama -->
    <link rel="manifest" href="./assets/manifest-D0qHSrzp.json">
    <script type="module" crossorigin src="./assets/main-BqKkk_Lj.js"></script>
    <script type="module" crossorigin src="./assets/rolldown-runtime-hePW80VL.js"></script>
    <script type="module" crossorigin src="./assets/vendor-ui-Be8JR3ju.js"></script>
    <script type="module" crossorigin src="./assets/vendor-bootstrap-Dj44xPS1.js"></script>
    <script type="module" crossorigin src="./assets/vendor-charts-_UPf9h_O.js"></script>
    <script type="module" crossorigin src="./assets/main-1Eks1J0_.js"></script>

    <!-- CSS Utama Template -->
    <link rel="stylesheet" crossorigin href="./assets/main-Kuf3cpUe.css">

    <!-- CSS Tambahan (Menggunakan versi statis tanpa filemtime agar terhindar dari crash jalur file) -->
    <link rel="stylesheet" href="<?= base_url('assets/main/panel-checkbox-cards.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/panel-sidebar.css'); ?>">
</head>

<body data-page="dashboard" class="admin-layout">
    <a href="#main-content" class="skip-link">Skip to main content</a>
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Admin App Container -->
    <div class="admin-app">
        <!-- Main Wrapper -->
        <div class="admin-wrapper" id="admin-wrapper">