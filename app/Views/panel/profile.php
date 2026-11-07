<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Profil Saya</h1>
        <p class="text-muted mb-0">Kelola informasi profil akun dan data guru yang terhubung.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 flex-shrink-0"></i>
            <div><?= esc($message); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 flex-shrink-0"></i>
            <div><?= esc($error); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (! empty($passwordMessage)) : ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-shield-check me-2 fs-5 flex-shrink-0"></i>
            <div><?= esc($passwordMessage); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('profile'); ?>" enctype="multipart/form-data">
        <?= csrf_field(); ?>

        <div class="row g-4">
            <!-- Kolom Kiri: Foto Profil & Ringkasan Akun -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <?php if (! empty($user['avatar_path'])) : ?>
                                <img id="profilePhotoPreview" src="<?= site_url('profile/photo'); ?>" alt="Foto Profil" width="130" height="130" class="rounded-circle border border-3 border-light shadow-sm object-fit-cover">
                            <?php else : ?>
                                <div id="profilePhotoFallback" class="rounded-circle border border-3 border-light shadow-sm bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 130px; height: 130px;">
                                    <i class="bi bi-person-fill text-secondary" style="font-size: 4rem;"></i>
                                </div>
                                <img id="profilePhotoPreview" src="" alt="Foto Profil" width="130" height="130" class="rounded-circle border border-3 border-light shadow-sm object-fit-cover d-none">
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-bold mb-1"><?= esc($user['display_name']); ?></h5>
                        <p class="text-muted small mb-4">@<?= esc($user['username']); ?></p>

                        <div class="text-start bg-light rounded-3 p-3 mb-3 border">
                            <label class="form-label fw-semibold small mb-1" for="profilePhoto">Unggah Foto Baru</label>
                            <input class="form-control form-control-sm" id="profilePhoto" name="photo" type="file" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text small text-muted mt-1">
                                Format JPG, PNG, atau WebP (Maks. 2 MB).
                            </div>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <a href="<?= site_url('profile/id-card'); ?>" target="_blank" class="btn btn-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm text-nowrap" title="Cetak / Pratinjau ID Card">
                                    <i class="bi bi-person-badge-fill"></i> <span>Generate ID Card</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 text-nowrap" id="btnOpenResetPassword" data-bs-toggle="modal" data-bs-target="#modalResetPassword">
                                    <i class="bi bi-key-fill"></i> <span>Reset Password</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Akun & Data Guru -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span>Informasi Akun User</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium" for="profileName">Nama Lengkap <span class="text-danger">*</span></label>
                                <input class="form-control" id="profileName" name="display_name" value="<?= esc($user['display_name']); ?>" maxlength="100" autocomplete="name" required placeholder="Masukkan nama lengkap">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" for="profileUsername">Username <span class="text-danger">*</span></label>
                                <input class="form-control" id="profileUsername" name="username" value="<?= esc($user['username']); ?>" minlength="3" maxlength="32" pattern="[A-Za-z0-9._-]{3,32}" autocomplete="username" required placeholder="Username akun">
                                <div class="form-text small">3–32 karakter: huruf, angka, titik, atau tanda hubung.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium" for="profileEmail">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input class="form-control bg-light" id="profileEmail" value="<?= esc($user['email']); ?>" readonly>
                                </div>
                                <div class="form-text small text-muted">Email terverifikasi tidak dapat diubah dari halaman ini.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Data Guru -->
                <?php if (($user['role'] ?? '') !== 'siswa') : ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-person-workspace text-primary"></i>
                                <span>Data Guru (Tersinkronisasi)</span>
                            </h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-arrow-repeat me-1"></i> Sinkron Otomatis
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-3">
                                <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0"></i>
                                <div>
                                    Perubahan data di bawah ini akan <strong>otomatis tersinkronisasi</strong> dengan Data Guru.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium" for="guruNip">NIP Guru</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                        <input class="form-control" id="guruNip" name="guru_nip" value="<?= esc($teacher['guru_nip'] ?? ''); ?>" maxlength="30" placeholder="Contoh: 198501012010011001">
                                    </div>
                                    <div class="form-text small">Kosongkan jika belum memiliki NIP.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" for="guruTelepon">Nomor Telepon / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                                        <input class="form-control" id="guruTelepon" name="guru_telepon" value="<?= esc($teacher['guru_telepon'] ?? ''); ?>" maxlength="30" placeholder="Contoh: 081234567890">
                                    </div>
                                    <div class="form-text small">Nomor kontak aktif untuk komunikasi.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" for="tempatLahir">Tempat Lahir</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input class="form-control" id="tempatLahir" name="tempat_lahir" value="<?= esc($teacher['tempat_lahir'] ?? ''); ?>" maxlength="50" placeholder="Contoh: Jakarta">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" for="tanggalLahir">Tanggal Lahir</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                        <input type="date" class="form-control" id="tanggalLahir" name="tanggal_lahir" value="<?= esc($teacher['tanggal_lahir'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-medium" for="alamat">Alamat Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                                        <input class="form-control" id="alamat" name="alamat" value="<?= esc($teacher['alamat'] ?? ''); ?>" placeholder="Jalan / Kampung / Dusun / Kelurahan">
                                    </div>
                                </div>

                                <div class="col-md-2 col-6">
                                    <label class="form-label fw-medium" for="rt">RT</label>
                                    <input class="form-control" id="rt" name="rt" value="<?= esc($teacher['rt'] ?? ''); ?>" maxlength="10" placeholder="001">
                                </div>

                                <div class="col-md-2 col-6">
                                    <label class="form-label fw-medium" for="rw">RW</label>
                                    <input class="form-control" id="rw" name="rw" value="<?= esc($teacher['rw'] ?? ''); ?>" maxlength="10" placeholder="002">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (($user['role'] ?? '') === 'siswa' && ! empty($student)) : ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-mortarboard text-primary"></i>
                                <span>Data Siswa Terkait</span>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Nama Siswa</label>
                                    <input class="form-control bg-light" value="<?= esc($student['nama_siswa'] ?? ''); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">NIS</label>
                                    <input class="form-control bg-light" value="<?= esc($student['nis'] ?? '-'); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Kelas</label>
                                    <input class="form-control bg-light" value="<?= esc($student['kelas_nama'] ?? '-'); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2" type="submit">
                        <i class="bi bi-check2-circle fs-5"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <a href="<?= site_url('panel'); ?>" class="btn btn-light px-3 py-2 border">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Popup Reset / Ubah Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="<?= site_url('profile/password'); ?>" id="formResetPassword">
                <?= csrf_field(); ?>
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modalResetPasswordLabel">
                        <i class="bi bi-key-fill"></i> Reset / Ubah Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if (! empty($passwordError)) : ?>
                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 small mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 flex-shrink-0"></i>
                            <div><?= esc($passwordError); ?></div>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted small mb-3">
                        Masukkan password saat ini untuk memverifikasi akun Anda, lalu tentukan password baru.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-medium" for="modalCurrentPassword">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input class="form-control" id="modalCurrentPassword" name="current_password" type="password" autocomplete="current-password" required placeholder="Masukkan password saat ini">
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="modalCurrentPassword" title="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium" for="modalNewPassword">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input class="form-control" id="modalNewPassword" name="new_password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required placeholder="Minimal 8 karakter">
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="modalNewPassword" title="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text small">Panjang password baru 8–72 karakter.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-medium" for="modalNewPasswordConfirm">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <input class="form-control" id="modalNewPasswordConfirm" name="new_password_confirm" type="password" minlength="8" maxlength="72" autocomplete="new-password" required placeholder="Ketik ulang password baru">
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="modalNewPasswordConfirm" title="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2">
                        <i class="bi bi-key-fill"></i>
                        <span>Perbarui Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Instant Preview Foto Profil
    const photoInput = document.getElementById('profilePhoto');
    const photoPreview = document.getElementById('profilePhotoPreview');
    const photoFallback = document.getElementById('profilePhotoFallback');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            const [file] = this.files;
            if (file) {
                photoPreview.src = URL.createObjectURL(file);
                photoPreview.classList.remove('d-none');
                if (photoFallback) {
                    photoFallback.classList.add('d-none');
                }
            }
        });
    }

    // Toggle Eye Show/Hide Password
    document.querySelectorAll('.btn-toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });

    // Reset Password Modal Button Trigger & Fallback
    const btnOpenReset = document.getElementById('btnOpenResetPassword');
    const modalResetEl = document.getElementById('modalResetPassword');
    if (btnOpenReset && modalResetEl) {
        btnOpenReset.addEventListener('click', function () {
            if (window.bootstrap && window.bootstrap.Modal) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(modalResetEl);
                modal.show();
            }
        });
    }

    <?php if (! empty($passwordError)) : ?>
    // Otomatis buka popup modal jika ada error password
    if (modalResetEl && window.bootstrap && window.bootstrap.Modal) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalResetEl);
        modal.show();
    }
    <?php endif; ?>
});
</script>
<?= $this->endSection(); ?>
