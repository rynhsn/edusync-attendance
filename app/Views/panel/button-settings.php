<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Akses Tombol</h1>
        <p class="text-muted mb-0">Super Admin dapat memberikan izin aksi tambah, edit, hapus, dan simpan per role atau per akun user.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="alert alert-info" role="note">
        Izin berlaku untuk tombol di halaman dan endpoint prosesnya. Izin khusus user mengungguli izin role untuk tombol yang diatur; pilihan “Ikuti role” mewarisi pengaturan role user tersebut. Super Admin selalu memiliki seluruh izin.
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= site_url('button-settings'); ?>" class="row g-3 align-items-end">
                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="buttonTarget">Atur Akses Berdasarkan</label>
                    <select class="form-select" id="buttonTarget" name="target">
                        <option value="role" <?= $targetType === 'role' ? 'selected' : ''; ?>>Role</option>
                        <option value="user" <?= $targetType === 'user' ? 'selected' : ''; ?>>User tertentu</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3" id="buttonRoleWrap" <?= $targetType === 'user' ? 'hidden' : ''; ?>>
                    <label class="form-label" for="buttonRole">Pilih Role</label>
                    <select class="form-select" id="buttonRole" name="role">
                        <?php foreach ($roles as $role => $roleLabel) : ?>
                            <option value="<?= esc($role); ?>" <?= $selectedRole === $role ? 'selected' : ''; ?>><?= esc($roleLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-4" id="buttonUserWrap" <?= $targetType === 'role' ? 'hidden' : ''; ?>>
                    <label class="form-label" for="buttonUser">Pilih User</label>
                    <select class="form-select" id="buttonUser" name="user_id" <?= $targetType === 'user' ? 'required' : ''; ?>>
                        <?php foreach ($users as $user) : ?>
                            <option value="<?= (int) $user['user_id']; ?>" <?= $selectedUserId === (int) $user['user_id'] ? 'selected' : ''; ?>>
                                <?= esc(($user['display_name'] ?: $user['username'] ?: $user['email']) . ' — ' . $roles[$user['role']] . ' (' . $user['email'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Tampilkan Akses</button></div>
            </form>
        </div>
    </div>

    <?php if ($targetType === 'user' && $selectedUser === null) : ?>
        <div class="alert alert-warning" role="status">Belum ada akun non-Super Admin yang dapat diatur.</div>
    <?php else : ?>
    <div class="card">
        <div class="card-header">
            <h2 class="h5 mb-0">
                <?php if ($targetType === 'role') : ?>
                    Akses tombol untuk role <?= esc($roles[$selectedRole]); ?>
                <?php else : ?>
                    Akses tombol untuk <?= esc($selectedUser['display_name'] ?: $selectedUser['username'] ?: $selectedUser['email']); ?>
                    <span class="text-muted fs-6">(<?= esc($selectedUser['email']); ?>, <?= esc($roles[$selectedUser['role']]); ?>)</span>
                <?php endif; ?>
            </h2>
        </div>
        <div class="card-body">
            <form method="post" action="<?= site_url('button-settings'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="target" value="<?= esc($targetType); ?>">
                <?php if ($targetType === 'role') : ?>
                    <input type="hidden" name="role" value="<?= esc($selectedRole); ?>">
                <?php else : ?>
                    <input type="hidden" name="user_id" value="<?= (int) $selectedUserId; ?>">
                <?php endif; ?>
                <?php foreach ($actionGroups as $groupLabel => $actions) : ?>
                    <section class="mb-4" aria-labelledby="action-group-<?= esc(url_title($groupLabel, '-', true)); ?>">
                        <h3 class="h6 fw-bold border-bottom pb-2 mb-3" id="action-group-<?= esc(url_title($groupLabel, '-', true)); ?>"><?= esc($groupLabel); ?></h3>
                        <div class="row g-3">
                            <?php foreach ($actions as $key => $label) : ?>
                                <?php $inputId = 'action_' . str_replace('.', '_', $key); ?>
                                <div class="col-md-6 col-xl-4">
                                    <label class="panel-checkbox-card h-100" for="<?= esc($inputId); ?>">
                                        <?php if ($targetType === 'role') : ?>
                                            <input class="form-check-input" type="checkbox" id="<?= esc($inputId); ?>" name="actions[<?= esc($key); ?>]" value="1" <?= ! empty($settings[$key]) ? 'checked' : ''; ?>>
                                        <?php else : ?>
                                            <?php $userValue = array_key_exists($key, $userSettings) ? ($userSettings[$key] ? '1' : '0') : ''; ?>
                                            <select class="form-select form-select-sm mt-2" id="<?= esc($inputId); ?>" name="actions[<?= esc($key); ?>]">
                                                <option value="" <?= $userValue === '' ? 'selected' : ''; ?>>Ikuti role (<?= ! empty($settings[$key]) ? 'Diizinkan' : 'Tidak diizinkan'; ?>)</option>
                                                <option value="1" <?= $userValue === '1' ? 'selected' : ''; ?>>Izinkan</option>
                                                <option value="0" <?= $userValue === '0' ? 'selected' : ''; ?>>Jangan izinkan</option>
                                            </select>
                                        <?php endif; ?>
                                        <span class="fw-semibold"><?= esc($label); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
                <button class="btn btn-primary" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Pengaturan</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<script>
(() => {
    const target = document.getElementById('buttonTarget');
    const roleWrap = document.getElementById('buttonRoleWrap');
    const userWrap = document.getElementById('buttonUserWrap');
    const roleSelect = document.getElementById('buttonRole');
    const userSelect = document.getElementById('buttonUser');
    if (!target || !roleWrap || !userWrap || !roleSelect || !userSelect) return;
    target.addEventListener('change', () => {
        const isUser = target.value === 'user';
        roleWrap.hidden = isUser;
        userWrap.hidden = !isUser;
        roleSelect.disabled = isUser;
        userSelect.disabled = !isUser;
        userSelect.required = isUser;
    });
})();
</script>
<?= $this->endSection(); ?>
