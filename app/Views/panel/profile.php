<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Profil Saya</h1>
        <p class="text-muted mb-0">Perbarui nama, username, dan foto profil Anda.</p>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <?php if (! empty($message)) : ?>
                <div class="alert alert-success" role="status"><?= esc($message); ?></div>
            <?php endif; ?>
            <?php if (! empty($error)) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('profile'); ?>" enctype="multipart/form-data">
                <?= csrf_field(); ?>

                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label d-block" for="profilePhoto">Foto Profil</label>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <?php if (! empty($user['avatar_path'])) : ?>
                                <img src="<?= site_url('profile/photo'); ?>" alt="Foto profil saat ini" width="88" height="88" class="rounded-circle border object-fit-cover">
                            <?php else : ?>
                                <div class="rounded-circle border bg-light d-flex align-items-center justify-content-center" style="width: 88px; height: 88px;" aria-label="Belum ada foto profil">
                                    <i class="bi bi-person text-secondary" style="font-size: 2.5rem;" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <input class="form-control" id="profilePhoto" name="photo" type="file" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text">JPG, PNG, atau WebP; maksimal 2 MB dan 6000 × 6000 piksel.</div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h2 class="h5 card-title mb-1">Ubah Password</h2>
                                <p class="text-muted small mb-0">Masukkan password saat ini untuk mengatur password baru.</p>
                            </div>
                            <div class="card-body p-4">
                                <?php if (! empty($passwordMessage)) : ?>
                                    <div class="alert alert-success" role="status"><?= esc($passwordMessage); ?></div>
                                <?php endif; ?>
                                <?php if (! empty($passwordError)) : ?>
                                    <div class="alert alert-danger" role="alert"><?= esc($passwordError); ?></div>
                                <?php endif; ?>

                                <form method="post" action="<?= site_url('profile/password'); ?>">
                                    <?= csrf_field(); ?>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label" for="currentPassword">Password Saat Ini</label>
                                            <input class="form-control" id="currentPassword" name="current_password" type="password" autocomplete="current-password" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="newPassword">Password Baru</label>
                                            <input class="form-control" id="newPassword" name="new_password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
                                            <div class="form-text">Minimal 8 karakter.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="newPasswordConfirm">Konfirmasi Password Baru</label>
                                            <input class="form-control" id="newPasswordConfirm" name="new_password_confirm" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary" type="submit">Perbarui Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="profileName">Nama</label>
                        <input class="form-control" id="profileName" name="display_name" value="<?= esc($user['display_name']); ?>" maxlength="100" autocomplete="name" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="profileUsername">Username</label>
                        <input class="form-control" id="profileUsername" name="username" value="<?= esc($user['username']); ?>" minlength="3" maxlength="32" pattern="[A-Za-z0-9._-]{3,32}" autocomplete="username" required>
                        <div class="form-text">3–32 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="profileEmail">Email</label>
                        <input class="form-control" id="profileEmail" value="<?= esc($user['email']); ?>" readonly>
                        <div class="form-text">Email terverifikasi tidak dapat diubah dari halaman profil.</div>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
