<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Download' : 'Tambah Download' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui informasi file download' : 'Tambah file download baru' ?></p>
    </div>
    <a href="/admin/downloads" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/downloads<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-download me-2 text-primary"></i>Detail Download</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-heading text-body-tertiary"></i></span>
                            <input type="text" name="title" class="form-control border-start-0 ps-0" value="<?= $item['title'] ?? '' ?>" placeholder="e.g. Kalender Akademik 2026/2027" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Kategori <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-folder text-body-tertiary"></i></span>
                            <input type="text" name="category" class="form-control border-start-0 ps-0" value="<?= $item['category'] ?? '' ?>" placeholder="e.g. Kalender Akademik, Kurikulum, SK, RPP" required list="category-list">
                            <datalist id="category-list">
                                <option value="Kalender Akademik">
                                <option value="Kurikulum">
                                <option value="RPP">
                                <option value="SK">
                                <option value="Tata Tertib">
                                <option value="Juknis">
                                <option value="Formulir">
                                <option value="Materi">
                            </datalist>
                        </div>
                        <div class="form-text small">Ketik kategori baru atau pilih dari daftar</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Deskripsi singkat file download..."><?= $item['description'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-link me-2 text-primary"></i>File</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">URL Tautan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-external-link-alt text-body-tertiary"></i></span>
                            <input type="url" name="url" class="form-control border-start-0 ps-0" value="<?= $item['url'] ?? '' ?>" placeholder="https://drive.google.com/..." required>
                        </div>
                        <div class="form-text small">Gunakan link eksternal (Google Drive, dll.)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Ukuran File <small class="text-body-tertiary">(opsional)</small></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-weight text-body-tertiary"></i></span>
                            <input type="text" name="file_size" class="form-control border-start-0 ps-0" value="<?= $item['file_size'] ?? '' ?>" placeholder="e.g. 2.5 MB">
                        </div>
                        <div class="form-text small">Informasi ukuran file untuk pengunjung</div>
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
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Download' : 'Simpan Download' ?></button>
                <a href="/admin/downloads" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
