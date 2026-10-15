<!doctype html>
<html lang="id" data-bs-theme="light">

<head>
    <base href="<?= base_url('metis-1.0.0/dist-modern/'); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Autentikasi EduSync">
    <title><?= esc($title ?? 'Autentikasi'); ?> - EduSync</title>
    <!-- <link rel="icon" type="image/svg+xml" href="./assets/icons/favicon.svg"> -->
    <link rel="icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>?v=<?= filemtime(FCPATH . 'assets/img/favicon/favicon.ico') ?>" type="image/x-icon">
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