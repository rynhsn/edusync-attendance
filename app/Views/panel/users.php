<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Data User</h1>
        <p class="text-muted mb-0">Daftar akun dan pengaturan role. Hanya Super Admin yang dapat menetapkan role.</p>
    </div>
    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Daftar User</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($users); ?> user</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Atur Role</th>
                        <th scope="col">Status Email</th>
                        <th scope="col">Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= esc($user['display_name'] ?: '-'); ?></td>
                            <td><?= esc($user['username'] ?: '-'); ?></td>
                            <td><?= esc($user['email']); ?></td>
                            <td><span class="badge bg-primary-subtle text-primary"><?= esc($roles[$user['role']] ?? 'Tidak dikenal'); ?></span></td>
                            <td>
                                <?php if ((int) $user['user_id'] === (int) (session()->get('authUser')['user_id'] ?? 0)) : ?>
                                    <span class="text-muted small">Role akun aktif</span>
                                <?php else : ?>
                                    <form class="d-flex gap-2" method="post" action="<?= site_url('users/role/' . $user['user_id']); ?>">
                                        <?= csrf_field(); ?>
                                        <select class="form-select form-select-sm" name="role" aria-label="Pilih role <?= esc($user['display_name'] ?: $user['email']); ?>">
                                            <?php foreach ($roles as $roleValue => $roleLabel) : ?>
                                                <option value="<?= esc($roleValue); ?>" <?= $user['role'] === $roleValue ? 'selected' : ''; ?>><?= esc($roleLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary text-nowrap" type="submit">Simpan</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= (bool) $user['is_verified'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                                    <?= (bool) $user['is_verified'] ? 'Terverifikasi' : 'Belum diverifikasi'; ?>
                                </span>
                            </td>
                            <td><?= esc($user['created_at'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($users === []) : ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data user.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
