<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Guru' : 'Tambah Guru' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui informasi dan kredensial staf' : 'Daftarkan staf pengajar baru' ?></p>
    </div>
    <a href="/admin/teachers" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/teachers<?= $item ? '/update/' . $item['id'] : '' ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Informasi Pribadi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-user text-body-tertiary"></i></span>
                            <input type="text" name="name" class="form-control border-start-0 ps-0" value="<?= $item['name'] ?? '' ?>" placeholder="e.g. Ahmad Fauzi" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Jabatan / Posisi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-briefcase text-body-tertiary"></i></span>
                            <input type="text" name="role" class="form-control border-start-0 ps-0" value="<?= $item['role'] ?? '' ?>" placeholder="e.g. Guru Matematika">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Pendidikan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-graduation-cap text-body-tertiary"></i></span>
                            <input type="text" name="education" class="form-control border-start-0 ps-0" value="<?= $item['education'] ?? '' ?>" placeholder="e.g. S1 Pendidikan Matematika - UNJ">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Pengalaman</label>
                        <textarea name="experience" class="form-control" rows="4" placeholder="Describe teaching experience, achievements, or special skills..."><?= $item['experience'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2 text-primary"></i>Media Sosial</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-facebook-f text-primary"></i></span>
                                <input type="text" name="social_facebook" class="form-control border-start-0 ps-0" value="<?= $item['social_facebook'] ?? '' ?>" placeholder="URL or username">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-instagram text-danger"></i></span>
                                <input type="text" name="social_instagram" class="form-control border-start-0 ps-0" value="<?= $item['social_instagram'] ?? '' ?>" placeholder="URL or username">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">LinkedIn</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-linkedin-in text-info"></i></span>
                                <input type="text" name="social_linkedin" class="form-control border-start-0 ps-0" value="<?= $item['social_linkedin'] ?? '' ?>" placeholder="URL or username">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Foto</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (!empty($item['photo'])): ?>
                            <?php $photoUrl = preg_match('#^https?://#', $item['photo']) ? $item['photo'] : base_url('uploads/teachers/' . $item['photo']); ?>
                            <img src="<?= $photoUrl ?>" alt="preview" class="rounded-3 shadow-sm" style="width:150px;height:150px;object-fit:cover" id="photo-preview">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-body-tertiary" style="width:150px;height:150px" id="photo-preview-placeholder">
                                <i class="fas fa-user fa-4x text-body-tertiary opacity-50"></i>
                            </div>
                            <img src="" alt="preview" class="rounded-3 shadow-sm d-none" style="width:150px;height:150px;object-fit:cover" id="photo-preview">
                        <?php endif; ?>
                    </div>
                    <label class="form-label small fw-medium">Foto</label>
                    <input type="file" name="photo" class="form-control form-control-sm" accept="image/*" id="photo-input">
                    <input type="hidden" name="old_photo" value="<?= $item['photo'] ?? '' ?>">
                    <div class="form-text small">Upload foto guru (maks 2MB, JPG/PNG/WebP)</div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-sliders-h me-2 text-primary"></i>Tampilan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0">
                        <div class="form-text small">Angka kecil tampil lebih dulu</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show" value="1" id="show" <?= (!isset($item['show']) || $item['show']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="show">Tampil di website</label>
                    </div>
                </div>
            </div>

            <?php if ($item): ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Waktu</h6>
                </div>
                <div class="card-body small text-body-tertiary">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Dibuat</span>
                        <span><?= isset($item['created_at']) ? date('d M Y H:i', strtotime($item['created_at'])) : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Diubah</span>
                        <span><?= isset($item['updated_at']) ? date('d M Y H:i', strtotime($item['updated_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Guru' : 'Simpan Guru' ?></button>
                <a href="/admin/teachers" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('photo-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-preview-placeholder');
            if (preview) {
                preview.src = ev.target.result;
                preview.classList.remove('d-none');
            }
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?>