<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Program' : 'Tambah Program' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui informasi dan pengaturan program' : 'Tambah program unggulan baru' ?></p>
    </div>
    <a href="/admin/programs" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/programs<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-star me-2 text-primary"></i>Detail Program</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-heading text-body-tertiary"></i></span>
                            <input type="text" name="title" class="form-control border-start-0 ps-0" value="<?= $item['title'] ?? '' ?>" placeholder="e.g. Program Tahfidz Al-Qur'an" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Describe the program, its goals, and benefits for students..."><?= $item['description'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-palette me-2 text-primary"></i>Ikon</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary" style="width:80px;height:80px;font-size:2rem">
                            <i class="<?= $item['icon'] ?? 'fas fa-star' ?>"></i>
                        </div>
                    </div>
                    <label class="form-label small fw-medium">Font Awesome Class</label>
                    <input type="text" name="icon" class="form-control form-control-sm" value="<?= $item['icon'] ?? 'fas fa-star' ?>" placeholder="fas fa-graduation-cap" id="icon-input">
                    <div class="form-text small"><a href="https://fontawesome.com/search" target="_blank" class="text-decoration-none">Cari ikon <i class="fas fa-external-link-alt fa-xs"></i></a></div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-link me-2 text-primary"></i>Tautan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-external-link-alt text-body-tertiary"></i></span>
                            <input type="text" name="link_url" class="form-control border-start-0 ps-0" value="<?= $item['link_url'] ?? '' ?>" placeholder="/program-detail">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Teks Tombol</label>
                        <input type="text" name="link_text" class="form-control" value="<?= $item['link_text'] ?? 'Selengkapnya' ?>" placeholder="e.g. Selengkapnya">
                    </div>
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
                    <div class="d-flex justify-content-between mb-1 flex-wrap gap-1">
                        <span>Dibuat</span>
                        <span class="text-nowrap"><?= isset($item['created_at']) ? date('d M Y H:i', strtotime($item['created_at'])) : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap gap-1">
                        <span>Diubah</span>
                        <span class="text-nowrap"><?= isset($item['updated_at']) ? date('M d, Y H:i', strtotime($item['updated_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Program' : 'Simpan Program' ?></button>
                <a href="/admin/programs" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('icon-input')?.addEventListener('input', function() {
    const preview = this.closest('.card-body').querySelector('.rounded-3 i');
    if (preview) preview.className = this.value || 'fas fa-star';
});
</script>
<?= $this->endSection() ?>