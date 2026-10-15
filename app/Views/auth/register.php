<?= $this->extend('layouts/auth'); ?>

<?= $this->section('content'); ?>
<main class="card auth-card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <a class="auth-brand" href="<?= site_url('login'); ?>">EduSync</a>
        <h1 class="h3 mt-4 mb-2">Register</h1>
        <p class="text-muted mb-4">Buat akun, lalu verifikasi email dengan kode OTP. Kode hanya diperlukan satu kali saat pendaftaran.</p>

        <?php if (! empty($message)) : ?>
            <div class="alert alert-success" role="status"><?= esc($message); ?></div>
        <?php endif; ?>
        <?php if (! empty($error)) : ?>
            <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('auth/register'); ?>">
            <?= csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label" for="registerName">Nama</label>
                <input class="form-control" id="registerName" name="display_name" maxlength="100" autocomplete="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="registerUsername">Username</label>
                <input class="form-control" id="registerUsername" name="username" minlength="3" maxlength="32" pattern="[A-Za-z0-9._-]{3,32}" autocomplete="username" required>
                <div class="form-text">3–32 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="registerPassword">Password</label>
                <input class="form-control" id="registerPassword" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
                <div class="form-text">Minimal 8 karakter.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="registerEmail">Email</label>
                <input class="form-control" id="registerEmail" name="email" type="email" maxlength="254" autocomplete="email" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Daftar dan Kirim Kode OTP</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Sudah punya akun? <a href="<?= site_url('login'); ?>">Login</a>
        </p>
    </div>
</main>
<?= $this->endSection(); ?>
