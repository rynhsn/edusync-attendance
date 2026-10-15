<?= $this->extend('layouts/auth'); ?>

<?= $this->section('content'); ?>
<main class="card auth-card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <a class="auth-brand" href="<?= site_url('login'); ?>">EduSync</a>
        <h1 class="h3 mt-4 mb-2">Login Admin</h1>
        <p class="text-muted mb-4">Masuk menggunakan username dan password yang sudah terdaftar.</p>

        <?php if (! empty($message)) : ?>
            <div class="alert alert-success" role="status"><?= esc($message); ?></div>
        <?php endif; ?>
        <?php if (! empty($error)) : ?>
            <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('auth/login'); ?>">
            <?= csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label" for="loginUsername">Username</label>
                <input class="form-control" id="loginUsername" name="username" maxlength="32" autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="loginPassword">Password</label>
                <input class="form-control" id="loginPassword" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Belum punya akun? <a href="<?= site_url('register'); ?>">Register</a>
        </p>
    </div>
</main>
<?= $this->endSection(); ?>
