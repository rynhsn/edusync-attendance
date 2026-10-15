<?= $this->extend('layouts/auth'); ?>

<?= $this->section('content'); ?>
<main class="card auth-card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5 text-center">
        <a class="auth-brand" href="<?= site_url('login'); ?>">EduSync</a>
        <h1 class="h3 mt-4">Akses Admin Dibatasi</h1>
        <p class="text-muted">Akun Anda terverifikasi, tetapi tidak memiliki role Super Admin.</p>
        <form method="post" action="<?= site_url('auth/logout'); ?>">
            <?= csrf_field(); ?>
            <button class="btn btn-outline-primary" type="submit">Keluar</button>
        </form>
    </div>
</main>
<?= $this->endSection(); ?>
