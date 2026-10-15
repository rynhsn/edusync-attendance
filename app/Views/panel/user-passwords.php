<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Reset Password User</h1>
        <p class="text-muted mb-0">Tetapkan password sementara baru untuk membantu user yang tidak dapat masuk ke akunnya.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="alert alert-info" role="note">
        Reset password hanya tersedia untuk akun selain Super Admin. Berikan password baru kepada pemilik akun melalui saluran yang aman.
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Daftar Akun</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($users); ?> user</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Password Baru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= esc($user['display_name'] ?: '-'); ?></td>
                            <td><?= esc($user['username'] ?: '-'); ?></td>
                            <td><?= esc($user['email']); ?></td>
                            <td><span class="badge bg-primary-subtle text-primary"><?= esc(\App\Libraries\RoleAccess::label($user['role'])); ?></span></td>
                            <td>
                                <span class="badge <?= (bool) $user['is_verified'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                                    <?= (bool) $user['is_verified'] ? 'Terverifikasi' : 'Belum diverifikasi'; ?>
                                </span>
                            </td>
                            <td>
                                <form class="d-flex flex-column flex-lg-row gap-2" method="post" action="<?= site_url('user-passwords/reset/' . $user['user_id']); ?>">
                                    <?= csrf_field(); ?>
                                    <input class="form-control form-control-sm" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Password baru" aria-label="Password baru untuk <?= esc($user['display_name'] ?: $user['email']); ?>" required>
                                    <input class="form-control form-control-sm" name="password_confirm" type="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Ulangi password" aria-label="Konfirmasi password untuk <?= esc($user['display_name'] ?: $user['email']); ?>" required>
                                    <button class="btn btn-sm btn-outline-danger text-nowrap" type="submit">Reset</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($users === []) : ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada akun user yang dapat direset.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
