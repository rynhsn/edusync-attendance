<?= $this->extend('layouts/auth'); ?>

<?= $this->section('content'); ?>
<main class="card auth-card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <a class="auth-brand" href="<?= site_url('login'); ?>">EduSync</a>
        <h1 class="h3 mt-4 mb-2">Verifikasi Email</h1>
        <p class="text-muted mb-4">Masukkan kode 6 digit yang dikirim ke <strong><?= esc($email); ?></strong>. Kode berlaku 10 menit.</p>

        <?php if (! empty($message)) : ?>
            <div class="alert alert-success" role="status"><?= esc($message); ?></div>
        <?php endif; ?>
        <?php if (! empty($error)) : ?>
            <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('auth/verify-code'); ?>">
            <?= csrf_field(); ?>
            <input type="hidden" name="email" value="<?= esc($email, 'attr'); ?>">
            <div class="mb-3">
                <label class="form-label" for="otpCode">Kode verifikasi</label>
                <input class="form-control form-control-lg text-center" id="otpCode" name="code" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="one-time-code" required autofocus>
            </div>
            <button class="btn btn-primary w-100" type="submit">Verifikasi Email</button>
        </form>

        <form class="mt-3" method="post" action="<?= site_url('auth/resend-code'); ?>">
            <?= csrf_field(); ?>
            <button class="btn btn-outline-secondary w-100" type="submit">Kirim Ulang Kode</button>
        </form>
    </div>
</main>
<?= $this->endSection(); ?>
