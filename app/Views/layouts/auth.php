<!doctype html>
<html lang="id" data-bs-theme="light">

<head>
    <!-- Kita aktifkan kembali base href karena folder dist-modern sudah ada -->
    <base href="<?= base_url('metis-1.0.0/dist-modern/'); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Autentikasi EduSync">
    <title><?= esc($title ?? 'Autentikasi'); ?> - EduSync</title>

    <!-- Favicon Aman (Tanpa fungsi filemtime yang merusak jika file belum disalin) -->
    <link rel="icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>" type="image/x-icon">

    <!-- Mengembalikan CSS utama bawaan template menggunakan base href internal -->
    <link rel="stylesheet" crossorigin href="./assets/main-Kuf3cpUe.css">

    <style>
        body.auth-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background: #f5f6fa;
        }

        .auth-card {
            width: min(100%, 460px);
        }

        .auth-brand {
            color: #2563eb;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>

<body class="auth-page">
    <?= $this->renderSection('content'); ?>
</body>

</html>