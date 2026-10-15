<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Profil Saya</h4>
        <p class="text-body-tertiary small mb-0">Kelola email dan password akun Anda</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form action="/admin/profile/save" method="post">
            <?= csrf_field() ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-user-circle me-2 text-primary"></i>Informasi Akun</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-at text-body-tertiary"></i></span>
                            <input type="text" name="username" class="form-control border-start-0 ps-0" value="<?= esc($item['username'] ?? '') ?>" placeholder="username" required>
                        </div>
                        <div class="form-text small">Huruf dan angka saja, tidak boleh duplikat dengan akun lain</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-user text-body-tertiary"></i></span>
                            <input type="text" name="full_name" class="form-control border-start-0 ps-0" value="<?= esc($item['full_name'] ?? '') ?>" placeholder="Nama lengkap">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Role</label>
                        <div>
                            <?php if (($item['role'] ?? '') === 'superadmin'): ?>
                                <span class="badge bg-danger">Superadmin</span>
                            <?php elseif (($item['role'] ?? '') === 'admin'): ?>
                                <span class="badge bg-primary">Admin</span>
                            <?php elseif (($item['role'] ?? '') === 'editor'): ?>
                                <span class="badge bg-info">Editor</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-envelope me-2 text-primary"></i>Email</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-envelope text-body-tertiary"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0" value="<?= esc($item['email'] ?? '') ?>" placeholder="email@sekolah.sch.id">
                        </div>
                        <div class="form-text small">Kosongkan jika tidak ingin mengubah email</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-lock me-2 text-primary"></i>Ganti Password</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Password Lama</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-key text-body-tertiary"></i></span>
                            <input type="password" name="old_password" class="form-control border-start-0 ps-0" placeholder="Masukkan password lama" id="old-password">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-key text-body-tertiary"></i></span>
                            <input type="password" name="new_password" class="form-control border-start-0 ps-0" placeholder="Minimal 6 karakter" id="new-password">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-key text-body-tertiary"></i></span>
                            <input type="password" name="confirm_password" class="form-control border-start-0 ps-0" placeholder="Ulangi password baru" id="confirm-password">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="show-passwords">
                        <label class="form-check-label small fw-medium" for="show-passwords">Tampilkan password</label>
                    </div>
                    <div class="form-text small mt-2">Kosongkan semua field password jika tidak ingin mengubah password</div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                <a href="/admin/dashboard" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Info Akun</h6>
            </div>
            <div class="card-body small text-body-tertiary">
                <div class="d-flex justify-content-between mb-2 flex-wrap gap-1">
                    <span>Terdaftar</span>
                    <span class="text-nowrap"><?= isset($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : '-' ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 flex-wrap gap-1">
                    <span>Terakhir Login</span>
                    <span class="text-nowrap"><?= isset($item['last_login']) && $item['last_login'] ? date('d M Y H:i', strtotime($item['last_login'])) : '-' ?></span>
                </div>
                <div class="d-flex justify-content-between flex-wrap gap-1">
                    <span>Terakhir Update</span>
                    <span class="text-nowrap"><?= isset($item['updated_at']) ? date('d M Y H:i', strtotime($item['updated_at'])) : '-' ?></span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-4">
                <i class="fas fa-shield-alt fa-3x text-success mb-3 d-block"></i>
                <h6 class="fw-semibold">Keamanan Akun</h6>
                <p class="small text-body-tertiary mb-0">Ganti password secara berkala untuk menjaga keamanan akun Anda.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('show-passwords')?.addEventListener('change', function() {
    const t = this.checked ? 'text' : 'password';
    ['old-password', 'new-password', 'confirm-password'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.type = t;
    });
});
</script>
<?= $this->endSection() ?>
